<?php
require_once PUREMVC.'patterns/proxy/Proxy.php';

class BaseProxy extends Proxy
{
    const NAME = "BaseProxy";
    /**
    * @var MySQLProxy
    */
    protected $mysql;
    
    protected $user_session, $admin_session;
    protected $default_table="default_table", $primary_key="default_key";
    
    protected $token_holders = array('{','}'), $token_prepend='', $token_exclusions = array('active');
    
    public function __construct( $proxyname = null ){
        parent::__construct( $proxyname );
        $this->mysql = $this->facade->retrieveProxy( MySQLProxy::NAME );
    }
    
    public function all( $active = true ){
        return $this->mysql
                ->startQuery()
                ->table( $this->default_table )
                ->runQuery( false, $active );
    }
    
    
    /**
     * Returns an array of tokens created from data object
     * Returns blank if no data is set     * 
     * 
     * @param array $fields fields to include/exclude
     * @param boolean $exclude exclude or include fields
     * @param boolean $all show all fields regardless of exceptions/includes
     * @return array
     */
    public function tokens( $fields=null, $exclude = true, $all= false ) {
        if( !$this->hasData() ) return array();        
        
        if( !$all ){
            if( is_null($fields) ) $fields = $this->token_exclusions;
            else {
                if( $exclude ){
                    $fields = array_merge($fields, $this->token_exclusions);
                }
            }
        }
        
        $tokens = array();
        $allowed_types = array('string', 'integer', 'double', 'boolean' );
        foreach( $this->data as $key=>$value ){
            $type = gettype($value);
            if(!in_array($type, $allowed_types)) continue;
            
            if( !$all ){
                if( $exclude && in_array($key, $fields) ){
                    continue;
                }

                if( !$exclude && !in_array($key, $fields) ){
                    continue;
                }
            }
            
            $field_name = strtoupper($key);
            $tokens[ $this->token_holders[0].$this->token_prepend.'_'.$field_name.$this->token_holders[1] ] = (string)$value;
        }
        
        return $tokens;
    }
    
    public function checkID($id){        
        $pkey = $this->primary_key;
        if( is_null($id) || !isset($id) ) $id = $this->data->$pkey;
        return $id;
    }
    
    public function set( $var ){
        switch( gettype($var) ){
            case 'object':
                $result = $var;
                break;
            case 'integer':
            case 'string':
                $result = $this->getByID( intval($var) );
                break;
            
            default:
                $result = null;
                break;
        }
        
        return $this->check($result);
    }
    
    
    public function check($result){
        if( $result !== false ){
            $this->data = $result;
        } else {
            $this->data = null;
        }
        return $this->hasData();
    }
    
    public function getByID( $id ){
        return $this->mysql
                ->startQuery()
                ->table($this->default_table)
                ->where( $this->primary_key, $id)
                ->runQuery(true);
    }
    
    
    public function get(){
        if( $this->hasData() ){
            return $this->data;
        } else {
            return false;
        }
    }
    
    protected function getResults( $active = true, $single = false, $table="", $where=array(), $ordering=array() ){
        
        $table = empty($table) ? $this->default_table : $table;
        
        $query = $this->mysql->startQuery()->table($table);
        
        foreach( $where as $w ){
            $query = $query->where( $w[0], $w[1] );
        }
        
        foreach( $ordering as $o ){
            $query = ( isset($o[1]) ) ? $query->orderBy( $o[0], $o[1] ) : $query->orderBy( $o[0] );
        }
        
        $results = $query->runQuery( $single, $active);
        $query->endQuery();
        
        return $results();
    }
   
    /**
     * Check whether the data var is set on the proxy
     * 
     * @return boolean Is the data set
     */
    public function hasData() {
        return isset($this->data);
    }
    
    
    /**
     * Takes the existing data object and maps the specified fields to the MySQL update function
     * @param array $fields the array of fields to update
     */
    public function update( $fields ) {
        $data = array();
        foreach( $fields as $f ){
            if( isset($this->data->$f ) ){
                $data[$f] = $this->data->$f;
            }
        }
        
        $key = $this->primary_key;
        $this->mysql->update($this->default_table, $data, $key, $this->data->$key);
    }
}

?>