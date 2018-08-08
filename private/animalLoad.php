<?php
//
// Description
// ===========
// This method will return all the information about an animal.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant the animal is attached to.
// animal_id:           The ID of the animal to get the details for.
//
// Returns
// -------
//
function ciniki_petadoptions_animalLoad($ciniki, $tnid, $animal_id, $args) {

    $strsql = "SELECT ciniki_petadoption_animals.id, "
        . "ciniki_petadoption_animals.name, "
        . "ciniki_petadoption_animals.permalink, "
        . "ciniki_petadoption_animals.flags, "
        . "ciniki_petadoption_animals.status, "
        . "ciniki_petadoption_animals.category, "
        . "ciniki_petadoption_animals.breed, "
        . "ciniki_petadoption_animals.sex, "
        . "ciniki_petadoption_animals.years, "
        . "ciniki_petadoption_animals.color, "
        . "ciniki_petadoption_animals.size, "
        . "ciniki_petadoption_animals.location, "
        . "ciniki_petadoption_animals.primary_image_id, "
        . "ciniki_petadoption_animals.synopsis, "
        . "ciniki_petadoption_animals.description, "
        . "ciniki_petadoption_animals.youtube_id "
        . "FROM ciniki_petadoption_animals "
        . "WHERE ciniki_petadoption_animals.tnid = '" . ciniki_core_dbQuote($ciniki, $tnid) . "' "
        . "";
    if( !is_numeric($animal_id) ) {
        $strsql .= "AND ciniki_petadoption_animals.permalink = '" . ciniki_core_dbQuote($ciniki, $animal_id) . "' ";
    } else {
        $strsql .= "AND ciniki_petadoption_animals.id = '" . ciniki_core_dbQuote($ciniki, $animal_id) . "' ";
    }
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQuery');
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.petadoptions', 'animal');
    if( $rc['stat'] != 'ok' ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.petadoptions.8', 'msg'=>'Animals not found', 'err'=>$rc['err']));
    }
    if( !isset($rc['animal']) ) {
        return array('stat'=>'noexist', 'err'=>array('code'=>'ciniki.petadoptions.9', 'msg'=>'Unable to find animal.'));
    }
    $animal = $rc['animal'];

    //
    // Get the images as well
    //
    if( isset($args['images']) && $args['images'] == 'yes' ) {
        $strsql = "SELECT id, title, permalink, flags, image_id, description "
            . "FROM ciniki_petadoption_images "
            . "WHERE animal_id = '" . ciniki_core_dbQuote($ciniki, $animal['id']) . "' "
            . "AND tnid = '" . ciniki_core_dbQuote($ciniki, $tnid) . "' "
            . "AND (flags&0x01) = 0x01 "
            . "";
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
        $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.petadoptions', array(
            array('container'=>'images', 'fname'=>'id', 'fields'=>array('id', 'title', 'permalink', 'flags', 'image_id', 'description')),
        ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['images']) ) {
            $animal['images'] = $rc['images'];
        } else {
            $animal['images'] = array();
        }
    }

    return array('stat'=>'ok', 'animal'=>$animal);
}
?>
