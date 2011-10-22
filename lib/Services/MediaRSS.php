<?php
	defined('PUBWICH') or die('No direct access allowed.');
    require_once 'FeedMetaService.php';

	/**
	 * @classname MediaRSS
	 * @description Fetch RSS feeds with media items
	 * @version 20110601
	 * @author Rémi Prévost (exomel.com)
     * @author http://michael.haschke.biz/
     * @DEPRECATED 2011-10-22
	 */

	class MediaRSS extends FeedMetaService {

		private $dateFormat;

		public function __construct( $config ){
			$this->setHeaderLink( array( 'url' => $config['url'], 'type' => 'application/rss+xml' ) );
			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->channel->item;
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getParentData() {
            // TODO: check for usage of this method, remove it if not used!
			return parent::getData();
		}

		/**
		 * @return array
         * @since 20110531
		 */
        public function processDataItem( &$item ) {
			$link = htmlspecialchars($item->link);
            $title = strip_tags(trim( $item->title ));
            $author = trim($item->author);
			$date = $item->pubDate;
			$media = $item->children('http://search.yahoo.com/mrss/')->group;

             if ($media->content)
            {
                $media_content = (string) $media->content->attributes()->url;
            }
            else
            {
                $media_content = null;
            }

            if ($media->thumbnail)
            {
                $media_thumbnail = (string) $media->thumbnail->attributes()->url;
            }
            else
            {
                $media_thumbnail = null;
            }

            if ($media->title)
            {
                $title = trim($media->title);
            }

            if ($media->description)
            {
                $content = trim($media->description);
            }
            else
            {
                $content = null;
            }

            if (!$title) $title = strip_tags(str_replace(array('<br>','<br/>'), ' ', $content));
            if (strlen($title) > 200) $title = substr($title, 0, 200).'...';

            if (isset($this->dateFormat)) {
                $absolute_date = date($this->dateFormat, strtotime($date));
            }
            else {
                $absolute_date = null;
            }

            $timestamp = 0;
            $timestamp = strtotime($date);

            return array(
						'link' => $link,
						'title' => $title,
                        'author' => $author,
						'date' => Pubwich::time_since( $date ),
						'absolute_date' => $absolute_date,
                        'timestamp' => $timestamp,
						'content' => $content,
						'media_content' => $media_content,
						'media_thumbnail' => $media_thumbnail,
			);
        }
	}
