<?php
	defined('PUBWICH') or die('No direct access allowed.');

    /**
	 * Récupérateur de contenu provenant de fichiers à distance
	 *
	 * @classname FileFetcher
	 */
	class FileFetcher {
		private $url;

		/**
		 * Récupère le contenu d'un fichier
		 *
		 * @param string $url L'URL du fichier
		 * @return mixed Le contenu du fichier en cas de succès. FALSE en cas d'échec
		 */
		static function get( $url, $headers=null ) {
			if ( function_exists('curl_init') ) {
				return self::getCurl( $url, $headers );
			}
			if ( ini_get('allow_url_fopen') ) {
				return self::getRemote( $url );
			}
			return false;
		}

		/**
		 * Récupère le contenu à l'aide `file_get_contents`
		 *
		 * @param string $url L'URL du fichier
		 * @return string Le contenu du fichier
		 */
		static function getRemote($url) {
			if (empty($url)) {
				 return false;
			}
			
			return file_get_contents($url);
		}

		/**
		 * Récupère le contenu à l'aide de l'extension cURL
		 *
		 * @param string $url L'URL du fichier
		 * @return string Le contenu du fichier
		 */
		static function getCurl( $url, $headers=null ) {
            $curl_info = curl_version();
            $curl_version = $curl_info['version'];

			$timeout = 10;
            if (defined('FETCHDATA_TIMEOUT')) {
                $timeout = FETCHDATA_TIMEOUT;
            }

            $ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $url);
			if ( $headers ) {
				curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers );
			}
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt ($ch, CURLOPT_USERAGENT, PUBWICH_NAME.'/'.PUBWICH_VERSION.' (Stream Aggregator; '.PUBWICH_WEB.'; Allow like Gecko) cURL/'.$curl_version);
			
            // proxy stuff @see http://php.net/manual/en/function.curl-setopt.php
            if (defined('PUBWICH_HTTPPROXYTUNNEL')) curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, PUBWICH_HTTPPROXYTUNNEL);
            if (defined('PUBWICH_PROXY')) curl_setopt($ch, CURLOPT_PROXY, PUBWICH_PROXY);
            if (defined('PUBWICH_PROXYPORT')) curl_setopt($ch, CURLOPT_PROXYPORT, PUBWICH_PROXYPORT);
            if (defined('PUBWICH_PROXYTYPE')) curl_setopt($ch, CURLOPT_PROXYTYPE, PUBWICH_PROXYTYPE);
            if (defined('PUBWICH_PROXYAUTH')) curl_setopt($ch, CURLOPT_PROXYAUTH, PUBWICH_PROXYAUTH);
            if (defined('PUBWICH_PROXYUSERPWD')) curl_setopt($ch, CURLOPT_PROXYUSERPWD, PUBWICH_PROXYUSERPWD);
            
            // get content
            $file_contents = curl_exec($ch);
			curl_close($ch);
			return $file_contents; 
		}
	}
