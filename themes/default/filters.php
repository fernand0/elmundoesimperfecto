<?php
/* Theme specific filter
 *
 * - rename it to filters.php
 * - add filters
 *
 * Filters only work if the Service class has processDataStream() and processDataItem() implemented!
 *
 * Name convention for functions:
 *
 * ServiceParentClass_filterStream(ServiceParentClass object)
 * ServiceParentClass_filterItem(item of ProcessedDataArray)
 * ServiceParentClass_ServiceClass_filterStream(ServiceParentClass object)
 * ServiceParentClass_ServiceClass_filterItem(item of ProcessedDataArray)
 * ServiceParentClass_ServiceClass_ServiceConfigID_filterStream(ServiceParentClass object)
 * ServiceParentClass_ServiceClass_ServiceConfigID_filterItem(item of ProcessedDataArray)
 *
 * e.g.: Feed_filterStream($service)
 *       Feed_RSS_filterItem($item)
 *       Feed_Atom_weblog_filterItem($item)
 *
 */

/*
function Service_filterStream($service)
{
    // ...
}
*/

/*
function Service_filterItem($item)
{
    // this $item is a item of the already processed data
}
*/

function Feed_filterItem(&$item)
{
    $item['title'] = shortenString(
        $item['title'],
        175
    );
    $item['summary'] = shortenString(
        strip_tags(str_replace(array('<br>','<br/>'), ' ', $item['summary'])),
        250
    );

    return;
}

function shortenString($text, $maxlength, $endchars = array('.','!','?'), $fallbackendchars = array(',',' '))
{
    if (strlen($text) <= $maxlength) return $text;

    $text = substr($text, 0, $maxlength);

    // use configured ending as finishing character (if available)
    $lastpos = array();
    foreach($endchars as $endchar)
    {
        $lastpos[$endchar] = strrpos($text, $endchar)!==false ? strrpos($text, $endchar) : 0;
    }

    $cuttextatpos = max($lastpos);

    // use fallback char if no finishing char is found
    if ($cuttextatpos == 0)
    {
        foreach($fallbackendchars as $fallbackendchar)
        {
            if (strrpos($text, $fallbackendchar)!==false)
            {
                $cuttextatpos = strrpos($text, $fallbackendchar);
                break;
            }

        }
    }

    if ($cuttextatpos == 0) // still, even after checking fallbacks
    {
        $cutbodyatpos = $maxlength;
    }
    else
    {
        $cuttextatpos++;
    }


    return substr($text, 0, $cuttextatpos);

}

