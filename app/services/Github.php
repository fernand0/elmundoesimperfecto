<?php
	defined('PUBWICH') or die('No direct access allowed.');

	/**
	 * @classname GitHub
	 * @description Fetch GitHub user public activity feed
	 * @author Rémi Prévost (exomel.com)
	 * @author http://michael.haschke.biz/
	 * @methods GithubRecentActivity GithubRepositories
	 */

	Pubwich::requireServiceFile( 'Feed' );
	class GithubRecentActivity extends Feed {

		/**
		 * @constructor
		 */
		public function __construct( $config ){
			$config['url'] = sprintf( 'https://github.com/%s.atom', $config['username'] );
			$config['link'] = 'https://github.com/'.$config['username'];
			$config['contenttype'] = 'application/atom+xml';
			parent::__construct( $config );
			$this->setItemTemplate('<li><a href="{{{link}}}">{{{title}}}</a> ({{{date}}})</li>'.PHP_EOL);
		}

	}
    
	class GithubRepositories extends Service {

		/**
		 * @constructor
		 */
		public function __construct( $config ){
		    if (!isset($config['sort'])) $config['sort'] = 'updated';
		    $this->sorted_by = $config['sort'];
		    if (!isset($config['ownertype'])) $config['ownertype'] = 'users';
            $this->total = $config['total'];
			$this->setURL( sprintf( 'https://api.github.com/%s/%s/repos?type=owner&sort=%s', $config['ownertype'], $config['owner'], $config['sort'] ) );
			$this->setURLTemplate( 'https://github.com/'.$config['owner'] );
			$this->setItemTemplate('<li><a href="{{{link}}}">{{{name}}}</a> {{#fork}}(forked){{/fork}}<br/>{{{description}}} ({{{date}}})</li>'.PHP_EOL);
			parent::__construct( $config );
			$this->callback_function = array('Pubwich', 'json_decode');
		}

		public function populateItemTemplate( &$item ) {
			return $item;
		}

        /**
         * @return array
         * @since 20120909
         */
        public function processDataItem($item) {

            $date_used = ($this->sorted_by !== 'full_name') ? $this->sorted_by . '_at' : 'created_at';
            $date = $item->$date_used;
            
            return array(
                'name' => $item->name,
                'description' => $item->description,
                'owner' => $item->owner->login,
                'link' => $item->html_url,
                'language' => $item->language,
                'homepage' => $item->homepage,
                'fork' => $item->fork,
                'stars' => $item->watchers,
                'forks' => $item->forks,
                'issues' => $item->open_issues,
                'date' => Pubwich::time_since($date),
                'timestamp' => strtotime($date),
            );
        }
	}

	class GithubGists extends Service {

		/**
		 * @constructor
		 */
		public function __construct( $config ){
            $this->total = $config['total'];
			$this->setURL( sprintf( 'https://api.github.com/users/%s/gists', $config['owner'] ) );
			$this->setURLTemplate( 'https://gist.github.com/'.$config['owner'] );
			$this->setItemTemplate('<li><a href="{{link}}">{{{description}}}</a> ({{{date}}})</li>'.PHP_EOL);
			parent::__construct( $config );
			$this->callback_function = array('Pubwich', 'json_decode');
		}

		public function populateItemTemplate( &$item ) {
            return $item;
		}
		
        /**
         * @return array
         * @since 20120909
         */
        public function processDataItem($item) {

            return array(
                'description' => $item->description,
                'owner' => $item->user->login,
                'link' => $item->html_url,
                'public' => $item->public,
                'date' => Pubwich::time_since($item->updated_at),
                'timestamp' => strtotime($item->updated_at),
            );
        }
	}

