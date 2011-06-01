<?php
	defined('PUBWICH') or die('No direct access allowed.');
    require_once 'FeedMetaService.php';

	/**
	 * @classname RSS
	 * @description Fetch RSS feeds
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
     * @author http://michael.haschke.biz/
	 * @methods None
	 */

	class RSS extends FeedMetaService {

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
            $summary = strip_tags(trim( str_replace(array('<br>','<br/>'), ' ', $item->description) ));
            $content = trim($item->children('http://purl.org/rss/1.0/modules/content/')->encoded);
            // $comments_link = $item->comments;
			// $comments_count = $item->children('http://purl.org/rss/1.0/modules/slash/')->comments;
			$media = $item->children('http://search.yahoo.com/mrss/');

            if (!$title) $title = $summary ? $summary : strip_tags(str_replace(array('<br>','<br/>'), ' ', $content));
            if (strlen($title) > 200) $title = substr($title, 0, 200).'...';

            if (isset($this->dateFormat)) {
                $absolute_date = date($this->dateFormat, strtotime($date));
            }
            else {
                $absolute_date = null;
            }

            if ($media->content)
            {
                $media_content = $media->content->attributes();
                $medial_content = $media_content['url'];
            }
            else
            {
                $media_content = null;
            }

            if ($media->thumbnail)
            {
                $media_thumbnail = $media->thumbnail->attributes();
                $medial_thumbnail = $media_thumbnail['url'];
            }
            else
            {
                $media_thumbnail = null;
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
                        'summary' => $summary,
						'content' => $content,
						'media_content' => $media_content,
						'media_thumbnail' => $media_thumbnail,
						//'comments_link' => $comments_link,
						//'comments_count' => $comments_count,
			);
        }
	}
