<?php
	define( 'PUBWICH', 1 );
	require( dirname(__FILE__) . '/lib/Pubwich.php' );
	Pubwich::init();
    Pubwich::processServices();
    Pubwich::processFilters();
	Pubwich::renderTemplate();
