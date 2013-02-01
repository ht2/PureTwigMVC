<?php

require_once PUREMVC.'interfaces/IView.php';
require_once PUREMVC.'interfaces/IMediator.php';
require_once PUREMVC.'interfaces/INotification.php';
require_once PUREMVC.'interfaces/IObserver.php';

class View implements IView
{
  protected $mediatorMap;
  protected $observerMap;
  protected static $instance;
    
  private function __construct()
  {
    $this->mediatorMap = array();
    $this->observerMap = array();	
    $this->initializeView();	
  }

  protected function initializeView(){}

  public static function getInstance()
  {
    if ( View::$instance == null ) View::$instance = new View();
    return View::$instance;
  }
      
  public function registerObserver( $notificationName, IObserver $observer )
  {
    if (isset($this->observerMap[ $notificationName ]) && $this->observerMap[ $notificationName ] != null)
    {
      array_push( $this->observerMap[ $notificationName ], $observer );
    }
    else
    {
      $this->observerMap[ $notificationName ] = array( $observer );	
    }
  }

  public function notifyObservers( INotification $notification )
  {
    if ($this->observerMap[ $notification->getName() ] != null)
    {
      $observers = $this->observerMap[ $notification->getName() ];
      foreach ($observers as $observer)
      {
        $observer->notifyObserver( $notification );
      }
    }
  }

  public function registerMediator( IMediator $mediator )
  {
    $this->mediatorMap[ $mediator->getMediatorName() ] = $mediator;
    $interests = $mediator->listNotificationInterests();
    
    if (count($interests) > 0)
    {
	    $observer = new Observer( "handleNotification", $mediator );
	    foreach ($interests as $interest)
	    {
	      $this->registerObserver( $interest,  $observer );
	    }			
    }
    
    $mediator->onRegister();
  }

  public function retrieveMediator( $mediatorName )
  {
    return $this->mediatorMap[ $mediatorName ];
  }

  public function hasMediator( $mediatorName )
  {
	  return $this->mediatorMap[ $mediatorName ] != null;
  }

  public function removeMediator( $mediatorName )
  {

    foreach ( $this->observerMap as &$observers )
    {
      foreach ( $observers as &$observer )
      {
        if ($observer->compareNotifyContext( $this->retrieveMediator( $mediatorName ) ) == true)
        {
          unset($observer);

          if ( count($observers) == 0 )
          {
            unset($observers);
            break;
          }
        }
      }
    }			

    $mediator = $this->mediatorMap[ $mediatorName ];
    unset($this->mediatorMap[ $mediatorName ]);
    if ($mediator != null) { $mediator->onRemove(); }
    
    return $mediator;
  }
}
?>