<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Dribbble
	 * @description Fetch Dribbble shots
	 * @version 1.1 (20100728)
	 * @author Rémi Prévost (exomel.com)
	 * @methods DribbbleShots
	 * @deprecated 2015-04-08
	 * @see https://dribbble.com/api/deprecated
	 */

	class Dribbble extends Service {

		public function __construct( $config ){
			$this->callback_function = array('Pubwich', 'json_decode' );
			$this->setURL( sprintf('https://api.dribbble.com/players/%s/shots?per_page=%d', $config['username'], $config['total']));
			$this->setURLTemplate(sprintf('https://dribbble.com/%s', $config['username']));
			$this->setItemTemplate('<li><a title="{{{title}}}" href="{{{url}}}"><img src="{{{image_teaser_url}}}" alt="{{{title}}}" height="75" /></a></li>'."\n");
			parent::__construct( $config );
		}

		public function getData() {
			return parent::getData()->shots;
		}

		public function populateItemTemplate( &$item ) {
			return array(
				'player_avatar_url' => $item->player->avatar_url,
				'player_name' => $item->player->name,
				'player_location' => $item->player->location,
				'player_url' => $item->player->url,
				'player_id' => $item->player->id,
				'id' => $item->id,
				'title' => $item->title,
				'url' => $item->url,
				'image_url' => $item->image_url,
				'image_teaser_url' => $item->image_teaser_url,
				'height' => $item->height,
				'width' => $item->width,
			);
		}

	}


