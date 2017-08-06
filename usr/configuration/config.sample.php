<?php
    defined('PUBWICH') or die('No direct access allowed.');

    //! RENAME THIS FILE TO config.php

    /*
        Please follow this steps to configure PubwichFork:

        1) Server environment & Logging
        2) Localisation/Language
        3) Site informations
        4) Performance & Cache
        5) Proxy settings
        6) Social Web service configurations
        7) Add service configurations
    */

    /* -- 1) Server environment & Logging ------------------------------ */

    //! Production environment
    // ini_set('display_errors', 0); // uncomment this line in production environment (prevent errors from showing up)
    // error_reporting(0); // uncomment this line in production environment (prevent errors from showing up)

    // Logging configuration (you should not have to edit this)
    define('PUBWICH_LOGLEVEL', 0);
    define('PUBWICH_LOGTOFILE', false);

    /* -- 2) Localisation/Language ------------------------------------- */

    date_default_timezone_set( 'Europe/Berlin' );
    define('PUBWICH_LANG', ''); // leave to '' to keep Pubwich in english
    setlocale( LC_ALL, 'en_EN.UTF8' ); // for date methods

    /* -- 3) Site informations ----------------------------------------- */

    define('PUBWICH_THEME', 'default');
    define('PUBWICH_TITLE', 'My Pubwich-powered site');

    // it is not necessary to set the URL of you page manually but
    // in a few server environment the URL can not determined automatically.
    // You can use it manually as fallback in those situations.
    // define('PUBWICH_URL', 'http://localhost/pubwich/');

    /* -- 4) Performance & Cache --------------------------------------- */

    /*
       @see https://github.com/haschek/PubwichFork/wiki/PerformanceCacheOptimization
    */

    // define( 'CACHE_LOCATION', dirname(__FILE__) . '/../cache/' ); // only configure the cache directory location if you don't want to use the standard ``usr/cache/`` folder
    define( 'CACHE_LIMIT', 60 * 60 ); // 60 minutes
    define( 'OUTPUT_CACHE_LIMIT', 30 * 60 ); // 30 minutes
    define( 'CACHE_DISPLACEMENT', 0.5); // between 0 and under 1, eg. 30min and 0.5 leads to cache limit randomly calculated between 15min and 45min
    define( 'FETCHDATA_TIMEOUT', 5); // 5 seconds
    define( 'ENABLE_INVALID_CACHE', true ); // if available, use invalide cache for output to speed up response time

    /* -- 5) Proxy settings -------------------------------------------- */

    /* You probably do not need this, but in some cases your server may not
       have directly access to the web for aggregation. Then please ask
       your admin/service provider for the proxy server data.

       @see https://github.com/haschek/PubwichFork/wiki/UsingProxyServer
    */

    // define( 'PUBWICH_PROXY', 'http://proxy.example.com');
    // define( 'PUBWICH_PROXYPORT', '3128'); // 3128 is the standard port for a proxy, but it can vary
    // for more info @see https://github.com/haschek/PubwichFork/wiki/UsingProxyServer

    /* -- 6) Social Web service configurations ------------------------- */

    /* Here you need to configure the Social Web/Media services which you
       want to aggregate with PubwichFork; just edit and uncomment the
       the following examples of service configuration, or read the
       documentation. Later (in step 7) you need to group the configured
       services.

       @see https://github.com/haschek/PubwichFork/wiki/SocialWebServices
    */

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
    $dribbble = array(
	    'Dribbble', 'dribble', array(
			'username' => 'youraccount',
			'total' => 8,
			'title' => 'Dribbble',
			'description' => 'latest shots',
    ); //*/

    /*
    $facebook = array(
	    'Facebook', 'facebook_page', array(
	        // currently PubwichFork only provide FacebookPage method
			'method' => 'FacebookPage',

			// you need to config either pageid or pagename
			'pageid' => 'PAGEID', // https://www.facebook.com/pages/Name-of-Page/pageid
			'pagename' => 'PAGENAME', // https://www.facebook.com/pagename

			// you need to create a website app https://developers.facebook.com/apps/
			'app_id' => 'APPID', // see application dashboard
			'app_secret' => 'APPSECRET', // see application dashboard

			'total' => 5,
			'title' => 'Title',
			'description' => 'latest statuses',
		)
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
            'size' => 's', // s|q|t|m|n|z|c
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
        'Github', 'github', array(
        'username' => 'YOURUSERNAME',
        'total' => '10',
        'title' => 'Github',
        'description' => 'My recent activity',
        'method' => 'GithubRecentActivity'
        )
    ); //*/

    /*
    $goodreads = array(
        // TODO
    ); //*/

    /*
    $gnusocialprofile = array(
	    'Gnusocial', 'gnusocial_profile', array(
	        'serverurl' => 'https://gnu.example.com/',
	        'username' => 'USERNAME',
			'total' => 3,
			'title' => 'Name',
			'description' => 'GNUsocial profile'
	    )
    ); //*/

    /*
    $gnusocialtag = array(
	    'Gnusocial', 'gnusocial_tag', array(
	        'serverurl' => 'https://gnu.example.com/',
	        'tag' => 'tagname',
			'total' => 3,
			'title' => 'Tag title',
			'description' => 'GNUsocial tag'
	    )
    ); //*/	
	
	/*
    $instagram = array(
        'Instagram', 'instagram', array(
			'username'      => 'Your instagram user name',
			'access_token'  => 'Oauth access token',
			'total'         => '12',
			'title'         => 'Instagram',
            'description'   => 'my insta pics'
        )
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
	    'Pinboard', 'pinboard', array(
			'username' => 'YOUR_USERNAME',
			'total' => 3,
			'title' => 'Pinboard',
			'description' => 'Bookmarks'
		)
    ); //*/

    /*
    $reddit = array(
	    'Reddit', 'reddit_likes', array(
			'method' => 'RedditLiked',
			'username' => 'YOUR_USERNAME',
            'list' => 'likes',
			'total' => 2,
			'title' => 'Reddit',
			'description' => 'what I liked'
		)
    ); //*/

    /*
    $slideshare = array(
        // TODO
    ); //*/

    /*
    $twitter = array(
        'Twitter', 'twitter_feed', array(
            'method' => 'TwitterUser',
            'username' => 'YOURUSERNAME',
            'oauth' => array(
                'app_consumer_key'         => '',
                'app_consumer_secret'      => '',
                'user_access_token'        => '',
                'user_access_token_secret' => ''
            ),
            'total' => '10',
            'title' => 'Twitter'
        )
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
            'list' => 'uploads', // uploads | likes | favorites, or playlist ID
            'username' => 'YOUTUBE_USERNAME_HERE',
            'apikey' => 'PUBLICAPIKEY', // get it here https://console.developers.google.com/
            'total' => 3,
            'title' => 'Youtube',
            'description' => 'my recent videos'
        )
    ); //*/
	
	/*
    $steam_last_played_games = array(
        'Steam', 'steam_last_played', array(
            'method' => 'SteamLastPlayed',
            'apikey' => 'YOURAPIKEY', // get it here https://steamcommunity.com/dev/apikey
			'userid' => 'YOURUSERID', //if you have a vanity url, get your ID here : https://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?key=YOUR_KEY&vanityurl=YOUR_VANITY_URL Otherwise it's in your url when you visit your profile.
            'title' => 'Steam',
			'total' => '5',
            'description' => 'my last played games'
        )
    ); //*/

    /* -- 7) Add service configurations -------------------------------- */

    /*
        Use the configuration variables from step 6 to group the Social
        Web services (if you use the vars from the examples, do not forget
        to uncomment). The default theme can have up to 3 groups (columns).
    */

    Pubwich::setServices(
        array(
            // container 1
            array(
                // $infobox,
                // $feed_atom,
                // $feed_rss,
            ),
            // container 2
            array(
                // $flickr_user,
                // $vimeo_likes,
                // $youtube_uploads,
            ),
            // container 3
            array(
                // $dribbble,
                // $lastfm_weekly,
            ),

        )
    );

    // Don't forget to fill informations in /humans.txt
