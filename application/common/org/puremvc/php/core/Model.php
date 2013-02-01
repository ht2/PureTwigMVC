<?php
 
require_once PUREMVC.'interfaces/IModel.php';
require_once PUREMVC.'interfaces/IProxy.php';

class Model implements IModel
{
  protected $proxyMap;
  protected static $instance;

  private function __construct()
  {
    $this->proxyMap = array();
    $this->initializeModel();	
  }

  protected function initializeModel(){}

  static public function getInstance()
  {
    if (Model::$instance == null) Model::$instance = new Model();
    return Model::$instance;
  }

  public function registerProxy( IProxy $proxy )
  {
    $this->proxyMap[ $proxy->getProxyName() ] = $proxy;
    $proxy->onRegister();
  }

  public function retrieveProxy( $proxyName )
  {
    return $this->proxyMap[ $proxyName ];
  }

  public function removeProxy( $proxyName )
  {
    $proxy = $this->proxyMap[ $proxyName ];
    unset($this->proxyMap[ $proxyName ]);
    $proxy->onRemove();
    
    return $proxy;
  }

  public function hasProxy( $proxyName )
  {
  	return $this->proxyMap[ $proxyName ] != null;
  }
}
?>
