<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Facebook
	 * @description Fetch Facebook statuses
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	require_once 'Feed.php';
	
	class FacebookPage extends Feed {

		public function __construct( $config ){
			$config['contenttype'] = 'application/atom+xml';
			$config['link'] = 'http://www.facebook.com/'.$config['username'].'/';
			$config['url'] = sprintf( 'https://www.facebook.com/feeds/page.php?format=atom10&id=%d', $config['id']);
			parent::__construct( $config );
		}

	}
