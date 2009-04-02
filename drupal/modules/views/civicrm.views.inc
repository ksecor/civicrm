<?php
  /*
   * Copyright (C) 2009 James Rakich
   * Licensed to CiviCRM under the Academic Free License version 3.0.
   *
   */

  /*
   *  civicrm.views.inc  Called from civicrm.module, gives the views cache all information it needs
   *                     to access CiviCRM's database for use in Views, as well as referencing the
   *                     custom handlers for displaying, sorting, filtering and accepting arguments
   *                     for this data.
   *
   *  function civicrm_views_data() {  // Defines tables, joins and relationships
   *     CiviCRM Contacts Base Table
   *     CiviCRM Events Base Table
   *     CiviCRM Participants Base Table
   *     CiviCRM Contributions Base Table
   *     CiviCRM Activities Base Table
   *     Text Links for each Base Table
   *     Custom Data Cache Query and Calls
   *
   *  function civicrm_views_href( $text, $path, $query )
   *     Generates a link for CiviCRM Paths - unchanged from previous code (anonymous donation)
   *
   *  function civicrm_views_custom_data_cache($data, $entity_type, $groupID, $subType, $style)
   *     Collects the data from Custom Data Groups and assigns them to base tables.
   *
   *  function civicrm_get_field_handler ($type)
   *  function civicrm_get_argument_handler ($type)
   *  function civicrm_get_filter_handler ($type)
   *  function civicrm_get_sort_handler ($type)
   *     Assign handlers to custom fields based on the data type (from the database records)
   *
   *  function civicrm_views_handlers ()
   *     Tells Views where to get the custom Handlers
   */
   
   

  /**
   * Implementation of hook_views_data()
   */
function civicrm_views_data() {
    civicrm_initialize();

    require_once 'CRM/Core/Config.php';
    require_once 'CRM/Core/BAO/CustomGroup.php';
    require_once 'CRM/Core/DAO.php';
    require_once 'CRM/Core/Error.php';
    require_once 'CRM/Contact/BAO/Contact.php';
    //------------------------------------------------------
    // CiviCRM Contacts Views Table
    //------------------------------------------------------
    
    $data['civicrm_contact']['table']['group']  = t('CiviCRM Contacts');

    $data['civicrm_contact']['table']['base'] = array(
                                                      'field' => 'id', // Governs the whole mozilla
                                                      'title' => t('CiviCRM Contacts'),
                                                      'help' => t("View displays CiviCRM Contacts, of people, organizations, etc."),
                                                      );

    //TABLE JOINS FOR CIVICRM CONTACTS GO HERE!
  
    $data['civicrm_contact']['table']['join'] = array(
                                                      // Directly links to participants.
                                                      'civicrm_participant' => array(
                                                                                     'left_field' => 'contact_id',
                                                                                     'field' => 'id',
                                                                                     ),

                                                      // Directly links to contribution
                                                      'civicrm_contribution' => array(
                                                                                      'left_field' => 'contact_id',
                                                                                      'field' => 'id',
                                                                                      ),

                                                      // Directly links to activity.
                                                      'civicrm_activity' => array(
                                                                                  'left_field' => 'source_contact_id',
                                                                                  'field' => 'id',
                                                                                  ),
                                                      );

    // For other base tables, explain how we join -  
    $data['civicrm_contact']['table']['join']['users'] = array(
           'left_table'   => 'civicrm_uf_match',
           'left_field'   => 'contact_id',
           'field'        => 'id',
    );
    $data['civicrm_uf_match']['table']['join']['users'] = array(
           'left_field'   => 'uid',
           'field'        => 'uf_id',
    );    
    
    //CiviCRM Contacts - FIELDS


    //Numeric Contact ID
    $data['civicrm_contact']['id'] = array(
                                           'title' => t('Contact ID'),
                                           'help' => t('The numeric ID of the Contact'),
                                           'field' => array(
                                                            'handler' => 'views_handler_field_numeric',
                                                            'click sortable' => TRUE,
                                                            ),

                                           'argument' => array(
                                                               'handler' => 'views_handler_argument_numeric',
                                                               'numeric' => TRUE,
                                                               ),

                                           'filter' => array(
                                                             'handler' => 'views_handler_filter_numeric',
                                                             ),

                                           'sort' => array(
                                                           'handler' => 'views_handler_sort',
                                                           ),
                                           );

    // Drupal ID of the contact (from uf_match.uf_id)   
    $data['civicrm_contact']['drupal_id'] = array(  
                                                  'real field' => 'id',
                                                  'title' => t('Drupal ID'),
                                                  'help' => t('The drupal ID.'),
                                                  'field' => array(
                                                                   'handler' => 'civicrm_handler_field_drupalid',
                                                                   'click sortable' => TRUE,
                                                                   ),
                                                  'sort' => array(
                                                                  'handler' => 'views_handler_sort',
                                                                  ),
                                                  'relationship' => array(
                                                                          'base' => 'users',
                                                                          'field' => 'uid',
                                                                          'handler' => 'civicrm_handler_relationship_contact2users',
                                                                          'label' => t('Drupal User'),
                                                                          ),
                                                    );
 
    //SORT Name for the Contact (Last Name followed by First)
    $data['civicrm_contact']['sort_name'] = array(
                                                  'title' => t('Sort Name'),
                                                  'help' => t('The Contact Name for sorting purposes, Last Name then First'),
                                                  'field' => array(
                                                                   'handler' => 'civicrm_handler_field_contact_link',
                                                                   'click sortable' => TRUE,
                                                                   ),

                                                  'argument' => array(
                                                                      'handler' => 'views_handler_argument',
                                                                      ),

                                                  'filter' => array(
                                                                    'handler' => 'views_handler_filter_string',
                                                                    ),

                                                  'sort' => array(
                                                                  'handler' => 'views_handler_sort',
                                                                  ),
                                                  );
  
    //DISPLAY Name for the Contact (Full Name with Prefixes and Suffixes)
    $data['civicrm_contact']['display_name'] = array(
                                                     'title' => t('Display Name'),
                                                     'help' => t('Full Name of the Contact with prefixes and suffixes'),
                                                     'field' => array(
                                                                      'handler' => 'civicrm_handler_field_contact_link',
                                                                      'click sortable' => TRUE,
                                                                      ),

                                                     'argument' => array(
                                                                         'handler' => 'views_handler_argument',
                                                                         ),

                                                     'filter' => array(
                                                                       'handler' => 'views_handler_filter_string',
                                                                       ),

                                                     'sort' => array(
                                                                     'handler' => 'views_handler_sort',
                                                                     ),
                                                     );

    //Gender
    $data['civicrm_contact']['gender'] = array(
                                               'title' => t('Gender'),
                                               'real field' => 'gender_id',
                                               'help' => t('Contact\'s Gender'),
                                               'field' => array(
                                                                'handler' => 'civicrm_handler_field_gender',
                                                                'click sortable' => TRUE,
                                                                ),

                                               'argument' => array(
                                                                   'handler' => 'views_handler_argument',
                                                                   ),

                                               'filter' => array(
                                                                 'handler' => 'civicrm_handler_filter_gender',
                                                                 ),

                                               'sort' => array(
                                                               'handler' => 'views_handler_sort',
                                                               ),
                                               );

    //FIRST NAME
    $data['civicrm_contact']['first_name'] = array(
                                                   'title' => t('First Name'),
                                                   'help' => t('First Name'),
                                                   'field' => array(
                                                                    'handler' => 'civicrm_handler_field_contact_link',
                                                                    'click sortable' => TRUE,
                                                                    ),

                                                   'argument' => array(
                                                                       'handler' => 'views_handler_argument',
                                                                       ),

                                                   'filter' => array(
                                                                     'handler' => 'views_handler_filter_string',
                                                                     ),

                                                   'sort' => array(
                                                                   'handler' => 'views_handler_sort',
                                                                   ),
                                                   );
  
    //MIDDLE NAME
    $data['civicrm_contact']['middle_name'] = array(
                                                    'title' => t('Middle Name'),
                                                    'help' => t('Middle Name'),
                                                    'field' => array(
                                                                     'handler' => 'civicrm_handler_field_contact_link',
                                                                     'click sortable' => TRUE,
                                                                     ),

                                                    'argument' => array(
                                                                        'handler' => 'views_handler_argument',
                                                                        ),

                                                    'filter' => array(
                                                                      'handler' => 'views_handler_filter_string',
                                                                      ),

                                                    'sort' => array(
                                                                    'handler' => 'views_handler_sort',
                                                                    ),
                                                    );
  
    //LAST NAME
    $data['civicrm_contact']['last_name'] = array(
                                                  'title' => t('Last Name'),
                                                  'help' => t('Last Name'),
                                                  'field' => array(
                                                                   'handler' => 'civicrm_handler_field_contact_link',
                                                                   'click sortable' => TRUE,
                                                                   ),

                                                  'argument' => array(
                                                                      'handler' => 'views_handler_argument',
                                                                      ),

                                                  'filter' => array(
                                                                    'handler' => 'views_handler_filter_string',
                                                                    ),

                                                  'sort' => array(
                                                                  'handler' => 'views_handler_sort',
                                                                  ),
                                                  );
  
    //BIRTH DATE
    $data['civicrm_contact']['birth_date'] = array(
                                                   'title' => t('Birth Date'),
                                                   'help' => t('The Contact\'s Date of Birth'),
                                                   'field' => array(
                                                                    'handler' => 'civicrm_handler_field_datetime',
                                                                    'click sortable' => TRUE,
                                                                    ),

                                                   'argument' => array(
                                                                       'handler' => 'views_handler_argument_date',
                                                                       ),

                                                   'filter' => array(
                                                                     'handler' => 'civicrm_handler_filter_datetime',
                                                                     ),

                                                   'sort' => array(
                                                                   'handler' => 'views_handler_sort_date',
                                                                   ),
                                                   );
  
    //BOOLEAN : IS Deceased
    $data['civicrm_contact']['is_deceased'] = array(
                                                    'title' => t('Is Deceased'),
                                                    'help' => t('Is the contact deceased?'),
                                                    'field' => array(
                                                                     'handler' => 'views_handler_field_boolean',
                                                                     'click sortable' => TRUE,
                                                                     ),

                                                    'argument' => array(
                                                                        'handler' => 'views_handler_argument',
                                                                        ),

                                                    'filter' => array(
                                                                      'handler' => 'views_handler_filter_boolean_operator',
                                                                      ),

                                                    'sort' => array(
                                                                    'handler' => 'views_handler_sort',
                                                                    ),
                                                    );
  
    //THUMBSUP: Contact Permissions ie Do not email, do not this, do not that.

    //BOOLEAN : DO NOT EMAIL
    $data['civicrm_contact']['do_not_email'] = array(
                                                     'title' => t('Do Not E-mail'),
                                                     'help' => t('Does this contact not want to be emailed?'),
                                                     'field' => array(
                                                                      'handler' => 'views_handler_field_boolean',
                                                                      'click sortable' => TRUE,
                                                                      ),

                                                     'argument' => array(
                                                                         'handler' => 'views_handler_argument',
                                                                         ),

                                                     'filter' => array(
                                                                       'handler' => 'views_handler_filter_boolean_operator',
                                                                       ),

                                                     'sort' => array(
                                                                     'handler' => 'views_handler_sort',
                                                                     ),
                                                     );
  
    //BOOLEAN : DO NOT EMAIL
    $data['civicrm_contact']['do_not_phone'] = array(
                                                     'title' => t('Do Not Phone'),
                                                     'help' => t('Does this contact not want to be phoned?'),
                                                     'field' => array(
                                                                      'handler' => 'views_handler_field_boolean',
                                                                      'click sortable' => TRUE,
                                                                      ),

                                                     'argument' => array(
                                                                         'handler' => 'views_handler_argument',
                                                                         ),

                                                     'filter' => array(
                                                                       'handler' => 'views_handler_filter_boolean_operator',
                                                                       ),

                                                     'sort' => array(
                                                                     'handler' => 'views_handler_sort',
                                                                     ),
                                                     );
  
    //BOOLEAN : DO NOT Mail
    $data['civicrm_contact']['do_not_mail'] = array(
                                                    'title' => t('Do Not Mail'),
                                                    'help' => t('Does this contact not want to be mailed?'),
                                                    'field' => array(
                                                                     'handler' => 'views_handler_field_boolean',
                                                                     'click sortable' => TRUE,
                                                                     ),

                                                    'argument' => array(
                                                                        'handler' => 'views_handler_argument',
                                                                        ),

                                                    'filter' => array(
                                                                      'handler' => 'views_handler_filter_boolean_operator',
                                                                      ),

                                                    'sort' => array(
                                                                    'handler' => 'views_handler_sort',
                                                                    ),
                                                    );
  
    //BOOLEAN : DO NOT Trade
    $data['civicrm_contact']['do_not_trade'] = array(
                                                     'title' => t('Do Not Mail'),
                                                     'help' => t('Does this contact not want trade?'),
                                                     'field' => array(
                                                                      'handler' => 'views_handler_field_boolean',
                                                                      'click sortable' => TRUE,
                                                                      ),

                                                     'argument' => array(
                                                                         'handler' => 'views_handler_argument',
                                                                         ),

                                                     'filter' => array(
                                                                       'handler' => 'views_handler_filter_boolean_operator',
                                                                       ),

                                                     'sort' => array(
                                                                     'handler' => 'views_handler_sort',
                                                                     ),
                                                     );
  
    //BOOLEAN : Opted out of Bulk Mailing
    $data['civicrm_contact']['is_opt_out'] = array(
                                                   'title' => t('Opted Out of Bulk Mail'),
                                                   'help' => t('Has this contact opted out of bulk mail?'),
                                                   'field' => array(
                                                                    'handler' => 'views_handler_field_boolean',
                                                                    'click sortable' => TRUE,
                                                                    ),

                                                   'argument' => array(
                                                                       'handler' => 'views_handler_argument',
                                                                       ),

                                                   'filter' => array(
                                                                     'handler' => 'views_handler_filter_boolean_operator',
                                                                     ),

                                                   'sort' => array(
                                                                   'handler' => 'views_handler_sort',
                                                                   ),
                                                   );
                                                   
    // Organization Name
    $data['civicrm_contact']['organization_name'] = array(
                                             'title' => t('Organization Name'), // The item it appears as on the UI,
                                             'help' => t('The display name of the contact\'s organizations.'), // The help that appears on the UI,
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
                                                     'help'  => t('Contact Type.'), // The help that appears on the UI,
                                                     'field' => array(
                                                                      'handler' => 'views_handler_field',
                                                                      'click sortable' => TRUE,
                                                                      ),
                                                     'sort' => array(
                                                                     'handler' => 'views_handler_sort',
                                                                     ),
                                                     'filter' => array(
                                                                       'handler' => 'civicrm_handler_filter_contact_type',
                                                                       ),
                                                     );                                                                                           
                                                   
    // add the other generic fields
    $objType = 'Individual';
    $fields = CRM_Contact_BAO_Contact::exportableFields( $objType );

    $skipFields = array( 'id'                => 1,
                         'sort_name'         => 1,
                         'display_name'      => 1,
                         'gender'            => 1,
                         'first_name'        => 1,
                         'middle_name'       => 1, 
                         'last_name'         => 1,
                         'birth_date'        => 1,
                         'is_deceased'       => 1,
                         'do_not_email'      => 1,
                         'do_not_phone'      => 1,
                         'do_not_mail'       => 1,
                         'do_not_trade'      => 1,
                         'is_opt_out'        => 1,  
                         'contact_type'      => 1,
                         'organization_name' => 1,                       
                         );
    civicrm_views_add_fields( $fields, $data['civicrm_contact'], $skipFields, 'civicrm_contact' );                                                   
                                                   

    // CHILLING OUT AT THE CIVICRM_ADDRESS TABLE

    $data['civicrm_address']['table']['group']  = t('CiviCRM Address');

    // Explain how this table joins to others.
    $data['civicrm_address']['table']['join'] = array(
                                                      // Directly links to contact table.
                                                      'civicrm_contact' => array(
                                                                                 'left_field' => 'id',
                                                                                 'field' => 'contact_id',
                                                                                 ),
                                                      );
  
    //CiviCRM Address Using Contacts as a pitstop before going on to Participants
    $data['civicrm_address']['table']['join']['civicrm_participant'] = array(
                                                                             'left_table' => 'civicrm_contact',
                                                                             'left_field' => 'id',
                                                                             'field' => 'contact_id',
                                                                             );

    //CiviCRM Address Using Contacts as a pitstop before going on to Contributions
    $data['civicrm_address']['table']['join']['civicrm_contribution'] = array(
                                                                              'left_table' => 'civicrm_contact',
                                                                              'left_field' => 'id',
                                                                              'field' => 'contact_id',
                                                                              );
  
    //CiviCRM Address Using Contacts as a pitstop before going on to Activities
    $data['civicrm_address']['table']['join']['civicrm_activity'] = array(
                                                                          'left_table' => 'civicrm_contact',
                                                                          'left_field' => 'id',
                                                                          'field' => 'contact_id',
                                                                          );

    //CiviCRM Address Using Locations as a pitstop before going on to Events
    $data['civicrm_address']['table']['join']['civicrm_event'] = array(
                                                                       'left_table' => 'civicrm_loc_block',
                                                                       'left_field' => 'address_id',
                                                                       'field' => 'id',
                                                                       );

    //BOOLEAN : IS PRIMARY ADDRESS
    $data['civicrm_address']['is_primary'] = array(
                                                   'title' => t('Is Primary Address'),
                                                   'help' => t('Is this address the contact\'s primary address?'),
                                                   'field' => array(
                                                                    'handler' => 'views_handler_field_boolean',
                                                                    'click sortable' => TRUE,
                                                                    ),

                                                   'argument' => array(
                                                                       'handler' => 'views_handler_argument',
                                                                       ),

                                                   'filter' => array(
                                                                     'handler' => 'views_handler_filter_boolean_operator',
                                                                     ),

                                                   'sort' => array(
                                                                   'handler' => 'views_handler_sort',
                                                                   ),
                                                   );
  
    //BOOLEAN : IS BILLING ADDRESS
    $data['civicrm_address']['is_billing'] = array(
                                                   'title' => t('Is Billing Address'),
                                                   'help' => t('Is this address the contact\'s billing address?'),
                                                   'field' => array(
                                                                    'handler' => 'views_handler_field_boolean',
                                                                    'click sortable' => TRUE,
                                                                    ),

                                                   'argument' => array(
                                                                       'handler' => 'views_handler_argument',
                                                                       ),

                                                   'filter' => array(
                                                                     'handler' => 'views_handler_filter_boolean_operator',
                                                                     ),

                                                   'sort' => array(
                                                                   'handler' => 'views_handler_sort',
                                                                   ),
                                                   );

    //ADDRESS'S LOCATION TYPE
    $data['civicrm_address']['location_type'] = array(
                                                      'title' => t('Location Type'),
                                                      'real field' => 'location_type_id',
                                                      'help' => t('The Location of the Address for the Contact'),
                                                      'field' => array(
                                                                       'handler' => 'civicrm_handler_field_location_type',
                                                                       'click sortable' => TRUE,
                                                                       ),

                                                      'argument' => array(
                                                                          'handler' => 'views_handler_argument',
                                                                          ),

                                                      'filter' => array(
                                                                        'handler' => 'civicrm_handler_filter_location_type',
                                                                        ),

                                                      'sort' => array(
                                                                      'handler' => 'views_handler_sort',
                                                                      ),
                                                      );

    //FULL STREET ADDRESS
    $data['civicrm_address']['street_address'] = array(
                                                       'title' => t('Full Street Address'),
                                                       'help' => t('Full Street Address without Country, State or Postcode'),
                                                       'field' => array(
                                                                        'handler' => 'views_handler_field',
                                                                        'click sortable' => TRUE,
                                                                        ),

                                                       'argument' => array(
                                                                           'handler' => 'views_handler_argument',
                                                                           ),

                                                       'filter' => array(
                                                                         'handler' => 'views_handler_filter_string',
                                                                         ),

                                                       'sort' => array(
                                                                       'handler' => 'views_handler_sort',
                                                                       ),
                                                       );
  
    //CITY OR SUBURB THE ADDRESS IS AT
    $data['civicrm_address']['city'] = array(
                                             'title' => t('City / Suburb'),
                                             'help' => t('The City or Suburb the address is at'),
                                             'field' => array(
                                                              'handler' => 'views_handler_field',
                                                              'click sortable' => TRUE,
                                                              ),

                                             'argument' => array(
                                                                 'handler' => 'views_handler_argument',
                                                                 ),

                                             'filter' => array(
                                                               'handler' => 'views_handler_filter_string',
                                                               ),

                                             'sort' => array(
                                                             'handler' => 'views_handler_sort',
                                                             ),
                                             );
  
    //STATE OR PROVINCE
    $data['civicrm_address']['state_province'] = array(
                                                       'title' => t('State / Province'),
                                                       'real field' => 'state_province_id',
                                                       'help' => t('The State or Province of the Address'),
                                                       'field' => array(
                                                                        'handler' => 'civicrm_handler_field_state',
                                                                        'click sortable' => TRUE,
                                                                        ),

                                                       'argument' => array(
                                                                           'handler' => 'views_handler_argument',
                                                                           ),

                                                       'filter' => array(
                                                                         'handler' => 'civicrm_handler_filter_state',
                                                                         ),

                                                       'sort' => array(
                                                                       'handler' => 'views_handler_sort',
                                                                       ),
                                                       );
  
    //COUNTRY
    $data['civicrm_address']['country'] = array(
                                                'title' => t('Country'),
                                                'real field' => 'country_id',
                                                'help' => t('The Country of the Address'),
                                                'field' => array(
                                                                 'handler' => 'civicrm_handler_field_country',
                                                                 'click sortable' => TRUE,
                                                                 ),

                                                'argument' => array(
                                                                    'handler' => 'views_handler_argument',
                                                                    ),

                                                'filter' => array(
                                                                  'handler' => 'civicrm_handler_filter_country',
                                                                  ),

                                                'sort' => array(
                                                                'handler' => 'views_handler_sort',
                                                                ),
                                                );
  

    // CHILLING OUT AT THE CIVICRM_EMAIL TABLE

    $data['civicrm_email']['table']['group']  = t('CiviCRM Email');

    // Explain how this table joins to others.
    $data['civicrm_email']['table']['join'] = array(
                                                    // Directly links to contact table.
                                                    'civicrm_contact' => array(
                                                                               'left_field' => 'id',
                                                                               'field' => 'contact_id',
                                                                               ),
                                                    );
  
    //CiviCRM Email Using Contacts as a pitstop before going on to Participants
    $data['civicrm_email']['table']['join']['civicrm_participant'] = array(
                                                                           'left_table' => 'civicrm_contact',
                                                                           'left_field' => 'id',
                                                                           'field' => 'contact_id',
                                                                           );

    //CiviCRM Email Using Contacts as a pitstop before going on to Contributions
    $data['civicrm_email']['table']['join']['civicrm_contribution'] = array(
                                                                            'left_table' => 'civicrm_contact',
                                                                            'left_field' => 'id',
                                                                            'field' => 'contact_id',
                                                                            );

    //CiviCRM Email Using Contacts as a pitstop before going on to Activities
    $data['civicrm_email']['table']['join']['civicrm_activity'] = array(
                                                                        'left_table' => 'civicrm_contact',
                                                                        'left_field' => 'id',
                                                                        'field' => 'contact_id',
                                                                        );

    //CiviCRM Email Using Locations as a pitstop before going on to Events
    $data['civicrm_email']['table']['join']['civicrm_event'] = array(
                                                                     'left_table' => 'civicrm_loc_block',
                                                                     'left_field' => 'email_id',
                                                                     'field' => 'id',
                                                                     );

    //BOOLEAN : IS PRIMARY EMAIL
    $data['civicrm_email']['is_primary'] = array(
                                                 'title' => t('Is Primary Email'),
                                                 'help' => t('Is this address the contact\'s primary email?'),
                                                 'field' => array(
                                                                  'handler' => 'views_handler_field_boolean',
                                                                  'click sortable' => TRUE,
                                                                  ),

                                                 'argument' => array(
                                                                     'handler' => 'views_handler_argument',
                                                                     ),

                                                 'filter' => array(
                                                                   'handler' => 'views_handler_filter_boolean_operator',
                                                                   ),

                                                 'sort' => array(
                                                                 'handler' => 'views_handler_sort',
                                                                 ),
                                                 );
  
    //BOOLEAN : IS BILLING EMAIL
    $data['civicrm_email']['is_billing'] = array(
                                                 'title' => t('Is Billing Email'),
                                                 'help' => t('Is this address the contact\'s billing email?'),
                                                 'field' => array(
                                                                  'handler' => 'views_handler_field_boolean',
                                                                  'click sortable' => TRUE,
                                                                  ),

                                                 'argument' => array(
                                                                     'handler' => 'views_handler_argument',
                                                                     ),

                                                 'filter' => array(
                                                                   'handler' => 'views_handler_filter_boolean_operator',
                                                                   ),

                                                 'sort' => array(
                                                                 'handler' => 'views_handler_sort',
                                                                 ),
                                                 );

    //EMAIL'S LOCATION TYPE
    $data['civicrm_email']['location_type'] = array(
                                                    'title' => t('Location Type'),
                                                    'real field' => 'location_type_id',
                                                    'help' => t('The Location of the Address for the Contact'),
                                                    'field' => array(
                                                                     'handler' => 'civicrm_handler_field_location_type',
                                                                     'click sortable' => TRUE,
                                                                     ),

                                                    'argument' => array(
                                                                        'handler' => 'views_handler_argument',
                                                                        ),

                                                    'filter' => array(
                                                                      'handler' => 'civicrm_handler_filter_location_type',
                                                                      ),

                                                    'sort' => array(
                                                                    'handler' => 'views_handler_sort',
                                                                    ),
                                                    );

    //FULL EMAIL ADDRESS
    $data['civicrm_email']['email'] = array(
                                            'title' => t('Email Address'),
                                            'help' => t('Email Address'),
                                            'field' => array(
                                                             'handler' => 'civicrm_handler_field_email',
                                                             'click sortable' => TRUE,
                                                             ),

                                            'argument' => array(
                                                                'handler' => 'views_handler_argument',
                                                                ),

                                            'filter' => array(
                                                              'handler' => 'views_handler_filter_string',
                                                              ),

                                            'sort' => array(
                                                            'handler' => 'views_handler_sort',
                                                            ),
                                            );


    // CHILLING OUT AT THE CIVICRM_PHONE TABLE

    $data['civicrm_phone']['table']['group']  = t('CiviCRM Phone Details');

    // Explain how this table joins to others.
    $data['civicrm_phone']['table']['join'] = array(
                                                    // Directly links to contact table.
                                                    'civicrm_contact' => array(
                                                                               'left_field' => 'id',
                                                                               'field' => 'contact_id',
                                                                               ),
                                                    );
  
    //CiviCRM Phone Using Contacts as a pitstop before going on to Participants
    $data['civicrm_phone']['table']['join']['civicrm_participant'] = array(
                                                                           'left_table' => 'civicrm_contact',
                                                                           'left_field' => 'id',
                                                                           'field' => 'contact_id',
                                                                           );
  
    //CiviCRM Phone Using Contacts as a pitstop before going on to Contributions
    $data['civicrm_phone']['table']['join']['civicrm_contribution'] = array(
                                                                            'left_table' => 'civicrm_contact',
                                                                            'left_field' => 'id',
                                                                            'field' => 'contact_id',
                                                                            );
                                                                     
    //CiviCRM Phone Using Contacts as a pitstop before going on to Activities
    $data['civicrm_phone']['table']['join']['civicrm_activity'] = array(
                                                                        'left_table' => 'civicrm_contact',
                                                                        'left_field' => 'id',
                                                                        'field' => 'contact_id',
                                                                        );


    //CiviCRM Phone Using Locations as a pitstop before going on to Events
    $data['civicrm_phone']['table']['join']['civicrm_event'] = array(
                                                                     'left_table' => 'civicrm_loc_block',
                                                                     'left_field' => 'phone_id',
                                                                     'field' => 'id',
                                                                     );

    //BOOLEAN : IS PRIMARY EMAIL
    $data['civicrm_phone']['is_primary'] = array(
                                                 'title' => t('Is Primary Phone'),
                                                 'help' => t('Is this the contact\'s primary phone?'),
                                                 'field' => array(
                                                                  'handler' => 'views_handler_field_boolean',
                                                                  'click sortable' => TRUE,
                                                                  ),

                                                 'argument' => array(
                                                                     'handler' => 'views_handler_argument',
                                                                     ),

                                                 'filter' => array(
                                                                   'handler' => 'views_handler_filter_boolean_operator',
                                                                   ),

                                                 'sort' => array(
                                                                 'handler' => 'views_handler_sort',
                                                                 ),
                                                 );
  
    //BOOLEAN : IS BILLING EMAIL
    $data['civicrm_phone']['is_billing'] = array(
                                                 'title' => t('Is Billing Phone'),
                                                 'help' => t('Is this the contact\'s billing phone?'),
                                                 'field' => array(
                                                                  'handler' => 'views_handler_field_boolean',
                                                                  'click sortable' => TRUE,
                                                                  ),

                                                 'argument' => array(
                                                                     'handler' => 'views_handler_argument',
                                                                     ),

                                                 'filter' => array(
                                                                   'handler' => 'views_handler_filter_boolean_operator',
                                                                   ),

                                                 'sort' => array(
                                                                 'handler' => 'views_handler_sort',
                                                                 ),
                                                 );

    //PHONE'S LOCATION TYPE
    $data['civicrm_phone']['location_type'] = array(
                                                    'title' => t('Location Type'),
                                                    'real field' => 'location_type_id',
                                                    'help' => t('The Location of the Phone for the Contact'),
                                                    'field' => array(
                                                                     'handler' => 'civicrm_handler_field_location_type',
                                                                     'click sortable' => TRUE,
                                                                     ),

                                                    'argument' => array(
                                                                        'handler' => 'views_handler_argument',
                                                                        ),

                                                    'filter' => array(
                                                                      'handler' => 'civicrm_handler_filter_location_type',
                                                                      ),

                                                    'sort' => array(
                                                                    'handler' => 'views_handler_sort',
                                                                    ),
                                                    );

    //FULL PHONE NUMBER
    $data['civicrm_phone']['phone'] = array(
                                            'title' => t('Phone Number'),
                                            'help' => t('Full Phone Number'),
                                            'field' => array(
                                                             'handler' => 'views_handler_field',
                                                             'click sortable' => TRUE,
                                                             ),

                                            'argument' => array(
                                                                'handler' => 'views_handler_argument',
                                                                ),

                                            'filter' => array(
                                                              'handler' => 'views_handler_filter_string',
                                                              ),

                                            'sort' => array(
                                                            'handler' => 'views_handler_sort',
                                                            ),
                                            );

    //TODO: Mobile Provider

    //PHONE TYPE  , Phone, Mobile, Fax or Pager
    $data['civicrm_phone']['phone_type'] = array(
                                                 'title' => t('Phone Type'),
                                                 'help' => t('The Type, either Phone, Mobile, Fax or Pager'),
                                                 'field' => array(
                                                                  'handler' => 'views_handler_field',
                                                                  'click sortable' => TRUE,
                                                                  ),

                                                 'argument' => array(
                                                                     'handler' => 'views_handler_argument',
                                                                     ),

                        
                                                 'filter' => array(
                                                                   'handler' => 'views_handler_filter_string',
                                                                   ),

                                                 'sort' => array(
                                                                 'handler' => 'views_handler_sort',
                                                                 ),
                                                 );

  


    //----------------------------------------------------------------
    // CIVICRM Events Be next bro! It's A BASE TABLE
    //----------------------------------------------------------------

    $data['civicrm_event']['table']['group']  = t('CiviCRM Events');

    $data['civicrm_event']['table']['base'] = array(
                                                    'field' => 'id', // Governs the whole mozilla
                                                    'title' => t('CiviCRM Events'),
                                                    'help' => t("View displays CiviCRM Events"),
                                                    );

    //TABLE JOINS FOR CIVICRM EVENTS GO HERE!
  
    $data['civicrm_event']['table']['join'] = array(
                                                    // Directly links to contact table.
                                                    'civicrm_participant' => array(
                                                                                   'left_field' => 'event_id',
                                                                                   'field' => 'id',
                                                                                   ),
                                                    );

    //CiviCRM Events - FIELDS


    //Numeric Event ID
    $data['civicrm_event']['id'] = array(
                                         'title' => t('Event ID'),
                                         'help' => t('The numeric ID of the Event'),
                                         'field' => array(
                                                          'handler' => 'views_handler_field_numeric',
                                                          'click sortable' => TRUE,
                                                          ),

                                         'argument' => array(
                                                             'handler' => 'views_handler_argument_numeric',
                                                             'numeric' => TRUE,
                                                             ),

                                         'filter' => array(
                                                           'handler' => 'views_handler_filter_numeric',
                                                           ),

                                         'sort' => array(
                                                         'handler' => 'views_handler_sort',
                                                         ),
                                         );

    //Event Type
    $data['civicrm_event']['event_type'] = array(
                                                 'title' => t('Event Type'),
                                                 'real field' => 'event_type_id',
                                                 'help' => t('The Event Type'),
                                                 'field' => array(
                                                                  'handler' => 'civicrm_handler_field_event_type',
                                                                  'click sortable' => TRUE,
                                                                  ),

                                                 'argument' => array(
                                                                     'handler' => 'views_handler_argument',
                                                                     ),

                                                 'filter' => array(
                                                                   'handler' => 'civicrm_handler_filter_event_type',
                                                                   ),

                                                 'sort' => array(
                                                                 'handler' => 'views_handler_sort',
                                                                 ),
                                                 );

    //BOOLEAN : IS EVENT PUBLIC
    $data['civicrm_event']['is_public'] = array(
                                                'title' => t('Is Public'),
                                                'help' => t('Has this event been made public?'),
                                                'field' => array(
                                                                 'handler' => 'views_handler_field_boolean',
                                                                 'click sortable' => TRUE,
                                                                 ),

                                                'argument' => array(
                                                                    'handler' => 'views_handler_argument',
                                                                    ),

                                                'filter' => array(
                                                                  'handler' => 'views_handler_filter_boolean_operator',
                                                                  ),

                                                'sort' => array(
                                                                'handler' => 'views_handler_sort',
                                                                ),
                                                );
  
    //BOOLEAN : IS EVENT ACTIVE
    $data['civicrm_event']['is_active'] = array(
                                                'title' => t('Is Active'),
                                                'help' => t('Has this event been made active?'),
                                                'field' => array(
                                                                 'handler' => 'views_handler_field_boolean',
                                                                 'click sortable' => TRUE,
                                                                 ),

                                                'argument' => array(
                                                                    'handler' => 'views_handler_argument',
                                                                    ),

                                                'filter' => array(
                                                                  'handler' => 'views_handler_filter_boolean_operator',
                                                                  ),

                                                'sort' => array(
                                                                'handler' => 'views_handler_sort',
                                                                ),
                                                );

    //EVENT TITLE
    $data['civicrm_event']['title'] = array(
                                            'title' => t('Title'),
                                            'help' => t('The Event\'s Title'),
                                            'field' => array(
                                                             'handler' => 'civicrm_handler_field_event_link',
                                                             'click sortable' => TRUE,
                                                             ),

                                            'argument' => array(
                                                                'handler' => 'views_handler_argument',
                                                                ),

                                            'filter' => array(
                                                              'handler' => 'views_handler_filter_string',
                                                              ),

                                            'sort' => array(
                                                            'handler' => 'views_handler_sort',
                                                            ),
                                            );

    // EVENT SUMMARY
    $data['civicrm_event']['summary'] = array(
                                              'title' => t('Summary'),
                                              'help' => t('The Event\'s Summary'),
                                              'field' => array(
                                                               'handler' => 'views_handler_field',
                                                               'click sortable' => TRUE,
                                                               ),
                                              );
  
    // EVENT DESCRIPTION
    $data['civicrm_event']['description'] = array(
                                                  'title' => t('Description'),
                                                  'help' => t('The Event\'s Full Description'),
                                                  'field' => array(
                                                                   'handler' => 'views_handler_field',
                                                                   'click sortable' => TRUE,
                                                                   ),
                                                  );
  
    //EVENT START DATE
    $data['civicrm_event']['start_date'] = array(
                                                 'title' => t('Start Date'),
                                                 'help' => t('The Event\'s Start Date'),
                                                 'field' => array(
                                                                  'handler' => 'civicrm_handler_field_datetime',
                                                                  'click sortable' => TRUE,
                                                                  ),

                                                 'argument' => array(
                                                                     'handler' => 'views_handler_argument_date',
                                                                     ),

                                                 'filter' => array(
                                                                   'handler' => 'civicrm_handler_filter_datetime',
                                                                   ),

                                                 'sort' => array(
                                                                 'handler' => 'views_handler_sort_date',
                                                                 ),
                                                 );
  
    //EVENT END DATE
    $data['civicrm_event']['end_date'] = array(
                                               'title' => t('End Date'),
                                               'help' => t('The Event\'s End Date'),
                                               'field' => array(
                                                                'handler' => 'civicrm_handler_field_datetime',
                                                                'click sortable' => TRUE,
                                                                ),

                                               'argument' => array(
                                                                   'handler' => 'views_handler_argument_date',
                                                                   ),

                                               'filter' => array(
                                                                 'handler' => 'civicrm_handler_filter_datetime',
                                                                 ),

                                               'sort' => array(
                                                               'handler' => 'views_handler_sort_date',
                                                               ),
                                               );
  
    //BOOLEAN : IS ONLINE REGISTRATION ENABLED
    $data['civicrm_event']['is_online_registration'] = array(
                                                             'title' => t('Is Online Registration On'),
                                                             'help' => t('Is Online Registration enabled for the event?'),
                                                             'field' => array(
                                                                              'handler' => 'views_handler_field_boolean',
                                                                              'click sortable' => TRUE,
                                                                              ),

                                                             'argument' => array(
                                                                                 'handler' => 'views_handler_argument',
                                                                                 ),

                                                             'filter' => array(
                                                                               'handler' => 'views_handler_filter_boolean_operator',
                                                                               ),

                                                             'sort' => array(
                                                                             'handler' => 'views_handler_sort',
                                                                             ),
                                                             );
  
    //EVENT ONLINE REGISTRATION START DATE
    $data['civicrm_event']['registration_start_date'] = array(
                                                              'title' => t('Registration Start Date'),
                                                              'help' => t('The Event\'s Online Registration Start Date'),
                                                              'field' => array(
                                                                               'handler' => 'civicrm_handler_field_datetime',
                                                                               'click sortable' => TRUE,
                                                                               ),

                                                              'argument' => array(
                                                                                  'handler' => 'views_handler_argument_date',
                                                                                  ),

                                                              'filter' => array(
                                                                                'handler' => 'civicrm_handler_filter_datetime',
                                                                                ),

                                                              'sort' => array(
                                                                              'handler' => 'views_handler_sort_date',
                                                                              ),
                                                              );
  
    //EVENT ONLINE REGISTRATION END DATE
    $data['civicrm_event']['registration_end_date'] = array(
                                                            'title' => t('Registration End Date'),
                                                            'help' => t('The Event\'s Online Registration End Date'),
                                                            'field' => array(
                                                                             'handler' => 'civicrm_handler_field_datetime',
                                                                             'click sortable' => TRUE,
                                                                             ),

                                                            'argument' => array(
                                                                                'handler' => 'views_handler_argument_date',
                                                                                ),

                                                            'filter' => array(
                                                                              'handler' => 'civicrm_handler_filter_datetime',
                                                                              ),

                                                            'sort' => array(
                                                                            'handler' => 'views_handler_sort_date',
                                                                            ),
                                                            );
  

  
    //TODO: CIVICRM_EVENT_PAGE TO BE LINKED IN TO THIS MOFO

    // CHILLING OUT AT THE CIVICRM_LOC_BLOCK TABLE

    $data['civicrm_loc_block']['table']['group']  = t('CiviCRM Locations');

    // Explain how this table joins to others.
    $data['civicrm_loc_block']['table']['join'] = array(
                                                        // Directly links to event table
                                                        'civicrm_event' => array(
                                                                                 'left_field' => 'loc_block_id',
                                                                                 'field' => 'id',
                                                                                 ),
                                                        );
                                                    
    //THE NAME OF THE LOCATION
    $data['civicrm_loc_block']['name'] = array(
                                               'title' => t('Name'),
                                               'help' => t('The Name of the Event Location'),
                                               'field' => array(
                                                                'handler' => 'views_handler_field',
                                                                'click sortable' => TRUE,
                                                                ),

                                               'argument' => array(
                                                                   'handler' => 'views_handler_argument',
                                                                   ),

                                               'filter' => array(
                                                                 'handler' => 'views_handler_filter_string',
                                                                 ),

                                               'sort' => array(
                                                               'handler' => 'views_handler_sort',
                                                               ),
                                               );
  
    //CIVICRM_EVENT_PAGE TABLE

    $data['civicrm_event_page']['table']['group']  = t('CiviCRM Event Pages');

    // Explain how this table joins to others.
    $data['civicrm_event_page']['table']['join'] = array(
                                                         // Directly links to event table
                                                         'civicrm_event' => array(
                                                                                  'left_field' => 'id',
                                                                                  'field' => 'event_id',
                                                                                  ),
                                                         );
                                                    
    //Event Type
    $data['civicrm_event_page']['price_set'] = array(
                                                     'title' => t('Event Prices'),
                                                     'help' => t('The Prices available for this event'),
                                                     'field' => array(
                                                                      'handler' => 'civicrm_handler_field_event_price_set',
                                                                      'click sortable' => TRUE,
                                                                      ),
                                                     );
    //----------------------------------------------------------------
    // CIVICRM Participants are here, base tabling it up.
    //----------------------------------------------------------------

    $data['civicrm_participant']['table']['group']  = t('CiviCRM Participants');

    $data['civicrm_participant']['table']['base'] = array(
                                                          'field' => 'id', // Governs the whole mozilla
                                                          'title' => t('CiviCRM Participants'),
                                                          'help' => t("View displays CiviCRM Participants, often in conjunction with Event Filters"),
                                                          );

    //TABLE JOINS FOR CIVICRM PARTICIPANTS GO HERE!



    //CiviCRM Participants - FIELDS
  
    //Numeric Event ID
    $data['civicrm_participant']['id'] = array(
                                               'title' => t('Participant ID'),
                                               'help' => t('The numeric ID of the Participant'),
                                               'field' => array(
                                                                'handler' => 'views_handler_field_numeric',
                                                                'click sortable' => TRUE,
                                                                ),

                                               'argument' => array(
                                                                   'handler' => 'views_handler_argument_numeric',
                                                                   'numeric' => TRUE,
                                                                   ),

                                               'filter' => array(
                                                                 'handler' => 'views_handler_filter_numeric',
                                                                 ),

                                               'sort' => array(
                                                               'handler' => 'views_handler_sort',
                                                               ),
                                               );
  
    //Numeric Event ID
    $data['civicrm_participant']['contact_id'] = array(
                                                       'title' => t('Participant\'s Contact ID'),
                                                       'help' => t('The numeric ID of the Contact of the Participant'),
                                                       'field' => array(
                                                                        'handler' => 'views_handler_field_numeric',
                                                                        'click sortable' => TRUE,
                                                                        ),

                                                       'argument' => array(
                                                                           'handler' => 'views_handler_argument_numeric',
                                                                           'numeric' => TRUE,
                                                                           ),

                                                       'filter' => array(
                                                                         'handler' => 'views_handler_filter_numeric',
                                                                         ),

                                                       'sort' => array(
                                                                       'handler' => 'views_handler_sort',
                                                                       ),
                                                       );
  
    //Participant Status
    $data['civicrm_participant']['status'] = array(
                                                   'title' => t('Participant Status'),
                                                   'real field' => 'status_id',
                                                   'help' => t('The Status of the Participant\'s Booking and Attendance'),
                                                   'field' => array(
                                                                    'handler' => 'civicrm_handler_field_participant_status',
                                                                    'click sortable' => TRUE,
                                                                    ),

                                                   'argument' => array(
                                                                       'handler' => 'views_handler_argument',
                                                                       ),

                                                   'filter' => array(
                                                                     'handler' => 'civicrm_handler_filter_participant_status',
                                                                     ),

                                                   'sort' => array(
                                                                   'handler' => 'views_handler_sort',
                                                                   ),
                                                   );
  
    //Participant Role
    $data['civicrm_participant']['role'] = array(
                                                 'title' => t('Participant Role'),
                                                 'real field' => 'role_id',
                                                 'help' => t('The Role the Participant is registered for.'),
                                                 'field' => array(
                                                                  'handler' => 'civicrm_handler_field_participant_role',
                                                                  'click sortable' => TRUE,
                                                                  ),

                                                 'argument' => array(
                                                                     'handler' => 'views_handler_argument',
                                                                     ),

                                                 'filter' => array(
                                                                   'handler' => 'civicrm_handler_filter_participant_role',
                                                                   ),

                                                 'sort' => array(
                                                                 'handler' => 'views_handler_sort',
                                                                 ),
                                                 );
  
    //Source
    $data['civicrm_participant']['source'] = array(
                                                   'title' => t('Source'),
                                                   'help' => t('Where did this registration come from?'),
                                                   'field' => array(
                                                                    'handler' => 'views_handler_field',
                                                                    'click sortable' => TRUE,
                                                                    ),

                                                   'argument' => array(
                                                                       'handler' => 'views_handler_argument',
                                                                       ),

                                                   'filter' => array(
                                                                     'handler' => 'views_handler_filter_string',
                                                                     ),

                                                   'sort' => array(
                                                                   'handler' => 'views_handler_sort',
                                                                   ),
                                                   );
  
    //Participant Registration Date
    $data['civicrm_participant']['register_date'] = array(
                                                          'title' => t('Register Date'),
                                                          'help' => t('The Date the participant Registered for the Event'),
                                                          'field' => array(
                                                                           'handler' => 'civicrm_handler_field_datetime',
                                                                           'click sortable' => TRUE,
                                                                           ),

                                                          'argument' => array(
                                                                              'handler' => 'views_handler_argument_date',
                                                                              ),

                                                          'filter' => array(
                                                                            'handler' => 'civicrm_handler_filter_datetime',
                                                                            ),

                                                          'sort' => array(
                                                                          'handler' => 'views_handler_sort_date',
                                                                          ),
                                                          );
  
    //BOOLEAN : IS SET TO PAY LATER
    $data['civicrm_participant']['is_pay_later'] = array(
                                                         'title' => t('Is Pay Later'),
                                                         'help' => t('Opted to Pay Later after Registration, and is still pending payment.'),
                                                         'field' => array(
                                                                          'handler' => 'views_handler_field_boolean',
                                                                          'click sortable' => TRUE,
                                                                          ),

                                                         'argument' => array(
                                                                             'handler' => 'views_handler_argument',
                                                                             ),

                                                         'filter' => array(
                                                                           'handler' => 'views_handler_filter_boolean_operator',
                                                                           ),

                                                         'sort' => array(
                                                                         'handler' => 'views_handler_sort',
                                                                         ),
                                                         );

    // Registered by ID - Participant ID of the person who made the registration
    $data['civicrm_participant']['registered_by_id'] = array(
                                                             'title' => t('Registered by ID'),
                                                             'help'  => t('The participant who registered this participant.'),
                                                             'relationship' => array(
                                                                                     'base' => 'civicrm_participant',
                                                                                     'field' => 'id',
                                                                                     'handler' => 'views_handler_relationship',
                                                                                     'label' => t('CiviCRM Source Record Contact'),
                                                                                     ),
                                                             );

    //Fee Level
    $data['civicrm_participant']['fee_level'] = array(
                                                      'title' => t('Fee Level'),
                                                      'help' => t('The Fee Level the Participant is registered for.'),
                                                      'field' => array(
                                                                       'handler' => 'views_handler_field',
                                                                       'click sortable' => TRUE,
                                                                       ),

                                                      'argument' => array(
                                                                          'handler' => 'views_handler_argument',
                                                                          ),

                                                      'filter' => array(
                                                                        'handler' => 'views_handler_filter_string',
                                                                        ),

                                                      'sort' => array(
                                                                      'handler' => 'views_handler_sort',
                                                                      ),
                                                      );

    // Fee Amount , the amount the Participant has paid or will have to pay.
    $data['civicrm_participant']['fee_amount'] = array(
                                                       'title' => t('Fee Amount'),
                                                       'help' => t('The amount the Participant has paid or will have to pay.'),
                                                       'field' => array(
                                                                        'handler' => 'views_handler_field_numeric',
                                                                        'click sortable' => TRUE,
                                                                        ),

                                                       'argument' => array(
                                                                           'handler' => 'views_handler_argument_numeric',
                                                                           'numeric' => TRUE,
                                                                           ),

                                                       'filter' => array(
                                                                         'handler' => 'views_handler_filter_numeric',
                                                                         ),

                                                       'sort' => array(
                                                                       'handler' => 'views_handler_sort',
                                                                       ),
                                                       );
  
    // HOP SCOTCHING OUR WAY THROUGH THE CIVICRM_PARTICIPANT_PAYMENT

    // Explain how this table joins to others.
    $data['civicrm_participant_payment']['table']['join'] = array(
                                                                  // Directly links to event table
                                                                  'civicrm_participant' => array(
                                                                                                 'left_field' => 'id',
                                                                                                 'field' => 'participant_id',
                                                                                                 ),
                                                                  );
                                                    

                                                    
  
    //----------------------------------------------------------------
    // CIVICRM Contributions are here with all the bling, base tabling it up.
    //----------------------------------------------------------------

    $data['civicrm_contribution']['table']['group']  = t('CiviCRM Contributions');

    $data['civicrm_contribution']['table']['base'] = array(
                                                           'field' => 'id', // Governs the whole mozilla
                                                           'title' => t('CiviCRM Contributions'),
                                                           'help' => t("View displays CiviCRM Contributions, with relation to contacts."),
                                                           );

    //TABLE JOINS FOR CIVICRM CONTRIBUTIONS GO HERE!

    $data['civicrm_contribution']['table']['join']['civicrm_participant'] = array(
                                                                                  'left_table' => 'civicrm_participant_payment',
                                                                                  'left_field' => 'contribution_id',
                                                                                  'field' => 'id',
                                                                                  );

    //CiviCRM Contributions - FIELDS
  
    //Numeric Contribution ID
    $data['civicrm_contribution']['id'] = array(
                                                'title' => t('Contribution ID'),
                                                'help' => t('The numeric ID of the Contribution'),
                                                'field' => array(
                                                                 'handler' => 'views_handler_field_numeric',
                                                                 'click sortable' => TRUE,
                                                                 ),

                                                'argument' => array(
                                                                    'handler' => 'views_handler_argument_numeric',
                                                                    'numeric' => TRUE,
                                                                    ),

                                                'filter' => array(
                                                                  'handler' => 'views_handler_filter_numeric',
                                                                  ),

                                                'sort' => array(
                                                                'handler' => 'views_handler_sort',
                                                                ),
                                                );
  
    //Contribution Type
    $data['civicrm_contribution']['contribution_type'] = array(
                                                               'title' => t('Contribution Type'),
                                                               'real field' => 'contribution_type_id',
                                                               'help' => t('The Type of Contribution made, ie Member Dues, Event Fees etc'),
                                                               'field' => array(
                                                                                'handler' => 'civicrm_handler_field_contribution_type',
                                                                                'click sortable' => TRUE,
                                                                                ),

                                                               'argument' => array(
                                                                                   'handler' => 'views_handler_argument',
                                                                                   ),

                                                               'filter' => array(
                                                                                 'handler' => 'civicrm_handler_filter_contribution_type',
                                                                                 ),

                                                               'sort' => array(
                                                                               'handler' => 'views_handler_sort',
                                                                               ),
                                                               );

    //Contribution Page
    $data['civicrm_contribution']['contribution_page'] = array(
                                                               'title' => t('Contribution Page'),
                                                               'real field' => 'contribution_page_id',
                                                               'help' => t('The Page this Contribution came from'),
                                                               'field' => array(
                                                                                'handler' => 'civicrm_handler_field_contribution_page',
                                                                                'click sortable' => TRUE,
                                                                                ),

                                                               'argument' => array(
                                                                                   'handler' => 'views_handler_argument',
                                                                                   ),

                                                               'filter' => array(
                                                                                 'handler' => 'civicrm_handler_filter_contribution_page',
                                                                                 ),

                                                               'sort' => array(
                                                                               'handler' => 'views_handler_sort',
                                                                               ),
                                                               );
  
    //Payment Instrument Used
    $data['civicrm_contribution']['payment_instrument'] = array(
                                                                'title' => t('Payment Instrument'),
                                                                'real field' => 'payment_instrument_id',
                                                                'help' => t('The Payment Instrument Used for the Contribution'),
                                                                'field' => array(
                                                                                 'handler' => 'civicrm_handler_field_payment_instrument',
                                                                                 'click sortable' => TRUE,
                                                                                 ),

                                                                'argument' => array(
                                                                                    'handler' => 'views_handler_argument',
                                                                                    ),

                                                                'filter' => array(
                                                                                  'handler' => 'civicrm_handler_filter_payment_instrument',
                                                                                  ),

                                                                'sort' => array(
                                                                                'handler' => 'views_handler_sort',
                                                                                ),
                                                                );


  
    //Contribution Receive Date
    $data['civicrm_contribution']['receive_date'] = array(
                                                          'title' => t('Receive Date'),
                                                          'help' => t('The Date the Contribution was received / paid'),
                                                          'field' => array(
                                                                           'handler' => 'civicrm_handler_field_datetime',
                                                                           'click sortable' => TRUE,
                                                                           ),

                                                          'argument' => array(
                                                                              'handler' => 'views_handler_argument_date',
                                                                              ),

                                                          'filter' => array(
                                                                            'handler' => 'civicrm_handler_filter_datetime',
                                                                            ),

                                                          'sort' => array(
                                                                          'handler' => 'views_handler_sort_date',
                                                                          ),
                                                          );
  
    //Non Deductible Amount of Contribution
    $data['civicrm_contribution']['non_deductible_amount'] = array(
                                                                   'title' => t('Non Deductible Amount'),
                                                                   'help' => t('The Non Deductible Part of the Contribution'),
                                                                   'field' => array(
                                                                                    'handler' => 'views_handler_field_numeric',
                                                                                    'click sortable' => TRUE,
                                                                                    ),

                                                                   'argument' => array(
                                                                                       'handler' => 'views_handler_argument_numeric',
                                                                                       'numeric' => TRUE,
                                                                                       ),

                                                                   'filter' => array(
                                                                                     'handler' => 'views_handler_filter_numeric',
                                                                                     ),

                                                                   'sort' => array(
                                                                                   'handler' => 'views_handler_sort',
                                                                                   ),
                                                                   );
  
    $data['civicrm_contribution']['total_amount'] = array(
                                                          'title' => t('Total Amount'),
                                                          'help' => t('The Total Contribution made by the Contact'),
                                                          'field' => array(
                                                                           'handler' => 'views_handler_field_numeric',
                                                                           'click sortable' => TRUE,
                                                                           ),

                                                          'argument' => array(
                                                                              'handler' => 'views_handler_argument_numeric',
                                                                              'numeric' => TRUE,
                                                                              ),

                                                          'filter' => array(
                                                                            'handler' => 'views_handler_filter_numeric',
                                                                            ),

                                                          'sort' => array(
                                                                          'handler' => 'views_handler_sort',
                                                                          ),
                                                          );
  
    $data['civicrm_contribution']['fee_amount'] = array(
                                                        'title' => t('Fee Amount'),
                                                        'help' => t('The fee charged by the Payment Processor for the transaction.'),
                                                        'field' => array(
                                                                         'handler' => 'views_handler_field_numeric',
                                                                         'click sortable' => TRUE,
                                                                         ),

                                                        'argument' => array(
                                                                            'handler' => 'views_handler_argument_numeric',
                                                                            'numeric' => TRUE,
                                                                            ),

                                                        'filter' => array(
                                                                          'handler' => 'views_handler_filter_numeric',
                                                                          ),

                                                        'sort' => array(
                                                                        'handler' => 'views_handler_sort',
                                                                        ),
                                                        );
  
    $data['civicrm_contribution']['net_amount'] = array(
                                                        'title' => t('Net Amount'),
                                                        'help' => t('The Full Contribution minus Payment Processor Fees.'),
                                                        'field' => array(
                                                                         'handler' => 'views_handler_field_numeric',
                                                                         'click sortable' => TRUE,
                                                                         ),

                                                        'argument' => array(
                                                                            'handler' => 'views_handler_argument_numeric',
                                                                            'numeric' => TRUE,
                                                                            ),

                                                        'filter' => array(
                                                                          'handler' => 'views_handler_filter_numeric',
                                                                          ),

                                                        'sort' => array(
                                                                        'handler' => 'views_handler_sort',
                                                                        ),
                                                        );
  
    //Transaction ID
    $data['civicrm_contribution']['trxn_id'] = array(
                                                     'title' => t('Transaction ID'),
                                                     'help' => t('Unique Transaction ID from Payment Processor / Bank etc'),
                                                     'field' => array(
                                                                      'handler' => 'views_handler_field',
                                                                      'click sortable' => TRUE,
                                                                      ),

                                                     'argument' => array(
                                                                         'handler' => 'views_handler_argument',
                                                                         ),

                                                     'filter' => array(
                                                                       'handler' => 'views_handler_filter_string',
                                                                       ),

                                                     'sort' => array(
                                                                     'handler' => 'views_handler_sort',
                                                                     ),
                                                     );
  
    //Invoice ID
    $data['civicrm_contribution']['invoice_id'] = array(
                                                        'title' => t('Invoice ID'),
                                                        'help' => t('Unique Invoice ID from system'),
                                                        'field' => array(
                                                                         'handler' => 'views_handler_field',
                                                                         'click sortable' => TRUE,
                                                                         ),

                                                        'argument' => array(
                                                                            'handler' => 'views_handler_argument',
                                                                            ),

                                                        'filter' => array(
                                                                          'handler' => 'views_handler_filter_string',
                                                                          ),

                                                        'sort' => array(
                                                                        'handler' => 'views_handler_sort',
                                                                        ),
                                                        );
  
    //Currency
    $data['civicrm_contribution']['currency'] = array(
                                                      'title' => t('Currency'),
                                                      'help' => t('Currency used for the Contribution'),
                                                      'field' => array(
                                                                       'handler' => 'views_handler_field',
                                                                       'click sortable' => TRUE,
                                                                       ),

                                                      'argument' => array(
                                                                          'handler' => 'views_handler_argument',
                                                                          ),

                                                      'filter' => array(
                                                                        'handler' => 'views_handler_filter_string',
                                                                        ),

                                                      'sort' => array(
                                                                      'handler' => 'views_handler_sort',
                                                                      ),
                                                      );

    //Contribution Cancellation Date
    $data['civicrm_contribution']['cancel_date'] = array(
                                                         'title' => t('Cancellation Date'),
                                                         'help' => t('The Date the Contribution was cancelled (if it was)'),
                                                         'field' => array(
                                                                          'handler' => 'civicrm_handler_field_datetime',
                                                                          'click sortable' => TRUE,
                                                                          ),

                                                         'argument' => array(
                                                                             'handler' => 'views_handler_argument_date',
                                                                             ),

                                                         'filter' => array(
                                                                           'handler' => 'civicrm_handler_filter_datetime',
                                                                           ),

                                                         'sort' => array(
                                                                         'handler' => 'views_handler_sort_date',
                                                                         ),
                                                         );
  
    //Cancellation Reason
    $data['civicrm_contribution']['cancel_reason'] = array(
                                                           'title' => t('Cancellation Reason'),
                                                           'help' => t('The Reason for the Contribution Cancellation'),
                                                           'field' => array(
                                                                            'handler' => 'views_handler_field',
                                                                            'click sortable' => TRUE,
                                                                            ),

                                                           'argument' => array(
                                                                               'handler' => 'views_handler_argument',
                                                                               ),

                                                           'filter' => array(
                                                                             'handler' => 'views_handler_filter_string',
                                                                             ),

                                                           'sort' => array(
                                                                           'handler' => 'views_handler_sort',
                                                                           ),
                                                           );
  
    //Contribution Receipt Date
    $data['civicrm_contribution']['receipt_date'] = array(
                                                          'title' => t('Receipt Date'),
                                                          'help' => t('The Date a receipt for the contribution was sent.'),
                                                          'field' => array(
                                                                           'handler' => 'civicrm_handler_field_datetime',
                                                                           'click sortable' => TRUE,
                                                                           ),

                                                          'argument' => array(
                                                                              'handler' => 'views_handler_argument_date',
                                                                              ),

                                                          'filter' => array(
                                                                            'handler' => 'civicrm_handler_filter_datetime',
                                                                            ),

                                                          'sort' => array(
                                                                          'handler' => 'views_handler_sort_date',
                                                                          ),
                                                          );

    //Contribution Thank You Date
    $data['civicrm_contribution']['thankyou_date'] = array(
                                                           'title' => t('Thank You Date'),
                                                           'help' => t('The Date a thank you to the donor for the contribution was sent.'),
                                                           'field' => array(
                                                                            'handler' => 'civicrm_handler_field_datetime',
                                                                            'click sortable' => TRUE,
                                                                            ),

                                                           'argument' => array(
                                                                               'handler' => 'views_handler_argument_date',
                                                                               ),

                                                           'filter' => array(
                                                                             'handler' => 'civicrm_handler_filter_datetime',
                                                                             ),

                                                           'sort' => array(
                                                                           'handler' => 'views_handler_sort_date',
                                                                           ),
                                                           );
  
    //Contribution Source
    $data['civicrm_contribution']['source'] = array(
                                                    'title' => t('Source'),
                                                    'help' => t('Where the Contribution came through.'),
                                                    'field' => array(
                                                                     'handler' => 'views_handler_field',
                                                                     'click sortable' => TRUE,
                                                                     ),

                                                    'argument' => array(
                                                                        'handler' => 'views_handler_argument',
                                                                        ),

                                                    'filter' => array(
                                                                      'handler' => 'views_handler_filter_string',
                                                                      ),

                                                    'sort' => array(
                                                                    'handler' => 'views_handler_sort',
                                                                    ),
                                                    );
  
    //Contribution Status
    $data['civicrm_contribution']['contribution_status'] = array(
                                                                 'title' => t('Contribution Status'),
                                                                 'real field' => 'contribution_status_id',
                                                                 'help' => t('The Status of this Contribution'),
                                                                 'field' => array(
                                                                                  'handler' => 'civicrm_handler_field_contribution_status',
                                                                                  'click sortable' => TRUE,
                                                                                  ),

                                                                 'argument' => array(
                                                                                     'handler' => 'views_handler_argument',
                                                                                     ),

                                                                 'filter' => array(
                                                                                   'handler' => 'civicrm_handler_filter_contribution_status',
                                                                                   ),

                                                                 'sort' => array(
                                                                                 'handler' => 'views_handler_sort',
                                                                                 ),
                                                                 );
  
    //TODO: CONTRIBUTION RECURRING STUFF + HONOR_CONTACT_ID + HONOR_TYPE_ID

    //BOOLEAN : IS A TEST CONTRIBUTION
    $data['civicrm_contribution']['is_test'] = array(
                                                     'title' => t('Is Test'),
                                                     'help' => t('Is the contribution a test entry?'),
                                                     'field' => array(
                                                                      'handler' => 'views_handler_field_boolean',
                                                                      'click sortable' => TRUE,
                                                                      ),

                                                     'argument' => array(
                                                                         'handler' => 'views_handler_argument',
                                                                         ),

                                                     'filter' => array(
                                                                       'handler' => 'views_handler_filter_boolean_operator',
                                                                       ),

                                                     'sort' => array(
                                                                     'handler' => 'views_handler_sort',
                                                                     ),
                                                     );

    //BOOLEAN : IS SET TO PAY LATER
    $data['civicrm_contribution']['is_pay_later'] = array(
                                                          'title' => t('Is Pay Later'),
                                                          'help' => t('Opted to Pay Contribution Later, and is still pending payment.'),
                                                          'field' => array(
                                                                           'handler' => 'views_handler_field_boolean',
                                                                           'click sortable' => TRUE,
                                                                           ),

                                                          'argument' => array(
                                                                              'handler' => 'views_handler_argument',
                                                                              ),

                                                          'filter' => array(
                                                                            'handler' => 'views_handler_filter_boolean_operator',
                                                                            ),

                                                          'sort' => array(
                                                                          'handler' => 'views_handler_sort',
                                                                          ),
                                                          );

    // Registered by ID - Participant ID of the person who made the registration
    $data['civicrm_contribution']['registered_by_id'] = array(
                                                              'title' => t('Registered by ID'),
                                                              'help' => t('The numeric ID of the Participant who registered this Participant'),
                                                              'field' => array(
                                                                               'handler' => 'views_handler_field_numeric',
                                                                               'click sortable' => TRUE,
                                                                               ),

                                                              'argument' => array(
                                                                                  'handler' => 'views_handler_argument_numeric',
                                                                                  'numeric' => TRUE,
                                                                                  ),

                                                              'filter' => array(
                                                                                'handler' => 'views_handler_filter_numeric',
                                                                                ),

                                                              'sort' => array(
                                                                              'handler' => 'views_handler_sort',
                                                                              ),
                                                              );
  
  
  
    //----------------------------------------------------------------------------------------
    // CIVICRM Activities are here with all the activeness they can muster, base tabling it up.
    //----------------------------------------------------------------------------------------

    $data['civicrm_activity']['table']['group']  = t('CiviCRM Activities');

    $data['civicrm_activity']['table']['base'] = array(
                                                       'field' => 'id', // Governs the whole mozilla
                                                       'title' => t('CiviCRM Activities'),
                                                       'help' => t("View displays CiviCRM Activities"),
                                                       );

    //TABLE JOINS FOR CIVICRM ACTIVITIES GO HERE!


    //CiviCRM Activities - FIELDS
  
    //Numeric Activity ID
    $data['civicrm_activity']['id'] = array(
                                            'title' => t('Activity ID'),
                                            'help' => t('The numeric ID of the Activity Instance'),
                                            'field' => array(
                                                             'handler' => 'views_handler_field_numeric',
                                                             'click sortable' => TRUE,
                                                             ),

                                            'argument' => array(
                                                                'handler' => 'views_handler_argument_numeric',
                                                                'numeric' => TRUE,
                                                                ),

                                            'filter' => array(
                                                              'handler' => 'views_handler_filter_numeric',
                                                              ),

                                            'sort' => array(
                                                            'handler' => 'views_handler_sort',
                                                            ),
                                            );

    $data['civicrm_activity']['source_contact_id'] = array(
                                                           'title' => t('Source Contact'),
                                                           'help'  => t('The contact who scheduled this activity'),
                                                           'relationship' => array(
                                                                                   'base' => 'civicrm_contact',
                                                                                   'field' => 'id',
                                                                                   'handler' => 'views_handler_relationship',
                                                                                   'label' => t('CiviCRM Source Contact'),
                                                                                   ),
                                                           );

    $data['civicrm_activity']['source_record_id'] = array(
                                                          'title' => t('Source Record ID'),
                                                          'help'  => t('The contact who made this activity'),
                                                          'relationship' => array(
                                                                                  'base' => 'civicrm_contact',
                                                                                  'field' => 'id',
                                                                                  'handler' => 'views_handler_relationship',
                                                                                  'label' => t('CiviCRM Source Record Contact'),
                                                                                  ),
                                                          );

    //Activity Type
    $data['civicrm_activity']['activity_type'] = array(
                                                       'title' => t('Activity Type'),
                                                       'real field' => 'activity_type_id',
                                                       'help' => t('The Type of Activity'),
                                                       'field' => array(
                                                                        'handler' => 'civicrm_handler_field_activity_type',
                                                                        'click sortable' => TRUE,
                                                                        ),

                                                       'argument' => array(
                                                                           'handler' => 'views_handler_argument',
                                                                           ),

                                                       'filter' => array(
                                                                         'handler' => 'civicrm_handler_filter_activity_type',
                                                                         ),

                                                       'sort' => array(
                                                                       'handler' => 'views_handler_sort',
                                                                       ),
                                                       );

    //Activity Status
    $data['civicrm_activity']['status'] = array(
                                                'title' => t('Activity Status'),
                                                'real field' => 'status_id',
                                                'help' => t('The Status of this Activity'),
                                                'field' => array(
                                                                 'handler' => 'civicrm_handler_field_activity_status',
                                                                 'click sortable' => TRUE,
                                                                 ),

                                                'argument' => array(
                                                                    'handler' => 'views_handler_argument',
                                                                    ),

                                                'filter' => array(
                                                                  'handler' => 'civicrm_handler_filter_activity_status',
                                                                  ),

                                                'sort' => array(
                                                                'handler' => 'views_handler_sort',
                                                                ),
                                                );

    //Activity Subject
    $data['civicrm_activity']['subject'] = array(
                                                 'title' => t('Subject'),
                                                 'help' => t('A short description of the activity..'),
                                                 'field' => array(
                                                                  'handler' => 'views_handler_field',
                                                                  'click sortable' => TRUE,
                                                                  ),

                                                 'argument' => array(
                                                                     'handler' => 'views_handler_argument',
                                                                     ),

                                                 'filter' => array(
                                                                   'handler' => 'views_handler_filter_string',
                                                                   ),

                                                 'sort' => array(
                                                                 'handler' => 'views_handler_sort',
                                                                 ),
                                                 );

    //Scheduled Activity Date
    $data['civicrm_activity']['activity_date_time'] = array(
                                                            'title' => t('Scheduled Activity Date'),
                                                            'help' => t('The Date and Time the activity is scheduled to happen.'),
                                                            'field' => array(
                                                                             'handler' => 'civicrm_handler_field_datetime',
                                                                             'click sortable' => TRUE,
                                                                             ),

                                                            'argument' => array(
                                                                                'handler' => 'views_handler_argument_date',
                                                                                ),

                                                            'filter' => array(
                                                                              'handler' => 'civicrm_handler_filter_datetime',
                                                                              ),

                                                            'sort' => array(
                                                                            'handler' => 'views_handler_sort_date',
                                                                            ),
                                                            );
  
    //Due Activity Date
    $data['civicrm_activity']['due_date_time'] = array(
                                                       'title' => t('Activity Due Date'),
                                                       'help' => t('The Date and Time the activity is due to happen.'),
                                                       'field' => array(
                                                                        'handler' => 'civicrm_handler_field_datetime',
                                                                        'click sortable' => TRUE,
                                                                        ),

                                                       'argument' => array(
                                                                           'handler' => 'views_handler_argument_date',
                                                                           ),

                                                       'filter' => array(
                                                                         'handler' => 'civicrm_handler_filter_datetime',
                                                                         ),

                                                       'sort' => array(
                                                                       'handler' => 'views_handler_sort_date',
                                                                       ),
                                                       );
  
    //Activity Duration
    $data['civicrm_activity']['duration'] = array(
                                                  'title' => t('Duration (min)'),
                                                  'help' => t('The Activity\'s Duration in Minutes'),
                                                  'field' => array(
                                                                   'handler' => 'views_handler_field_numeric',
                                                                   'click sortable' => TRUE,
                                                                   ),

                                                  'argument' => array(
                                                                      'handler' => 'views_handler_argument_numeric',
                                                                      'numeric' => TRUE,
                                                                      ),

                                                  'filter' => array(
                                                                    'handler' => 'views_handler_filter_numeric',
                                                                    ),

                                                  'sort' => array(
                                                                  'handler' => 'views_handler_sort',
                                                                  ),
                                                  );
  
    //Activity Location
    $data['civicrm_activity']['location'] = array(
                                                  'title' => t('Location'),
                                                  'help' => t('Activity\'s Location in a text string.'),
                                                  'field' => array(
                                                                   'handler' => 'views_handler_field',
                                                                   'click sortable' => TRUE,
                                                                   ),

                                                  'argument' => array(
                                                                      'handler' => 'views_handler_argument',
                                                                      ),

                                                  'filter' => array(
                                                                    'handler' => 'views_handler_filter_string',
                                                                    ),

                                                  'sort' => array(
                                                                  'handler' => 'views_handler_sort',
                                                                  ),
                                                  );
  
    //Activity Details / Notes
    $data['civicrm_activity']['details'] = array(
                                                 'title' => t('Details'),
                                                 'help' => t('Details and Notes regarding the activity.'),
                                                 'field' => array(
                                                                  'handler' => 'views_handler_field',
                                                                  'click sortable' => TRUE,
                                                                  ),

                                                 'argument' => array(
                                                                     'handler' => 'views_handler_argument',
                                                                     ),

                                                 'filter' => array(
                                                                   'handler' => 'views_handler_filter_string',
                                                                   ),

                                                 'sort' => array(
                                                                 'handler' => 'views_handler_sort',
                                                                 ),
                                                 );
  
    //BOOLEAN : IS A TEST ACTIVITY
    $data['civicrm_activity']['is_test'] = array(
                                                 'title' => t('Is Test'),
                                                 'help' => t('Is the activity a test entry?'),
                                                 'field' => array(
                                                                  'handler' => 'views_handler_field_boolean',
                                                                  'click sortable' => TRUE,
                                                                  ),

                                                 'argument' => array(
                                                                     'handler' => 'views_handler_argument',
                                                                     ),

                                                 'filter' => array(
                                                                   'handler' => 'views_handler_filter_boolean_operator',
                                                                   ),

                                                 'sort' => array(
                                                                 'handler' => 'views_handler_sort',
                                                                 ),
                                                 );

    // CHILLING OUT AT THE CIVICRM_ACTIVTY_ASSIGNMENT TABLE

    $data['civicrm_activity_assignment']['table']['group']  = t('CiviCRM Activity Assignments');

    // Explain how this table joins to others.
    $data['civicrm_activity_assignment']['table']['join'] = array(
                                                                  // Directly links to event table
                                                                  'civicrm_activity' => array(
                                                                                              'left_field' => 'id',
                                                                                              'field' => 'activity_id',
                                                                                              ),
                                                                  );
  
    $data['civicrm_activity_assignment']['assignee_contact_id'] = array(
                                                                        'title' => t('Assignee Contact ID'),
                                                                        'help'  => t('The contact who is assigned the activity.'),
                                                                        'relationship' => array(
                                                                                                'base' => 'civicrm_contact',
                                                                                                'field' => 'id',
                                                                                                'handler' => 'views_handler_relationship',
                                                                                                'label' => t('CiviCRM Assignee Contact ID'),
                                                                                                ),
                                                                        );

    // CHILLING OUT AT THE CIVICRM_ACTIVTY_ASSIGNMENT TABLE

    $data['civicrm_activity_target']['table']['group']  = t('CiviCRM Activity Targets');

    // Explain how this table joins to others.
    $data['civicrm_activity_target']['table']['join'] = array(
                                                              // Directly links to event table
                                                              'civicrm_activity' => array(
                                                                                          'left_field' => 'id',
                                                                                          'field' => 'activity_id',
                                                                                          ),
                                                              );
  
    $data['civicrm_activity_target']['target_contact_id'] = array(
                                                                  'title' => t('Target Contact ID'),
                                                                  'help'  => t('The contact who is the activity\'s target.'),
                                                                  'relationship' => array(
                                                                                          'base' => 'civicrm_contact',
                                                                                          'field' => 'id',
                                                                                          'handler' => 'views_handler_relationship',
                                                                                          'label' => t('CiviCRM Target Contact ID'),
                                                                                          ),
                                                                  );

    //TEXT LINKS THAT AREN'T PART OF THE TABLE
    //Contact Text Link
    $data['civicrm_contact']['text_link'] = array(
                                                  'field' => array(
                                                                   'title' => t('Text Link'),
                                                                   'help' => t('Creates a Text Link for various contact actions.'),
                                                                   'handler' => 'civicrm_handler_field_link_contact',
                                                                   ),

                                                  );

    //Event Text Link
    $data['civicrm_event']['text_link'] = array(
                                                'field' => array(
                                                                 'title' => t('Text Link'),
                                                                 'help' => t('Creates a Text Link for various event actions.'),
                                                                 'handler' => 'civicrm_handler_field_link_event',
                                                                 ),

                                                );



    //Participant Text Link
    $data['civicrm_participant']['text_link'] = array(
                                                      'field' => array(
                                                                       'title' => t('Text Link'),
                                                                       'help' => t('Creates a Text Link for various participant actions.'),
                                                                       'handler' => 'civicrm_handler_field_link_participant',
                                                                       ),

                                                      );

    //Contribution Text Link
    $data['civicrm_contribution']['text_link'] = array(
                                                       'field' => array(
                                                                        'title' => t('Text Link'),
                                                                        'help' => t('Creates a Text Link for various contribution actions.'),
                                                                        'handler' => 'civicrm_handler_field_link_contribution',
                                                                        ),

                                                       );
  
 
    //----------------------------------------------------------------
    // CIVICRM Relationships are here with all the connections, base tabling it up.
    //----------------------------------------------------------------
  
  
    $data['civicrm_relationship']['table']['group']  = t('CiviCRM Relationships');
  
    $data['civicrm_relationship']['table']['base'] = array(
                                                           'field' => 'id', // Governs the whole mozilla
                                                           'title' => t('CiviCRM Relationships'),
                                                           'help' => t("View displays CiviCRM Relationships, with connection to contacts."),
                                                           );
  
    //TABLE JOINS FOR CIVICRM RELATIONSHIPS GO HERE!
  
    $data['civicrm_relationship']['table']['join'] = array(
                                                           // Directly links to contact table - link A
                                                           'civicrm_contact' => array(
                                                                                      'left_field' => 'id',
                                                                                      'field' => 'contact_id_a',
                                                                                      ),
                                                           // Directly links to contact table - link B also.
                                                           //		   'civicrm_contact' => array(
                                                           //									  'left_field' => 'id',
                                                           //									  'field' => 'contact_id_b',
                                                           //									  ),
                                                           );
  
    //CiviCRM Relationships - FIELDS
  
    //Numeric Relationship ID
    $data['civicrm_relationship']['id'] = array(
                                                'title' => t('Relationship ID'),
                                                'help' => t('The numeric ID of the Relationship'),
                                                'field' => array(
                                                                 'handler' => 'views_handler_field_numeric',
                                                                 'click sortable' => TRUE,
                                                                 ),
                                              
                                                'argument' => array(
                                                                    'handler' => 'views_handler_argument_numeric',
                                                                    'numeric' => TRUE,
                                                                    ),
                                              
                                                'filter' => array(
                                                                  'handler' => 'views_handler_filter_numeric',
                                                                  ),
                                              
                                                'sort' => array(
                                                                'handler' => 'views_handler_sort',
                                                                ),
                                                );
  
    //Relationship Type
    $data['civicrm_relationship']['relationship_type'] = array(
                                                               'title' => t('Relationship Type A-to-B'),
                                                               'real field' => 'relationship_type_id',
                                                               'help' => t('The Type of Relationship, i.e. Employee of, Household Head for...'),
                                                               'field' => array(
                                                                                'handler' => 'civicrm_handler_field_relationship_type',
                                                                                'click sortable' => TRUE,
                                                                                ),
                                                             
                                                               'argument' => array(
                                                                                   'handler' => 'views_handler_argument',
                                                                                   ),
                                                             
                                                               'filter' => array(
                                                                                 'handler' => 'civicrm_handler_filter_relationship_type',
                                                                                 ),
                                                             
                                                               'sort' => array(
                                                                               'handler' => 'views_handler_sort',
                                                                               ),
                                                               );
  
    $data['civicrm_relationship']['contact_id_a'] = array(
                                                          'title' => t('Contact ID A'),
                                                          'help' => t('The contact A'),
                                                          'relationship' => array(
                                                                                  'base' => 'civicrm_contact',
                                                                                  'field' => 'id',
                                                                                  'handler' => 'views_handler_relationship',
                                                                                  'label' => t('CiviCRM Contact A'),
                                                                                  ),
                                                          );
  
    $data['civicrm_relationship']['contact_id_b'] = array(
                                                          'title' => t('Contact ID B'),
                                                          'help' => t('The contact B'),
                                                          'relationship' => array(
                                                                                  'base' => 'civicrm_contact',
                                                                                  'field' => 'id',
                                                                                  'handler' => 'views_handler_relationship',
                                                                                  'label' => t('CiviCRM Contact B'),
                                                                                  ),
                                                          );
  
  
    $data['civicrm_relationship']['description'] = array(	
                                                         'title' => t('Description'),
                                                         'help' =>  t('Description of the relationship'),
                                                         'field' => array(
                                                                          'handler' => 'views_handler_field',
                                                                          'click sortable' => TRUE,
                                                                          ),
                                                       
                                                         'argument' => array(
                                                                             'handler' => 'views_handler_argument',
                                                                             ),
                                                       
                                                         'filter' => array(
                                                                           'handler' => 'views_handler_filter_string',
                                                                           ),
                                                       
                                                         'sort' => array(
                                                                         'handler' => 'views_handler_sort',
                                                                         ),
                                                            );
  
    //Relationship start date
    $data['civicrm_relationship']['start_date'] = array(
                                                        'title' => t('Start Date'),
                                                        'help' => t('The Relationship\'s Start Date'),
                                                        'field' => array(
                                                                         'handler' => 'civicrm_handler_field_datetime',
                                                                         'click sortable' => TRUE,
                                                                         ),
                                                      
                                                        'argument' => array(
                                                                            'handler' => 'views_handler_argument_date',
                                                                            ),
                                                      
                                                        'filter' => array(
                                                                          'handler' => 'civicrm_handler_filter_datetime',
                                                                          ),
                                                      
                                                        'sort' => array(
                                                                        'handler' => 'views_handler_sort_date',
                                                                        ),
                                                        );
  
    //Relationship end date
    $data['civicrm_relationship']['end_date'] = array(
                                                      'title' => t('End Date'),
                                                      'help' => t('The Relationship\'s End Date'),
                                                      'field' => array(
                                                                       'handler' => 'civicrm_handler_field_datetime',
                                                                       'click sortable' => TRUE,
                                                                       ),
                                                    
                                                      'argument' => array(
                                                                          'handler' => 'views_handler_argument_date',
                                                                          ),
                                                    
                                                      'filter' => array(
                                                                        'handler' => 'civicrm_handler_filter_datetime',
                                                                        ),
                                                    
                                                      'sort' => array(
                                                                      'handler' => 'views_handler_sort_date',
                                                                      ),
                                                      );
  
    //BOOLEAN : IS RELATIONSHIP ACTIVE
    $data['civicrm_relationship']['is_active'] = array(
                                                       'title' => t('Is Relationship Active'),
                                                       'help' => t('Is the Relationship active?'),
                                                       'field' => array(
                                                                        'handler' => 'views_handler_field_boolean',
                                                                        'click sortable' => TRUE,
                                                                        ),
                                                     
                                                       'argument' => array(
                                                                           'handler' => 'views_handler_argument',
                                                                           ),
                                                     
                                                       'filter' => array(
                                                                         'handler' => 'views_handler_filter_boolean_operator',
                                                                         ),
                                                     
                                                       'sort' => array(
                                                                       'handler' => 'views_handler_sort',
                                                                       ),
                                                       );
  
    //Activity Text Link
    $data['civicrm_activity']['text_link'] =
        array(
              'field' => array(
                               'title' => t('Text Link'),
                               'help' => t('Creates a Text Link for various activity actions.'),
                               'handler' => 'civicrm_handler_field_link_activity',
                               ),
              );

  
    //Relationship Text Link
    $data['civicrm_relationship']['text_link'] =
        array(
              'field' => array(
                               'title' => t('Text Link'),
                               'help' => t('Creates a Text Link for various relationship actions.'),
                               'handler' => 'civicrm_handler_field_link_relationship',
                               ),
              );  

    $query = "select id, extends, extends_entity_column_value, style from civicrm_custom_group where is_active = 1";

    $dao = CRM_Core_DAO::executeQuery( $query );
  

    while ( $dao->fetch( ) ) {
        // call getTree using $dao->id as groupID and $dao->extends as entityType

        $data = civicrm_views_custom_data_cache ($data, $dao->extends, $dao->id,  $dao->extends_entity_column_value, $dao->style );
    }



    return $data;
  }



// RETURNS A LINK TO A CIVICRM PATH
function civicrm_views_href( $text, $path, $query ) {
    civicrm_initialize( );
    require_once 'CRM/Utils/System.php';
    return CRM_Utils_System::href( $text, $path, $query );
}

// Creates new View fields from CiviCRM fields
function civicrm_views_add_fields( &$fields, &$data, &$skipFields, $tableName ) {
    foreach ( $fields as $name => $value ) {
        if ( CRM_Utils_Array::value( $name, $skipFields ) ||
             substr( $value['where'], 0, strlen( $tableName ) + 1 ) != "{$tableName}." ) {
            continue;
        }

        $data[$value['name']] = array(
                             'title' => $value['title'],
                             'help'  => $value['title'],
                             'field' => array( 
                                              'handler' => civicrm_get_field_handler  ( $value['type'] ),
                                              'click sortable' => true,
                                              ),
                             'sort'  => array(
                                              'handler' => civicrm_get_sort_handler   ( $value['type'] ),
                                              ),
                             'filter' => array(
                                               'handler' => civicrm_get_filter_handler( $value['type'] ),
                                               ),
                             );
    }

}



function civicrm_views_custom_data_cache($data, $entity_type, $groupID, $subType, $style) {

    //Feels a bit hacky this next 10 lines but it was the only way I could get getTree to play nice with the data
    $config =& CRM_Core_Config::singleton( );
    if ($style == 'Inline') {
        $tree = CRM_Core_BAO_CustomGroup::getTree( $entity_type, CRM_Core_DAO::$_nullObject, null, null, $subType, null );
    }
    else {
        $tree = CRM_Core_BAO_CustomGroup::getTree( $entity_type, CRM_Core_DAO::$_nullObject, null, $groupID);
    }

    switch ($entity_type) {
             
    case "Contact":
    case "Individual":
    case "Household":
    case "Organization":
        $jointable = 'civicrm_contact';
        break;
    case "Event":
        $jointable = 'civicrm_event';
        break;
    case "Participant":
        $jointable = 'civicrm_participant';
        break;
    case "Contribution":
        $jointable = 'civicrm_contribution';
        break;
    case "Activity":
        $jointable = 'civicrm_activity';
        break;
    case "Relationship":
        $jointable = 'civicrm_relationship';
        break;

    }

    foreach ($tree as $groupkey => $currentgroup) {
        if ($groupkey == 'info') { return $data; } // dodges an invalid argument call after all the groups go through // have a look at what $tree outputs to to see why.

        $data[$currentgroup['table_name']]['table']['group']  = t('CiviCRM Custom: ') . $currentgroup['title'];

        // Join this table to Contacts
        $data[$currentgroup['table_name']]['table']['join'] =
            array(
                  // Directly links to join table.
                  $jointable => array(
                                      'left_field' => 'id',
                                      'field' => 'entity_id',
                                      ),
                  );
        $currentgroupfields = $currentgroup['fields'];
        foreach ($currentgroupfields as $key => $currentfield) {
            if ($currentfield['help_post'] == '' || $currentfield['help_post'] == null) { 
                $currentfield['help_post'] = t('Custom Data Field'); 
            } // Populates help text so there isn't miles of error: Missing Help


              //Create the Views Field
            $data[$currentgroup['table_name']][$currentfield['column_name']] = array(
                                                                                     'title' => $currentfield['label'],
                                                                                     'help' => $currentfield['help_post'],
                                                                                     'field' => array(
                                                                                                      'handler' => civicrm_get_field_handler($currentfield['data_type']),
                                                                                                      'click sortable' => TRUE,
                                                                                                      ),

                                                                                     'argument' => array(
                                                                                                         'handler' => civicrm_get_argument_handler($currentfield['data_type']),
                                                                                                         ),

                                                                                     'filter' => array(
                                                                                                       'handler' => civicrm_get_filter_handler($currentfield['data_type']),
                                                                                                       ),

                                                                                     'sort' => array(
                                                                                                     'handler' => civicrm_get_sort_handler($currentfield['data_type']),
                                                                                                     ),
                                                                                     );
        }
    }
    return $data;
}


//Getting the proper handlers by checking against the field's data_type

function civicrm_get_field_handler($type) {
    switch ($type) {
    case "String":
    case "Memo":
        return 'views_handler_field';   

    case "Float":
    case "Int":
        return 'views_handler_field_numeric';

    case "Date":
        return 'civicrm_handler_field_datetime';

    case "Boolean":
        return 'views_handler_field_boolean';

    case "StateProvince":
        return 'civicrm_handler_field_state';
  
    case "Country":
        return 'civicrm_handler_field_country';
    
    default:
        return 'views_handler_field'; 
    }
}

function civicrm_get_argument_handler($type) {
    switch ($type) {
    case "String":
    case "Memo":
        return 'views_handler_argument';   

    case "Float":
    case "Int":
        return 'views_handler_argument_numeric';

    case "Date":
        return 'views_handler_argument_date';

    case "Boolean":
        return 'views_handler_argument';

    case "StateProvince":
        return 'views_handler_argument';
  
    case "Country":
        return 'views_handler_argument';
    
    default:
        return 'views_handler_argument';
    }
}

function civicrm_get_filter_handler($type) {
    switch ($type) {
    case "String":
    case "Memo":
        return 'views_handler_filter_string';

    case "Float":
    case "Int":
        return 'views_handler_filter_numeric';

    case "Date":
        return 'civicrm_handler_filter_datetime';

    case "Boolean":
        return 'views_handler_filter_boolean_operator';

    case "StateProvince":
        return 'civicrm_handler_filter_state';
  
    case "Country":
        return 'civicrm_handler_filter_country';
    
    default:
        return 'views_handler_filter_string';
    }
}

function civicrm_get_sort_handler($type) {
    switch ($type) {
    case "String":
    case "Memo":
        return 'views_handler_sort';   

    case "Float":
    case "Int":
        return 'views_handler_sort_numeric';

    case "Date":
        return 'views_handler_sort_date';

    case "Boolean":
        return 'views_handler_sort';

    case "StateProvince":
        return 'views_handler_sort';
  
    case "Country":
        return 'views_handler_sort';
    
    default:
        return 'views_handler_sort';
    }
}

// Telling Views where to get the Handlers
function civicrm_views_handlers() {
    return array(
                 'info' => array(
                                 'path' => drupal_get_path('module', 'civicrm') . '/modules/views/civicrm',
                                 ),
                 'handlers' =>

                 //FIELD HANDLAAARRRRS! Field Handlers!

                 array(
                       // LINK FIELDS GO HERE
                       'civicrm_handler_field_link_contact' => array(
                                                                     'parent' => 'views_handler_field',
                                                                     ),
                       'civicrm_handler_field_link_event' => array(
                                                                   'parent' => 'views_handler_field',
                                                                   ),
                       'civicrm_handler_field_link_participant' => array(
                                                                         'parent' => 'views_handler_field',
                                                                         ),
                       'civicrm_handler_field_link_contribution' => array(
                                                                          'parent' => 'v§iews_handler_field',
                                                                          ),
                       'civicrm_handler_field_link_activity' => array(
                                                                      'parent' => 'views_handler_field',
                                                                      ),
					   'civicrm_handler_field_link_relationship' => array(
                                                                          'parent' => 'views_handler_field',
                                                                          ),

                       'civicrm_handler_field_contact_link' => array(
                                                                     'parent' => 'views_handler_field',
                                                                     ),
                       'civicrm_handler_field_gender' => array(
                                                               'parent' => 'views_handler_field',
                                                               ),
                       'civicrm_handler_field_location_type' => array(
                                                                      'parent' => 'views_handler_field',
                                                                      ),
                       'civicrm_handler_field_state' => array(
                                                              'parent' => 'views_handler_field',
                                                              ),
                       'civicrm_handler_field_country' => array(
                                                                'parent' => 'views_handler_field',
                                                                ),
                       'civicrm_handler_field_email' => array(
                                                              'parent' => 'views_handler_field',
                                                              ),
                       'civicrm_handler_field_datetime' => array(
                                                                 'parent' => 'views_handler_field_date',
                                                                 ),

                       'civicrm_handler_field_event_type' => array(
                                                                   'parent' => 'views_handler_field',
                                                                   ),
                       'civicrm_handler_field_event_link' => array(
                                                                   'parent' => 'views_handler_field',
                                                                   ),
                       'civicrm_handler_field_event_price_set' => array(
                                                                        'parent' => 'views_handler_field_prerender_list',
                                                                        ),
                       'civicrm_handler_field_participant_status' => array(
                                                                           'parent' => 'views_handler_field',
                                                                           ),
                       'civicrm_handler_field_participant_role' => array(
                                                                         'parent' => 'views_handler_field',
                                                                         ),
                       'civicrm_handler_field_contribution_type' => array(
                                                                          'parent' => 'views_handler_field',
                                                                          ),
                       'civicrm_handler_field_contribution_page' => array(
                                                                          'parent' => 'views_handler_field',
                                                                          ),
                       'civicrm_handler_field_payment_instrument' => array(
                                                                           'parent' => 'views_handler_field',
                                                                           ),
                       'civicrm_handler_field_contribution_status' => array(
                                                                            'parent' => 'views_handler_field',
                                                                            ),
                       'civicrm_handler_field_activity_status' => array(
                                                                        'parent' => 'views_handler_field',
                                                                        ),
                       'civicrm_handler_field_activity_type' => array(
                                                                      'parent' => 'views_handler_field',
                                                                      ),
					   'civicrm_handler_field_relationship_type' => array(
                                                                          'parent' => 'views_handler_field',
                                                                          ),
                                                                          
                       'civicrm_handler_field_drupalid' => array(
                                                            'parent' => 'views_handler_field',
                                                           ),      
                       'civicrm_handler_relationship_contact2users' => array(
                                                            'parent' => 'views_handler_relationship',
                                                           ),                                                                                                                 
                                                                          
                       //FILTER HANDDLEAARRRS! Filter Handlers!
                       'civicrm_handler_filter_datetime' => array(
                                                                  'parent' => 'views_handler_filter_date',
                                                                  ),


                       'civicrm_handler_filter_gender' => array(
                                                                'parent' => 'views_handler_filter_in_operator',
                                                                ),
                       'civicrm_handler_filter_location_type' => array(
                                                                       'parent' => 'views_handler_filter_in_operator',
                                                                       ),
                       'civicrm_handler_filter_state' => array(
                                                               'parent' => 'views_handler_filter_in_operator',
                                                               ),
                       'civicrm_handler_filter_country' => array(
                                                                 'parent' => 'views_handler_filter_in_operator',
                                                                 ),
                       'civicrm_handler_filter_event_type' => array(
                                                                    'parent' => 'views_handler_filter_in_operator',
                                                                    ),
                       'civicrm_handler_filter_participant_status' => array(
                                                                            'parent' => 'views_handler_filter_in_operator',
                                                                            ),
                       'civicrm_handler_filter_participant_role' => array(
                                                                          'parent' => 'views_handler_filter_in_operator',
                                                                          ),
                       'civicrm_handler_filter_contribution_type' => array(
                                                                           'parent' => 'views_handler_filter_in_operator',
                                                                           ),
                       'civicrm_handler_filter_contribution_page' => array(
                                                                           'parent' => 'views_handler_filter_in_operator',
                                                                           ),
                       'civicrm_handler_filter_payment_instrument' => array(
                                                                            'parent' => 'views_handler_filter_in_operator',
                                                                            ),
                       'civicrm_handler_filter_contribution_status' => array(
                                                                             'parent' => 'views_handler_filter_in_operator',
                                                                             ),
                       'civicrm_handler_filter_activity_status' => array(
                                                                         'parent' => 'views_handler_filter_in_operator',
                                                                         ),
                       'civicrm_handler_filter_activity_type' => array(
                                                                       'parent' => 'views_handler_filter_in_operator',
                                                                       ),
                       'civicrm_contact_date_api_filter_handler' => array(
                                                                          'parent' => 'date_api_filter_handler',
                                                                          ),
                       'civicrm_handler_filter_relationship_type' => array(
                                                                           'parent' => 'views_handler_filter_in_operator',
                                                                           ),    
                       'civicrm_handler_filter_contact_type' => array(
                                                                      'parent' => 'views_handler_filter_in_operator',
                                                                      ),                                                                           
                                                                           
                                                        
                       // ARGUMENT HANDLERS
                       'civicrm_handler_argument_date_api_contact' => array(
                                                                            'parent' => 'date_api_argument_handler',
                                                                            ),

                       ),
                 );
                 

}