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

		public function buildCache($Cache_Lite = null) {

			if (!$recenturi = $this->getURL()) {
				// get user id by user name
		        $userdata = Pubwich::json_decode(file_get_contents(
		            sprintf(
	                    'https://api.instagram.com/v1/users/search?q=%s&count=1&access_token=%s',
	                    trim($this->getConfigValue('username')),
	                    trim($this->getConfigValue('access_token'))
	    	        )
		        ));

	            if ($userdata && isset($userdata->data)) {
	                $userid = $userdata->data[0]->id;
	            }
	            else {
	                return false;
	            }

				$recenturi = sprintf(
	                'https://api.instagram.com/v1/users/%s/media/recent?access_token=%s',
	                $userid,
	                trim($this->getConfigValue('access_token'))
				);

				$this->setURL($recenturi);

			}

			return parent::buildCache($Cache_Lite);
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

		    return $this->data->entry_data->ProfilePage[0]->user->media->nodes;
		}

		public function processDataItem($item) {
			$timestamp = $item->date;
			$date = Pubwich::time_since($timestamp);
			$link = 'https://www.instagram.com/p/' . $item->code;
			$ig_description = $item->caption;
		    $title = strip_tags(explode("\n", $ig_description)[0]);
            $thumbnail = $item->thumbnail_src;

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
