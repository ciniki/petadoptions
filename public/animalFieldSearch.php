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
function ciniki_petadoptions_animalFieldSearch($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'),
        'field'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Field'),
        'start_needle'=>array('required'=>'yes', 'blank'=>'yes', 'name'=>'Search String'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];

    //
    // Check access to tnid as owner, or sys admin.
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'petadoptions', 'private', 'checkAccess');
    $rc = ciniki_petadoptions_checkAccess($ciniki, $args['tnid'], 'ciniki.petadoptions.animalFieldSearch');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Get the list of field values
    //
    if( in_array($args['field'], array('category', 'breed', 'sex', 'years', 'color', 'size')) ) {
        $strsql = "SELECT DISTINCT " . $args['field'] . " AS name "
            . "FROM ciniki_petadoption_animals "
            . "WHERE ciniki_petadoption_animals.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND " . $args['field'] . " <> '' "
            . "ORDER BY name "
            . "";
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
        $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.petadoptions', array(
            array('container'=>'results', 'fname'=>'name', 'fields'=>array('name')),
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( !isset($rc['results']) ) {
            return array('stat'=>'ok', 'results'=>array());
        } 

        return array('stat'=>'ok', 'results'=>$rc['results']);
    }

    return array('stat'=>'ok', 'results'=>array());
}
?>
