<?php
//
// Description
// -----------
// This method will return the list of Animalss for a tenant.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:        The ID of the tenant to get Animals for.
//
// Returns
// -------
//
function ciniki_petadoptions_animalList($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'),
        'status'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Status'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];

    //
    // Check access to tnid as owner, or sys admin.
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'petadoptions', 'private', 'checkAccess');
    $rc = ciniki_petadoptions_checkAccess($ciniki, $args['tnid'], 'ciniki.petadoptions.animalList');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Get the list of animals
    //
    $strsql = "SELECT ciniki_petadoption_animals.id, "
        . "ciniki_petadoption_animals.name, "
        . "ciniki_petadoption_animals.permalink, "
        . "ciniki_petadoption_animals.flags, "
        . "ciniki_petadoption_animals.status, "
        . "ciniki_petadoption_animals.status AS status_text, "
        . "ciniki_petadoption_animals.category, "
        . "ciniki_petadoption_animals.breed, "
        . "ciniki_petadoption_animals.sex, "
        . "ciniki_petadoption_animals.years, "
        . "ciniki_petadoption_animals.color, "
        . "ciniki_petadoption_animals.size, "
        . "ciniki_petadoption_animals.location "
        . "FROM ciniki_petadoption_animals "
        . "WHERE ciniki_petadoption_animals.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . (isset($args['status']) ? " AND ciniki_petadoption_animals.status = '" . ciniki_core_dbQuote($ciniki, $args['status']) . "' " : '')
        . "ORDER BY name "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
    $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.petadoptions', array(
        array('container'=>'animals', 'fname'=>'id', 
            'fields'=>array('id', 'name', 'permalink', 'flags', 'status', 'status_text', 'category', 'breed', 'sex', 'years', 'color', 'size', 'location')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['animals']) ) {
        $animals = $rc['animals'];
        $animal_ids = array();
        foreach($animals as $iid => $animal) {
            $animal_ids[] = $animal['id'];
        }
    } else {
        $animals = array();
        $animal_ids = array();
    }

    return array('stat'=>'ok', 'animals'=>$animals, 'nplist'=>$animal_ids);
}
?>
