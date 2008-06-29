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
    $data['civicrm_contact']['table']['group']  = t('CiviCRM');

    $data['civicrm_contact']['table']['base'] = array(
                                                      'field' => 'id',
                                                      'title' => t('CiviCRM Contacts'),
                                                      'help' => t('CiviCRM Contacts. First attempt at a views integration'),
                                                      );

    $data['civicrm_contact']['table']['join'] = array(
  );

    // id
    $data['civicrm_contact']['id'] = array(
                                           'title' => t('Contact ID'),
                                           'help' => t('The contact ID'), // The help that appears on the UI,
                                           'field' => array(
                                                            'handler' => 'views_handler_field_civicrm',
                                                            'click sortable' => TRUE,
                                                            ),
                                           'argument' => array(
                                                               'handler' => 'views_handler_argument_civicrm_id',
                                                               'name field' => 'name', // display this field in the summary
                                                               ),
                                           'filter' => array(
                                                             'title' => t('Name'),
                                                             'handler' => 'views_handler_filter_civicrm_name',
                                                             ),
                                           'sort' => array(
                                                           'handler' => 'views_handler_sort',
                                                           ),
                                           );

    // id
    $data['civicrm_contact']['id_current'] = array(
                                                   'real field' => 'id',
                                                   'title' => t('Current'),
                                                   'help' => t('Filter the view to the currently logged in user.'),
                                                   'filter' => array(
                                                                     'handler' => 'views_handler_filter_civicrm_current',
                                                                     ),
                                                   );
    
    // name
    $data['civicrm_contact']['name'] = array(
                                             'title' => t('Name'), // The item it appears as on the UI,
                                             'real field' => 'display_name',
                                             'help' => t('The contact name.'), // The help that appears on the UI,
                                             'field' => array(
                                                              'handler' => 'views_handler_field_civicrm_name',
                                                              'click sortable' => TRUE,
                                                              ),
                                             'sort' => array(
                                                             'handler' => 'views_handler_sort',
                                                             ),
                                             'argument' => array(
                                                                 'handler' => 'views_handler_argument_string',
                                                                 ),
                                             );

    $data['civicrm_contact']['edit_node'] = array(
                                                  'field' => array(
                                                                   'title' => t('Edit link'),
                                                                   'help' => t('Provide a simple link to edit the user.'),
                                                                   'handler' => 'views_handler_field_civicrm_link_edit',
                                                                   ),
                                                  );

    $data['civicrm_contact']['delete_node'] = array(
                                                    'field' => array(
                                                                     'title' => t('Delete link'),
                                                                     'help' => t('Provide a simple link to delete the user.'),
                                                                     'handler' => 'views_handler_field_civicrm_link_delete',
                                                                     ),
                                                    );

    // ----------------------------------------------------------------------
    // CiviCRM email table

    $data['civicrm_email']['table']['group']  = t('CiviCRM');

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
        if (!empty($this->options['link_to_civicrm'])) {
            $this->additional_fields['id'] = 'id';
        }
    }

    function option_definition() {
        $options = parent::option_definition();
        $options['link_to_civicrm'] = array('default' => TRUE);
        return $options;
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
        if (!empty($this->options['link_to_civicrm']) && user_access('access user profiles') && $values->{$this->aliases['id']}) {
            return l($data, 'civicrm/contact/view?reset=1&id=' . $values->{$this->aliases['id']}, array('html' => TRUE));
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
 * Field handler to provide simple renderer that allows using a themed user link
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_civicrm_name extends views_handler_field_civicrm {
    function render_link($data, $values) {
        return $data;
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
        return l($text, "civicrm/contact/view?reset=1&id=$id");
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
        $id = $values->{$this->aliases['id']};
        return l($text, "civicrm/contact/add?reset=1&id=$id");
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
        return l($text, "user/$id/delete", array('query' => drupal_get_destination()));
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
 * Argument handler to accept a user id.
 *
 * @ingroup views_argument_handlers
 */
class views_handler_argument_civicrm_id extends views_handler_argument_numeric {
    /**
     * Override the behavior of title(). Get the name of the user.
     */
    function title_query() {
        if (!$this->argument) {
            return array(variable_get('anonymous', t('Anonymous')));
        }
      
        $titles = array();
        $placeholders = implode(', ', array_fill(0, sizeof($this->value), '%d'));
      
        $result = db_query("SELECT c.display_name FROM {civicrm_contact} c WHERE c.id IN ($placeholders)", $this->value);
        while ($term = db_fetch_object($result)) {
            $titles[] = check_plain($term->name);
        }
        return $titles;
    }
}

/**
 * Filter handler for usernames
 *
 * @ingroup views_filter_handlers
 */
class views_handler_filter_civicrm_name extends views_handler_filter_in_operator {
    function value_form(&$form, &$form_state) {
        $values = array();
        if ($this->value) {
            $result = db_query("SELECT * FROM {users} u WHERE uid IN ("  . implode(', ', $this->value) . ")");
            while ($account = db_fetch_object($result)) {
                if ($account->uid) {
                    $values[] = $account->name;
                }
                else {
                    $values[] = 'Anonymous'; // Intentionally NOT translated.
                }
            }
        }

        sort($values);
        $form['value'] = array(
                               '#type' => 'textfield',
                               '#title' => t('Usernames'),
                               '#description' => t('Enter a comma separated list of user names.'),
                               '#default_value' => implode(', ', $values),
                               '#autocomplete_path' => 'admin/views/ajax/autocomplete/user',
                               );
    }

    function value_validate(&$form, &$form_state) {
        $values = drupal_explode_tags($form_state['values']['options']['value']);
        $ids = $this->validate_civicrm_strings($form['value'], $values);

        if ($ids) {
            $form_state['values']['options']['value'] = $ids;
        }
    }

    function exposed_validate(&$form, &$form_state) {
        if (empty($this->options['exposed'])) {
            return;
        }

        if (empty($this->options['expose']['identifier'])) {
            return;
        }

        $identifier = $this->options['expose']['identifier'];
        $values = drupal_explode_tags($form_state['values'][$identifier]);

        $ids = $this->validate_civicrm_strings($form[$identifier], $values);

        if ($ids) {
            $form_state['values'][$identifier] = $ids;
        }
    }

    /**
     * Validate the user string. Since this can come from either the form
     * or the exposed filter, this is abstracted out a bit so it can
     * handle the multiple input sources.
     */
    function validate_civicrm_strings(&$form, $values) {
        $ids = array();
        $placeholders = array();
        $args = array();
        $results = array();
        foreach ($values as $value) {
            if (strtolower($value) == 'anonymous') {
                $ids[] = 0;
            }
            else {
                $missing[strtolower($value)] = TRUE;
                $args[] = $value;
                $placeholders[] = "'%s'";
            }
        }

        if (!$args) {
            return $ids;
        }

        $result = db_query("SELECT * FROM {users} WHERE name IN (" . implode(', ', $placeholders) . ")", $args);
        while ($account = db_fetch_object($result)) {
            unset($missing[strtolower($account->name)]);
            $ids[] = $account->uid;
        }

        if ($missing) {
            form_error($form, format_plural(count($missing), 'Unable to find user: @users', 'Unable to find users: @users', array('@users' => implode(', ', array_keys($missing)))));
        }

        return $ids;
    }

    function value_submit() {
        // prevent array filter from removing our anonymous user.
    }

    // Override to do nothing.
    function get_value_options() { }

    function admin_summary() {
        // set up $this->value_options for the parent summary
        $this->value_options = array();

        if ($this->value) {
            $result = db_query("SELECT * FROM {users} u WHERE uid IN ("  . implode(', ', $this->value) . ")");

            while ($account = db_fetch_object($result)) {
                if ($account->uid) {
                    $this->value_options[$account->uid] = $account->name;
                }
                else {
                    $this->value_options[$account->uid] = 'Anonymous'; // Intentionally NOT translated.
                }
            }
        }

        return parent::admin_summary();
    }
}

/**
 * Filter handler for the current user
 *
 * @ingroup views_filter_handlers
 */
class views_handler_filter_civicrm_current extends views_handler_filter_boolean_operator {
    function construct() {
        parent::construct();
        $this->value_value = t('Is the logged in user');
    }

    function query() {
        $this->ensure_my_table();
        $this->query->add_where($this->options['group'], "$this->table_alias.$this->real_field " . (empty($this->value) ? '!=' : '=') . " ***CURRENT_CIVICRM***");
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

/**
 * @}
 */
