<?php
define( 'DEBUG', $_SERVER['REMOTE_ADDR']=='127.0.0.1' );
define( 'HOST', dirname( __FILE__ ) );
define( 'HTML', HOST.'/view/templates/' );
define( 'COMMON', HOST.'/application/common/' );
define( 'PUREMVC', HOST.'/application/common/org/puremvc/php/' );
define( 'BASEDIR', HOST.'/application/index/' );

require_once BASEDIR.'Application.php'; new Application();
?>