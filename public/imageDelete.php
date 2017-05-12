<?php
//
// Description
// -----------
// This method will delete an image.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:            The ID of the business the image is attached to.
// image_id:            The ID of the image to be removed.
//
// Returns
// -------
//
function ciniki_petadoptions_imageDelete(&$ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'),
        'animal_image_id'=>array('required'=>'yes', 'blank'=>'yes', 'name'=>'Image'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];

    //
    // Check access to business_id as owner
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'petadoptions', 'private', 'checkAccess');
    $rc = ciniki_petadoptions_checkAccess($ciniki, $args['business_id'], 'ciniki.petadoptions.imageDelete');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Get the current settings for the image
    //
    $strsql = "SELECT id, uuid "
        . "FROM ciniki_petadoption_images "
        . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "AND id = '" . ciniki_core_dbQuote($ciniki, $args['animal_image_id']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.petadoptions', 'image');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['image']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.petadoptions.15', 'msg'=>'Image does not exist.'));
    }
    $image = $rc['image'];

    //
    // Check for any dependencies before deleting
    //

    //
    // Check if any modules are currently using this object
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectCheckUsed');
    $rc = ciniki_core_objectCheckUsed($ciniki, $args['business_id'], 'ciniki.petadoptions.image', $args['animal_image_id']);
    if( $rc['stat'] != 'ok' ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.petadoptions.16', 'msg'=>'Unable to check if the image is still being used.', 'err'=>$rc['err']));
    }
    if( $rc['used'] != 'no' ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.petadoptions.17', 'msg'=>'The image is still in use. ' . $rc['msg']));
    }

    //
    // Start transaction
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDelete');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectDelete');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbAddModuleHistory');
    $rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.petadoptions');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Remove the image
    //
    $rc = ciniki_core_objectDelete($ciniki, $args['business_id'], 'ciniki.petadoptions.image', $args['animal_image_id'], $image['uuid'], 0x04);
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
