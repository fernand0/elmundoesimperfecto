<?php
	defined('PUBWICH') or die('No direct access allowed.');
    require_once 'Feed.php';

	/**
	 * @classname MediaRSS
	 * @description Fetch RSS feeds with media items
	 * @version 20110601
	 * @author Rémi Prévost (exomel.com)
     * @author http://michael.haschke.biz/
     * @DEPRECATED 2011-10-22
	 */

	class MediaRSS extends Feed {

		public function __construct( $config ){
			$config['contenttype'] = 'application/rss+xml';
			parent::__construct( $config );
		}

	}
