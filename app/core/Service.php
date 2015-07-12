<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Service
	 */
	class Service {

		public $data,
               $cache_id,
               $cache_options,
               $title,
               $description,
               $urlTemplate,
               $username,
               $total,
               $method,
               $callback_function,
               $header_link,
               $http_headers;

		private $configuration, $url, $itemTemplate, $tmpTemplate, $boxTemplate, $tmpBoxTemplate;

		/**
		 * @constructor
		 */
		public function __construct( $config=array() ) {
			PubwichLog::log( 2, sprintf( Pubwich::_("Creating an instance of %s"), get_class( $this ) ) );

            // save original service config
            $this->configuration = $config;

			if (isset($config['title'])) $this->title = $config['title'];
			if (isset($config['description'])) $this->description = $config['description'];
			if (isset($config['total'])) $this->total = $config['total'];

			$id = md5( $this->getURL() );
			$this->cache_id = $id . '.data';

			if ( !$this->callback_function ) {
				$this->callback_function = array($this, 'simplexml_load_string');
			}

			$this->cache_options = array(
				'cacheDir' => CACHE_LOCATION,
				'lifeTime' => $this->getDisplacedCacheInvalidationTime(),
				'readControl' => true,
				'readControlType' => 'strlen',
				'errorHandlingAPIBreak' => true,
                'fileNameProtection' => false,
				'automaticSerialization' => false
			);

			$this->itemTemplate = new PubwichTemplate();
			if ( $this->tmpTemplate ) {
				$this->setItemTemplate( $this->tmpTemplate );
				$this->tmpTemplate = null;
			}

			$this->boxTemplate = new PubwichTemplate();
			if ( $this->tmpBoxTemplate ) {
				$this->setBoxTemplate( $this->tmpBoxTemplate );
				$this->tmpBoxTemplate = null;
			}
		}

		/**
         * @since 2011-11-13
		 */
        public function getConfigValue($key)
        {
            if (isset($this->configuration[$key])) {
                return $this->configuration[$key];
            }

            return null;
        }

		/**
		 * Use timeshift factor to randomize range of cache invalidation time.
         * Default: 0.5 (with 1h cache it means 0.5h to 1.5h)
         * @since 20110531
		 */
        public function getDisplacedCacheInvalidationTime()
        {
            $displacementfactor = 0.5; // default

            if (defined('CACHE_DISPLACEMENT')) {
                $displacementfactor = CACHE_DISPLACEMENT;
            }
            
            $cache_limit = 0; // default, no caching

            if (isset($this->configuration['cache_limit'])) {
                $cache_limit = intval($this->configuration['cache_limit']);
            }
            elseif (defined('CACHE_LIMIT')) {
                $cache_limit = CACHE_LIMIT;
            }

            $limit_min = ceil($cache_limit * (1 - $displacementfactor));
            $limit_max = ceil($cache_limit * (1 + $displacementfactor));

            $limit_new = rand($limit_min, $limit_max);

            return $limit_new;
        }

		/**
		 * Double SimpleXML method to fix the CDATA problem
         * @since 20110531
		 */
        static function simplexml_load_string($xmldata)
        {
            if (defined('LIBXML_VERSION') && LIBXML_VERSION >= 20621) {
                return simplexml_load_string($xmldata, 'SimpleXMLElement', LIBXML_NOCDATA);
            }
            else {
                return simplexml_load_string($xmldata);
            }
        }

		/**
		 * @return array
		 */
		public function getCacheOptions() {
			return $this->cache_options;
		}

		/**
		 * @return string
		 */
		public function getURL() {
			return $this->url;
		}

		/**
		 * @param string $url
		 * @return void
		 */
		public function setURL( $url ) {
			PubwichLog::log( 3, sprintf( Pubwich::_("Setting the URL for %s: %s"), get_class( $this ), $url ) );
			$this->url = $url;
		}

		/**
		 * @param string $url
		 * @return Service
		 */
		public function init() {
			PubwichLog::log( 2, sprintf( Pubwich::_("Initializing instance of %s"), get_class( $this ) ) );
			$url = $this->getURL();
			$Cache_Lite = new Cache_Lite( $this->cache_options );

			$data = $Cache_Lite->get( $this->cache_id);
			
			if (!$data) {
				$data = $this->buildCache( $Cache_Lite );
			}
			
			/* TODO:
			   it is really strange but only the lasfm cache strings do not
			   get returned correctly. They available in buildCache but after
			   returning the data var the string is empty here.
			*/
			
            // echo '<!-- init '.$this->cache_id.': '.(!empty($data)).' -->'.PHP_EOL; 
            
			if ($data) {
			    libxml_use_internal_errors( true );
			    if ( is_string( $data ) ) {
				    $data = call_user_func( $this->callback_function, $data );
			    }
			    libxml_clear_errors();
            }
            
			$this->data = $data;
			
			return $this;
		}

		/**
		 * [@param Cache_Lite $Cache_Lite]
		 * @return void
		 */
		public function buildCache( $Cache_Lite = null ) {
			PubwichLog::log( 2, sprintf( Pubwich::_('Rebuilding the cache for %s service' ), get_class( $this ) ) );
			$url = $this->getURL();

            if ( !$Cache_Lite ) {
                // create cache object
				$Cache_Lite = new Cache_Lite( $this->cache_options );
				// $Cache_Lite->get( $this->cache_id );
			}
			
			$data = false;
			
			if ( !isset($this->callback_getdata) || !$this->callback_getdata ) {
				$data = FileFetcher::get( $url, $this->http_headers );
			} else {
				$data = call_user_func( $this->callback_getdata[0], $this->callback_getdata[1] );
			}
			
			if ( $data !== false ) {
				$cacheWrite = $Cache_Lite->save( $data, $this->cache_id );
			}
            elseif (ENABLE_INVALID_CACHE === true) {
                // enabling alltime cache by setting lifetime unreachable high
                $Cache_Lite->setLifeTime(time()+666);
                //PubwichLog::log( 1, Pubwich::_("Use invalid output cache content.") );
                $data = $Cache_Lite->get( $this->cache_id );
            }
            
            // echo '<!-- buildCache '.$this->cache_id.': '.(!empty($data)).' -->'.PHP_EOL; 
            
            return $data;
		}

		/**
		 * @return string
		 */
		public function getData() {
			return $this->data;
		}

		/**
		 * @return string
         * @since 20110531
		 */
		public function getProcessedData() {
            if (isset($this->data_processed)) {
                return $this->data_processed;
            }
            else {
    			return $this->getData();
            }
		}

		/**
		 * @return void
         * @since 20110531
		 */
        public function prepareService() {
            if (method_exists($this, 'processDataStream')) {
                $data_processed = $this->processDataStream();
                if ($data_processed !== false) {
                    $this->data_processed = $data_processed;
                }
            }
            return;
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
		 * @return string
		 */
		public function getVariable() {
			return $this->variable;
		}

		/**
		 * @param string $variable
		 * @return void
		 */
		public function setVariable( $variable ) {
			$this->variable = $variable;
		}

		/**
		 * @param string $template
		 * @return void
		 */
		public function setURLTemplate( $template ) {
			$this->urlTemplate = $template;
		}

		/**
		 * @param string $template
		 * @return void
		 */
		public function setItemTemplate( $template ) {
			if ( !$this->itemTemplate ) {
				$this->tmpTemplate = $template;
			} else {
				$this->itemTemplate->setTemplate( $template );
			}
		}

		/**
		 * @return PubwichTemplate
		 */
		public function getItemTemplate() {
			return $this->itemTemplate;
		}

		/**
		 * @param string $template
		 */
		public function setBoxTemplate( $template ) {
			if ( !$this->boxTemplate ) {
				$this->tmpBoxTemplate = $template;
			} else {
				$this->boxTemplate->setTemplate( $template );
			}
		}

		/**
		 * @return PubwichTemplate
		 */
		public function getBoxTemplate() {
			return $this->boxTemplate;
		}

		/**
		 * return @array
		 */
		public function populateBoxTemplate() {
			return array(
				'id' => $this->getVariable(),
				'url' => $this->urlTemplate,
				'title' => $this->title,
				'description' => $this->description,
			);
		}
		
		public function getClassesStack() {
            
            $classes = class_parents($this, true);
            
            if (!$classes) {
                $classes = array();
                $classes[] = get_class($this);
                if (get_parent_class($this)) {
                    $classes[] = get_parent_class($this);
                }
            }
            else {
                $classes = array_merge(array(get_class($this)), $classes);
            }
            
            $ignoreKey = array_search('Service', $classes);
            
            if ($ignoreKey !== false) {
                unset($classes[$ignoreKey]);
            }
            
            return $classes;
		}
		
		public function getClassesStackStrings($separator = '_', $extensions = array()) {
		
            if ($extensions) {
                if (!is_array($extensions)) {
                    $extensions = array($extensions);
                }
            }
            else {
                $extensions = array();
            }
		
		    $classes = array_reverse($this->getClassesStack());
		    $classchains = array();
		    
		    for ($i = count($classes); $i > 0; $i = $i - 1) {
		        $classchains[] = implode($separator, array_slice($classes, 0, $i));
		    }
		    
		    $extchains = array();
		    
		    for ($i = count($extensions); $i > 0; $i = $i - 1) {
		        $extchains[] = implode($separator, array_slice($extensions, 0, $i));
		    }
		    
            // string patterns combining classes and service id extension is
            // deprecated but stay here for backwards compatibility
		    $patterns = array();
		    foreach (array_reverse($classchains) as $classchain) {
		        foreach ($extchains as $extchain) {
		            $patterns[] = $classchain . $separator . $extchain;
		        }
		    }
		    
		    $strings = array_merge(array_reverse($classchains), $extensions, $patterns);
		    
		    return $strings;
		}

		/*
		 * @return string
		 */
		public function renderBox( ) {

			$items = '';
			$classData = $this->getProcessedData();
            $compteur = 0;

            $htmlClass = strtolower(implode(' ', $this->getClassesStack()));
			//$htmlClass = strtolower( get_class( $this ) ).' '.( get_parent_class( $this ) != 'Service' ? strtolower( get_parent_class( $this ) ) : '' );

			if ( !$classData ) {
				$items = '<li class="nodata">' .
				         sprintf(
				            Pubwich::_('An error occured with the %s API. The data is therefore unavailable.'),
				            get_class($this)
				         ) . ' ' .
				         (isset($this->errorMessage) ? $this->errorMessage : '') .
				         '</li>';
				$htmlClass .= ' nodata';
			} else {
				foreach( $classData as $item ) {
					$compteur++;
					if ($this->total && $compteur > $this->total) { break; }
					$populate = $this->populateItemTemplate( $item );

                    /*
                    Removed b/c this can be done via filters
					if ( function_exists( get_class( $this ) . '_populateItemTemplate' ) ) {
						$populate = call_user_func( get_class( $this ) . '_populateItemTemplate', $item ) + $populate;
					}
					*/

					$this->getItemTemplate()->populate( $populate );
					$items .= $this->getItemTemplate()->output();
				}
			}

			$data = array(
				'class' => $htmlClass,
				'items' => $items
			);

			// Let the service override it
			$data = $this->populateBoxTemplate( $data ) + $data;

            /*
            Removed b/c this can be done via filters
			// Let the theme override it
			if ( function_exists( 'populateBoxTemplate' ) ) {
				$data = call_user_func( 'populateBoxTemplate', $this, $data ) + $data;
			}
			*/

			$this->getBoxTemplate()->populate( $data );
			return $this->getBoxTemplate()->output();
		}

		public function setHeaderLink( $link ) {
			$this->header_link = $link;
		}

		public function getHeaderLink() {
			return $this->header_link;
		}

	}
