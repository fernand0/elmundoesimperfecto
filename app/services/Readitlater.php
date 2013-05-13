<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Readitlater
	 * @description Readitlaterlist Pubwich Service
	 * @version 0.9
	 * @author Jean-Philippe Doyle (j15e.com)
	 */

	class Readitlater extends Service 
	{

		/**
		 * @constructor
		 */
		public function __construct( $config ) 
		{
			$this->config = $config;
			$this->setURLTemplate( sprintf( 'http://readitlaterlist.com/users/%s/feed/', $config['username'] ) );
			$this->setItemTemplate('<li class="clearfix state{%state%}"><span class="date">{%time_updated%}</span> <p><a target="_blank" href="{%url%}">{%title%}</a></p></li>');
			parent::__construct( $config );
		}

		public function getData() 
		{
			$Cache_Lite = new Cache_Lite( parent::getCacheOptions() );
			$url = sprintf('https://readitlaterlist.com/v2/get?username=%s&password=%s&apikey=%s&count=%s&format=json', $this->config['username'], $this->config['password'], $this->config['apikey'], $this->config['total']);
			$id = $url;
			if ($data = $Cache_Lite->get( $id )) 
			{				
				$data = json_decode($data);
			}
			else 
			{
				$Cache_Lite->get( $id );
				PubwichLog::log( 2, Pubwich::_( 'Rebuilding cache for a Readitlater' ) );
				PubwichLog::log( 2, $this->url );
				$data = file_get_contents( $url );	
				$data = json_decode($data);
				foreach($data->list as $item)
				{
					// Default value
					$item->title = parse_url($item->url, PHP_URL_HOST);
					// If check page title
					if(!empty($this->config['getTitle']))
					{				
						$file = @fopen($item->url,"r");
						if($file)
						{					
							$text = fread($file, 1024 * 3);
							if (preg_match('/<title>(.*?)<\/title>/is', $text, $found)) 
							{
								$item->title = $found[1];
							}
						}
					}
				}
				// Write cache
				$cacheWrite = $Cache_Lite->save( json_encode($data) );
			}	
			return $data->list;
		}

		public function populateItemTemplate( $item ) 
		{
			return array(
				'url' => $item->url,
				'title' => $item->title,
				'state' => $item->state,
				'time_added' => Pubwich::time_since( strftime('%T', $item->time_added) ),
				'time_updated' => Pubwich::time_since( strftime('%T', $item->time_updated) ),
			);
		}

	}