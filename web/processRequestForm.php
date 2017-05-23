<?php
//
// Description
// -----------
// This function will process a form for the pet adoptions module.
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
function ciniki_petadoptions_web_processRequestForm(&$ciniki, $settings, $business_id, $args) {

    //
    // Check to make sure the module is enabled
    //
    if( !isset($ciniki['business']['modules']['ciniki.petadoptions']) ) {
        return array('stat'=>'404', 'err'=>array('code'=>'ciniki.petadoptions.22', 'msg'=>"I'm sorry, the page you requested does not exist."));
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
    // Load business settings
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'intlSettings');
    $rc = ciniki_businesses_intlSettings($ciniki, $business_id);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $intl_timezone = $rc['settings']['intl-default-timezone'];

    //
    // Load the form
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'petadoptions', 'forms', 'generic');
    $rc = ciniki_petadoptions_forms_generic($ciniki, $business_id);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $form = $rc['form'];
    $values = array();

    //
    // Check if form was submitted
    //
    $display = 'form';
    $dt = new DateTime('now', new DateTimezone($intl_timezone));
    $values['todays_date'] = $dt->format('M j, Y');

    $cur_section = 1;
    if( isset($_POST['cur_section']) ) {
        $cur_section = $_POST['cur_section'];
    }
    if( isset($_POST['action']) && $_POST['action'] == 'Previous' && isset($_POST['previous']) ) {
        $cur_section = $_POST['previous'];
    } elseif( isset($_POST['action']) && $_POST['action'] == 'Next' && isset($_POST['next']) ) {
        $cur_section = $_POST['next'];
    }

    //
    // Check submitted answers
    //
    foreach($form['sections'] as $sid => $section) {
        foreach($section['fields'] as $fid => $field) {

            if( $field['type'] == 'checkboxes' ) {    
                $c = 0;
                $values[$fid] = array();
                foreach($field['options'] as $option) {
                    if( isset($_POST[$fid . '_' . $c]) ) {
                        $values[$fid][] = $option;
                    }
                    $c++;
                }
            } elseif( isset($_POST[$fid]) ) { 
                $values[$fid] = $_POST[$fid];
            }

            //
            // Check conditions
            //
            $visible = 'yes';
            if( isset($field['conditions']) ) {
                $visible = 'yes';
                foreach($field['conditions'] as $condition) {
                    //
                    // Check if conditions field exists
                    //
                    if( !isset($values[$condition['field']]) || $values[$condition['field']] != $condition['value'] ) {
                        $visible = 'no';
                    }
                }
            }
            $form['sections'][$sid]['fields'][$fid]['visible'] = $visible;

            //
            // Check for missing required fields on the submitted section
            //
            if( isset($_POST['action']) && $_POST['action'] != 'Previous' ) {
                if( isset($_POST['cur_section']) && $sid == $_POST['cur_section'] && isset($field['required']) && $field['required'] == 'yes' && $visible == 'yes' ) {
                    if( $field['type'] == 'text' && (!isset($values[$fid]) || trim($values[$fid]) == '') ) {
                        $form['sections'][$sid]['fields'][$fid]['err_msg'] = 'This field is required';
                        $cur_section = $sid;
                    }
                    elseif( $field['type'] == 'radio' && (!isset($values[$fid]) ||!in_array($values[$fid], $field['options'])) ) {
                        $form['sections'][$sid]['fields'][$fid]['err_msg'] = 'This field is required';
                        $cur_section = $sid;
                    }
                    elseif( $field['type'] == 'checkboxes' && (!isset($values[$fid]) || count($values[$fid]) == 0) ) {
                        $form['sections'][$sid]['fields'][$fid]['err_msg'] = 'This field is required';
                        $cur_section = $sid;
                    }
                }
            }
        }
    }

    //
    // Process the form submission
    //
    if( isset($_POST['action']) && $_POST['action'] == 'Submit' ) {
        $html_content = '<table width="100%" border="0" cellpadding="5" cellspacing="0" bgcolor="#ffffff" style="border:0px;"><tbody>';
        $text_content = '';
        foreach($form['sections'] as $sid => $section) {
            foreach($section['fields'] as $fid => $field) {
                if( $field['visible'] == 'no' ) {
                    continue;
                }
                if( isset($field['type']) && $field['type'] == 'label' ) {
                    $html_content .= "<tr bgcolor='#dfdfdf'><td colspan='2'>" . $field['label'] . "</td></tr>";
                } else {
                    if( isset($field['label']) && $field['label'] != '' ) {
                        $html_content .= "<tr bgcolor='#EAF2FA'><td colspan='2'>" . $field['label'] . "</td></tr>";
                    }
                    $html_content .= "<tr><td width='20'>&nbsp;</td><td>";
                    if( $field['type'] == 'checkboxes' ) {
                        if( isset($values[$fid]) ) {
                            foreach($values[$fid] as $v) {
                                $html_content .= $v . "<br/>";
                                $text_content .= $v . "\n";
                            }
                        }
                        $text_content .= "\n";
                    } elseif( $field['type'] != 'textarea' ) {
                        $html_content .= (isset($values[$fid]) ? $values[$fid] : '');
                        $text_content .= $field['label'] . ": ";
                        $text_content .= (isset($values[$fid]) ? $values[$fid] : '') . "\n";
                    } else {
                        $html_content .= (isset($values[$fid]) ? $values[$fid] : '');
                        $text_content .= $field['label'] . ":\n";
                        $text_content .= (isset($values[$fid]) ? $values[$fid] : '') . "\n\n";
                    }
                    $html_content .= "</td></tr>";
                }
            }
        }
        $html_content .= "</tbody></table>";

        //
        // Get the business owners
        //
        ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'hooks', 'businessOwners');
        $rc = ciniki_businesses_hooks_businessOwners($ciniki, $business_id, array());
        if( $rc['stat'] != 'ok' ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.mail.16', 'msg'=>'Unable to get business owners', 'err'=>$rc['err']));
        }
        $owners = $rc['users'];

        foreach($owners as $user_id => $owner) {
            ciniki_core_loadMethod($ciniki, 'ciniki', 'mail', 'hooks', 'addMessage');
            $rc = ciniki_mail_hooks_addMessage($ciniki, $business_id, array(
                'customer_email'=>$owner['email'],
                'customer_name'=>$owner['firstname'] . ' ' . $owner['lastname'],
                'subject'=>'New Adoption Application for ' . $values['animal_name'],
                'html_content'=>$html_content,
                'text_content'=>$text_content,
                ));
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
            $ciniki['emailqueue'][] = array('mail_id'=>$rc['id'], 'business_id'=>$business_id);
        }
        $display = 'submitted';
    }

    //
    // Display the form
    //
    if( $display == 'submitted' ) {
        $page['blocks'][] = array('type'=>'content', 'content'=>"Thank you for your adoption application, we will be in touch.");
    } else {
        $page['blocks'][] = array('type'=>'sectionedform', 
            'base_url'=>$args['base_url'], 
            'section'=>$cur_section, 
            'form'=>$form, 
            'values'=>$values,
            );
    }

    return array('stat'=>'ok', 'page'=>$page);
}
?>
