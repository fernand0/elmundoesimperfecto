<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * Youtube Service
     *
	 * Fetch Youtube videos.
     *
	 * @version 1.2 (20100530)
	 * @author Rémi Prévost (exomel.com)
     * @author Michael Haschke, http://michael.haschke.biz/
	 */

	class Youtube extends Service {

		public function __construct( $config ){
			parent::__construct( $config );
			$this->callback_function = array('Pubwich', 'json_decode');
			$this->cache_id = md5(implode('/', $config)) . '.data';
			
			$this->setURLTemplate('https://www.youtube.com/user/'.$config['username']);

			$this->setItemTemplate(
                '<li>
                    <a href="{{{link}}}"><img src="{{{media_thumbnail_url}}}" alt="" class="item-media-thumbnail" height="75"/>
                    {{{title}}}</a> ({{{date}}})
                 </li>'."\n"
            );
		}

		public function buildCache($Cache_Lite = null) {

            $listid = false;
			$options = array('uploads', 'likes', 'favorites', 'watchHistory', 'watchLater');
            $conflist = trim($this->getConfigValue('list'));

            if (in_array($conflist, $options)) {
		        $channelsuri = sprintf(
		                        'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=%s&key=%s',
		                        trim($this->getConfigValue('username')),
		                        trim($this->getConfigValue('apikey'))
		        );
		        $channeldata = Pubwich::json_decode(file_get_contents($channelsuri));
		        $playlists = $channeldata->items[0]->contentDetails->relatedPlaylists;

                if (isset($playlists->$conflist)) {
                    $listid = $playlists->$conflist;
                }
            }
            else {
                $listid = $conflist;
            }

            if (!$listid) {
                return false;
            }
            
			$playlisturi = sprintf(
		                'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=%s&maxResults=%s&key=%s',
		                $listid,
		                trim($this->getConfigValue('total')),
		                trim($this->getConfigValue('apikey'))
			);
			
			$this->setURL($playlisturi);

			return parent::buildCache($Cache_Lite);
		}
		
		function getData() {
		    if ($this->data === false) {
		        return false;
		    }
		    
		    return $this->data->items;
		}

		public function populateItemTemplate( &$item ) {
			return $item;
		}

        public function processDataItem($item) {

            $itemdata = $item->snippet;

            $title = $itemdata->title;
			$description = $itemdata->description;
			$date = Pubwich::time_since($itemdata->publishedAt);
			$timestamp = strtotime($itemdata->publishedAt);
			$videoid = $itemdata->resourceId->videoId;
			$link = 'https://www.youtube.com/watch?v=' . $videoid;
			
			$thumbs = $itemdata->thumbnails;
			
			$media_thumbnail_url = isset($thumbs->default) ? $thumbs->default->url : false;
			$media_medium_url = isset($thumbs->medium) ? $thumbs->medium->url : false;
			$media_high_url = isset($thumbs->high) ? $thumbs->high->url : false;
			$media_standard_url = isset($thumbs->standard) ? $thumbs->standard->url : false;

			return array(
	            'title' => $title,
				'description' => $description,
				'date' => $date,
				'timestamp' => $timestamp,
				'media_thumbnail_url' => $media_thumbnail_url,
				'media_medium_url' => $media_medium_url,
				'media_high_url' => $media_high_url,
				'media_standard_url' => $media_standard_url,
				'videoid' => $videoid,
				'link' => $link,
			);
        }
	}

