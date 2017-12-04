<?php
//
// Description
// ===========
// This method will return all the information about an image.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant the image is attached to.
// image_id:          The ID of the image to get the details for.
//
// Returns
// -------
//
function ciniki_petadoptions_imageGet($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'),
        'animal_image_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Image'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];

    //
    // Make sure this module is activated, and
    // check permission to run this function for this tenant
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'petadoptions', 'private', 'checkAccess');
    $rc = ciniki_petadoptions_checkAccess($ciniki, $args['tnid'], 'ciniki.petadoptions.imageGet');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Load tenant settings
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'tenants', 'private', 'intlSettings');
    $rc = ciniki_tenants_intlSettings($ciniki, $args['tnid']);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $intl_timezone = $rc['settings']['intl-default-timezone'];
    $intl_currency_fmt = numfmt_create($rc['settings']['intl-default-locale'], NumberFormatter::CURRENCY);
    $intl_currency = $rc['settings']['intl-default-currency'];

    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki, 'php');

    //
    // Return default for new Image
    //
    if( $args['animal_image_id'] == 0 ) {
        $image = array('id'=>0,
            'animal_id'=>'',
            'title'=>'',
            'permalink'=>'',
            'flags'=>'1',
            'image_id'=>'0',
            'description'=>'',
        );
    }

    //
    // Get the details for an existing Image
    //
    else {
        $strsql = "SELECT ciniki_petadoption_images.id, "
            . "ciniki_petadoption_images.animal_id, "
            . "ciniki_petadoption_images.title, "
            . "ciniki_petadoption_images.permalink, "
            . "ciniki_petadoption_images.flags, "
            . "ciniki_petadoption_images.image_id, "
            . "ciniki_petadoption_images.description "
            . "FROM ciniki_petadoption_images "
            . "WHERE ciniki_petadoption_images.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND ciniki_petadoption_images.id = '" . ciniki_core_dbQuote($ciniki, $args['animal_image_id']) . "' "
            . "";
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
        $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.petadoptions', array(
            array('container'=>'images', 'fname'=>'id', 
                'fields'=>array('id', 'animal_id', 'title', 'permalink', 'flags', 'image_id', 'description'),
                ),
            ));
        if( $rc['stat'] != 'ok' ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.petadoptions.18', 'msg'=>'Image not found', 'err'=>$rc['err']));
        }
        if( !isset($rc['images'][0]) ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.petadoptions.19', 'msg'=>'Unable to find Image'));
        }
        $image = $rc['images'][0];
    }

    return array('stat'=>'ok', 'image'=>$image);
}
?>
