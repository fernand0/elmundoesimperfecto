<?php
	defined('PUBWICH') or die('No direct access allowed.');
    require_once 'Feed.php';

	/**
	 * @classname Atom
	 * @description Fetch Atom feeds
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
     * @author http://michael.haschke.biz/
	 * @methods None
	 */

	class Atom extends Feed {

		public function __construct( $config ){
			$config['contenttype'] = 'application/rss+xml';
			parent::__construct( $config );
		}

	}
