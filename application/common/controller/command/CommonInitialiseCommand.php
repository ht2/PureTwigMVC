<?php
require_once COMMON.'Session.php';
require_once COMMON.'Utils.php';

require_once PUREMVC.'patterns/command/SimpleCommand.php';  
require_once COMMON.'controller/command/ExtendedCommand.php';  
require_once COMMON.'controller/command/HTMLCommand.php';  
require_once COMMON.'controller/command/JSONCommand.php';  
require_once PUREMVC.'interfaces/INotification.php';

foreach( glob(COMMON.'model/proxies/*.php') as $filename ) require $filename;

require_once COMMON.'view/Twig/Autoloader.php'; 
require_once COMMON.'view/IncludeHandler.php'; 

class CommonInitialiseCommand extends SimpleCommand
{
	public function execute( INotification $notification )
	{	
        Twig_Autoloader::register();
        
		// Register Mediators / Proxies
		$this->facade->registerProxy( new MySQLProxy() );
		$this->facade->registerProxy( new URLProxy() );
		// Get current state
		$this->facade->sendNotification( ApplicationFacade::STATE );	
	}
}

?>
