<?php
defined('PUBWICH') or die('No direct access allowed.');

/**
 * Delicious Service
 *
 * Fetch Delicious bookmarks.
 *
 * @version 1.1 (20090929)
 * @author Rémi Prévost (exomel.com)
 * @author Michael Haschke, http://michael.haschke.biz/
 * @methods None
 */

Pubwich::requireServiceFile( 'Feed' );
class Delicious extends Feed {

    public function __construct( $config ){
        $config['link'] = 'http://delicious.com/'.$config['username'].'/';
        $config['url'] = sprintf( 'http://feeds.delicious.com/v2/rss/%s?count=%s', $config['username'], $config['total'] );
        parent::__construct( $config );
        $this->setItemTemplate(
            '<li>
                <a href="{{{link}}}">{{{title}}}</a>
                ({{{date}}})
                {{#category}}
                    <em><a href="{{{service_link}}}{{{category}}}">{{{category}}}</a></em>
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
        return $item;
    }
}

