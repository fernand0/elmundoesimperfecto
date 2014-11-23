<?php
defined('PUBWICH') or die('No direct access allowed.');

/**
 * Pinboard.in Service Class
 *
 * Fetch bookmarks from Pinboard.in
 *
 * @classname Pinboard
 * @description Fetch Pinboard bookmarks
 * @version 1.0 (20110115)
 * @author Rémi Prévost (exomel.com)
 * @contributor Michael Haschke <http://michael.haschke.biz>
 * @methods None
 * @see https://pinboard.in/howto/#rss
 */

require_once 'Feed.php';

class Pinboard extends Feed {

	public function __construct( $config ){
		$config['link'] = 'http://pinboard.in/u:'.$config['username'].'/';
		if (isset($config['secret']) && $config['secret']) {
		    $config['url'] = sprintf( 'https://feeds.pinboard.in/rss/secret:%s/u:%s/', $config['secret'], $config['username'] );
	    }
	    else {
		    $config['url'] = sprintf( 'https://feeds.pinboard.in/rss/u:%s/', $config['username'] );
	    }
		parent::__construct( $config );
        $this->setItemTemplate(
            '<li>
                <a href="{{{link}}}">{{{title}}}</a>
                ({{{date}}})
                {{#category}}
                    <em><a href="{{{service_link}}}t:{{{category}}}/">{{{category}}}</a></em>
                {{/category}}
             </li>'."\n"
        );
	}

    /**
     * @return array
     * @since 20110531
     */
    public function processDataItem( $item ) {
        $item = parent::processDataItem($item);
        $item['service_link'] = $this->getConfigValue('link');
        $tags = explode(' ', $item['category']);
        if (isset($tags[0])) {
            $item['category'] = $tags[0];
        }
        return $item;
    }

}

