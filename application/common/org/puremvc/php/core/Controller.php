<?php

require_once PUREMVC.'patterns/observer/Observer.php';
require_once PUREMVC.'core/View.php';
require_once PUREMVC.'interfaces/IController.php';
require_once PUREMVC.'interfaces/INotification.php';
	
class Controller implements IController
{
  protected $view;
  protected $commandMap;
  protected static $instance;
  
  private function __construct()
  {
    $this->commandMap = array();
    $this->initializeController();
  }

  protected function initializeController()
  {
    $this->view = View::getInstance();
  }

  public static function getInstance()
  {
    if ( Controller::$instance == null ) Controller::$instance = new Controller();
    return Controller::$instance;
  }

  public function executeCommand( INotification $note )
  {
	$commandClassName = $this->commandMap[ $note->getName() ];
	$commandClassReflector = new ReflectionClass( $commandClassName );
	$commandClassRef = $commandClassReflector->newInstance();
	$commandClassRef->execute( $note );
  }

  public function registerCommand( $notificationName, $commandClassRef )
  {
    $this->commandMap[$notificationName] = $commandClassRef;
    $this->view->registerObserver( $notificationName, new Observer("executeCommand", $this) );
  }

  public function hasCommand( $notificationName )
  {
  	return $this->commandMap[ $notificationName ] != null;
  }
  
  public function removeCommand( $notificationName )
  {
    $this->commandMap[ $notificationName ] = null;
  }
}
?>
