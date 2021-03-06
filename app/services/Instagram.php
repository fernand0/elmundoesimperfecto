<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * Instagram Service
     *
	 * Fetch recent pictures by a user on Instagram.
     *
     * @author Michael Haschke, http://michael.haschke.biz/
	 */


	/*
		TODO: enable usage of FileFetcher::get instead of file_get contents b/c
		config options like proxy is used there.
	*/
	class Instagram extends Service {

		public function __construct( $config ){
			parent::__construct( $config );
			$this->callback_function = array('Pubwich', 'json_decode');
			$this->cache_id = md5(implode('/', $config)) . '.data';
			$this->setURLTemplate('https://instagram.com/'.$config['username'].'/');
			$this->setItemTemplate('<li><a title="{{{title}}}" href="{{{link}}}"><img src="{{{thumbnail}}}" alt="{{{title}}}" height="75" /></a></li>'."\n");
		}

		function getData() {
		    if ($this->data === false) {
		        return false;
		    }

		    return $this->data->data;
		}

		public function populateItemTemplate( &$item ) {
			return $item;
		}

        public function processDataItem($item) {
			$date = Pubwich::time_since($item->created_time);
			$timestamp = $item->created_time;
			$link = $item->link;
			$ig_description = $item->caption->text;
			if ($ig_description) {
			    $title = strip_tags(explode("\n", $ig_description)[0]);
			}
			else {
			    $title = null;
			}
            $thumbnail = $item->images->thumbnail->url;

			return array(
				'date' => $date,
				'timestamp' => $timestamp,
				'link' => $link,
	            'title' => $title,
				'description' => $ig_description,
				'thumbnail' => $thumbnail
			);
        }
	}

	class InstagramApiData extends Instagram {
		public function __construct( $config ){
			$this->setURL(
				sprintf(
	                'https://api.instagram.com/v1/users/self/media/recent/?access_token=%s',
	                $config['access_token']
				)
			);
			parent::__construct( $config );
		}
	}

	class InstagramProfileData extends Instagram {

		public function __construct( $config ){
			$this->callback_getdata = array(array($this, 'fetchJsonFromWebProfile'), $config['username']);
			$this->setURL('https://instagram.com/'.$config['username'].'/');
			parent::__construct( $config );
			$this->cache_id = md5($this->getURL()) . '.data';
		}

		public function fetchJsonFromWebProfile($username) {
			$stringStart = '<script type="text/javascript">window._sharedData = ';
			$stringStop = ';</script>';
			if (
				($dataHtml = file_get_contents($this->getURL())) && //FileFetcher::get($this->urlTemplate, $this->http_headers) &&
				($dataStart = strpos($dataHtml, $stringStart)) &&
				($dataStop = strpos($dataHtml, $stringStop, $dataStart))
			) {
				$data = trim(
					substr(
						$dataHtml,
						$dataStart + strlen($stringStart),
						$dataStop - $dataStart - strlen($stringStart)
					)
				);
				return $data;
			}

			return false;
		}

		function getData() {
		    if ($this->data === false) {
		        return false;
		    }

		    return $this->data->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->edges;
		}

		public function processDataItem($item) {
			$item = $item->node;
			$timestamp = $item->taken_at_timestamp;
			$date = Pubwich::time_since($timestamp);
			$link = 'https://www.instagram.com/p/' . $item->shortcode;
			$title = $item->edge_media_to_caption->edges[0]->node->text;
            $thumbnail = $item->thumbnail_src;

			return array(
				'date' => $date,
				'timestamp' => $timestamp,
				'link' => $link,
	            'title' => $title,
				'thumbnail' => $thumbnail
			);
        }
	}
