<?php

defined('PUBWICH') or die('No direct access allowed.');

/**
 * @classname Bandsintown
 * @description Fetch events for artists
 * @author Michael Haschke
 */
//ini_set('display_errors', 1); // uncomment this line in production environment (prevent errors from showing up)
//error_reporting(E_ALL); // uncomment this line in production environment (prevent errors from showing up)
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
                'http://api.bandsintown.com/artists/%s/events?format=json&app_id=%s&date=%s&api_version=2.0',
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
                {{#media_thumbnail_url}}
                    <img src="{{{media_thumbnail_url}}}" class="item-media-thumbnail" alt="{{{media_caption}}}" height="75" />
                {{/media_thumbnail_url}}
                <a href="{{link}}"><strong>{{{day}}}</strong> @ {{{venue}}}, {{{locality}}}, {{{state}}}</a>
                {{#lineup}}
                    <br/>{{{lineup}}}
                {{/lineup}}
             </li>'."\n"
        );

        $this->callback_function = array('Pubwich', 'json_decode' );
        parent::__construct( $config );
    }

    public function processDataItem($item) {

        // date
        $timestamp = strtotime($item->datetime . date('P'));

        // title
        $title = $item->title;

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
        $link = explode('?', $item->facebook_rsvp_url);
        $link = $link[0];

        // artist lineup
        $lineup = array();
        $lineup_extended = array();
        foreach ($item->artists as $artist) {
            // name
            $a_name = $artist->name;

            // link

            $musicbrainzid = $artist->mbid;
            $facebook = $artist->facebook_page_url;
            $bandsintownid = $artist->url;
            $website = $artist->website;
            $a_link = $website || $facebook;
            if (!$a_link && $musicbrainzid) {
                $a_link = 'http://musicbrainz.org/artist/' . $musicbrainzid;
            }
            elseif (!$a_link && $bandsintownid) {
                $a_link = 'https://www.bandsintown.com/' . $bandsintownid;
            }

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
