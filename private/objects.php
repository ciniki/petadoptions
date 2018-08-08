<?php
//
// Description
// -----------
// This function returns the list of objects for the module.
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_petadoptions_objects(&$ciniki) {
    //
    // Build the objects
    //
    $objects = array();
    $objects['animal'] = array(
        'name'=>'Animals',
        'sync'=>'yes',
        'o_name'=>'animal',
        'o_container'=>'animals',
        'table'=>'ciniki_petadoption_animals',
        'fields'=>array(
            'name'=>array('name'=>'Name'),
            'permalink'=>array('name'=>'Permalink'),
            'flags'=>array('name'=>'Options', 'default'=>1),
            'status'=>array('name'=>'Status', 'default'=>'10'),
            'category'=>array('name'=>'Category', 'default'=>''),
            'breed'=>array('name'=>'Breed', 'default'=>''),
            'sex'=>array('name'=>'Sex', 'default'=>''),
            'years'=>array('name'=>'Age', 'default'=>''),
            'color'=>array('name'=>'Color', 'default'=>''),
            'size'=>array('name'=>'Size', 'default'=>''),
            'location'=>array('name'=>'Location', 'default'=>''),
            'primary_image_id'=>array('name'=>'Image', 'ref'=>'ciniki.images.image', 'default'=>'0'),
            'synopsis'=>array('name'=>'Synopsis', 'default'=>''),
            'description'=>array('name'=>'Description', 'default'=>''),
            'youtube_id'=>array('name'=>'Youtube', 'default'=>''),
            ),
        'history_table'=>'ciniki_petadoptions_history',
        );
    $objects['image'] = array(
        'name'=>'Image',
        'sync'=>'yes',
        'o_name'=>'image',
        'o_container'=>'images',
        'table'=>'ciniki_petadoption_images',
        'fields'=>array(
            'animal_id'=>array('name'=>'Adoption', 'ref'=>'ciniki.petadoptions.animal'),
            'title'=>array('name'=>'Title', 'default'=>''),
            'permalink'=>array('name'=>'permalink'),
            'flags'=>array('name'=>'Options', 'default'=>'1'),
            'image_id'=>array('name'=>'Image', 'ref'=>'ciniki.images.image', 'default'=>'0'),
            'description'=>array('name'=>'Description', 'default'=>''),
            ),
        'history_table'=>'ciniki_petadoptions_history',
        );

    return array('stat'=>'ok', 'objects'=>$objects);
}
?>
