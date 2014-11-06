<?php
	defined('PUBWICH') or die('No direct access allowed.');
    require_once 'Feed.php';

	/**
	 * @classname Atom
	 * @description Fetch Atom feeds
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
     * @author http://michael.haschke.biz/
     * @DEPRECATED 2011-10-22
	 */

	class Atom extends Feed {

		public function __construct( $config ){
			$config['contenttype'] = 'application/atom+xml';
			parent::__construct( $config );
		}

	}
