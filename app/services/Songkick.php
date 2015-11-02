<?php

defined('PUBWICH') or die('No direct access allowed.');

/**
 * @classname Songkick
 * @description Fetch events for artists and venues
 * @author Michael Haschke
 */

class Songkick extends Service {

    protected $apigroup = 'unknown';

    public function __construct($config) {
        parent::__construct( $config );

        $this->setItemTemplate(
            '<li>
                {{#media_thumbnail_url}}
                    <img src="{{{media_thumbnail_url}}}" class="item-media-thumbnail" alt="{{{media_caption}}}" height="75" />
                {{/media_thumbnail_url}}
                <a href="{{link}}"><strong>{{{day}}}</strong> @ {{{venue}}}, {{{locality}}}, {{{state}}}</a>
                {{#lineup}}
                    <br/>{{{lineup_extended}}}
                {{/lineup}}
             </li>'."\n"
        );

        $this->callback_function = array('Pubwich', 'json_decode' );
		$this->cache_id = md5(implode('/', $config)) . '.data';
    }

    public function getProfileId($results) {
        return false;
    }

    public function buildCache($Cache_Lite = null) {

        $timeline = 'gigography';

        if (!$this->getConfigValue('date') || $this->getConfigValue('date') !== 'past') {
            $timeline = 'calendar';
        }

        $profileid = $this->getConfigValue('profileid');

        if (!$this->getConfigValue('profileid')) {

            if (!$this->getConfigValue('name')) {
                return false;
            }
            // get profile id by name
            $profiledata = Pubwich::json_decode(file_get_contents(
                sprintf(
                    'https://api.songkick.com/api/3.0/search/'.$this->apigroup.'.json?query=%s&apikey=%s',
                    rawurlencode(trim($this->getConfigValue('name'))),
                    trim($this->getConfigValue('apikey'))
                )
            ));

            if ($profiledata && count($profiledata->resultsPage->results) > 0) {
                $profileid = $this->getProfileId($profiledata->resultsPage->results);
            }
            else {
                return false;
            }
        }

        $this->setURL(
            sprintf(
                'https://api.songkick.com/api/3.0/'.$this->apigroup.'/%s/%s.json?apikey=%s&order=%s',
                $profileid,
                $timeline,
                $this->getConfigValue('apikey'),
                $this->getConfigValue('sort')
            )
        );
        $this->setURLTemplate(
            sprintf(
                'https://www.songkick.com/'.$this->apigroup.'/%s',
                $profileid
            )
        );

        return parent::buildCache($Cache_Lite);
    }

    public function processDataItem($item) {

        // date
        $timestamp = strtotime($item->start->date . 'T' . $item->start->time . date('P'));

        // title
        $title = $item->displayName;

        // description
        //$description = preg_replace( '/(https?:\/\/[^\s\)]+)/', '<a href="\\1">\\1</a>', $item->description);

        // venue
        $venue = $item->venue->displayName;

        // location
        $locality = $item->venue->metroArea->displayName;

        // state
        $region = isset($item->venue->metroArea->state->displayName) ? $item->venue->metroArea->state->displayName : false;
        // country
        $country = isset($item->venue->metroArea->country->displayName) ? $item->venue->metroArea->country->displayName : false;

        // state or country
        $place = $item->location->city;
        $state_or_country = (count(explode(',', $place)) === 3) ? $region : $country;

        // event link
        $link = explode('?', $item->uri);
        $link = $link[0];

        // artist lineup
        $lineup = array();
        $lineup_extended = array();
        foreach ($item->performance as $artist) {
            // name
            $a_name = $artist->displayName;

            // link
            $a_link = explode('?', $artist->artist->uri);
            $a_link = $a_link[0];

            $lineup[] = $a_name;
            $lineup_extended[] = $a_link ? '<a href="'.$a_link.'">'.$a_name.'</a>' : $a_name;
        }
        $lineup = implode(', ', $lineup);
        $lineup_extended = implode(', ', $lineup_extended);

        return array(
            'day' => date('Y-m-d', $timestamp),
            'time' => date('H:m', $timestamp),
            'timestamp' => $timestamp,
            'title' => $title,
            //'description' => $description,
            'venue' => $venue,
            'locality' => $locality,
            'country' => $country,
            'region' => $region,
            'state' => $state_or_country,
            'link' => $link,
            'lineup' => $lineup,
            'lineup_extended' => $lineup_extended
        );
    }

    public function sortEvents($a, $b) {
        // sort descending
        if ($a['timestamp'] < $b['timestamp']) {
            return -1;
        }

        return 1;
    }

    public function processDataStream() {
        $data_source = $this->getData();
        if (!$data_source || !isset($data_source->resultsPage->totalEntries) || $data_source->resultsPage->totalEntries < 1) {
            return array();
        }

        $data_processed = array();

        foreach ($data_source->resultsPage->results->event as $data_item) {
            $data_processed[] = $this->processDataItem($data_item);
        }

        $data =  $data_processed;

        $sort = $this->getConfigValue('sort');
        $limit = $this->getConfigValue('total');

        switch ($sort) {
            case 'desc':
                usort($data, array($this, 'sortEvents'));
                $data = array_reverse($data);
                break;
            default:
                usort($data, array($this, 'sortEvents'));
        }

        if ($limit) {
            return array_slice($data, 0, $limit);
        }

        return $data;
    }

    public function populateItemTemplate(&$item) {
        return $item;
    }

}

class SongkickArtists extends Songkick {

    protected $apigroup = 'artists';

    public function getProfileId($results) {
        return $results->artist[0]->id;
    }

}

class SongkickVenues extends Songkick {

    protected $apigroup = 'venues';

    public function __construct($config) {
        $config['date'] = 'upcoming'; // Songkick don't provide gigography api for venues
        parent::__construct( $config );
    }

    public function getProfileId($results) {
        return $results->venue[0]->id;
    }

}
