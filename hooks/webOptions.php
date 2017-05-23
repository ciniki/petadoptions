<?php
//
// Description
// -----------
// This function will return the list of options for the module that can be set for the website.
//
// Arguments
// ---------
// ciniki:
// settings:        The web settings structure.
// business_id:     The ID of the business to get petadoptions web options for.
//
// args:            The possible arguments for posts
//
//
// Returns
// -------
//
function ciniki_petadoptions_hooks_webOptions(&$ciniki, $business_id, $args) {

    //
    // Check to make sure the module is enabled
    //
    if( !isset($ciniki['business']['modules']['ciniki.petadoptions']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.petadoptions.13', 'msg'=>"I'm sorry, the page you requested does not exist."));
    }

    //
    // Get the settings from the database
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDetailsQueryDash');
    $rc = ciniki_core_dbDetailsQueryDash($ciniki, 'ciniki_web_settings', 'business_id', $business_id, 'ciniki.web', 'settings', 'page-petadoptions');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['settings']) ) {
        $settings = array();
    } else {
        $settings = $rc['settings'];
    }

    $poptions = array();

    $foptions = array();

    $pages['ciniki.petadoptions.available'] = array('name'=>'Pet Available', 'options'=>$poptions);
    $pages['ciniki.petadoptions.adopted'] = array('name'=>'Pets Adopted', 'options'=>$poptions);
    $pages['ciniki.petadoptions.form'] = array('name'=>'Application Form', 'options'=>$foptions);

    return array('stat'=>'ok', 'pages'=>$pages);
}
?>
