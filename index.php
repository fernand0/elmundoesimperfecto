<?php
	error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 1);
	define( 'PUBWICH', 1 );
	require( dirname(__FILE__) . '/app/core/Pubwich.php' );
	Pubwich::init();
    Pubwich::processServices();
    Pubwich::processFilters();
	Pubwich::renderTemplate();
