<?php
class URLProxy extends BaseProxy
{
	const NAME = "URLProxy";
    
	public function __construct()
	{
		parent::__construct( self::NAME );
	}
    
   public function getParams( $base = null ){
       
        if( $base == null ){
            $base = $this->mysql->base_dir;
        }
        
       
        $request  = str_replace( $base, "", $_SERVER['REQUEST_URI']); 
        $all_params = explode( '?', $request );
        if( isset($all_params)){
            return explode("/", $all_params[0]);
        } else {
            return array();
        }
   }
   
   public function routingDebug(){
       
        $html  = para("You are viewing {VIEW}/{COMMAND}/{ID}/{SUB}");        
        $html .= para("RELATIVE_BASE = {RELATIVE_BASE}");
        $html .= para("BASE_DIR = {BASE_DIR}");
        $html .= para("ABSOLUTE_URL = {ABSOLUTE_URL}");
        $html .= para("SITE_ROOT = {SITE_ROOT}");        
        return $html;
   }
	
}

?>