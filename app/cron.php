<?php

	define( 'PUBWICH_CRON', true );
	define( 'PUBWICH', 1 );

	require( dirname(__FILE__) . '/core/Pubwich.php');
	Pubwich::init();
	Pubwich::rebuildCache();

?>
