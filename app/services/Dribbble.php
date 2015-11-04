<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Dribbble
	 * @description Fetch Dribbble data
	 */

	class Dribbble extends Service {

		public function __construct( $config ){
			$this->callback_function = array('Pubwich', 'json_decode' );
			$this->setURL(
				sprintf(
					'https://api.dribbble.com/v1/users/%s/shots?per_page=%d&access_token=%s',
					$config['username'],
					$config['total'],
					$config['access_token']
				)
			);
			$this->setURLTemplate(sprintf('https://dribbble.com/%s', $config['username']));
			$this->setItemTemplate(
				'<li>
					<a title="{{{title}}}" href="{{{url}}}">
						<img src="{{{image_teaser_url}}}" alt="{{{title}}} ({{{date}}})" height="75" />
					</a>
				</li>' . PHP_EOL
			);
			parent::__construct($config);
		}

		public function getData() {
			if ($data = parent::getData()) {
				return $data;
			}
		}

		public function processDataItem($item) {
			return array(
				'id' => $item->id,
				'title' => $item->title,
				'date' => Pubwich::time_since($item->created_at),
				'timestamp' => $item->created_at,
				'description' => $item->description,
				'url' => $item->html_url,
				'image_url' => $item->images->hidpi,
				'likes' => $item->likes_count,
				'comments' => $item->comments_count,
				'image_teaser_url' => $item->images->teaser,
				'height' => $item->height,
				'width' => $item->width,
			);
		}

		public function processDataStream() {
	        $data = parent::processDataStream();
	        $limit = $this->getConfigValue('total');

	        if ($limit) {
	            return array_slice($data, 0, $limit);
	        }

	        return $data;
	    }

		public function populateItemTemplate(&$item) {
	        return $item;
	    }

	}
