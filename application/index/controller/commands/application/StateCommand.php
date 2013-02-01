<?php

require_once PUREMVC.'patterns/command/SimpleCommand.php';  
require_once PUREMVC.'interfaces/INotification.php';
require_once BASEDIR.'ApplicationFacade.php';

class StateCommand extends SimpleCommand
{
    private $view, $params;
    private $routes;
        
	public function execute( INotification $notification )
	{	
        $mysql = $this->facade->retrieveProxy( MySQLProxy::NAME );
        
        /*
         * Define the routes in arrays. The first route is the default if no view is defined
         */
        
        //Routing for the user
        $this->routes = array(
            'api'      =>  ApplicationFacade::API_TEST,
            'html'      =>  ApplicationFacade::HTML_TEST
        );
        
        //Set the current view
        $this->setView();
        
        //Update the routes based on the current view
        switch( $this->view ){
            default:
                $routes = $this->routes;
                break;
        }
        
        $this->handleRouting( $routes );
        
    }
    
    private function setView( $base = null ){      
        $this->params = $this->facade->retrieveProxy( URLProxy::NAME )->getParams($base);
        $this->view = ( isset( $this->params[1] ) ) ? strtolower($this->params[1]) : '';
    }
        
    
    private function handleRouting( $routes ){
        if( isset($routes[$this->view]) ){
            //if the route is set in the array then use it
            $the_route = $routes[$this->view];
        } else {
            //if not found then point towards the first route as default
            reset( $routes );
            $default_view = key($routes);            
            $the_route = $routes[$default_view];            
        }
        
        $this->facade->sendNotification( $the_route );
    }
}

?>
