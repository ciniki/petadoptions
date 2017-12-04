<?php
//
// Description
// -----------
// This method searchs for a Animalss for a tenant.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:        The ID of the tenant to get Animals for.
// start_needle:       The search string to search for.
// limit:              The maximum number of entries to return.
//
// Returns
// -------
//
function ciniki_petadoptions_animalSearch($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'),
        'start_needle'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Search String'),
        'limit'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Limit'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];

    //
    // Check access to tnid as owner, or sys admin.
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'petadoptions', 'private', 'checkAccess');
    $rc = ciniki_petadoptions_checkAccess($ciniki, $args['tnid'], 'ciniki.petadoptions.animalSearch');
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
        . "ciniki_petadoption_animals.category, "
        . "ciniki_petadoption_animals.breed, "
        . "ciniki_petadoption_animals.sex, "
        . "ciniki_petadoption_animals.years, "
        . "ciniki_petadoption_animals.color, "
        . "ciniki_petadoption_animals.size, "
        . "ciniki_petadoption_animals.location "
        . "FROM ciniki_petadoption_animals "
        . "WHERE ciniki_petadoption_animals.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND ("
            . "name LIKE '" . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
            . "OR name LIKE '% " . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
        . ") "
        . "";
    if( isset($args['limit']) && is_numeric($args['limit']) && $args['limit'] > 0 ) {
        $strsql .= "LIMIT " . ciniki_core_dbQuote($ciniki, $args['limit']) . " ";
    } else {
        $strsql .= "LIMIT 25 ";
    }
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
    $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.petadoptions', array(
        array('container'=>'animals', 'fname'=>'id', 
            'fields'=>array('id', 'name', 'permalink', 'flags', 'status', 'category', 'breed', 'sex', 'years', 'color', 'size', 'location')),
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
