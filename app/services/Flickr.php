<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Flickr
	 * @description Retreives photos from Flickr
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods FlickrUser FlickrGroup FlickrTags
	 */

	class Flickr extends Service {

		public $sort;

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			parent::__construct( $config );
		}

		/**
		 * Sets some common variables (row, sort, compteur) and item template
		 * @param array $config The config array
		 * @return void
		 */
		public function setVariables( $config ) {
			$this->sort = isset( $config['sort'] ) ? $config['sort'] : 'date-posted-desc';
			$this->setItemTemplate('<li><a title="{{{title}}}" href="{{{link}}}"><img src="{{{photo}}}" alt="{{{title}}}" height="75" /></a></li>'."\n");
		}

		/**
		 * Return an array of key->value using the item data
		 * @return array
		 */
		public function populateItemTemplate( &$item ) {
			$path = $item['pathalias']!='' ? $item['pathalias'] : $item['owner'];
			return array(
						'link' => 'https://www.flickr.com/photos/'.$path.'/'.$item['id'].'/',
						'title' => htmlspecialchars( $item['title'] ),
						'photo' => $this->getAbsoluteUrl( $item )
			);
		}

		/**
		 * Return a Flickr photo URL
		 * @param array $photo Photo item
		 * @return string
		 */
		public function getAbsoluteUrl( $photo, $size= 's' ) {
			return sprintf( 'https://farm%d.static.flickr.com/%s/%s_%s_%s.jpg',
				$photo['farm'],
				$photo['server'],
				$photo['id'],
				$photo['secret'],
				$size
			);
		}

		/**
		 * Overcharge parent::getData()
		 * @return SimpleXMLElement
		 */
		public function getData() {
			$data = parent::getData();
			return $data->photos->photo;
		}

	}

	class FlickrUser extends Flickr {

		public function __construct( $config ) {
			parent::setVariables( $config );
			$this->setURL( sprintf( 'https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=%s&user_id=%s&sort=%s&extras=owner_name,path_alias&per_page=%d', $config['key'], $config['userid'], $this->sort, $config['total'] ) );
			$this->setURLTemplate('https://www.flickr.com/photos/'.$config['username'].'/');
			parent::__construct( $config );
		}

	}

	class FlickrGroup extends Flickr {

		private $groupname;

		public function __construct( $config ) {
			parent::setVariables( $config );
			$this->groupname = $config['groupname'];
			$this->setURL( sprintf( 'https://api.flickr.com/services/rest/?method=flickr.groups.pools.getPhotos&api_key=%s&group_id=%s&extras=owner_name,path_alias&per_page=%d', $config['key'], $config['groupid'], $config['total'] ) );
			$this->setURLTemplate('https://www.flickr.com/groups/'.$config['groupname'].'/');
			parent::__construct( $config  );
		}

		public function populateItemTemplate( &$item ) {
			$path = $item['pathalias']!='' ? $item['pathalias'] : $item['owner'];
			$original = parent::populateItemTemplate( $item );
			$original['link'] = 'https://www.flickr.com/photos/'.$path.'/'.$item['id'].'/in/pool-'.$this->groupname.'/';
			return $original;
		}

	}

	class FlickrTags extends Flickr {

		public function __construct( $config ) {
			parent::setVariables( $config );

			if ( !is_array( $config['tags'] ) ) { $config['tags'] = explode( ',', $config['tags'] ); }
			$maintag = $config['tags'][0];
			$config['tags'] = implode( ',', $config['tags'] );

			$this->setURL( sprintf( 'https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=%s&tags=%s&sort=%s&per_page=%d&extras=owner_name,path_alias', $config['key'], $config['tags'], $this->sort, $config['total'] ) );
			$this->setURLTemplate('https://www.flickr.com/photos/tags/'.$maintag.'/');
			parent::__construct( $config );
		}

	}
