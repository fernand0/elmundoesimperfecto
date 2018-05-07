<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Twitter
	 * @description Retrieves statuses from Twitter
	 * @version 1.1 (20090929)
	 * @author Rémi Prévost (exomel.com)
	 * @methods TwitterUser TwitterSearch
	 */

	require_once( 'php-oauth/index.php' );
	class Twitter extends Service {

		private $oauth;

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			parent::__construct( $config );
			$this->callback_function = array('Pubwich', 'json_decode');
		}

		/**
		 * @param array $config
		 * @return void
		 */
		public function setVariables( $config ) {
			$this->oauth = $config['oauth'];
		}

		/**
		 * @return string
		 */
		public function filterContent( $text ) {
			$text = strip_tags( $text );
			$text = preg_replace( '/(https?:\/\/[^\s\)]+)/', '<a href="\\1">\\1</a>', $text );
			$text = preg_replace( '/(^|\s)\#([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1<a href="https://twitter.com/hashtag/\\2">#\\2</a>', $text );
			$text = preg_replace( '/(^|\s)\@([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1@<a href="https://twitter.com/\\2">\\2</a>', $text );
			return $text;
		}

        public function processDataItem($item) {
			return array(
			            'text' => $item->text,
						'status' => $this->filterContent( $item->text ),
						'date' => Pubwich::time_since( $item->created_at ),
						'timestamp' => strtotime($item->created_at),
						);
        }

		public function populateItemTemplate( &$item ) {
			return $item;
		}

		public function oauthRequest( $params=array() ) {
			$method = $params[0];
			$additional_params = isset( $params[1] ) ? $params[1] : array();
			$base = isset( $params[2] ) ? $params[2] : "https://api.twitter.com/1.1/";

			$sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
			$consumer = new OAuthConsumer( $this->oauth['app_consumer_key'], $this->oauth['app_consumer_secret'] );
			$token = new OAuthConsumer( $this->oauth['user_access_token'], $this->oauth['user_access_token_secret'] );

			$request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $base.$method.'.json', $additional_params);
			$request->sign_request($sha1_method, $consumer, $token);

			return FileFetcher::get($request->to_url());
		}

	}

	class TwitterUser extends Twitter {

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->callback_getdata = array( array($this, 'oauthRequest'), array( 'statuses/user_timeline', array('screen_name'=>$config['username'], 'count'=>$config['total']) ) );
			$this->setURL('https://twitter.com/'.$config['username'].'/'.$config['total']); // for cache hash
			$this->username = $config['username'];
			$this->setItemTemplate('<li>{{{status}}} (<a href="{{{link}}}">{{{date}}}</a>)</li>'.PHP_EOL);
			$this->setURLTemplate('https://www.twitter.com/'.$config['username']);

			parent::__construct( $config );
		}

		public function processDataItem( $item ) {
			return parent::processDataItem( $item ) + array(
					'link' => sprintf( 'https://www.twitter.com/%s/statuses/%s/', $item->user->screen_name, $item->id_str ),
					'user_image' => str_replace('_normal.', '_bigger.', $item->user->profile_image_url_https),
					'user_name' => $item->user->name,
					'user_nickname' => $item->user->screen_name,
					'user_link' => sprintf( 'https://www.twitter.com/%s', $item->user->screen_name ),
			);
		}

	}

	class TwitterSearch extends Twitter {

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->callback_getdata = array( array($this, 'oauthRequest'), array( 'search/tweets', array('q'=>$config['terms'], 'count'=>$config['total'], 'result_type'=>'recent' ) ) );
			$this->setURL(
			    'https://api.twitter.com/1.1/search/tweets.json?q=' .
			    urlencode($config['terms']) .
			    '&count=' . $config['total'] .
			    '&result_type=recent'
			); // for cache hash
			$this->setItemTemplate( '<li><a href="{{{user_link}}}"><img class="item-media-thumbnail" width="48" height="48" src="{{{user_image}}}" alt="" /> <strong>@{{{user_nickname}}}:</strong></a> {{{status}}} (<a href="{{{link}}}">{{{date}}}</a>)</li>'.PHP_EOL );
			$this->setURLTemplate(
			    'https://twitter.com/search?q=' .
			    urlencode('"'.$config['terms'].'"')
			);

			parent::__construct( $config );
		}

		public function getData() {
			$data = parent::getData();
			// print_r($data);
			// return $data->results;
			return $data->statuses;
		}

		public function processDataItem( $item ) {
			return parent::processDataItem( $item ) + array(
						'link' => sprintf( 'https://www.twitter.com/%s/statuses/%s/', $item->user->screen_name, $item->id_str ),
						'user_image' => str_replace('_normal.', '_bigger.', $item->user->profile_image_url_https),
					    'user_name' => $item->user->name,
						'user_nickname' => $item->user->screen_name,
						'user_link' => sprintf( 'https://www.twitter.com/%s', $item->user->screen_name ),
			);
		}

	}
