<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExtendedCommand
 *
 * @author JamesMullaney
 */
class ExtendedCommand  extends SimpleCommand{
    
    /**
    * @var MySQLProxy
    */
    protected $mysql_proxy;
    
    /**
    * @var URLProxy
    */
    protected $url_proxy;
    
    protected $view, $command, $id, $sub;
    
    protected $base_dir;


    public function __construct() {
        parent::__construct();
        
        $this->mysql_proxy  = $this->facade->retrieveProxy( MySQLProxy::NAME );
        $this->url_proxy    = $this->facade->retrieveProxy( URLProxy::NAME );
        
        $this->base_dir     = $this->mysql_proxy->base_dir;
    }
    
    protected function setParams(){
        
        $this->params = $this->url_proxy->getParams($this->base_dir);
        
        $view_default       = isset($this->params[1]) ? $this->params[1] : "";
        $command_default    = isset($this->params[2]) ? $this->params[2] : "";
        $id_default         = isset($this->params[3]) ? $this->params[3] : "";
        $sub_default        = isset($this->params[4]) ? $this->params[4] : "";
        
        $this->view     = $this->checkPost('view',      1,  $view_default );
        $this->command  = $this->checkPost('command',   1,  $command_default );
        $this->id       = $this->checkPost('id',        1,  $id_default );
        $this->sub      = $this->checkPost('sub',       1,  $sub_default );
    }
    
    
    protected function checkPost( $val, $type=1, $default=null, $striptags=false )
	{
        if( $default == null ){
            switch( $type ){
                default:
                case 1: $default = "";      break;
                case 2: $default = 0;       break;
                case 3: $default = false;   break;
                case 4: $default = array(); break;
            }
        }
        
		$return_val = isset( $_REQUEST[$val] ) ? $_REQUEST[$val] : $default;
		
        if( $striptags ) $return_val = strip_tags($striptags);
        
		switch( $type )
		{
			default:
            case 1: return trim($return_val);       break;			
            case 2: return intval($return_val); 	break;			
            case 3: return (boolean)$return_val;    break;        
            case 4: return (array)$return_val;      break;
		}
	}
    
    protected function redirect( $goto='/' )
	{
		header('Location:'.$goto );
		exit();
	}
    
}

?>
