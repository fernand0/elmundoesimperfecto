<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname Facebook
	 * @description Fetch Facebook statuses
	 * @version 2.4 (2015-07-12)
	 * @author Michael Haschke <http://michael.haschke.biz>
	 * @methods FacebookPage
	 */

	class Facebook extends Service {
	    // only stub for other stuff that may come later.
	}

	class FacebookPage extends Facebook {

		public function __construct( $config ){
			$this->callback_function = array('Pubwich', 'json_decode');

            $pagekey = isset($config['pagename']) ? $config['pagename'] : $config['pageid'];

            // @see https://developers.facebook.com/docs/graph-api/reference/v2.4/post
			$this->setURL(
			        sprintf(
			            'https://graph.facebook.com/v2.12/%s/posts?fields=' . implode(',', array(
							'id',
							'status_type',
							'type',
							'created_time',
							'story',
							'message',
							'picture',
							'full_picture',
							'message_tags',
							'name',
							'caption',
							'description',
							'link'
						)) . '&limit=%s&access_token=%s',
			            trim($pagekey),
			            trim($config['total']),
			            trim($config['app_id']).'|'.trim($config['app_secret'])
			        )
			); // for cache hash

			$this->setURLTemplate(
			    'http://www.facebook.com/' . trim($pagekey)
			);

			$this->setItemTemplate(
			    '<li class="facebook-{{{type}}}">
			        {{#media_thumbnail_url}}
                        <img src="{{{media_thumbnail_url}}}" class="item-media-thumbnail" alt="" height="75" />
                    {{/media_thumbnail_url}}
                    {{{status}}}
			        (<a href="{{{link}}}">{{{date}}}</a>)
			     </li>'.PHP_EOL
			);

			parent::__construct( $config );
		}

		public function getData() {
		    if ($this->data === false || !isset($this->data->data)) {

		        if (isset($this->data->error) && isset($this->data->error->message)) {
		            $this->errorMessage = $this->data->error->message;
		        }

		        return false;
		    }

		    return $this->data->data;
		}

		public function populateItemTemplate( &$item ) {
			return $item;
		}

        public function processDataItem($item) {

            $type = $item->type;
            $date = Pubwich::time_since($item->created_time);
            $timestamp = strtotime($item->created_time);
            $message = isset($item->message) ? $item->message : '';
            $media_thumbnail_url = isset($item->picture) ? $item->picture : false;
            $media_picture_url = isset($item->full_picture) ? $item->full_picture : false;
            $link_name = isset($item->name) ? $item->name : false;
            $link_caption = isset($item->caption) ? $item->caption : false;
            $link_url = isset($item->link) ? $item->link : false;

            $id = explode('_', $item->id);
            $id_page = $id[0];
            $id_message = $id[1];
            $link = 'https://www.facebook.com/'.$id_page.'/posts/'.$id_message;

			$status = strip_tags($message);
			$status = preg_replace('/(https?:\/\/[^\s\)]+)/', '<a href="\\1">\\1</a>', $status);
			$status = preg_replace('/(^|\s)\#([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1<a href="https://www.facebook.com/hashtag/\\2">#\\2</a>', $status);
			$status = preg_replace('/(^|\s)\@([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1@<a href="https://www.facebook.com/\\2">\\2</a>', $status);

            $status_extended = $status;

            if ($link_name && $link_url) {
                $status_extended = str_replace('>'.$link_url.'<', '>'.$link_name.'<', $status_extended);

                if ($status === $status_extended) {
                    $status_extended .= ' ' . '<a href="'.$link_url.'">'.$link_name.'</a>';
                    if ($link_caption) {
                        $status_extended .= ' ('.$link_caption.')';
                    }
                }

            }

            if (isset($item->to)) {
                foreach($item->to->data as $to) {
                    $status_extended = str_replace($to->name, '<a href="'.$to->link.'">'.$to->name.'</a>', $status_extended);
                }
            }

            $status = $status_extended;

			return array(
	            'type' => $type,
				'message' => $message,
				'date' => $date,
				'timestamp' => $timestamp,
				'media_thumbnail_url' => $media_thumbnail_url,
				'media_picture_url' => $media_picture_url,
				'status' => $status,
				'link' => $link,
			);
        }

	}
