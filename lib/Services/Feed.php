<?php

defined('PUBWICH') or die('No direct access allowed.');

/**
 * @classname Feed
 * @description Service extension to read standard RSS and Atom feeds, using SimplePie.
 * @since 2011-10-22
 * @author http://michael.haschke.biz/
 */
class Feed  extends Service {

    public $dateFormat;

    public function __construct( $config ){

        // Include SimplePie
        include_once('SimplePie'.DIRECTORY_SEPARATOR.'SimplePieAutoloader.php');
        include_once('SimplePie'.DIRECTORY_SEPARATOR.'idn'.DIRECTORY_SEPARATOR.'idna_convert.class.php');

        if (!isset($config['contenttype'])) $config['contenttype'] = 'application/xml';

        $this->setHeaderLink( array( 'url' => $config['url'], 'type' => $config['contenttype'] ) );
        $this->total = $config['total'];
        if (isset($config['date_format']) && $config['date_format']) $this->dateFormat = $config['date_format'];
        $this->callback_function = array($this, 'loadStringIntoSimplePieObject');

        $this->setURL( $config['url'] );
        $this->setItemTemplate('<li><a href="{{link}}">{{{title}}}</a> {{{date}}}</li>'."\n");
        $this->setURLTemplate( $config['link'] );

        parent::__construct( $config );
    }

    /**
     * @return array of SimplePie feed item objects
     */
    public function getData() {
        static $id = 0;
        $data = parent::getData();
        return $data->get_items();
        $feedItem = $data->get_item($id);
        $id++;
        //print_r($data);
        //print_r($feedItem);
        //die($id);
        return $feedItem;
    }

    public function loadStringIntoSimplePieObject($xmldata)
    {
        // Create a new instance of the SimplePie object
        $feed = new SimplePie();
        $feed->enable_cache(false);
        // $feed->set_feed_url('http://simplepie.org/blog/feed/');
        $feed->set_raw_data($xmldata);
        $feed->init();
        $feed->handle_content_type();

        return $feed;

    }

    /**
     * @return array
     */
    public function populateItemTemplate( &$item ) {
        return $item;
    }

    /**
     * @return array
     * @since 20110531
     */
    public function processDataStream() {
        if (!method_exists($this, 'processDataItem')) return false;

        $data_source = $this->getData();
        $data_processed = array();

        if (!$data_source) return false;

        foreach ($data_source as $data_item) {
            $data_processed[] = $this->processDataItem($data_item);
        }

        return $data_processed;

    }

    /**
     * @return array
     * @since 20110531
     */
    public function processDataItem( &$item ) {

        // meta

        $link = $item->get_permalink();
        $author = ($author = $item->get_author())?trim($author->get_name()):'null';
        $category = ($category = $item->get_category())?trim($category->get_label()):'null';
        $date = $item->get_date('c');

        if (isset($this->dateFormat)) {
            $absolute_date = date($this->dateFormat, strtotime($date));
        }
        else {
            $absolute_date = null;
        }

        $timestamp = 0;
        $timestamp = strtotime($date);

        // content

        $summary = strip_tags(trim($item->get_description()), '<br>');
        $content = trim($item->get_content());
        $title = strip_tags(trim( $item->get_title() ));
        if (!$title) $title = $summary ? $summary : $content;
        $title = strip_tags(str_replace(array('<br>','<br/>'), ' ', $title));

        // media

        $media = $item->get_enclosure();
        if ($media)
        {
            $media_url = $media->get_link();
            $media_thumbnail_url = $media->get_thumbnail();

            $media_extension = $media->get_extension();
            $media_type = explode('/', $media->get_real_type());
            $media_type[] = array(null);
            $media_type = ($media_type = trim($media_type[0]))?$media_type:$media->get_medium();

            $media_caption = trim(strip_tags($media->get_description()));
            if (!$media_caption)
            {
                $media_caption = $media->get_caption();
                $media_caption = $media_caption?trim(strip_tags($media->get_text())):'';
            }
        }
        else
        {
            $media_url = null;
            $media_thumbnail_url = null;
            $media_extension = null;
            $media_type = null;
            $media_caption = null;
        }

        // comments TODO

        // compile array to return

        return array(
                'link' => $link,
                'author' => $author,
                'category' => $category,
                'date' => Pubwich::time_since( $date ),
                'absolute_date' => $absolute_date,
                'timestamp' => $timestamp,
                'title' => $title,
                'summary' => $summary,
                'content' => $content,
                'media_url' => $media_url,
                'media_thumbnail_url' => $media_thumbnail_url,
                'media_type' => $media_type,
                'media_caption' => $media_caption,
        );
    }

}
