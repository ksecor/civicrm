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
 * Default argument plugin to extract a civicrm user id via menu_get_object
 */
class views_plugin_argument_default_civicrm extends views_plugin_argument_default {
    var $option_name = 'default_argument_civicrm';

    function argument_form(&$form, &$form_state) {
        $form[$this->option_name] = array(
                                          '#type' => 'checkbox',
                                          '#title' => t('Also look for a node and use the node author'),
                                          '#default_value' => !empty($this->argument->options[$this->option_name]),
                                          '#process' => array('views_process_dependency'),
                                          '#dependency' => array(
                                                                 'radio:options[default_action]' => array('default'),
                                                                 'radio:options[default_argument_type]' => array($this->id)
                                                                 ),
                                          '#dependency_count' => 2,
                                          );
    }

    function get_argument() {
        foreach (range(1, 3) as $i) {
            $user = menu_get_object('user', $i);
            if (!empty($user)) {
                return $user->uid;
            }
        }

        foreach (range(1, 3) as $i) {
            $user = menu_get_object('civicrm_uid_optional', $i);
            if (!empty($user)) {
                return $user->uid;
            }
        }

        if (!empty($this->argument->options[$this->option_name])) {
            foreach (range(1, 3) as $i) {
                $node = menu_get_object('node', $i);
                if (!empty($node)) {
                    return $node->uid;
                }
            }
        }
    }
}

/**
 * Field handler to provide simple renderer that allows linking to a user.
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_civicrm extends views_handler_field {
    /**
     * Override init function to provide generic option to link to user.
     */
    function init(&$view, &$data) {
        parent::init($view, $data);
    }

    /**
     * Provide link to node option
     */
    function options_form(&$form, &$form_state) {
        parent::options_form($form, $form_state);
        $form['link_to_civicrm'] = array(
                                         '#title' => t('Link this field to its user'),
                                         '#type' => 'checkbox',
                                         '#default_value' => $this->options['link_to_civicrm'],
                                         );
    }

    function render_link($data, $values) {
        if (!empty($this->options['link_to_civicrm']) && user_access('access CiviCRM') && $values->id) {
            return civicrm_views_href( $data,
                                       'civicrm/contact/view',
                                       "reset=1&cid={$values->id}" );
        }
        else {
            return $data;
        }
    }

    function render($values) {
        return $this->render_link(check_plain($values->{$this->field_alias}), $values);
    }
}

/**
 * Field handler to present a link to the user.
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_civicrm_link extends views_handler_field {
    function construct() {
        parent::construct();
        $this->additional_fields['id'] = 'id';
    }

    function option_definition() {
        $options = parent::option_definition();
        $options['text'] = array('default' => '', 'translatable' => TRUE);
        return $options;
    }

    function options_form(&$form, &$form_state) {
        parent::options_form($form, $form_state);
        $form['text'] = array(
                              '#type' => 'textfield',
                              '#title' => t('Text to display'),
                              '#default_value' => $this->options['text'],
                              );
    }

    // An example of field level access control.
    function access() {
        return user_access('access CiviCRM');
    }

    function query() {
        $this->ensure_my_table();
        $this->add_additional_fields();
    }

    function render($values) {
        $text = !empty($this->options['text']) ? $this->options['text'] : t('view');
        $id = $values->{$this->aliases['id']};
        return civicrm_views_href($text, "civicrm/contact/view", "reset=1&id=$id");
    }
}

/**
 * Field handler to present a link to user edit.
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_civicrm_link_edit extends views_handler_field_civicrm_link {
    // An example of field level access control.
    function access() {
        return user_access('access CiviCRM');
    }

    function render($values) {
        $text = !empty($this->options['text']) ? $this->options['text'] : t('edit');
        $id = $values->id;
        return civicrm_views_href( $text,
                                   'civicrm/contact/add',
                                   "reset=1&action=update&cid=$id" );
    }
}

/**
 * Field handler to present a link to user delete.
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_civicrm_link_delete extends views_handler_field_civicrm_link {
    // An example of field level access control.
    function access() {
        return user_access('access CiviCRM');
    }

    function render($values) {
        $text = !empty($this->options['text']) ? $this->options['text'] : t('delete');
        $id = $values->{$this->aliases['id']};
        return civicrm_views_href( $text,
                                   'civicrm/contact/delete',
                                   "reset=1&id=$id" );
    }
}

/**
 * Field handler to provide acess control for the email field
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_civicrm_mail extends views_handler_field_civicrm {
    function option_definition() {
        $options = parent::option_definition();
        $options['link_to_civicrm'] = array('default' => 'mailto');
        return $options;
    }

    function options_form(&$form, &$form_state) {
        parent::options_form($form, $form_state);
        $form['link_to_civicrm'] = array(
                                         '#title' => t('Link this field'),
                                         '#type' => 'radios',
                                         '#options' => array(
                                                             0 => t('No link'),
                                                             'user' => t('To the user'),
                                                             'mailto' => t("With a mailto:"),
                                                             ),
                                         '#default_value' => $this->options['link_to_civicrm'],
                                         );
    }

    function render($values) {
        if ($this->options['link_to_civicrm'] == 'mailto') {
            return l($values->{$this->field_alias}, "mailto:" . $values->{$this->field_alias});
        }
        return $this->render_link(check_plain($values->{$this->field_alias}), $values);
    }
}

/**
 * Field handler to provide acess control for the email field
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_civicrm_email extends views_handler_field_civicrm {
  function option_definition() {
    $options = parent::option_definition();
    $options['link_to_user'] = array('default' => 'mailto');
    return $options;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['link_to_user'] = array(
      '#title' => t('Link this field'),
      '#type' => 'radios',
      '#options' => array(
        0 => t('No link'),
        'user' => t('To the user'),
        'mailto' => t("With a mailto:"),
      ),
      '#default_value' => $this->options['link_to_user'],
    );
  }

  function render($values) {
    if ($this->options['link_to_user'] == 'mailto') {
      return l($values->{$this->field_alias}, "mailto:" . $values->{$this->field_alias});
    }
    return $this->render_link(check_plain($values->{$this->field_alias}), $values);
  }
}

/**
 * Field handler to provide acess control for the location field (which is a lookup)
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_civicrm_location extends views_handler_field_civicrm {
    static $_locationTypes;

    function construct( ) {
        if ( ! self::$_locationTypes ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_locationTypes = CRM_Core_PseudoConstant::locationType( );
        }
    }

    function render( $values ) {
        $lid = $values->{$this->field_alias};
        if ( empty( $lid ) ||
             (int ) $lid <= 0 ) {
            return null;
        }

        return self::$_locationTypes[$values->{$this->field_alias}];
    }
}

class views_handler_filter_civicrm_location extends views_handler_filter_in_operator {
    static $_locationTypes;

    function construct( ) {
        if ( ! self::$_locationTypes ) {
            civicrm_initialize( );
            require_once 'CRM/Core/PseudoConstant.php';
            self::$_locationTypes = CRM_Core_PseudoConstant::locationType( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Location type');
            $options = array( );
            foreach (self::$_locationTypes as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }

}

class views_handler_filter_civicrm_contact_type extends views_handler_filter_in_operator {
    static $_contactTypes;

    function construct( ) {
        if ( ! self::$_contactTypes ) {
            civicrm_initialize( );
            require_once 'CRM/Core/SelectValues.php';
            self::$_contactTypes = CRM_Core_SelectValues::contactType( );
        }
    }

    function get_value_options() {
        if (!isset($this->value_options)) {
            $this->value_title = t('Contact type');
            $options = array( );
            foreach (self::$_contactTypes as $id => $name ) {
                $options[$id] = $name;
            }
            $this->value_options = $options;
        }
    }
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
 * @}
 */
