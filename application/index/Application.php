<?php
require_once BASEDIR.'ApplicationFacade.php';

class Application
{
	public function __construct()
	{
		$facade = ApplicationFacade::getInstance();
		$facade->initialise();		
	}
}

?>
