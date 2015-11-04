<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * Instagram Service
     *
	 * Fetch recent pictures by a user on Instagram.
     *
     * @author Michael Haschke, http://michael.haschke.biz/
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

            // get user id by user name
	        $userdata = Pubwich::json_decode(file_get_contents(
	            sprintf(
                    'https://api.instagram.com/v1/users/search?q=%s&count=1&client_id=%s',
                    trim($this->getConfigValue('username')),
                    trim($this->getConfigValue('client_id'))
    	        )
	        ));

            if ($userdata && isset($userdata->data)) {
                $userid = $userdata->data[0]->id;
            }
            else {
                return false;
            }

			$recenturi = sprintf(
                'https://api.instagram.com/v1/users/%s/media/recent?client_id=%s',
                $userid,
                trim($this->getConfigValue('client_id'))
			);

			$this->setURL($recenturi);

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
			$description = $item->caption->text;
			if ($description) {
			    $title = strip_tags(explode("\n", $description)[0]);
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
				'description' => $description,
				'thumbnail' => $thumbnail
			);
        }
	}
