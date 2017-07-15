<?php
//
// Description
// -----------
// This function will process a web request for the pet adoptions module.
//
// Arguments
// ---------
// ciniki:
// settings:        The web settings structure.
// business_id:     The ID of the business to get food market request for.
//
// args:            The possible arguments for posts
//
//
// Returns
// -------
//
function ciniki_petadoptions_web_processRequest(&$ciniki, $settings, $business_id, $args) {

    //
    // Check to make sure the module is enabled
    //
    if( !isset($ciniki['business']['modules']['ciniki.petadoptions']) ) {
        return array('stat'=>'404', 'err'=>array('code'=>'ciniki.petadoptions.11', 'msg'=>"I'm sorry, the page you requested does not exist."));
    }

    //
    // Check if adoption form requested
    //
    if( isset($args['module_page']) && $args['module_page'] == 'ciniki.petadoptions.form' ) {
        ciniki_core_loadMethod($ciniki, 'ciniki', 'petadoptions', 'web', 'processRequestForm');
        return ciniki_petadoptions_web_processRequestForm($ciniki, $settings, $business_id, $args);
    }

    //
    // Decide which status 
    //
    $status = 0;
    $nodata = "We're sorry, but there are no animals available at this time.";
    if( isset($args['module_page']) && $args['module_page'] == 'ciniki.petadoptions.adopted' ) {
        $status = 50;
        $nodata = "We're sorry, but there are no animals available at this time.";
    } else if( isset($args['module_page']) && $args['module_page'] == 'ciniki.petadoptions.available' ) {
        $status = 10;
        $nodata = "We're sorry, but there are no animals available at this time.";
    }

    //
    // Build the page
    //
    $page = array(
        'title'=>$args['page_title'],
        'breadcrumbs'=>$args['breadcrumbs'],
        'blocks'=>array(),
        'submenu'=>array(),
        );

    $ciniki['response']['head']['og']['url'] = $args['domain_base_url'];

    //
    // Check for image formats
    //
    $thumbnail_format = 'square-cropped';
    $thumbnail_padding_color = '#ffffff';
    if( isset($settings['page-petadoptions-thumbnail-format']) && $settings['page-petadoptions-thumbnail-format'] == 'square-padded' ) {
        $category_thumbnail_format = $settings['page-petadoptions-thumbnail-format'];
        if( isset($settings['page-petadoptions-thumbnail-padding-color']) && $settings['page-petadoptions-thumbnail-padding-color'] != '' ) {
            $category_thumbnail_padding_color = $settings['page-petadoptions-thumbnail-padding-color'];
        } 
    }

    //
    // FIXME: Add category check
    //

    //
    // Parse the url
    //
    $display = 'list';
    if( isset($args['uri_split'][0]) && $args['uri_split'][0] != '' ) {
        $display = 'animal';
        $animal_permalink = $args['uri_split'][0];
        if( isset($args['uri_split'][2]) && $args['uri_split'][1] == 'gallery' && $args['uri_split'][2] != '' ) {
            $display = 'animalpic';
            $image_permalink = $args['uri_split'][2];
        }
    }

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');

    //
    // Display the animal
    //
    if( $display == 'animal' || $display == 'animalpic' ) {
        //
        // Load the animal details
        //
        ciniki_core_loadMethod($ciniki, 'ciniki', 'petadoptions', 'private', 'animalLoad');
        $rc = ciniki_petadoptions_animalLoad($ciniki, $business_id, $animal_permalink, array('images'=>'yes'));
        if( $rc['stat'] != 'ok' ) {
            if( $rc['stat'] == 'noexist' ) {
                return array('stat'=>'404', 'err'=>array('code'=>'ciniki.petadoptions.12', 'msg'=>"We're sorry, but we couldn't find the animal you're looking for."));
            }
            return $rc;
        }
        $animal = $rc['animal'];
        $page['breadcrumbs'][] = array('name'=>$animal['name'], 'url'=>$args['base_url'] . '/' . $animal_permalink);
        $ciniki['response']['head']['og']['url'] = $args['domain_base_url'] . '/' . $animal_permalink;
        $base_url = $args['base_url'] . '/' . $animal_permalink;
        $page['title'] = $animal['name'];

        //
        // Setup sharing information
        //
        if( isset($animal['synopsis']) && $animal['synopsis'] != '' ) {
            $ciniki['response']['head']['og']['description'] = strip_tags($animal['synopsis']);
        } elseif( isset($animal['description']) && $animal['description'] != '' ) {
            $ciniki['response']['head']['og']['description'] = strip_tags($animal['description']);
        }

        if( $display == 'animalpic' ) {
            if( !isset($animal['images']) || count($animal['images']) < 1 ) {
                $page['blocks'][] = array('type'=>'message', 'section'=>'petadoptions-image', 'content'=>"I'm sorry, but we can't seem to find the image you requested.");
            } else {
                ciniki_core_loadMethod($ciniki, 'ciniki', 'web', 'private', 'galleryFindNextPrev');
                $rc = ciniki_web_galleryFindNextPrev($ciniki, $animal['images'], $image_permalink);
                if( $rc['stat'] != 'ok' ) {
                    return $rc;
                }
                if( $rc['img'] == NULL ) {
                    $page['blocks'][] = array('type'=>'message', 'section'=>'petadoptions-image', 'content'=>"I'm sorry, but we can't seem to find the image you requested.");
                } else {
                    $page['breadcrumbs'][] = array('name'=>$rc['img']['title'], 'url'=>$base_url . '/gallery/' . $image_permalink);
                    if( $rc['img']['title'] != '' ) {
                        $page['title'] .= ' - ' . $rc['img']['title'];
                    }
                    $block = array('type'=>'galleryimage', 'section'=>'petadoptions-image', 'primary'=>'yes', 'image'=>$rc['img']);
                    if( $rc['prev'] != null ) {
                        $block['prev'] = array('url'=>$base_url . '/gallery/' . $rc['prev']['permalink'], 'image_id'=>$rc['prev']['image_id']);
                    }
                    if( $rc['next'] != null ) {
                        $block['next'] = array('url'=>$base_url . '/gallery/' . $rc['next']['permalink'], 'image_id'=>$rc['next']['image_id']);
                    }
                    $page['blocks'][] = $block;
                }
            }
        } 

        //
        // Setup the blocks to display the animal
        //
        else {
            //
            // Add primary image
            //
            if( isset($animal['primary_image_id']) && $animal['primary_image_id'] > 0 ) {
                $page['blocks'][] = array('type'=>'asideimage', 'section'=>'primary-image', 'primary'=>'yes', 
                    'image_id'=>$animal['primary_image_id'], 'title'=>$animal['name'], 'caption'=>'');
            }

            //
            // Add description
            //
            $content = '';
            if( isset($animal['description']) && $animal['description'] != '' ) {
                $page['blocks'][] = array('type'=>'content', 'section'=>'content', 'title'=>'', 'content'=>$animal['description']);
            } elseif( isset($animal['synopsis']) ) {
                $page['blocks'][] = array('type'=>'content', 'section'=>'content', 'title'=>'', 'content'=>$animal['synopsis']);
            }

            //
            // Add prices, links, files, etc to the page blocks
            //
            if( !isset($settings['page-petadoptions-share-buttons']) || $settings['page-petadoptions-share-buttons'] == 'yes' ) {
                $tags = array();
                $page['blocks'][] = array('type'=>'sharebuttons', 'section'=>'share', 'pagetitle'=>$animal['name'], 'tags'=>$tags);
            }
            if( isset($animal['images']) && count($animal['images']) > 0 ) {
                $page['blocks'][] = array('type'=>'gallery', 'section'=>'gallery', 'title'=>'Additional Images', 
                    'base_url'=>$base_url . '/gallery', 'images'=>$animal['images']);
            }
        }

    }
    
    //
    // Display the list of animals
    //
    if( $display == 'list' ) {
        $strsql = "SELECT id, name, permalink, primary_image_id, synopsis, description, 'yes' AS is_details "
            . "FROM ciniki_petadoption_animals "
            . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
            . "AND status = '" . ciniki_core_dbQuote($ciniki, $status) . "' "
            . "AND (flags&0x01) = 0x01 "
            . "";
        $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.petadoptions', array(
            array('container'=>'animal', 'fname'=>'id', 'fields'=>array('id', 'name', 'permalink', 'image_id'=>'primary_image_id', 'synopsis', 'description', 'is_details')),
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['animal']) && count($rc['animal']) > 0 ) {
            $page['blocks'][] = array('type'=>'imagelist', 'base_url'=>$args['base_url'], 'list'=>$rc['animal']);
        } else {
            $page['blocks'][] = array('type'=>'content', 'content'=>$nodata);
        }

        if( $status == 10 ) {
            $strsql = "SELECT id, name, permalink, primary_image_id, synopsis, description, 'yes' AS is_details "
                . "FROM ciniki_petadoption_animals "
                . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
                . "AND status = '30' "
                . "";
            $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.petadoptions', array(
                array('container'=>'animal', 'fname'=>'id', 'fields'=>array('id', 'name', 'permalink', 'image_id'=>'primary_image_id', 'synopsis', 'description', 'is_details')),
                ));
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
            if( isset($rc['animal']) && count($rc['animal']) > 0 ) {
                $page['blocks'][] = array('type'=>'imagelist', 'title'=>'Pending Adoptions', 'base_url'=>$args['base_url'], 'list'=>$rc['animal']);
            }
        }
    }

    return array('stat'=>'ok', 'page'=>$page);
}
?>
