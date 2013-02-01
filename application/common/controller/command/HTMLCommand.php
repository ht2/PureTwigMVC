<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HTMLCommand
 *
 * @author JamesMullaney
 */
class HTMLCommand extends ExtendedCommand {
    //put your code here
    
    protected $templates_dir = HTML;
    protected $twig_loader;
    
    /**
    * @var Twig_Environment
    */
    protected $twig;

    protected $includes = "";


    /**
    * @var IncludeHandler
    */
    protected $includeHandler;
    protected $page_title = "";

    public function __construct() {
        parent::__construct();
        
        $this->includeHandler = new IncludeHandler( "/".$this->base_dir);
    }

    public function execute( \INotification $notification ) {
        parent::execute( $notification );
        
        $this->loadTwig();
    }
    
    protected function loadTwig(){
        $this->twig_loader  = new Twig_Loader_Filesystem( $this->templates_dir );
        $this->twig         = new Twig_Environment( $this->twig_loader, array(
            'cache' => false
        ));
    }
    
    protected function printHTML( $template, $tokens=array() ) {
        
        $this->includes .= $this->includeHandler->getIncludes();
        
        
        $tokens['container_includes']   = $this->includes;
        $tokens['page_title']           = $this->page_title;
        $tokens['footer_year']          = date('Y');
        
        
        echo $this->twig->render($template, $tokens);
    }

}

?>
