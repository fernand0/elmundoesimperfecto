<?php

defined('PUBWICH') or die('No direct access allowed.');
require_once 'Feed.php';

/**
 * @classname IdenticaFeed
 * @description Extends Feed service class to simplify Identi.ca integration.
 * @since 2012-09-12
 * @author http://michael.haschke.biz/
 */
class IdenticaFeed  extends Feed {

    public $dateFormat;

    public function __construct($config) {

        $username = '';
        $this->hasUsername = null;
        if (isset($config['username'])) {
            $username = $config['username'].'/';
            $this->hasUsername = $config['username'];
        }
        
        $tag = '';
        $this->hasTag = null;
        if (isset($config['tag'])) {
            $tag = 'tag/'.$config['tag'].'/';
            $this->hasTag = $config['tag'];
        }
        
        $config['contenttype'] = 'application/rss+xml';
        $config['link'] = 'http://identi.ca/'.$username.$tag;
        $config['url'] = $config['link'].'rss';

        parent::__construct( $config );
        
        $this->setItemTemplate(
            '<li>'.(
                    (!$this->hasUsername) ?
                    '<strong><a href="{{{user_link}}}">@{{{user_name}}}</a></strong>: ':''
                ).
                '{{{status}}}
                (<a href="{{{link}}}">{{{date}}}</a>)
             </li>'.PHP_EOL
        );
    }

	/**
	 * @return string
	 * @author Rémi Prévost (exomel.com)
	 */
	public function filterContent( $text ) {
		$text = strip_tags( $text );
		$text = preg_replace( '/(https?:\/\/[^\s\)]+)/', '<a href="\\1">\\1</a>', $text );
		$text = preg_replace( '/(^|\s)\#([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1<a href="http://identi.ca/tag/\\2">#\\2</a>', $text );
		$text = preg_replace( '/(^|\s)\!([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1<a href="http://identi.ca/group/\\2">!\\2</a>', $text );
		$text = preg_replace( '/(^|\s)\@([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1@<a href="http://identi.ca/\\2">\\2</a>', $text );
		return $text;
	}
		
    public function processDataItem($item) {
        
        $item = parent::processDataItem($item);
        
        $text = substr($item['title'], strpos($item['title'], ':') + 2);
        $user_name = substr($item['title'], 0, strpos($item['title'], ':'));
        $item['date'] = trim($item['date']);
        
        return $item + array(
            'user_name' => $user_name,
            'user_link' => 'http://identi.ca/' . $user_name,
            'text' => $text,
            'status' => $this->filterContent($text)
        );
    }

}
