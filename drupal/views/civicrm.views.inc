<?php
  /**
   * @file
   * Provide views data and handlers for civicrm.module
   */

  /**
   * @defgroup views_civicrm_module civicrm.module handlers
   *
   * @{
   */

  /**
   * Implementation of hook_views_data()
   */
function civicrm_views_data() {
    // ----------------------------------------------------------------
    // civicrm_contact table

    // Define the base group of this table. Fields that don't
    // have a group defined will go into this field by default.

    $data = array( );

    civicrm_initialize( );
    require_once 'CRM/Core/Config.php';
    $config =& CRM_Core_Config::singleton( );

    civicrm_views_data_contact     ( $data, $config );

    civicrm_views_data_email       ( $data, $config );

    civicrm_views_data_contribution( $data, $config );

    return $data;
  }

/**
 * Implementation of hook_views_plugins
 */
function civicrm_views_plugins() {
    return array(
                 'module' => 'views', // This just tells our themes are elsewhere.
                 'argument default' => array(
                                             'user' => array(
                                                             'title' => t('Contact ID from URL'),
                                                             'handler' => 'views_plugin_argument_default_civicrm',
                                                             ),
                                             ),
                 );
}


/**
 * Allow replacement of current userid so we can cache these queries
 */
function civicrm_views_query_substitutions($view) {
    global $user;
    $uid = intval( $user->uid );
    civicrm_initialize( );
    require_once 'CRM/Core/BAO/UFMatch.php';
    $id  = CRM_Core_BAO_UFMatch::getContactId( $uid );
    return array('***CURRENT_CIVICRM***' => intval($id));
}

function civicrm_views_href( $text, $path, $query ) {
    civicrm_initialize( );
    require_once 'CRM/Utils/System.php';
    return CRM_Utils_System::href( $text, $path, $query );
}
 
function civicrm_views_data_contact( &$data, &$config ) {
   $data['civicrm_contact']['table']['group']  = t('CiviCRM Contact');

    $data['civicrm_contact']['table']['base'] = array(
                                                      'field' => 'id',
                                                      'title' => t('CiviCRM Contacts'),
                                                      'help' => t('CiviCRM Contacts'),
                                                      'database' => 'civicrm',
                                                      );

    // id
    $data['civicrm_contact']['id'] = array(
                                           'title' => t('Contact ID'),
                                           'help' => t('The contact ID'), // The help that appears on the UI,
                                           'field' => array(
                                                            'handler' => 'views_handler_field',
                                                            'click sortable' => TRUE,
                                                            ),
                                           'sort' => array(
                                                           'handler' => 'views_handler_sort',
                                                           ),
                                           );

    // name
    $data['civicrm_contact']['name'] = array(
                                             'title' => t('Name'), // The item it appears as on the UI,
                                             'real field' => 'display_name',
                                             'help' => t('The display name of the contact.'), // The help that appears on the UI,
                                             'field' => array(
                                                              'handler' => 'views_handler_field',
                                                              'click sortable' => TRUE,
                                                              ),
                                             'sort' => array(
                                                             'handler' => 'views_handler_sort',
                                                             ),
                                             'argument' => array(
                                                                 'handler' => 'views_handler_argument_string',
                                                                 ),
                                             );

    // contact_type
    $data['civicrm_contact']['contact_type'] = array(
                                                     'title' => t('Contact Type'), // The item it appears as on the UI,
                                                     'help' => t('Contact Type.'), // The help that appears on the UI,
                                                     'field' => array(
                                                                      'handler' => 'views_handler_field',
                                                                      'click sortable' => TRUE,
                                                                      ),
                                                     'sort' => array(
                                                                     'handler' => 'views_handler_sort',
                                                                     ),
                                                     'filter' => array(
                                                                       'handler' => 'views_handler_filter_civicrm_contact_type',
                                                                       ),
                                                     );

    $data['civicrm_contact']['edit_contact'] = array(
                                                     'field' => array(
                                                                      'title' => t('Edit link'),
                                                                      'help' => t('link to edit the contact.'),
                                                                      'handler' => 'views_handler_field_civicrm_link_edit',
                                                                      ),
                                                     );

    $data['civicrm_contact']['delete_contact'] = array(
                                                       'field' => array(
                                                                        'title' => t('Delete link'),
                                                                        'help' => t('link to delete the contact.'),
                                                                        'handler' => 'views_handler_field_civicrm_link_delete',
                                                                        ),
                                                       );

    // add the other generic fields
    civicrm_initialize( );
    $objType = 'Individual';
    require_once 'CRM/Contact/BAO/Contact.php';
    $fields = CRM_Contact_BAO_Contact::exportableFields( $objType );

    $skipFields = array( 'id' => 1,
                         'contact_type' => 1,
                         'contact_sub_type' => 1,
                         );
    foreach ( $fields as $name => $value ) {
        if ( CRM_Utils_Array::value( $name, $skipFields ) ||
             substr( $value['where'], 0, 15 ) != 'civicrm_contact' ) {
            continue;
        }

        $data['civicrm_contact'][$name] = array(
                                                'title' => $value['title'],
                                                'help'  => $value['title'],
                                                'field' => array( 
                                                                 'handler' => civicrm_views_get_field_handler  ( $value['type'], $name, $objType ),
                                                                 'click sortable' => true,
                                                                  ),
                                                'sort'  => array(
                                                                 'handler' => civicrm_views_get_sort_handler   ( $value['type'], $name, $objType ),
                                                                 ),
                                                'filter' => array(
                                                                  'handler' => civicrm_views_get_filter_handler( $value['type'], $name, $objType ),
                                                                  ),
                                                );
    }
}

function civicrm_views_data_email( &$data, &$config ) {
    // ----------------------------------------------------------------------
    // CiviCRM email table

    $data['civicrm_email']['table']['group']  = t('CiviCRM Contact');

    // Explain how this table joins to others.
    $data['civicrm_email']['table']['join'] = array(
                                                    // Directly links to contact table.
                                                    'civicrm_contact' => array(
                                                                               'left_field' => 'id',
                                                                               'field' => 'contact_id',
                                                                               ),
                                                    );

    $data['civicrm_email']['email'] = array(
                                            'title' => t('Email'),
                                            'help' => t('Email address of the contact'),
                                            'field' => array(
                                                             'handler' => 'views_handler_field_civicrm_email',
                                                             ),
                                            'filter' => array(
                                                              'handler' => 'views_handler_filter_string',
                                                              ),
                                            );

    $data['civicrm_email']['email_location'] = array(
                                                     'title' => t('Email Location'),
                                                     'real field' => 'location_type_id',
                                                     'help' => t('Email location of the contact (home,work...)'),
                                                     'field' => array(
                                                                      'handler' => 'views_handler_field_civicrm_location',
                                                                      ),
                                                     'filter' => array(
                                                                       'handler' => 'views_handler_filter_civicrm_location',
                                                                       ),
                                            );

    $data['civicrm_email']['email_is_primary'] = array(
                                                       'title' => t('Is Primary Email?'),
                                                       'real field' => 'is_primary',
                                                       'help' => t('Is this the primary email?'),
                                                       'field' => array(
                                                                        'handler' => 'views_handler_field_boolean',
                                                                        ),
                                                       'filter' => array(
                                                                         'handler' => 'views_handler_filter_boolean_operator',
                                                                         ),
                                                       );

}

function civicrm_views_data_contribution( &$data, &$config ) {
    // ----------------------------------------------------------------------
    // CiviCRM email table

    $data['civicrm_contribution']['table']['group']  = t('CiviCRM Contributions');

    $data['civicrm_contribution']['table']['base'] =
        array(
              'field' => 'id',
              'title' => t('CiviCRM Contributions'),
              'help' => t('CiviCRM Contributions' ), 
              'database' => 'civicrm',
             );

    // Explain how this table joins to others.
    $data['civicrm_contribution']['table']['join'] = array(
                                                           // Directly links to contact table.
                                                           'civicrm_contact' => array(
                                                                                      'left_field' => 'id',
                                                                                      'field' => 'contact_id',
                                                                                      ),
                                                           );


    $data['civicrm_contribution']['id'] = array(
                                                'title' => t('Contribution ID'),
                                                'help'  => t('The contribution ID'),
                                                'field' => array(
                                                                 'handler' => 'views_handler_field_numeric',
                                                                 'click sortable' => TRUE,
                                                                 ),
                                                'filter' => array(
                                                                  'handler' => 'views_handler_filter_numeric',
                                                                  ),
                                                'sort' => array(
                                                                'handler' => 'views_handler_sort',
                                                                ),
                                                'argument' => array(
                                                                    'handler' => 'views_handler_argument',
                                                                    ),
                                                );

    $data['civicrm_contribution']['contact_id'] = array(
                                                        'title' => t('Contact ID'),
                                                        'help' => t('The contact who made this contribution'),
                                                        'relationship' => array(
                                                                                'base' => 'civicrm_contact',
                                                                                'field' => 'id',
                                                                                'handler' => 'views_handler_relationship',
                                                                                'label' => t('CiviCRM Contact'),
                                                                                ),
                                                        );

    $data['civicrm_contribution']['total_amount'] = array(
                                                          'title' => t('Total Amount'),
                                                          'help' => t('Total Amount contributed'),
                                                          'field' => array(
                                                                           'handler' => 'views_handler_field_numeric',
                                                                           ),
                                                          'filter' => array(
                                                                            'handler' => 'views_handler_filter_numeric',
                                                                            ),
                                                          );
}

function civicrm_views_get_field_handler( $type, $fieldName, $objectName ) {
    return 'views_handler_field';
}

function civicrm_views_get_filter_handler( $type, $fieldName, $objectName ) {
    return 'views_handler_filter';
}

function civicrm_views_get_sort_handler( $type, $fieldName, $objectName ) {
    return 'views_handler_sort';
}

/**
 * Implementation of hook_views_handlers() to register all of the basic handlers
 * views uses.
 */
function civicrm_views_handlers() {
    return array(
                 'info' => array(
                                 'path' => drupal_get_path('module', 'views') . '/modules/civicrm',
                                 ),
                 'handlers' => array(
                                     'views_handler_field_civicrm' 
                                     => array(
                                              'parent' => 'views_handler_field',
                                              ),
                                     'views_handler_field_civicrm_link' 
                                     => array(
                                              'parent' => 'views_handler_field',
                                              ),
                                     'views_handler_field_civicrm_link_edit' 
                                     => array(
                                              'parent' => 'views_handler_field_civicrm_link',
                                              ),
                                     'views_handler_field_civicrm_link_delete' 
                                     => array(
                                              'parent' => 'views_handler_field_civicrm_link',
                                              ),
                                     'views_handler_field_civicrm_mail' 
                                     => array(
                                              'parent' => 'views_handler_field_civicrm',
                                              ),
                                     'views_handler_field_civicrm_email' 
                                     => array(
                                              'parent' => 'views_handler_field_civicrm',
                                              ),
                                     'views_handler_field_civicrm_location' 
                                     => array(
                                              'parent' => 'views_handler_field_civicrm',
                                              ),
                                     'views_handler_filter_civicrm_contact_type' 
                                     => array(
                                              'parent' => 'views_handler_filter_in_operator',
                                              ),
                                     'views_handler_filter_civicrm_location'
                                     => array(
                                              'parent' => 'views_handler_filter_in_operator',
                                              ),

                                     ),
                 );
}
