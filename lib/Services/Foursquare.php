<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Foursquare
	 * @description Retrieves checkins from Foursquare
	 * @version 0.1 (20101124)
	 * @author Stewart Malik (stewartmalik.net)
	 * @methods FoursquareCheckins
	 */

	require_once( dirname(__FILE__) . '/../OAuth/OAuth.php' );
	class Foursquare extends Service {

		private $oauth;

		/**
		 * @constructor
		 */
		public function __construct( $config ) {
			parent::__construct( $config );
			$this->callback_function = array(Pubwich, 'json_decode');
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
			$text = preg_replace( '/(^|\s)\@([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1@<a href="https://foursquare.com/\\2">\\2</a>', $text );
			$text = '<p>' . $text . '</p>';
			return $text;
		}

		public function populateItemTemplate( &$item ) {
			return array(
						'text' => $this->filterContent( $item->text ),
						'date' => Pubwich::time_since( $item->created_at ),
						'location' => $item->user->location,
						'source' => $item->source,
						);
		}

		public function oauthRequest( $params=array() ) {
			$method = $params[0];
			$additional_params = isset( $params[1] ) ? $params[1] : array();

			$sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
			$consumer = new OAuthConsumer( $this->oauth['app_consumer_key'], $this->oauth['app_consumer_secret'] );
			$token = new OAuthConsumer( $this->oauth['user_access_token'], $this->oauth['user_access_token_secret'] );
			
			$request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', 'https://api.foursquare.com/v1/'.$method.'.json', $additional_params);
			$request->sign_request($sha1_method, $consumer, $token);

			return FileFetcher::get($request->to_url());
		}

	}

	class FoursquareUser extends Foursquare {

		public function __construct( $config ) {
			parent::setVariables( $config );

			$this->callback_getdata = array( array($this, 'oauthRequest'), array( 'history', array('count'=>$config['total']) ) );
			$this->setURL('http://foursquare.com/'.$config['username'].'/'.$config['total']);
			$this->username = $config['username'];
			$this->setItemTemplate('<li class="clearfix"><span class="date"><a href="{{{link}}}">{{{date}}}</a></span>{{{text}}}</li>'."\n");
			$this->setURLTemplate('http://www.foursquare.com/'.$config['username'].'/');

			parent::__construct( $config );
		}

		public function populateItemTemplate( &$item ) {
			return parent::populateItemTemplate( $item ) + array(
					'link' => sprintf( 'http://foursquare.com/venue/%s', $item->venue->id ),
					'venue_image' => $item->venue->iconurl,
					'venue_name' => $item->venue->name,
					'venue_shout' => $item->shout,
					'venue_time' => $item->created,
			);
		}

	}
