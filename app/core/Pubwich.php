<?php
	defined('PUBWICH') or die('No direct access allowed.');

    define('PUBWICH_NAME', 'PubwichFork');
	define('PUBWICH_VERSION', '3.0.0');
    define('PUBWICH_WEB', 'https://github.com/haschek/PubwichFork');

	/**
	 * @classname Pubwich
	 */
	class Pubwich {

		static private $services,
		               $classes,
		               $columns,
		               $theme_user_url = null,
		               $theme_user_path = null,
		               $theme_parent_url = null,
		               $theme_parent_path = null,
		               $theme_default_url = null,
		               $theme_default_path = null,
		               $header_links,
		               $gettext = null,
		               $disableOutput = false;

		/**
		 * Application initialisation
		 */
		static public function init() {

			// Let’s modify the `include_path`
			$path_app = dirname(__FILE__).'/../';
			// $path_services = $path_core.'../services/';
			$path_libs = $path_app . 'vendor/';
			$path_pear_core = $path_libs . 'pear-core/';
			$path_pear_cache_lite = $path_libs . 'pear-cache-lite/';
			$path_user = $path_app . '../usr/';
			set_include_path(
			    realpath($path_user) . PATH_SEPARATOR
			    . realpath($path_app) . PATH_SEPARATOR
			    . realpath($path_libs) . PATH_SEPARATOR
			    . realpath($path_pear_core) . PATH_SEPARATOR
			    . realpath($path_pear_cache_lite) . PATH_SEPARATOR
			    //. realpath($path_user) . PATH_SEPARATOR
			    . get_include_path()
			);

			require_once( 'PEAR.php' );

			// Exception class
			require_once( 'core/PubwichError.php' );

			// Configuration files
			if ( !file_exists( $path_user . 'configuration/config.php' ) ) {
				throw new PubwichError( 'You must rename <code>usr/configuration/config.sample.php</code> to <code>usr/configuration/config.php</code> and edit the Web service configuration details.' );
			} else {
				require_once( 'configuration/config.php' );
			}

			// Application URL
			if (!defined('PUBWICH_URL'))
			{
			    // protocol
			    $prot = 'http://';
			    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off')
			    {
			        $prot = 'https://';
			    }
			    
			    // server name
			    $server = trim($_SERVER['SERVER_NAME'], '/');
			    
			    // server path
			    $path = trim(str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(__DIR__ . '/../../')), '/');
			    //$path = trim(str_replace(basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_NAME']), '/');
			    
			    define('PUBWICH_URL', $prot . $server . '/' . $path . '/');
			}

			// Internationalization class
			if ( defined('PUBWICH_LANG') && PUBWICH_LANG != '' ) {
				require_once( 'php-gettext/streams.php' );
				require_once( 'php-gettext/gettext.php' );
				self::$gettext = @new gettext_reader( new FileReader( dirname(__FILE__).'/../lang/'.PUBWICH_LANG.'/pubwich-'.PUBWICH_LANG.'.mo' ) );
			}

			// Events logger (and first message)
			require_once('core/PubwichLog.php');
			PubwichLog::init();
			PubwichLog::log( 1, Pubwich::_("Pubwich object initialization") );

            // Caching
            if (!defined('CACHE_LOCATION')) {
                define('CACHE_LOCATION', $path_user . 'cache/');
            }
            
			require_once( 'Cache/Lite.php' );
			
			// Other classes
			require_once( 'FileFetcher.php' );
			

			if ( !defined( 'PUBWICH_CRON' ) ) {
				require_once( 'mustache.php/src/Mustache/Autoloader.php' );
				Mustache_Autoloader::register();
			}

            self::controlPreprocessingOutput();

			// JSON support
			if ( !function_exists( 'json_decode' ) ) {
				throw new PubwichError('PHP version with json_decode support is required: http://php.net/manual/en/json.installation.php');
			}
			
            // Theme
            if (file_exists(dirname(__FILE__) . '/../../usr/themes/' . PUBWICH_THEME) === true) {
			    self::$theme_user_path = dirname(__FILE__) . '/../../usr/themes/' . PUBWICH_THEME;
			    self::$theme_user_url = PUBWICH_URL . 'usr/themes/' . PUBWICH_THEME;
			}

            if (file_exists(dirname(__FILE__) . '/../themes/' . PUBWICH_THEME) === true) {
			    self::$theme_parent_path = dirname(__FILE__) . '/../themes/' . PUBWICH_THEME;
			    self::$theme_parent_url = PUBWICH_URL . 'app/themes/' . PUBWICH_THEME;
			}
			
            if (file_exists(dirname(__FILE__) . '/../themes/' . 'default') === true) {
			    self::$theme_default_path = dirname(__FILE__) . '/../themes/' . 'default';
			    self::$theme_default_url = PUBWICH_URL . 'app/themes/' . 'default';
			}
			
			require_once( 'core/PubwichTemplate.php' );

			// PHP objects creation
			self::setClasses();

		}

        static function controlPreprocessingOutput() {

            $output_cache_content = false;

            if ($output_cache = self::getOutputCacheObject()) {
                $cache_id = md5(PUBWICH_URL) . '.output';
				$output_valid_cache_content = $output_cache->get( $cache_id );

                if ($output_valid_cache_content) {
                    $output_cache_content = $output_valid_cache_content;
                    PubwichLog::log( 1, Pubwich::_("Use valid output cache content.") );
                    self::$disableOutput = true;
                }
                elseif (ENABLE_INVALID_CACHE === true) {
                    // enabling alltime cache by setting lifetime unreachable high
                    $output_cache->setLifeTime(time()+666);
                    PubwichLog::log( 1, Pubwich::_("Use invalid output cache content.") );
                    $output_cache_content = $output_cache->get( $cache_id );
                }
            }

            if ($output_cache_content) {
                /*
                    enabling of post output processing (experimental but it seems to work properly)
                    why: aggregating feeds and linked data is a performance issue
                         because the app needs to wait for a response to all the
                         http requests. To overcome this problem we could echo an
                         (old) output cache and process then the data aggregation to
                         create an updated cache for the next request.
                    @see http://www.brandonchecketts.com/archives/performing-post-output-script-processing-in-php
                    @see http://de2.php.net/manual/en/features.connection-handling.php#93441
                */
                ob_end_clean();
                header("Connection: close");
                header("Content-Encoding: none");
                header('Content-Type: text/html; charset=UTF-8');
                ignore_user_abort(true); // optional
                ob_start();
                echo $output_cache_content;
                $size = ob_get_length();
                header("Content-Length: $size");
                ob_end_flush();     // Strange behaviour, will not work
                flush();            // Unless both are called !
                @ob_end_clean();

                //do post output processing here
                PubwichLog::log(1, Pubwich::_('Start post output processing.'));
            }

            return;
        }

		/**
		 * Translate a string according to the defined locale/
		 *
		 * @param string $single
		 * @return string
		 */
		static public function _($single, $plural=false, $number=false) {
            // gettext lib throws notices, so we turn off all error reporting
            // for the translation process

            if ($plural===false && $number===false)
			return (self::$gettext ) ? @self::$gettext->translate( $single ) : $single;

            return (self::$gettext ) ? @self::$gettext->ngettext($single, $plural, $number) : $single;
		}

		/**
		 * Set the $classes array
		 *
		 * @return void
		 */
		static public function setClasses() {
			require_once( 'core/Service.php' );
			$columnCounter = 0;
			foreach ( self::getServices() as $column ) {
				$columnCounter++;
				self::$columns[$columnCounter] = array();
				foreach( $column as $service ) {

					list( $name, $variable, $config ) = $service;
					$name = ucfirst($name);
					$service_instance = strtolower( $name . '_' . $variable );
					${$service_instance} = Pubwich::loadService( $name, $config );
					${$service_instance}->setVariable( $variable );
					self::$classes[$variable] = ${$service_instance};
					self::$columns[$columnCounter][] = &${$service_instance};

				}
			}

            if (count(self::$classes) < 1) {
                $error_text_service = array(
                        'Text', 'error_empty_config', array(
                            'title' => 'Error: Please configure PubwichFork',
                            'text' => '
                                <p>There is no configuration found. Please edit <code>config.php</code>
                                and follow the instructions there. Do not forget to edit the
                                service configuration in step 6 and the service grouping in step 7.</p>
                                <p>You may check the <a href="https://github.com/haschek/PubwichFork/wiki">PubwichFork documentation</a>
                                for more informations.</p>',
                        )
                    );

                self::setServices(
                    array(
                        array(
                            $error_text_service
                        )
                    )
                );

                self::setClasses();
            }
		}

		/**
		 * loadConfiguredServices() is a synomym to setClasses()
		 *
		 * @return void
		 */
		static public function loadConfiguredServices() {
			self::setClasses();
            return;
		}

		/**
		 * Get an array with all intern IDs of active services
		 *
		 * @return array
		 */
		static public function listActiveServices() {
			$services = self::$classes;
            if (!is_array($services)) return array();
            return array_keys($services);
        }

		/**
		 * Get an currently active service object
		 *
         * @param string $id ID of active service
		 * @return object
		 */
		static public function getActiveService($service_id) {
			$services = self::$classes;
            if (!isset($services[$service_id])) return false;
            return $services[$service_id];
        }

		/**
		 * Renders the template according to the current theme
		 *
		 * @return void
		 */
		static public function renderTemplate() {

            if (self::$disableOutput === true) {
                die();
            }

			$siteTemplate = self::getTemplateSnippet('site', '');
		    // fallback for deprecated index template
			$templateChrome = self::getThemeFileLocation('index.tpl.php');

		    if ( !$siteTemplate && !$templateChrome ) {
			    throw new PubwichError( sprintf( Pubwich::_( 'The file <code>%s</code> was not found. It has to be there.' ), realpath(self::getThemePath()).'/templates/site.mustache'));
		    }
            
			if ( !$templateSnippets = self::getThemeFileLocation('functions.php') ) {
                /*
                Removed: functions.php is only a fallback and deprecated
				throw new PubwichError( sprintf( Pubwich::_( 'The file <code>%s</code> was not found. It has to be there.' ), self::getThemePath().'/functions.php' ) );
                */
            }

            if ($output_cache = self::getOutputCacheObject()) {
                $output_cache->setLifeTime(0); // always overwrite cache
                $cache_id = md5(PUBWICH_URL) . '.output';
            }

            if ($output_cache) {
                ob_start();
                ob_implicit_flush(false);
            }
            if ($templateSnippets) {
                /* Fallback but deprecated */
                require_once($templateSnippets);
            }
            self::applyTheme();
            if ($templateChrome) {
                // Fallback: deprecated index template
			    include_once ($templateChrome);
			}
            elseif ($siteTemplate) {
                $m = new Mustache_Engine;
                echo $m->render(
                    $siteTemplate,
                    array(
                        'title' => PUBWICH_TITLE,
                        'content' => self::getLoop(),
                        'headerinclude' => self::getHeader(),
                        'footerinclude' => self::getFooter(),
                        'info' => sprintf(self::_('Fetched %s, proudly aggregated by %s.'), date('Y'), '<a class="pubwich" href="'.PUBWICH_WEB.'">'.PUBWICH_NAME.'</a>'),
                        'version' => PUBWICH_NAME . ' ' . PUBWICH_VERSION,
                        'themeurl' => self::getThemeUrl()
                    )
                );
            }
            if ($output_cache) {
                $data = ob_get_contents();
                ob_end_clean();
                $output_cache->save($data, $cache_id);
                if (self::$disableOutput === true) {
                    die();
                }
                echo($data);
            }
		}

        static function getOutputCacheObject() {
            if (defined('CACHE_LOCATION') && defined('OUTPUT_CACHE_LIMIT')) {
                $cache_options =  array(
                    'cacheDir' => CACHE_LOCATION,
                    'lifeTime' => OUTPUT_CACHE_LIMIT,
                    'readControl' => true,
                    'readControlType' => 'strlen',
                    'errorHandlingAPIBreak' => true,
                    'fileNameProtection' => false,
                    'automaticSerialization' => false
                );

                require_once( 'Cache/Lite/Output.php' );
                return new Cache_Lite_Output( $cache_options );
            }
            else {
                return false;
            }
        }

		/**
		 * @return string
		 */
		static public function getThemePath() {
			if (self::$theme_user_path) {
			    return self::$theme_user_path;
			}
			elseif (self::$theme_parent_path) {
			    return self::$theme_parent_path;
			}
			else {
			    return self::$theme_default_path;
			}
		}

		/**
		 * @return string
		 */
		static public function getThemeUrl() {
			if (self::$theme_user_url) {
			    return self::$theme_user_url;
			}
			elseif (self::$theme_parent_url) {
			    return self::$theme_parent_url;
			}
			else {
			    return self::$theme_default_url;
			}
		}

		/**
		 * Set the services to use
		 *
		 * @param array $services
		 * @return void
		 */
		static public function setServices( $services = array() ) {
			self::$services = $services;
		}

		/**
		 * @return array
		 */
		static public function getServices( ) {
			return self::$services;
		}

		/**
		 * Load a service file
		 *
		 * @param string $service The service name
		 * @param array $config The parameters
		 * @return Service
		 */
		static public function loadService( $service, $config ) {
			PubwichLog::log( 1, sprintf( Pubwich::_('Loading %s service'), $service ) );

			try {
    			@include_once('services/' . $service . '.php');
            } catch (Exception $e) {
				throw new PubwichError( sprintf( Pubwich::_( 'You told Pubwich to use the %s service, but the file <code>%s</code> couldn’t be found.' ), $service, $service.'.php' ) );
            }

			$classname = ( isset($config['method']) && $config['method'] ) ? $config['method'] : $service;
			if ( !class_exists( $classname ) ) {
				throw new PubwichError( sprintf( Pubwich::_( 'The class %s doesn\'t exist. Check your configuration file for inexistent services or methods.' ), $classname ) );
			}

			return new $classname( $config );
		}

		/**
		 * Rebuild the cache for each defined service
		 *
		 * @return void
		 */
		static public function rebuildCache() {

			PubwichLog::log( 1, Pubwich::_("Building application cache") );

			// First, let’s flush the cache directory
			$files = scandir(CACHE_LOCATION);
			foreach ( $files as $file ) {
				if ( substr( $file, 0, 1 ) != "." ) {
					unlink( CACHE_LOCATION . $file );
				}
			}

			// Then, we fetch everything
			foreach ( self::$classes as &$classe ) {
				$classe->buildCache();
			}

		}

		/**
		 * Apply box and items templates
		 *
		 * @return void
		 */
		static private function applyTheme() {

            $boxTemplate = self::getTemplateSnippet('box');
			if (!$boxTemplate) {
				throw new PubwichError( Pubwich::_('You must define a boxTemplate function in your theme\'s functions.php file.') );
			}

			foreach( self::$classes as $class ) {

				$templateSnippetNames = array();
				$parent = get_parent_class( $class );
				$classname = get_class( $class );
				$variable = $class->getVariable();

				if ( !$class->getBoxTemplate()->hasTemplate() && $boxTemplate ) {
					$class->setBoxTemplate( $boxTemplate );
				}

				if ( $parent != 'Service' ) {
					$templateSnippetNames = array(
						$parent,
						$parent . '_' . $classname,
						$parent . '_' . $classname . '_' . $variable,
					);
				} else {
					$templateSnippetNames = array(
						$classname,
						$classname . '_' . $variable,
					);
				}

				foreach ( $templateSnippetNames as $snippetname ) {
					
					$box_template = self::getTemplateSnippet($snippetname . '_box');
					$item_template = self::getTemplateSnippet($snippetname . '_item');

					if ($box_template) {
						$class->setBoxTemplate($box_template);
					}

					if ($item_template) {
						$class->setItemTemplate($item_template);
					}
				}
			}
		}

		/**
		 * Displays the generated HTML code
		 *
		 * @return string
		 */
		static public function getLoop() {

			$containerTemplate = self::getTemplateSnippet('container', '<div class="container-{{{number}}}">{{{content}}}</div>');

		    // fallback for deprecated column template
		    $columnTemplate = self::getTemplateSnippet('column', '');
			if ($columnTemplate) {
			    $containerTemplate = $columnTemplate;
            }
            
			$layoutTemplate = self::getTemplateSnippet('layout');

			$output_columns = array();
			$layoutTemplateAuto = '';
			$m = new Mustache_Engine;
			
			foreach( self::$columns as $col => $classes ) {
				$boxes = '';
				foreach( $classes as $class ) {
					$boxes .= $class->renderBox();
				}
				$output_columns['container'.$col] = $m->render($containerTemplate, array('number'=>$col, 'content'=>$boxes));

				$layoutTemplateAuto .= '{{{container'.$col.'}}} ';
			}
			
			if (!$layoutTemplate) {
			    $layoutTemplate = $layoutTemplateAuto;
			}
			
			return $m->render($layoutTemplate, $output_columns);
		}
		
		/**
		 * Get theme file location
		 *
		 * Try to read the file location of a given theme file.
		 *
		 * - checking user theme directory
		 * - checking default theme directory as fallback
		 *
		 * Returns the file location or false if it is not exist.
		 *
         * @since 2014-01-14
		 */
		static public function getThemeFileLocation($fileName) {
		    
		    if (file_exists(self::$theme_user_path . DIRECTORY_SEPARATOR . $fileName)) {
		        return self::$theme_user_path . DIRECTORY_SEPARATOR . $fileName;
		    }
		    
		    if (file_exists(self::$theme_parent_path . DIRECTORY_SEPARATOR . $fileName)) {
		        return self::$theme_parent_path . DIRECTORY_SEPARATOR . $fileName;
		    }
		    
		    if (file_exists(self::$theme_default_path . DIRECTORY_SEPARATOR . $fileName)) {
		        return self::$theme_default_path . DIRECTORY_SEPARATOR . $fileName;
		    }
		    
		    return false;
		}

		/**
		 * Get template snippet string.
		 *
		 * Try to read the string for the requested template snippet by:
		 *
		 * - checking for a existing function with the snippet name
		 * - checking for file with that name in the theme folder
		 * - check for fallback in default theme folder
		 *
		 * Returns the string of the template snippet or false if it is not
		 * available.
		 *
         * @since 2014-01-12
		 */
		static public function getTemplateSnippet($templateSnippetName, $fallbackString = false) {
		
		    static $snippets = array();
		    
		    // echo '<!-- TEMPLATESTRINGS: '.print_r($snippets, true).' -->'.PHP_EOL;
		    
		    // template snippet already known (or not available)?
		    
		    if (isset($snippets[$templateSnippetName])) {
		        return $snippets[$templateSnippetName];
		    }
		    
		    // check for funktion that gives back the template snippet string
		    
		    if (function_exists($templateSnippetName.'Template')) {
		        $snippets[$templateSnippetName] = call_user_func($templateSnippetName.'Template');
		        return $snippets[$templateSnippetName];
		    }
		    
		    if ($templateSnippetFile = self::getThemeFileLocation('templates'.DIRECTORY_SEPARATOR.$templateSnippetName.'.mustache'))
		    {
		        $snippets[$templateSnippetName] = file_get_contents($templateSnippetFile);
		        return $snippets[$templateSnippetName];
		    }
		    
		    // use fallbackstring
		    
		    if ($fallbackString) {
		        $snippets[$templateSnippetName] = $fallbackString;
		        return $snippets[$templateSnippetName];
		    }
		    
		    // no template snippet found
		    
		    return false;
		}

		/*
		 * Header hook
		 *
		 * @return string
		 */
		static public function getHeader() {
			$output = '';
			foreach ( self::$classes as $class ) {
				$link = $class->getHeaderLink();
				if ( $link ) {
					$output .= '<link rel="alternate" title="'.$class->title.' - '.$class->description.'" href="'.htmlspecialchars( $link['url'] ).'" type="'.$link['type'].'"/>'."\n";
				}
			}
			return $output;
		}

		/*
		 * Footer hook
		 *
		 * @return string
		 */
		static public function getFooter() {
			return '';
		}

		/**
		 * Return a date in a relative format
		 * Based on: http://snippets.dzone.com/posts/show/5565
		 *
		 * @param $original Date timestamp
		 * @return string
		 */
		static public function time_since( $original ) {

			$date_fromint = intval($original, 10);
			$date_fromstring = strtotime($original);
			$timestamp = ("$original" === "$date_fromint") ? $date_fromint : $date_fromstring;

			$today = time();
			$since = $today - $timestamp;

			if ( $since < 0 ) {
				return sprintf( Pubwich::_('just moments ago'), $since );
			}

			if ( $since >= ( 7 * 24 * 60 * 60 ) ) {
				return strftime( Pubwich::_('%e %B at %H:%M'), $timestamp );
			}

            $timechunks = array(
                array(60, 60,'1 second ago', '%d seconds ago'),
                array(60*60, 60, '1 minute ago', '%d minutes ago'),
                array(24*60*60, 24, '1 hour ago', '%d hours ago'),
                array(7*24*60*60, 7, '1 day ago', '%d days ago'),
			);

			for ( $i = 0, $j = count( $timechunks ); $i < $j; $i++ ) {
				$seconds = $timechunks[$i][0];
				$string_single = $timechunks[$i][2];
                $string_plural = $timechunks[$i][3];
				if ( $since < $seconds) {
                    $count = floor( $since / ($seconds/$timechunks[$i][1]));
					return sprintf( Pubwich::_($string_single, $string_plural, $count), $count );
				}
			}

		}

		/**
		 * @param string $str JSON-encoded object
		 * @return object PHP object
		 */
		static public function json_decode( $str ) {
			if ( function_exists( 'json_decode' ) ) {
				return json_decode( $str );
			} else {
				return Zend_Json::decode( $str, Zend_Json::TYPE_OBJECT );
			}
		}

		/**
		 * @return void
         * @since 20110531
		 */
        static public function processFilters() {

            /* the first and very simple approach to plug filter methods in,
             * they can be used to filter the now processed data before output
             *
             * for now put all filter functions in a filters.php in the theme
             * path, this should been enhanced later with paths for global
             * core filter, user filters, theme filters
             */

			if ( $themeFilters = self::getThemeFileLocation('filters.php')) {
				require_once( $themeFilters );
			}
            
			foreach( self::$classes as $service ) {

				$filtermethods = array();
				$parent = get_parent_class( $service );
				$classname = get_class( $service );
				$variable = $service->getVariable();

				if ( $parent != 'Service' ) {
					$filtermethods = array(
						$parent,
						$parent . '_' . $classname,
						$parent . '_' . $classname . '_' . $variable,
					);
				} else {
					$filtermethods = array(
						$classname,
						$classname . '_' . $variable,
					);
				}


				foreach ( $filtermethods as $filter ) {
					$stream_filter = $filter . '_filterStream';
					$item_filter = $filter . '_filterItem';

					if ( function_exists($stream_filter  ) ) {
						$stream_filter($service);
					}

					if ( function_exists( $item_filter ) && isset($service->data_processed) && is_array($service->data_processed)) {
                        foreach ($service->data_processed as $i => $v) {
                            $item_filter($service->data_processed[$i]);
                        }
					}
				}
			}

            return;
        }

		/**
		 * @return void
         * @since 20110531
		 */
        static public function processServices() {
			foreach (self::$classes as $classe) {
				$classe->init();
                $classe->prepareService();
			}
            return;
        }

	}
