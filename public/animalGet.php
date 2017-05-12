<?php
//
// Description
// ===========
// This method will return all the information about an animals.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:         The ID of the business the animals is attached to.
// animal_id:          The ID of the animals to get the details for.
//
// Returns
// -------
//
function ciniki_petadoptions_animalGet($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'),
        'animal_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Animals'),
        'images'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Images'),
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
    $rc = ciniki_petadoptions_checkAccess($ciniki, $args['business_id'], 'ciniki.petadoptions.animalGet');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Load business settings
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'intlSettings');
    $rc = ciniki_businesses_intlSettings($ciniki, $args['business_id']);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $intl_timezone = $rc['settings']['intl-default-timezone'];
    $intl_currency_fmt = numfmt_create($rc['settings']['intl-default-locale'], NumberFormatter::CURRENCY);
    $intl_currency = $rc['settings']['intl-default-currency'];

    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki, 'php');

    //
    // Return default for new Animals
    //
    if( $args['animal_id'] == 0 ) {
        $animal = array('id'=>0,
            'name'=>'',
            'permalink'=>'',
            'flags'=>'1',
            'status'=>'10',
            'category'=>'',
            'breed'=>'',
            'sex'=>'',
            'years'=>'',
            'color'=>'',
            'size'=>'',
            'location'=>'',
            'primary_image_id'=>'0',
            'synopsis'=>'',
            'description'=>'',
        );
    }

    //
    // Get the details for an existing Animals
    //
    else {
        ciniki_core_loadMethod($ciniki, 'ciniki', 'petadoptions', 'private', 'animalLoad');
        $rc = ciniki_petadoptions_animalLoad($ciniki, $args['business_id'], $args['animal_id'], $args);
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        $animal = $rc['animal'];

        //
        // Get the images
        //
        if( isset($args['images']) && $args['images'] == 'yes' && isset($animal['images']) ) {
            ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'hooks', 'loadThumbnail');
            foreach($animal['images'] as $img_id => $img) {
                if( isset($img['image_id']) && $img['image_id'] > 0 ) {
                    $rc = ciniki_images_hooks_loadThumbnail($ciniki, $args['business_id'], array('image_id'=>$img['image_id'], 'maxlength'=>75));
                    if( $rc['stat'] != 'ok' ) {
                        return $rc;
                    }
                    $animal['images'][$img_id]['image_data'] = 'data:image/jpg;base64,' . base64_encode($rc['image']);
                }
            }
        }
    }

    return array('stat'=>'ok', 'animal'=>$animal);
}
?>
