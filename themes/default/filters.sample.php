<?php
/* Theme specific filter
 *
 * - rename it to filters.php
 * - add filters
 *
 * Filters only work if the Service class has processDataStream() implemented!
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
 * e.g.: Service_filterStream($service)
 *       Service_RSS_filterItem($item)
 *       Service_Atom_weblog_filterItem($item)
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
