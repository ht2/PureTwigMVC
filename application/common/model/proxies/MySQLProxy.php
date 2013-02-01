<?php
require_once PUREMVC.'patterns/proxy/Proxy.php';

class MySQLProxy extends Proxy
{
	const NAME = "MySQLProxy";
	private $host;
    private $database;
    private $user;
    private $pass;
    private $debug;
    
    public $mysqli;
    public $result;
    private $last_query;
    
    public $site_root;
    public $base_dir;
    public $admin_dir;
    public $full_url;
    public $current_query;

    public function __construct()
    {
		parent::__construct( MySQLProxy::NAME );
        
        $this->site_root = $_SERVER['SERVER_NAME'];
        
        switch( $_SERVER['SERVER_NAME'] )
        {
            default:
            case "localhost":
                $this->host         =   "localhost";
                $this->database     =   "curatr";
                $this->user         =   "root";
                $this->pass         =   "";
                $this->site_root    =   "http://ht2.dev/";
                $this->base_dir     =   "pure_twig/";
                $this->debug        =   true;
            break;
        }
        
        //$this->mysqli = new mysqli( $this->host, $this->user, $this->pass, $this->database );
        //$this->checkConnectError();
        
    }
    
    private function checkConnectError(){
        if ($this->mysqli->connect_errno) {
            $this->error("Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error );
        }
    }
    
    /**
     * Runs a MYSQL query
     * 
     * @return int the last inserted id
     */
    public function query($query)
    {
        $this->last_query = $query;
        $this->result = $this->mysqli->query($query);
        if( !$this->result )
            $this->error( $this->mysqli->error);
        return $this->last_id();
    }	
    
    /**
     * Returns the native MySQL result
     * 
     * @return array An array of objects
     */
    public function result()
    {
        return $this->result;
    }
    
    
    /**
     * Return an array of the objects retrieved from the last query
     * 
     * @param array $selective_fields An array of fields for selective return
     * @param boolean $include_selective_fields Should the selected fields be included(false|default) or exluded(true)?
     * @return array An array of objects
     */
    public function results( $selective_fields = null, $include_selective_fields = false )
    {
        $results = array();
        
        if( $this->result === false ) return array();
        
        while ($obj = $this->result->fetch_object())
            array_push( $results, $obj );
        
        
        foreach($results as $r) {
            if (isset($r->timestamp)) {
                $r->unixtimestamp = strtotime($r->timestamp);
            }
        }
        
        if( isset($selective_fields) && $selective_fields !== null && !empty($selective_fields) ){
            foreach( $results as &$result ){
                $result = ($include_selective_fields) ? $this->includeSelectiveFields($result, $selective_fields) : $this->excludeSelectiveFields($result, $selective_fields);
            }
        }
        
        return $results;
    }	
    
    private function includeSelectiveFields( $result, $fields=array() ){
        $new_result = new stdClass;
        foreach( $fields as $f ){
            if( isset($result->$f) ){
                $new_result->$f = $result->$f;
            }
        }
        return $new_result;
    }
    
    private function excludeSelectiveFields( $result, $fields=array() ){
        foreach( $fields as $f ){
            if( isset($result->$f) ){
                unset($result->$f);
            }
        }
        return $result;
    }
    
    /**
     * Return the first object from the last query or false if no result
     */
    public function singleResult( $selective_fields = null, $include_selective_fields = false ){
        $results = $this->results($selective_fields, $include_selective_fields);
        if( sizeof($results)>0 )
            return $results[0];		
        else
            return false;
    }

    /**
     * Returns the number of rows in the last query
     * 
     * @return int The number of rows in the last query
     */
    public function num_rows()
    {
        return mysqli_num_rows( $this->result ); 
    }

    /**
     * Returns the last inserted ID
     * 
     * @return int the last inserted ID
     */
    public function last_id()
    {
            return $this->mysqli->insert_id;
    }	

    //Close the mysql connection
    public function close()
    {
        $this->mysqli->close();
    }

    //Echos out the mysqli error via var_dump
    public function error( $error )
    {
        if( $this->debug ){
            echo para($error);
            echo para($this->last_query);
        }
        exit();
    }

    /**
     * Starts the safe query, clears out old query setups
     * 
     * @return MySQLProxy
     */
    public function startQuery(){
        $this->current_query = new stdClass();
        $this->current_query->fields = array();
        $this->current_query->joins = array();
        $this->current_query->where = array();
        $this->current_query->ordering = array();
        return $this;
    }
    
    /**
     * Ends the query without executing it
     * 
     * @return MySQLProxy
     */
    public function endQuery(){
        $this->current_query = null;
        return $this;
    }
    
    /**
     * Set the table for the current query
     * 
     * @param string $table the table to select from
     * @return MySQLProxy
     */
    public function table( $table ){
        $this->current_query->table = $this->safe($table);
        return $this;
    }
    
    /**
     * An array of fields to return with the current query
     * 
     * @param array $fields the fields to return
     * @return MySQLProxy
     */
    public function fields( $fields=array() ){
        foreach( $fields as &$f ){
            $f = $this->safe($f);
            
            $f = $this->checkForTable($f);
        }
        
        $this->current_query->fields = array_merge($this->current_query->fields, $fields );
        
        return $this;
    }
    
    /**
     * Join a table onto the current query
     * 
     * @param string $table the table to join with
     * @param string $join_field the field to prepare the join with
     * @return MySQLProxy
     */
    public function join( $table, $join_field, $table2=null, $join_field2=null ){
        if( !isset($this->current_query->joins) ){
            $this->current_query->joins = array();
        }
        
        if( !isset($table2) ) $table2 = $this->current_query->table;
        if( !isset($join_field2) ) $join_field2 = $join_field;
        
        $this->current_query->joins[] = " JOIN ".$table." ON ".$table.".$join_field = ".$table2.".".$join_field2;
        return $this;
    }
    
    /**
     * A WHERE filter
     * 
     * @param string $field field name to filter with
     * @param string $value the value of the field
     * @param string $operator the operator to use in the where statement
     * @param boolean $surround_value Surround the value with appostrophes
     * @return MySQLProxy
     */
    public function where( $field, $value, $operator='=', $surround_value=true ){
        if( !isset($this->current_query->where) ){
            $this->current_query->where = array();
        }
        
        $field = $this->checkForTable($field);
        $value = $this->safe($value);
        
        if( $surround_value ){
            $value = "'".$value."'";
        }
        
        $this->current_query->where[] = $field.' '.$operator.' '.$value;
        return $this;
    }
    
    /**
     * Order the current query
     * 
     * @param string $field field name to order by
     * @param string $type how to order (ASC|DESC)
     * @return MySQLProxy
     */
    public function orderBy( $field, $type='ASC' ){
        if( !isset($this->current_query->where) ){
            $this->current_query->ordering = array();
        }
        
        $field = $this->checkForTable($field);
        
        $this->current_query->ordering[] = $field.' '.$type;
        return $this;
    }
    
    /**
     * Limit the current query (LIMIT $var1 | LIMIT $var1, $var2)
     * 
     * @param int $var1 var1
     * @param int $var2 var2
     * @return MySQLProxy
     */
    public function limit( $var1, $var2=null ){
        $limit = ($var2 != null) ? " LIMIT $var1, $var2" : " LIMIT $var1";
        $this->current_query->limit = $limit;
        return $this;
    }
    
    
    /**
     * Execute the current query. Returns the class if no query to be executed
     * 
     * @param boolean $single Return 1 record?
     * @param boolean $active Return only active records
     * @param array $selective_fields An array of fields for selective return
     * @param boolean $include_selective_fields Should the selected fields be included(false|default) or exluded(true)?
     * @param boolean $debug var_dump the query
     * @return mixed false if no query, array if multiple, object if single
     */
    public function runQuery( $single=false, $active=true, $selective_fields = null, $include_selective_fields = false, $debug = false ){
        if( isset($this->current_query) ){
            if( !isset($this->current_query->table) ){
                $this->error("No table specified on query");
            }
            
            $table = $this->current_query->table;
            
            //FIELDS
            if( isset($this->current_query->fields) && !empty($this->current_query->fields)){
                $fields = implode(', ', $this->current_query->fields);
            } else {
                $fields = "$table.*";
            }
            
            //JOINS
             if( isset($this->current_query->joins) && !empty($this->current_query->joins) ){
                $the_joins = $this->current_query->joins;
            } else {
                $the_joins = array();
            }
            $join = implode( br(), $the_joins);
            
            //WHERE        
            $the_wheres = ( isset($this->current_query->where) && !empty($this->current_query->where) ) ? $this->current_query->where : array();            
            if( $active ){
                $the_wheres[] = $table.".active = 1";
            }      
            $where = ( sizeof($the_wheres)>0 ) ? " WHERE ".implode(' AND ', $the_wheres) : "";
            
            //ORDERING
            if( isset($this->current_query->ordering) && !empty($this->current_query->ordering) ){
                $the_ordering = $this->current_query->ordering;
            } else {
                $the_ordering = array();
            }
            $ordering = (sizeof($the_ordering)>0) ? " ORDER BY ".implode(', ', $the_ordering) : "";
            
            //LIMIT
            $limit = (isset($this->current_query->limit)) ? $this->current_query->limit : "";
            
            $query = "SELECT ".$fields;
            $query.= br()." FROM ".$table;
            $query.= (!empty($join)) ? br().$join : ""; 
            $query.= (!empty($where)) ? br().$where : ""; 
            $query.= (!empty($ordering)) ? br().$ordering : ""; 
            $query.= (!empty($limit)) ? br().$limit : ""; 
            
            if( $debug )
                var_dump($query);
            
            $this->query($query);
            $this->endQuery();
            
            if( $single ){
                return $this->singleResult( $selective_fields, $include_selective_fields );
            } else {
                return $this->results( $selective_fields, $include_selective_fields );
            }
            
        } else {
            return ($single) ? false : array();
        }
    }
    
    public function select( $table, $where = NULL )
    {
        $query = ( is_null( $where ) ) ? "SELECT * FROM $table WHERE active='1'" : "SELECT * FROM $table WHERE active='1' and $where";
        $this->query( $query );
    }

    //Select all records
    public function select_all( $table, $where = NULL )
    {
        $query = ( is_null( $where ) ) ? "SELECT * FROM $table WHERE 1" : "SELECT * FROM $table WHERE $where";
        $this->query( $query );
    }

    //Do an insert 
    //Returns the last inserted ID
    public function insert( $table, $data ) 
    {
        foreach( $data as $field => $value ) 
        {
            $fields[] = '`' . $field . '`';
            $values[] = "'" . $this->safe($value) . "'";
        }
        $field_list = join( ',', $fields );
        $value_list = join( ', ', $values );
        $query = "INSERT INTO `" . $table . "` (" . $field_list . ") VALUES (" . $value_list . ")";
        
        return $this->query( $query );
    }

    //Update an existing record(s)
    //Returns last inserted ID
    public function update($table, $data, $id_field, $id_value) 
    {
        foreach ($data as $field => $value) $fields[] = sprintf("`%s` = '%s'", $field, $this->safe($value));
        $field_list = join(',', $fields);
        $query = sprintf("UPDATE `%s` SET %s WHERE `%s` = %s", $table, $field_list, $id_field, intval($id_value));
        $this->query( $query );
    }
    
    //Removes the data from the database
    public function destroy( $table, $id_field, $id_value)
    {
        $id_value = $this->safe($id_value);
        $query = "DELETE FROM $table WHERE $id_field='$id_value'";
        $this->query( $query );
    }
    
    //Turns off the record specified
    public function delete($table, $id_field, $id_value) 
    {
        $this->update($table, array('active'=>0), $id_field, $id_value);
    }
    
    //Does a standard mysqli query
    public function self_query( $query )
    {
        return $this->mysqli->query( $query );
    }
    
    //Escapes any nasty stuff
    public function safe($value)
    {
        return $this->mysqli->real_escape_string($value);
    }
	
    public function array_in($id_array) {
        return "(".implode(',', $id_array).")";
    }
    
    private function checkForTable($field){
        $pos = strpos($field, '.' );            
        if( $pos == false && isset($this->current_query->table)){
            return $this->current_query->table.'.'.$field;
        } else {
            return $field;
        }
    }
	
}

?>