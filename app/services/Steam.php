<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Steam
	 * @description Retrieves several things from Steam
	 * @version 1.0 
	 * @author warrows (warrows@gamer666.fr)
	 * @methods SteamLastPlayed
	 */

	class Steam extends Service {

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			parent::__construct( $config );
			$this->callback_function = array('Pubwich', 'json_decode');
		}
	}

	class SteamLastPlayed extends Steam {

		public function __construct( $config ) {
			parent::__construct( $config );

			$this->cache_id = md5(implode('/', $config)) . '.data';
			$this->setURLTemplate('http://steamcommunity.com/profiles/'.$config['userid'].'/'); // for cache hash
			$this->setItemTemplate('<li>{{{game}}} {{{playingtime}}} {{{date}}})</li>'.PHP_EOL);
			
			
			$this->setItemTemplate(
				'<li>
					<a href="{{link}}">
					{{#media_thumbnail_url}}
						<img src="{{{media_thumbnail_url}}}" class="item-media-thumbnail" alt="{{{game}}}"/>
					{{/media_thumbnail_url}}
					</a>
					{{#game}}
						<br/><a href="{{link}}">{{{game}}}</a> : {{{playingtime}}}
					{{/game}}
					{{#nogame}}
						No game played recently to display.
					{{/nogame}}
				 </li>'."\n"
			);
		}

		public function buildCache($Cache_Lite = null) {
            
			$userdata = sprintf(
					'https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v1/?key=%s&steamid=%s&count=%s',
                    trim($this->getConfigValue('apikey')),
                    trim($this->getConfigValue('userid')),
                    trim($this->getConfigValue('total'))
    	        );

			$this->setURL($userdata);

			return parent::buildCache($Cache_Lite);
		}

		function getData() {
		    if ($this->data === false) {
		        return false;
		    }
		    if ($this->data->response === false) {
		        return false;
		    }
		    if ($this->data->response->total_count === 0) {
		        return $this->data;
		    }
		    return $this->data->response->games;
		}

		public function populateItemTemplate( &$item ) {
			return $item;
		}
		
		public function processDataItem( $item ) {
			if (!isset($item))
			{
				return array(
					'nogame' => 1,
				);
			}
			else
			{
				return array(
					'game' => $item->name,
					'playingtime' => date('G\hi\m\n', mktime(0,$item->playtime_2weeks)),
					'media_thumbnail_url' => 'http://cdn.akamai.steamstatic.com/steamcommunity/public/images/apps/'.$item->appid.'/'.$item->img_icon_url.'.jpg',
					'link' => 'http://steamcommunity.com/app/'.$item->appid,
				);
			}
		}
	}
