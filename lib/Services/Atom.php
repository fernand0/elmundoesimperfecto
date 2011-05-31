<?php
	defined('PUBWICH') or die('No direct access allowed.');
    require_once 'FeedMetaService.php';

	/**
	 * @classname Atom
	 * @description Fetch Atom feeds
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
     * @author http://michael.haschke.biz/
	 * @methods None
	 */

	class Atom extends FeedMetaService {

		private $dateFormat;

		public function __construct( $config ){
			$this->setHeaderLink( array( 'url' => $config['url'], 'type' => 'application/atom+xml' ) );
			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->entry;
		}

		/**
		 * @return array
         * @since 20110531
		 */
        public function processDataItem( &$item ) {
			$link = htmlspecialchars($item->link->attributes()->href);
            $title = strip_tags(trim( $item->title ));
            $author = trim($item->author->name);
			$date = $item->published ? $item->published : $item->updated;
            $summary = strip_tags(trim( str_replace(array('<br>','<br/>'), ' ', $item->summary) ));
            $content = trim($item->content);

            if (!$title) $title = $summary ? $summary : strip_tags(str_replace(array('<br>','<br/>'), ' ', $content));
            if (strlen($title) > 140) $title = substr($title, 0, 140).'...';

            return array(
						'link' => $link,
						'title' => $title,
                        'author' => $author,
						'date' => Pubwich::time_since( $date ),
						'absolute_date' => date($this->dateFormat, strtotime($date)),
                        'summary' => $summary,
						'content' => $content,
			);
            
        }
	}
