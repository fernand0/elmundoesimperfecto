<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Vimeo
	 * @description Fetch Vimeo videos
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

    require_once 'Feed.php';
    
	class Vimeo extends Feed {

		public function __construct( $config ){
			$config['list'] = isset($config['list']) ? $config['list']:'videos';
            $config['url'] = sprintf( 'http://vimeo.com/%s/%s/rss', $config['username'], $config['list'] );
			$config['link'] = 'http://www.vimeo.com/'.$config['username'].'/';
            $config['contenttype'] = 'application/rss+xml';

			parent::__construct( $config );

			$this->setItemTemplate(
                '<li>
                    <a href="{{{link}}}"><img src="{{{media_thumbnail_url}}}" alt="" class="item-media-thumbnail" height="75"/>
                    {{{title}}}</a> ({{{date}}})
                 </li>'."\n"
            );
		}

	}

