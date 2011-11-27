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
	define( 'CACHE_LIMIT', 60 * 60 ); // 60 minutes
	define( 'OUTPUT_CACHE_LIMIT', 30 * 60 ); // 30 minutes
    define( 'CACHE_DISPLACEMENT', 0.5); // between 0 and under 1, eg. 30min and 0.5 leads to cache limit randomly calculated between 15min and 45min
    define( 'FETCHDATA_TIMEOUT', 5); // 5 seconds
	define( 'ENABLE_INVALID_CACHE', true ); // if available, use invalide cache for output to speed up response time

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

    // read Service documentation
    // @see https://github.com/haschek/PubwichFork/Services

	/*
	$delicious = array(
	    'Delicious', 'bookmarks', array(
			'username' => 'DELICIOUS_USERNAME',
			'total' => 5,
			'title' => 'Delicious',
			'description' => 'latest bookmarks',
		)
	); //*/

    /*
    $dribbble = array(
        // TODO
    ); //*/

	/*
	$facebook = array(
        // TODO
    ); //*/

	/*
	$feed_atom = array(
	        'Feed', 'feed_atom', array(
			'url' => 'http://example.org/feed.atom',
			'contenttype' => 'application/atom+xml',
			'link' => 'http://example.org/',
			'total' => 3,
			'title' => 'Site title',
			'description' => 'Site description'
		)
	); //*/

	/*
	$feed_rss = array(
	        'Feed', 'feed_rss', array(
			'url' => 'http://example.com/feed.rss',
			'contenttype' => 'application/rss+xml',
			'link' => 'http://example.com/',
			'total' => 3,
			'title' => 'Site title',
			'description' => 'Site description'
		)
	); //*/

	/*
	$flickr_user = array(
	    'Flickr', 'flickruser', array(
			'method' => 'FlickrUser',
			'key' => 'FLICKR_KEY_HERE',
			'userid' => 'FLICKER_USERID_HERE', // use http://www.idgettr.com to find it
			'username' => 'FLICKR_USERNAME_HERE',
			'total' => 8,
			'title' => 'Flick<em>r</em>',
			'description' => 'my latest photos'
		)
	); //*/

    /*
    $foursquare = array(
        // TODO
    ); //*/

    /*
    $github = array(
        // TODO
    ); //*/

    /*
    $goodreads = array(
        // TODO
    ); //*/

    /*
    $gowalla = array(
        // TODO
    ); //*/

    /*
    $instapaper = array(
        // TODO
    ); //*/

	/*
	$lastfm_weekly = array(
	    'Lastfm', 'lastfm_weekly', array(
			'method' => 'LastFMWeeklyAlbums',
			'key' => 'LASTFM_KEY_HERE',
			'username' => 'LASTFM_USERNAME_HERE',
			'total' => 5,
			'size' => 75,
			'title' => 'Last.fm',
			'description' => 'weekly top albums',
		)
	); //*/

    /*
    $pinboard = array(
        // TODO
    ); //*/

	/*
	$readernaut = array(
	    //  TODO
    ); //*/

    /*
    $readitlater = array(
        // TODO
    ); //*/

    /*
    $reddit = array(
        // TODO
    ); //*/

    /*
    $slideshare = array(
        // TODO
    ); //*/

    /*
    $statusnet = array(
        // TODO
    ); //*/

	/*
	$twitter = array(
	    // TODO
	); //*/

	/*
	$vimeo_likes = array(
	    'Vimeo', 'vimeo_likes', array(
			'username' => 'VIMEO_USERNAME_HERE',
            'list' => 'likes',
			'total' => 3,
			'title' => 'Vimeo',
			'description' => 'what I liked'
		)
	); //*/

	/*
	$youtube_uploads = array(
	    'Youtube', 'youtube_uploads', array(
			'list' => 'uploads',
			'username' => 'YOUTUBE_USERNAME_HERE',
			'total' => 3,
			'title' => 'Youtube',
			'description' => 'my recent videos'
		)
	); //*/

	Pubwich::setServices(
		array(
		    // column 1
			array(
                // $infobox,
                // $feed_atom,
                // $feed_rss,
			),
            // column 2
			array(
                // $flickr_user,
                // $vimeo_likes,
                // $youtube_uploads,
			),
            // column 3
			array(
			    // $delicious,
                // $lastfm_weekly,
			),

		)
	);

	// Don't forget to fill informations in /humans.txt
