<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname FeedMetaService
	 * @description Parental class for Atom and RSS service classes
	 * @since 20110531
     * @author http://michael.haschke.biz/
     * @DEPRECATED 2011-10-22
	 */

	class FeedMetaService extends Service {

		private $dateFormat;

		public function __construct( $config ){
			$this->total = $config['total'];
			if (isset($config['date_format']) && $config['date_format']) $this->dateFormat = $config['date_format'];
			$this->setURL( $config['url'] );
			$this->setItemTemplate('<li><a href="{{link}}">{{{title}}}</a> {{{date}}}</li>'."\n");
			$this->setURLTemplate( $config['link'] );
			parent::__construct( $config );
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
            
			return array();

        }
	}
