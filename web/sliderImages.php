<?php
//
// Description
// -----------
// This function will process a list of events, and format the html.
//
// Arguments
// ---------
// ciniki:
// settings:        The web settings structure, similar to ciniki variable but only web specific information.
// events:          The array of events as returned by ciniki_events_web_list.
// limit:           The number of events to show.  Only 2 events are shown on the homepage.
//
// Returns
// -------
//
function ciniki_petadoptions_web_sliderImages(&$ciniki, $settings, $business_id, $args) {

    //
    // Get the images for the home page slider
    //
    $strsql = "SELECT a.id, "
        . "a.permalink, "
        . "a.primary_image_id AS image_id, "
        . "a.name AS caption, "
        . "'middle' AS image_offset, "
        . "UNIX_TIMESTAMP(a.last_updated) AS last_updated "
        . "FROM ciniki_petadoption_animals AS a "
        . "WHERE a.status < 50 "
        . "AND a.business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
    $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.web', array(
        array('container'=>'images', 'fname'=>'id', 'fields'=>array('id', 'permalink', 'image_id', 'image_offset', 'caption', 'last_updated')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $images = array();
    if( isset($rc['images']) ) {
        $images = $rc['images'];
        foreach($images as $iid => $image) {
            $images[$iid]['url'] = $args['base_url'] . '/' . $image['permalink'];
        }
    }

    return array('stat'=>'ok', 'images'=>$images);
}
?>
