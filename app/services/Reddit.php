<?php
defined('PUBWICH') or die('No direct access allowed.');

/**
 * @classname Reddit
 * @description Fetch Reddit stories
 * @version 1.0 (20100530)
 * @author Rémi Prévost (exomel.com)
 * @contributor Michael Haschke <http://michael.haschke.biz/>
 * @methods RedditLiked
 * @see http://www.reddit.com/dev/api
 */

class Reddit extends Service {

	private $base = 'http://www.reddit.com';

	public function __construct( $config ){
		$this->http_headers['User-Agent'] = 'php:' . PUBWICH_NAME . ':' . PUBWICH_VERSION . ' (by /u/' . $config['username'] . ')';
		parent::__construct( $config );
		$this->total = $config['total'];
		$this->callback_function = array('Pubwich', 'json_decode');
	}

	public function getData() {
		if ($json = parent::getData()) {
			return $json->data->children;
		}

		return false;
	}

    public function processDataItem( $item ) {
		$data = $item->data;

        $date = $data->created_utc;

        if (isset($this->dateFormat)) {
            $absolute_date = date($this->dateFormat, strtotime($date));
        }
        else {
            $absolute_date = null;
        }

        $timestamp = 0;
        $timestamp = $date;

		//var_dump($data); die();

		return array(
			'base' => $this->base,
			'title' => isset($data->title) ? $data->title : $data->link_title,
			'link' => isset($data->permalink) ? $this->base.$data->permalink : $this->base.$data->link_permalink,
			'url' => isset($data->url) ? $data->url : $data->link_url,
			'subreddit' => $data->subreddit,
			'author' => $data->author,
			'score' => $data->score,
			'comments' => $data->num_comments,
			'over_18' => $data->over_18 == 'true',
			'domain' => isset($data->domain) ? $data->domain : false,
            'date' => Pubwich::time_since( $date ),
            'absolute_date' => $absolute_date,
            'timestamp' => $timestamp,
		);
    }

	public function populateItemTemplate( &$item ) {
        return $item;
	}

}

class RedditLiked extends Reddit {
	public function __construct( $config ){
		$this->setURLTemplate('https://www.reddit.com/user/'.$config['username'].'/liked/');
		$this->setURL( sprintf( 'https://www.reddit.com/user/liked.json?username=%s', $config['username'] ) );
		$this->setItemTemplate(
		    '<li>
		        <a href="{{{url}}}">{{{title}}} {{#domain}}/ <span>{{{domain}}}</span>{{/domain}}</a>
                (<a href="{{{link}}}">{{{date}}}</a> in <a href="{{{base}}}/r/{{{subreddit}}}/">{{{subreddit}}}</a>)
		    </li>'."\n");
		parent::__construct( $config );
	}
}
