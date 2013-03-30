<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * Youtube Service
     *
	 * Fetch Youtube videos.
     *
	 * @version 1.2 (20100530)
	 * @author Rémi Prévost (exomel.com)
     * @author Michael Haschke, http://michael.haschke.biz/
	 */

	Pubwich::requireServiceFile( 'Feed' );
	class Youtube extends Feed {

		public function __construct( $config ){
			$config['list'] = isset($config['list']) ? $config['list']:'uploads';
            $config['url'] = 'http://gdata.youtube.com/feeds/api/users/'.
                             $config['username'].'/'.$config['list'].
                             '?v=2&orderby=published&max-results='.$config['total'];
			$config['link'] = 'http://youtube.com/user/'.$config['username'];
            $config['contenttype'] = 'application/atom+xml';

			parent::__construct( $config );

			$this->setItemTemplate(
                '<li>
                    <a href="{{{link}}}"><img src="{{{media_thumbnail_url}}}" alt="" class="item-media-thumbnail" height="75"/>
                    {{{title}}}</a> ({{{date}}})
                 </li>'."\n"
            );
		}

	}

