<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Atom
	 * @description Fetch Atom feeds
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods None
	 */

	class Atom extends Service {

		private $dateFormat;

		public function __construct( $config ){
			$this->total = $config['total'];
			$this->dateFormat = $config['date_format'];
			$this->setURL( $config['url'] );
			$this->setHeaderLink( array( 'url' => $config['url'], 'type' => 'application/atom+xml' ) );
			$this->setItemTemplate('<li><a href="{{link}}">{{{title}}}</a> {{{date}}}</li>'."\n");
			$this->setURLTemplate( $config['link'] );
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
		 */
		public function populateItemTemplate( &$item ) {
			return array(
						'link' => $item['link'],
						'title' => $item['title'],
						'date' => $item['date'],
						'absolute_date' => $item['absolute_date'],
						'content' => $item['content'],
			);
		}

		/**
		 * @return array
         * @since 20110531
		 */
        public function processDataStream() {
            $data_source = $this->getData();
            $data_processed = array();

            if (!$data_source) return false;
            if (!method_exists($this, 'processDataItem')) return false;

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
			$link = htmlspecialchars($item->link->attributes()->href);
			$date = $item->published ? $item->published : $item->updated;
            $title = strip_tags(trim( $item->title ));
            $summary = strip_tags(trim( str_replace(array('<br>','<br/>'), ' ', $item->summary) ));
            $content = trim($item->content);
            $author = trim($item->author->name);
            if (!$title) $title = $summary ? $summary : strip_tags(str_replace(array('<br>','<br/>'), ' ', $content));
            if (strlen($title) > 140) $title = substr($title, 0, 140).'...';
			return array(
						'link' => $link,
						'title' => $title,
                        'author' => $author,
						'date' => Pubwich::time_since( $date ),
						'absolute_date' => date($this->dateFormat, strtotime($date)),
						'content' => $content,
			);
            
        }
	}
