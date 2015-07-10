<?php

defined('PUBWICH') or die('No direct access allowed.');
require_once 'Feed.php';

/**
 * @classname Gnusocial
 * @description Extends Feed service class to simplify GNUsocial integration.
 * @since 2015-07-10
 * @author http://michael.haschke.biz/
 */
class Gnusocial extends Feed {

    public $serverurl;
    public $username;
    public $tag;

    public function __construct($config) {

        if (isset($config['serverurl'])) {
            $this->serverurl = trim($config['serverurl'], " \t\n\r\0\x0B/");
        }
        
        if (isset($config['username'])) {
            $this->username = trim($config['username'], " \t\n\r\0\x0B/");
        }
        
        if (isset($config['tag'])) {
            $this->tag = trim($config['tag'], " \t\n\r\0\x0B/");
        }
        
        if (isset($config['search'])) {
            $this->username = null;
            $this->tag = null;
            $this->search = trim($config['search']);

            $config['link'] = $this->serverurl . '/search/notice?q=' . urldecode($this->search);
            $config['url'] = $this->serverurl . '/search/notice/rss?q=' . urldecode($this->search);
        }
        else {
            $config['link'] = $this->serverurl .
                              ($this->username ? '/' . $this->username: '' ) .
                              ($this->tag ? '/tag/' . $this->tag: '' );
            $config['url'] = $config['link'] . '/rss';
        }
        
        $config['contenttype'] = 'application/rss+xml';

        parent::__construct( $config );
        
        $this->setItemTemplate(
            '<li>'.(
                    (!$this->username) ?
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
		$text = preg_replace( '/(^|\s)\#([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1<a href="'.$this->serverurl.'/tag/\\2">#\\2</a>', $text );
		$text = preg_replace( '/(^|\s)\!([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1<a href="'.$this->serverurl.'/group/\\2">!\\2</a>', $text );
		$text = preg_replace( '/(^|\s)\@([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1@<a href="'.$this->serverurl.'/\\2">\\2</a>', $text );
		return $text;
	}
		
    public function processDataItem($item) {
        
        $item = parent::processDataItem($item);
        
        $text = substr($item['title'], strpos($item['title'], ':') + 2);
        $user_name = substr($item['title'], 0, strpos($item['title'], ':'));
        $item['date'] = trim($item['date']);
        
        return $item + array(
            'user_name' => $user_name,
            'user_link' => $this->serverurl . '/' . $user_name,
            'text' => $text,
            'status' => $this->filterContent($text)
        );
    }

}
