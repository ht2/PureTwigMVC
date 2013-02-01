<?php
/**
 * Description of IncludeHandler
 *
 * @author JamesMullaney
 */
class IncludeHandler {
    
    private $types, $rel_base;


    public function __construct( $rel_base = "" ) {
        $this->rel_base = $rel_base;
        $this->types = array();
    }

    public function addTypes(){        
        $types = func_get_args();
        foreach( $types as $t ){
            if( gettype( $t ) === "string" ){
                $this->types[] = $t;
            }
        }
        
        
    }
    
    public function removeTypes(){
        $types = func_get_args();
        foreach( $types as $t ){
            if( gettype( $t ) === "string" ){
                $key = array_search($t, $this->types);
                if( $key ){
                    unset($this->types[$key]);
                }
            }
        }
    }

    public function getIncludes() {
        $includes = "";
        
        foreach( $this->types as $t ){
            $includes .= $this->getType($t);
        }
        
        return $includes;
    }

    public function getType( $t ) {
        $include = "";
        
        switch( $t ){
            case 'jquery':
                $include .= $this->includeJS('jquery.js');
                break;
            
            case 'bootstrap':
                $include .= $this->includeCSS('bootstrap.css');
                $include .= $this->includeCSS('bootstrap-responsive.css');
                $include .= $this->includeJS('bootstrap.js');
                break;
        }
        
        return $include;
    }
    
    public function includeCSS( $file, $relative=true, $location=null )
	{
        $file_loc = $this->makeLocation( 'view/css/', $location, $relative ).$file;
		return br('<link href="'.$file_loc.'" rel="stylesheet" type="text/css" />');
	}
    
	public function includeJS( $file, $relative=true, $location=null )
	{        
        $file_loc = $this->makeLocation( 'view/js/', $location, $relative ).$file;
		return br('<script src="'.$file_loc.'" type="text/javascript"></script>');
	}
    
    private function makeLocation( $default = "view/", $location=null, $relative=true){
        if( is_null($location) ){
            $location = $default;
        }
        
        if( $relative ){
            $location = $this->rel_base.$location;
        }
        return $location;
    }
}

?>
