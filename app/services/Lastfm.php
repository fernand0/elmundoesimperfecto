<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @description Fetch data from Last.fm
	 * @author Rémi Prévost
	 * @author Gareth Simpson
	 * @author http://michael.haschke.biz/
	 * @methods LastFMRecentTracks LastFMLovedTracks LastFMWeeklyTracks LastFMWeeklyAlbums LastFMTopAlbums
	 * @version GPL2
	 */

	class LastFM extends Service {

		public $username, $size, $key, $classes, $compteur;

		public function setVariables( $config ) {
			$this->compteur = 0;
			$this->username = $config['username'];
			$this->key = $config['key'];
			$this->total = $config['total'];
			$this->setURLTemplate('http://www.last.fm/user/'.$config['username'].'/');
		}

		public function buildCache($Cache_Lite = null) {
			parent::buildCache($Cache_Lite);
		}

		public function getData() {
			return parent::getData();
		}

		public function init() {
			parent::init();
		}

	}

	class LastFMRecentTracks extends LastFM {

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->setURL( sprintf( 'http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&api_key=%s&user=%s&limit=%d', $this->key, $this->username, $this->total ) );
			$this->setItemTemplate('<li><a href="{{{link}}}"><strong>{{{track}}}</strong> — {{{artist}}}</a></li>'."\n");

			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			if (!isset($data->recenttracks) || !isset(get_object_vars($data->recenttracks)['track'])) {
				return array();
			}
			return get_object_vars($data->recenttracks)['track'];
		}

        /**
         * @return array
         * @since 20120318
         */
        public function processDataItem( $item ) {
			$album = $item->album;
			$artist = $item->artist;
			$title= $item->name;
			$this->compteur++;
			return array(
				'link' => htmlspecialchars( $item->url ),
				'artist' => $artist,
				'album' => $album,
				'track' => $title,
				'date' => $item->date,
			);
        }

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
            return $item;
		}

	}


	class LastFMLovedTracks extends LastFM {

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->setURL( sprintf( 'http://ws.audioscrobbler.com/2.0/?method=user.getlovedtracks&api_key=%s&user=%s&limit=%d', $this->key, $this->username, $this->total ) );
			$this->setItemTemplate('<li><a href="{{{link}}}"><strong>{{{track}}}</strong> — {{{artist}}}</a></li>'."\n");

			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			if (!isset($data->lovedtracks) || !isset(get_object_vars($data->lovedtracks)['track'])) {
				return array();
			}
			return get_object_vars($data->lovedtracks)['track'];
		}

        /**
         * @return array
         * @since 20120318
         */
        public function processDataItem( $item ) {
			$artist = $item->artist->name;
			$title= $item->name;
			$this->compteur++;
			return array(
				'link' => htmlspecialchars( $item->url ),
				'artist' => $artist,
				'track' => $title,
				'date' => $item->date,
			);
        }

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
            return $item;
		}

	}


	class LastFMWeeklyTracks extends LastFM {

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->setURL( sprintf( 'http://ws.audioscrobbler.com/2.0/?method=user.getweeklytrackchart&api_key=%s&user=%s', $this->key, $this->username ) );
			$this->setItemTemplate('<li><a href="{{{link}}}"><strong>{{{track}}}</strong> — {{{artist}}}</a> ({{{playcount}}}x)</li>'."\n");

			parent::__construct( $config );
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			if (!isset($data->weeklytrackchart) || !isset(get_object_vars($data->weeklytrackchart)['track'])) {
				return array();
			}
			return get_object_vars($data->weeklytrackchart)['track'];
		}

        /**
         * @return array
         * @since 20120318
         */
        public function processDataItem( $item ) {
			$artist = $item->artist;
			$title= $item->name;
			$this->compteur++;
			return array(
				'link' => htmlspecialchars( $item->url ),
				'artist' => $artist,
				'track' => $title,
				'date' => $item->date,
				'playcount' => $item->playcount,
			);
        }

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
            return $item;
		}

	}


	class LastFMWeeklyAlbums extends LastFMTopAlbums {
		public function __construct( $config ) {
		    $config['period'] = '7day';
			parent::__construct( $config );
		}
	}

	class LastFMTopAlbums extends LastFM {
		public function __construct( $config ) {
			parent::setVariables( $config );
			$period = isset($config['period']) ? $config['period'] : 'overall';
			$this->setURL( sprintf( 'http://ws.audioscrobbler.com/2.0/?method=user.gettopalbums&api_key=%s&user=%s&period=%s', $this->key, $this->username, $period ) );
			$this->setItemTemplate('<li><a href="{{{link}}}">{{#image_medium}}<img class="item-media-thumbnail" src="{{{image_medium}}}" width="{{{size}}}" height="{{{size}}}" alt="{{{title}}}">{{/image_medium}}<strong>{{{artist}}}</strong> {{{album}}}</a> ({{{playcount}}}x)</li>'."\n");
			parent::__construct( $config );
		}

		public function setVariables( $config ) {
            parent::setVariables($config);
			$this->size = $config['size'];
		}

		/**
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			//var_dump($data); die();
			if (!isset($data->topalbums) || !isset(get_object_vars($data->topalbums)['album'])) {
				return array();
			}
			return get_object_vars($data->topalbums)['album'];
			return $data->topalbums->album;
		}

        /**
         * @return array
         * @since 20120318
         */
        public function processDataItem( $item ) {
			$images = new StdClass;
			foreach( $item->image as $k=>$i ) {
				$key = (string) $i['size'];
				$val = (string) $i;
				$images->{$key} = $val;
			}
			return array(
				'size' => $this->size,
				'link' => $item->url,
				'playcount' => $item->playcount,
				'album' => $item->name,
				'artist' => $item->artist->name,
				'image_small' => $images->small,
				'image_medium' => $images->medium,
				'image_large' => $images->large,
				'image_extralarge' => $images->extralarge,
			);
        }

		/**
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
            return $item;
		}
	}
