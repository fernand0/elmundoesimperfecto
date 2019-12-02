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
			            'https://graph.facebook.com/v5.0/%s/posts?fields=' . implode(',', array(
							'id',
							'created_time',
							'message',
							'picture',
							'full_picture',
							'permalink_url',
							'to{id,name}',
							'attachments{title,unshimmed_url,description}',
						)) . '&limit=%s&access_token=%s',
			            trim($pagekey),
			            trim($config['total']),
			            isset($config['access_token']) ? trim($config['access_token']) : trim($config['app_id']).'|'.trim($config['app_secret'])
			        )
			); // for cache hash

			$this->setURLTemplate(
			    'https://facebook.com/' . trim($pagekey)
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

            $date = Pubwich::time_since($item->created_time);
            $timestamp = strtotime($item->created_time);
            $message = isset($item->message) ? $item->message : '';
            $media_thumbnail_url = isset($item->picture) ? $item->picture : false;
            $media_picture_url = isset($item->full_picture) ? $item->full_picture : false;
			$link = isset($item->permalink_url) ? $item->permalink_url : false;

			$status = strip_tags($message);
			$status = preg_replace('/(https?:\/\/[^\s\)]+)/', '<a href="\\1">\\1</a>', $status);
			$status = preg_replace('/(^|\s)\#([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1<a href="https://facebook.com/hashtag/\\2">#\\2</a>', $status);
			$status = preg_replace('/(^|\s)\@([^\s\ \:\.\;\-\,\!\)\(\"]+)/', '\\1@<a href="https:/facebook.com/\\2">\\2</a>', $status);

            $status_extended = $status;

			if (isset($item->to)) {
                foreach($item->to->data as $to) {
                    $status_extended = str_replace($to->name, '<a href="https://facebook.com/'.$to->id.'">'.$to->name.'</a>', $status_extended);
                }
            }

            if (isset($item->attachments)) {
                $status_extended = isset($item->attachments->data[0]->title) ? str_replace('>'.$item->attachments->data[0]->unshimmed_url.'<', '>'.$item->attachments->data[0]->title.'<', $status_extended) : $status_extended;

                if ($status === $status_extended) {
                    $status_extended .= isset($item->attachments->data[0]->title) ? ' ' . '<a href="'.$item->attachments->data[0]->unshimmed_url.'">'.$item->attachments->data[0]->title.'</a>' : '';
                    if ($item->attachments->data[0]->description) {
                        $status_extended .= ' ('.$item->attachments->data[0]->description.')';
                    }
                }

            }

            $status = $status_extended;

			return array(
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
