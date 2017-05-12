<?php
//
// Description
// ===========
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_petadoptions_imageUpdate(&$ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'),
        'animal_image_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Image'),
        'animal_id'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Adoption'),
        'title'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Title'),
        'permalink'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'permalink'),
        'flags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Options'),
        'image_id'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Image'),
        'description'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Description'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];

    //
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'petadoptions', 'private', 'checkAccess');
    $rc = ciniki_petadoptions_checkAccess($ciniki, $args['business_id'], 'ciniki.petadoptions.imageUpdate');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    $strsql = "SELECT uuid "
        . "FROM ciniki_petadoption_images "
        . "WHERE id = '" . ciniki_core_dbQuote($ciniki, $args['animal_image_id']) . "' "
        . "AND business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.petadoptions', 'animal');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['animal']) ) {
         return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.petadoptions.32', 'msg'=>'Unable to find image.'));
    }
    $animal = $rc['animal'];

    if( isset($args['title']) ) {
        if( $args['title'] != '' ) {
            ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'makePermalink');
            $args['permalink'] = ciniki_core_makePermalink($ciniki, $args['title']);
        } else {
            $args['permalink'] = $animal['uuid'];
        }
        //
        // Make sure the permalink is unique
        //
        $strsql = "SELECT id, title, permalink "
            . "FROM ciniki_petadoption_images "
             . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
             . "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
            . "AND id <> '" . ciniki_core_dbQuote($ciniki, $args['animal_image_id']) . "' "
             . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.petadoptions', 'item');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
          }
        if( $rc['num_rows'] > 0 ) {
             return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.petadoptions.20', 'msg'=>'You already have an image with this title, please choose another.'));
        }
    }

    //
    // Start transaction
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbAddModuleHistory');
    $rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.petadoptions');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Update the Image in the database
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectUpdate');
    $rc = ciniki_core_objectUpdate($ciniki, $args['business_id'], 'ciniki.petadoptions.image', $args['animal_image_id'], $args, 0x04);
    if( $rc['stat'] != 'ok' ) {
        ciniki_core_dbTransactionRollback($ciniki, 'ciniki.petadoptions');
        return $rc;
    }

    //
    // Commit the transaction
    //
    $rc = ciniki_core_dbTransactionCommit($ciniki, 'ciniki.petadoptions');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Update the last_change date in the business modules
    // Ignore the result, as we don't want to stop user updates if this fails.
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'updateModuleChangeDate');
    ciniki_businesses_updateModuleChangeDate($ciniki, $args['business_id'], 'ciniki', 'petadoptions');

    return array('stat'=>'ok');
}
?>
