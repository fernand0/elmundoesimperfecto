<?php
	defined('PUBWICH') or die('No direct access allowed.');

	//! Rename this file to config.php

    //! Production environment
    // ini_set('display_errors', 0); // uncomment this line in production environment (prevent errors from showing up)
	// error_reporting(0); // uncomment this line in production environment (prevent errors from showing up)

	// Localisation
	date_default_timezone_set( 'Europe/Berlin' );
	define('PUBWICH_LANG', ''); // leave to '' to keep Pubwich in english
	setlocale( LC_ALL, 'en_EN.UTF8' ); // for date methods

	// General site informations
	define('PUBWICH_URL', 'http://localhost/pubwich/');
	define('PUBWICH_THEME', 'default');
	define('PUBWICH_TITLE', 'My Pubwich-powered site');

	// Logging configuration (you should not have to edit this)
	define('PUBWICH_LOGLEVEL', 0);
	define('PUBWICH_LOGTOFILE', false);

	// Performance - Cache and Timeouts
	define( 'CACHE_LOCATION', dirname(__FILE__) . '/../cache/' );
	define( 'CACHE_LIMIT', 30 * 60 ); // 30 minutes
    define( 'CACHE_DISPLACEMENT', 0.5); // between 0 and under 1, eg. 30min and 0.5 leads to cache limit randomly calculated between 15min and 45min
    define( 'FETCHDATA_TIMEOUT', 5); // 5 seconds

	// Pubwich services configuration
	// first, we have some examples to configure single services
	// below, we use the service configs to group them
	
	//*
	$infobox = array(
	    'Text', 'intro', array(
		    'title' => 'About PubwichFork',
		    'text' => '
		        <p>PubwichFork is an open-source PHP Web application that
		        allows you to aggregate your published data from multiple
		        Websites and services into a single HTML page.</p>
		        <p>PubwichFork is an improved version of the original
		        Pubwich application, since Pubwich is not really maintained
		        anymore by the original author. PubwichFork fixes several
		        bugs and integrates pre-output filtering of the data
		        streams.</p>',
		)
	); //*/

	/*
	$flickr = array(
	    'Flickr', 'photos', array( 
			'method' => 'FlickrUser',
			'key' => 'FLICKR_KEY_HERE',
			'userid' => 'FLICKER_USERID_HERE', // use http://www.idgettr.com to find it
			'username' => 'FLICKR_USERNAME_HERE',
			'total' => 12,
			'title' => 'Flick<em>r</em>',
			'description' => 'latest photos',
			'row' => 4,
		)
	); //*/

	/*
	$vimeo = array(
	    'Vimeo', 'videos', array(
			'username' => 'VIMEO_USERNAME_HERE',
			'total' => 4,
			'title' => 'Vimeo',
			'description' => 'latest videos'
		)
	); //*/

	/*
	$youtube = array(
	    'Youtube', 'youtube', array(
			'method' => 'YoutubeVideos',
			'username' => 'YOUTUBE_USERNAME_HERE',
			'total' => 4,
			'size' => 120,
			'title' => 'Youtube',
			'description' => 'latest videos'
		)
	); //*/
	
	/*
	$twitter = array(
	    'Twitter', 'etats', array(
			'method' => 'TwitterUser',
			'username' => 'TWITTER_USERNAME_HERE',
			'oauth' => array(
				// You have to create a new application at http://dev.twitter.com/apps to get these keys
				// See the tutorial at http://pubwich.org/wiki/Using_Twitter_with_Pubwich
				'app_consumer_key' => '',
				'app_consumer_secret' => '',
				'user_access_token' => '',
				'user_access_token_secret' => ''
			),
			'total' => 10,
			'title' => 'Twitter',
			'description' => 'latest statuses'
		)
	); //*/

	/*
	$delicious = array(
	    'Delicious', 'liens', array(
			'username' => 'DELICIOUS_USERNAME_HERE',
			'total' => 5,
			'title' => 'del.icio.us',
			'description' => 'latest bookmarks',
		)
	); //*/

	/*
	$facebook = array(
	    'Facebook', 'status', array(
			'id' => 'FACEBOOK_USERID_HERE',
			'key' => 'FACEBOOK_KEY_HERE',
			'username' => 'FACEBOOK_USERNAME_HERE',
			'total' => 5,
			'title' => 'Facebook',
			'description' => 'latest statuses',
		)
	); //*/

	/*
	$rss_news = array(
	    'RSS', 'ixmedia', array(
			'url' => 'http://feeds2.feedburner.com/ixmediablogue',
			'link' => 'http://blogue.ixmedia.com/',
			'total' => 5,
			'title' => 'Blogue iXmédia',
			'description' => 'latest atom blog entries'
		)
	); //*/
	
	/*
	$atom_news = array(
	    'Atom', 'effair', array(
			'url' => 'http://remiprevost.com/atom/',
			'link' => 'http://remiprevost.com/',
			'total' => 5,
			'title' => 'Effair',
			'description' => 'latest rss blog entries'
		)
	); //*/

	/*
	$readernaut = array(
	    'Readernaut', 'livres', array(
			'method' => 'ReadernautBooks',
			'username' => 'READERNAUT_USERNAME_HERE',
			'total' => 9,
			'size' => 50,
			'title' => 'Readernaut',
			'description' => 'latest books'
		)
    ); //*/

	/*
	$lastfm = array(
	    'Lastfm', 'albums', array(
			'method' => 'LastFMWeeklyAlbums',
			'key' => 'LASTFM_KEY_HERE',
			'username' => 'LASTFM_USERNAME_HERE',
			'total' => 5,
			'size' => 64,
			'title' => 'Last.fm',
			'description' => 'weekly top albums',
		) 
	); //*/


	Pubwich::setServices(
		array(
		    // column 1
			array(
                $infobox,
                // $youtube,
                // $vimeo,
                // $flickr,
			),
			array(
			    // $twitter,
			    // $delicious,
			    // $facebook,
			    // $rss_news,
			),
			array(
                // $atom_news,
                // $readernaut,
                // $lastfm,
			),

		)
	);

	// Don't forget to fill informations in /humans.txt
