<?php

defined('PUBWICH') or die('No direct access allowed.');

/**
 * @classname Dribbble
 * @description Fetch Dribbble data
 */

class Dribbble extends Service {

	public function __construct( $config ){
		$this->callback_function = array('Pubwich', 'json_decode' );
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
		if ($this->data !== false && isset($this->data->message)) {
			$this->errorMessage = $this->data->message;
			return false;
		}

		return $this->data;
	}

	public function processDataItem($item) {
		return array(
			'id' => $item->id,
			'title' => $item->title,
			'date' => Pubwich::time_since($item->published_at),
			'timestamp' => $item->published_at,
			'description' => $item->description,
			'url' => $item->html_url,
			'image_url' => $item->attachments[0]->url,
			'likes' => $item->likes_count,
			'comments' => $item->comments_count,
			'image_teaser_url' => $item->attachments[0]->thumbnail_url,
		);
	}

	public function processDataStream() {
		if (count($this->getData()) < 1) return array();
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

class DribbbleShots extends Dribbble {

	public function __construct( $config ){
		$this->setURL(
			sprintf(
				'https://api.dribbble.com/v2/user/shots?access_token=%s',
				$config['access_token']
			)
		);
		parent::__construct($config);
	}
}

class DribbbleLikes extends Dribbble {

	public function __construct( $config ){
		$this->setURL(
			sprintf(
				'https://api.dribbble.com/v2/user/likes?access_token=%s',
				$config['access_token']
			)
		);
		parent::__construct($config);
	}

	public function processDataItem($item) {
		return parent::processDataItem($item->shot);
	}
}
