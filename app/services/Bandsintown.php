<?php

defined('PUBWICH') or die('No direct access allowed.');

/**
 * @classname Bandsintown
 * @description Fetch events for artists
 * @author Michael Haschke
 */

class Bandsintown extends Service {

    public function __construct($config) {

        if (!isset($config['sort'])) {
            $config['sort'] = 'asc';
        }

        if (!isset($config['date'])) {
            $config['date'] = 'upcoming';
        }

        if ($config['date'] === 'past') {
            $config['date'] = '1900-01-01,' . date('Y-m-d', strtotime('yesterday'));
        }

        $this->setURL(
            sprintf(
                'https://rest.bandsintown.com/artists/%s/events?format=json&app_id=%s&date=%s',
                urlencode(rawurlencode($config['artistname'])),
                PUBWICH_NAME.PUBWICH_VERSION,
                $config['date']
            )
        );
        $this->setURLTemplate(
            sprintf(
                'https://www.bandsintown.com/%s',
                rawurlencode($config['artistname'])
            )
        );
        $this->setItemTemplate(
            '<li>
                <a href="{{link}}"><strong>{{{day}}}</strong> @ {{{venue}}}, {{{locality}}}, {{{state}}}</a>
                {{#lineup}}
                    <br/>{{{lineup_extended}}}
                {{/lineup}}
             </li>'."\n"
        );

        $this->callback_function = array('Pubwich', 'json_decode' );
        parent::__construct( $config );
    }

    public function processDataItem($item) {

        // date
        $timestamp = strtotime($item->datetime . date('P'));

        // description
        $description = preg_replace( '/(https?:\/\/[^\s\)]+)/', '<a href="\\1">\\1</a>', $item->description);

        // venue
        $venue = $item->venue->name;

        // location
        $locality = $item->venue->city;

        // state
        $region = $item->venue->region;

        // country
        $country = $item->venue->country;

        // state or country
        $state_or_country = ($country === 'United States' || $country === 'Canada') ? $region : $country;

        // event link
        $link = explode('?', $item->url);
        $link = $link[0];

        // artist lineup
        $lineup = array();
        $lineup_extended = array();
        foreach ($item->lineup as $artist) {
            // link
            $a_link = 'https://www.bandsintown.com/' . urldecode($artist);

            $lineup[] = $artist;
            $lineup_extended[] = '<a href="'.$a_link.'">'.$artist.'</a>';
        }
        $lineup = implode(', ', $lineup);
        $lineup_extended = implode(', ', $lineup_extended);

        return array(
            'day' => date('Y-m-d', $timestamp),
            'time' => date('H:m', $timestamp),
            'timestamp' => $timestamp,
            'description' => $description,
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
        $data = parent::processDataStream();
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
