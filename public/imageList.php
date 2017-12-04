<?php
//
// Description
// -----------
// This method will return the list of Images for a tenant.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:        The ID of the tenant to get Image for.
//
// Returns
// -------
//
function ciniki_petadoptions_imageList($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];

    //
    // Check access to tnid as owner, or sys admin.
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'petadoptions', 'private', 'checkAccess');
    $rc = ciniki_petadoptions_checkAccess($ciniki, $args['tnid'], 'ciniki.petadoptions.imageList');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Get the list of images
    //
    $strsql = "SELECT ciniki_petadoption_images.id, "
        . "ciniki_petadoption_images.animal_id, "
        . "ciniki_petadoption_images.title, "
        . "ciniki_petadoption_images.permalink, "
        . "ciniki_petadoption_images.flags "
        . "FROM ciniki_petadoption_images "
        . "WHERE ciniki_petadoption_images.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
    $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.petadoptions', array(
        array('container'=>'images', 'fname'=>'id', 
            'fields'=>array('id', 'animal_id', 'title', 'permalink', 'flags')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['images']) ) {
        $images = $rc['images'];
        $image_ids = array();
        foreach($images as $iid => $image) {
            $image_ids[] = $image['id'];
        }
    } else {
        $images = array();
        $image_ids = array();
    }

    return array('stat'=>'ok', 'images'=>$images, 'nplist'=>$image_ids);
}
?>
