<?php
// $Id: index.php,v 0.8.1 2005-04-25 7:05:14 PM messina Exp $

/**
 * Originally update_page_header(), ripped directly from update.php.  Changed a little
 * for index.php.
 */
function install_page_header($title, $percent = NULL) {
  $output = "<html>\n<head>\n<title>CiviCRM installer: ". $title ."</title>\n";
  if (!is_null($percent)) {
    $output .= '<meta http-equiv="refresh" content="0; url=http://'. install_base_uri() .'/index.php?op=continue_dump" />'."\n";
  }
  $output .= <<<EOF
      <link rel="stylesheet" type="text/css" media="screen" href="css/install/styles.css" title="Installer styles" />
EOF;
  $output .= "</head>\n<body>\n";
  $output .= "<div class=\"hide\">\n";
  $output .= "  <a href=\"#content\" title=\"Skip the site navigation to go directly to the content\" class=\"skiplink\">Skip to content</a>\n";
  $output .= "</div>\n";

  $output .= "<div id=\"header\">\n";
  $output .= "  <div id=\"header-container\">\n";
  $output .= "    <h2 ><a href=\"http://sandbox.openngo.org/civicrm\" class=\"text-replacement\">CiviCRM</a></h2>\n";
  //$output .= "    <div id=\"panic\"><a href=\"panic\" class=\"text-replacement\" title=\"Panic!!\">Last resort panic link!</a></div>\n";
  $output .= "  </div>\n";
  $output .= "</div>\n";
  
  $output .= "<div id=\"wrapper\">\n";
  $output .= "<h2 id=\"site-name\">CiviCRM Installer</h2>\n";
  $output .= "<div id=\"content\">\n";

  return $output;
}

/**
 * Also ripped directly from update.php. Yeah, I'm still coding this quickly.
 * And I changed double to single quotes again.
 * Originally update_page_footer().
 */
function install_page_footer() {
  $output  = "</div>\n</div>";
  /* the origional version of this had these non-existant links. they may exist later
  $output .= "<div id=\"notices\">\n";
  $output .= "  <ul class=\"flat-navigation\">\n";
  $output .= '  <li class="first"><a href="http://www.civicrmlabs.org/">civicrm home</a></li>'."\n";
  $output .= '  <li class="last"><a href="http://www.civicrmlabs.org/about">about civicrm</a></li>'."\n";
  $output .= '  <li><a href="http://www.civicrmlabs.org/contact">contact us</a></li>'."\n";
  $output .= '  <li><a href="http://www.civicrmlabs.org/support">get support</a></li>'."\n";
  $output .= '  <li><a href="http://www.civicrmlabs.org/contribute">contribute</a></li>'."\n";
  $output .= '  <li><a href="http://www.civicrmlabs.org/privacy">privacy policy &amp; disclaimers</a></li>'."\n";
  $output .= "  </ul>\n";
  $output .= "</div>\n";
  */
  $output .= "</body>\n</html>\n";
  return $output;
}

// COMMENCE RIPPING FROM COMMON.INC!!

/**
 * Generate a form from a set of form elements.
 *
 * @param $form
 *   An HTML string containing one or more form elements.
 * @param $method
 *   The query method to use ("post" or "get").
 * @param $action
 *   The URL to send the form contents to, if not the current page.
 * @param $attributes
 *   An associative array of attributes to add to the form tag.
 * @result
 *   An HTML string with the contents of $form wrapped in a form tag.
 */
function form($form, $method = 'post', $action = NULL, $attributes = NULL) {
  if (!$action) {
    $action = request_uri();
  }
  return '<form action="'. $action .'" method="'. $method .'"'. drupal_attributes($attributes) .">\n". $form ."\n</form>\n";
}

/**
 * Return an associative array of all errors.
 */
function form_get_errors() {
  if (array_key_exists('form', $GLOBALS)) {
    return $GLOBALS['form'];
  }
}

/**
 * Return the error message filed against the form with the specified name.
 */
function _form_get_error($name) {
  if (array_key_exists('form', $GLOBALS)) {
    return $GLOBALS['form'][$name];
  }
}

function _form_get_class($name, $required, $error) {
  return $name. ($required ? ' required' : '') . ($error ? ' error' : '');
}

/**
 * File an error against the form element with the specified name.
 */
function form_set_error($name, $message) {
  $GLOBALS['form'][$name] = $message;
  drupal_set_message($message, 'error');
}

/**
 * Format a general form item.
 *
 * @param $title
 *   The label for the form item.
 * @param $value
 *   The contents of the form item.
 * @param $description
 *   Explanatory text to display after the form item.
 * @param $id
 *   A unique identifier for the form item.
 * @param $required
 *   Whether the user must fill in this form element before submitting the form.
 * @param $error
 *   An error message to display alongside the form element.
 * @return
 *   A themed HTML string representing the form item.
 */
function form_item($title, $value, $description = NULL, $id = NULL, $required = FALSE, $error = FALSE) {
  return theme('form_element', $title, $value, $description, $id, $required, $error);
}

/**
 * Format a group of form items.
 *
 * @param $legend
 *   The label for the form item group.
 * @param $group
 *   The form items within the group, as an HTML string.
 * @param $description
 *   Explanatory text to display after the form item group.
 * @return
 *   A themed HTML string representing the form item group.
 */
function form_group($legend, $group, $description = NULL) {
  return '<fieldset>' . ($legend ? '<legend>'. $legend .'</legend>' : '') . $group . ($description ? '<div class="description">'. $description .'</div>' : '') . "</fieldset>\n";
}

/**
 * Format a checkbox.
 *
 * @param $title
 *   The label for the checkbox.
 * @param $name
 *   The internal name used to refer to the button.
 * @param $value
 *   The value that the form element takes on when selected.
 * @param $checked
 *   Whether the button will be initially selected when the page is rendered.
 * @param $description
 *   Explanatory text to display after the form item.
 * @param $attributes
 *   An associative array of HTML attributes to add to the button.
 * @param $required
 *   Whether the user must check this box before submitting the form.
 * @return
 *   A themed HTML string representing the checkbox.
 */
function form_checkbox($title, $name, $value = 1, $checked = FALSE, $description = NULL, $attributes = NULL, $required = FALSE) {
  $element = '<input type="checkbox" class="'. _form_get_class('form-checkbox', $required, _form_get_error($name)) .'" name="edit['. $name .']" id="edit-'. $name .'" value="'. $value .'"'. ($checked ? ' checked="checked"' : '') . drupal_attributes($attributes) .' />';
  if (!is_null($title)) {
    $element = '<label class="option">'. $element .' '. $title .'</label>';
  }
  return form_hidden($name, 0) . theme('form_element', NULL, $element, $description, $name, $required, _form_get_error($name));
}

/**
 * Format a single-line text field.
 *
 * @param $title
 *   The label for the text field.
 * @param $name
 *   The internal name used to refer to the field.
 * @param $value
 *   The initial value for the field at page load time.
 * @param $size
 *   A measure of the visible size of the field (passed directly to HTML).
 * @param $maxlength
 *   The maximum number of characters that may be entered in the field.
 * @param $description
 *   Explanatory text to display after the form item.
 * @param $attributes
 *   An associative array of HTML attributes to add to the form item.
 * @param $required
 *   Whether the user must enter some text in the field.
 * @return
 *   A themed HTML string representing the field.
 */
function form_textfield($title, $name, $value, $size, $maxlength, $description = NULL, $attributes = NULL, $required = FALSE) {
  $size = $size ? ' size="'. $size .'"' : '';
  return theme('form_element', $title, '<input type="text" maxlength="'. $maxlength .'" class="'. _form_get_class('form-text', $required, _form_get_error($name)) .'" name="edit['. $name .']" id="edit-'. $name .'"'. $size .' value="'. check_form($value) .'"'. drupal_attributes($attributes) .' />', $description, 'edit-'. $name, $required, _form_get_error($name));
}

/**
 * Format a single-line text field that does not display its contents visibly.
 *
 * @param $title
 *   The label for the text field.
 * @param $name
 *   The internal name used to refer to the field.
 * @param $value
 *   The initial value for the field at page load time.
 * @param $size
 *   A measure of the visible size of the field (passed directly to HTML).
 * @param $maxlength
 *   The maximum number of characters that may be entered in the field.
 * @param $description
 *   Explanatory text to display after the form item.
 * @param $attributes
 *   An associative array of HTML attributes to add to the form item.
 * @param $required
 *   Whether the user must enter some text in the field.
 * @return
 *   A themed HTML string representing the field.
 */
function form_password($title, $name, $value, $size, $maxlength, $description = NULL, $attributes = NULL, $required = FALSE) {
  $size = $size ? ' size="'. $size .'"' : '';
  return theme('form_element', $title, '<input type="password" class="'. _form_get_class('form-password', $required, _form_get_error($name)) .'" maxlength="'. $maxlength .'" name="edit['. $name .']" id="edit-'. $name .'"'. $size .' value="'. check_form($value) .'"'. drupal_attributes($attributes) .' />', $description, 'edit-'. $name, $required, _form_get_error($name));
}

function check_form($text) {
  return drupal_specialchars($text, ENT_QUOTES);
}

/**
 * Store data in a hidden form field.
 *
 * @param $name
 *   The internal name used to refer to the field.
 * @param $value
 *   The stored data.
 * @return
 *   A themed HTML string representing the hidden field.
 *
 * This function can be useful in retaining information between page requests,
 * but be sure to validate the data on the receiving page as it is possible for
 * an attacker to change the value before it is submitted.
 */
function form_hidden($name, $value) {
  return '<input type="hidden" name="edit['. $name .']" value="'. check_form($value) ."\" />\n";
}

/**
 * Format a radio button.
 *
 * @param $title
 *   The label for the radio button.
 * @param $name
 *   The internal name used to refer to the button.
 * @param $value
 *   The value that the form element takes on when selected.
 * @param $checked
 *   Whether the button will be initially selected when the page is rendered.
 * @param $description
 *   Explanatory text to display after the form item.
 * @param $attributes
 *   An associative array of HTML attributes to add to the button.
 * @param $required
 *   Whether the user must select this radio button before submitting the form.
 * @return
 *   A themed HTML string representing the radio button.
 */
function form_radio($title, $name, $value = 1, $checked = FALSE, $description = NULL, $attributes = NULL, $required = FALSE) {
  $element = '<input type="radio" class="'. _form_get_class('form-radio', $required, _form_get_error($name)) .'" name="edit['. $name .']" value="'. $value .'"'. ($checked ? ' checked="checked"' : '') . drupal_attributes($attributes) .' />';
  if (!is_null($title)) {
    $element = '<label class="option">'. $element .' '. $title .'</label>';
  }
  return theme('form_element', NULL, $element, $description, $name, $required, _form_get_error($name));
}

/**
 * Format a set of radio buttons.
 *
 * @param $title
 *   The label for the radio buttons as a group.
 * @param $name
 *   The internal name used to refer to the buttons.
 * @param $value
 *   The currently selected radio button's key.
 * @param $options
 *   An associative array of buttons to display. The keys in this array are
 *   button values, while the values are the labels to display for each button.
 * @param $description
 *   Explanatory text to display after the form item.
 * @param $required
 *   Whether the user must select a radio button before submitting the form.
 * @param $attributes
 *   An associative array of HTML attributes to add to each button.
 * @return
 *   A themed HTML string representing the radio button set.
 */
function form_radios($title, $name, $value, $options, $description = NULL, $required = FALSE, $attributes = NULL) {
  if (count($options) > 0) {
    $choices = '';
    foreach ($options as $key => $choice) {
      $choices .= '<label class="option"><input type="radio" class="form-radio" name="edit['. $name .']" value="'. $key .'"'. ($key == $value ? ' checked="checked"' : ''). drupal_attributes($attributes). ' /> '. $choice .'</label><br />';
    }
    return theme('form_element', $title, $choices, $description, NULL, $required, _form_get_error($name));
  }
}

/**
 * Format an action button.
 *
 * @param $value
 *   Both the label for the button, and the value passed to the target page
 *   when this button is clicked.
 * @param $name
 *   The internal name used to refer to the button.
 * @param $type
 *   What type to pass to the HTML input tag.
 * @param $attributes
 *   An associative array of HTML attributes to add to the form item.
 * @return
 *   A themed HTML string representing the button.
 */
function form_button($value, $name = 'op', $type = 'submit', $attributes = NULL) {
  return '<input type="'. $type .'" class="form-'. $type .'" name="'. $name .'" value="'. check_form($value) .'" '. drupal_attributes($attributes) ." />\n";
}

/**
 * Format a form submit button.
 *
 * @param $value
 *   Both the label for the button, and the value passed to the target page
 *   when this button is clicked.
 * @param $name
 *   The internal name used to refer to the button.
 * @param $attributes
 *   An associative array of HTML attributes to add to the form item.
 * @return
 *   A themed HTML string representing the button.
 */
function form_submit($value, $name = 'op', $attributes = NULL) {
  return form_button($value, $name, 'submit', $attributes);
}

/**
 * Format an attribute string to insert in a tag.
 *
 * @param $attributes
 *   An associative array of HTML attributes.
 * @return
 *   An HTML string ready for insertion in a tag.
 */
function drupal_attributes($attributes = array()) {
  if ($attributes) {
    $t = array();
    foreach ($attributes as $key => $value) {
      $t[] = $key .'="'. $value .'"';
    }

    return ' '. implode($t, ' ');
  }
}

/**
 * Encode special characters in a string for display as HTML.
 *
 * Note that we'd like to use htmlspecialchars($input, $quotes, 'utf-8')
 * as outlined in the PHP manual, but we can't because there's a bug in
 * PHP < 4.3 that makes it mess up multibyte charsets if we specify the
 * charset. This will be changed later once we make PHP 4.3 a requirement.
 */
function drupal_specialchars($input, $quotes = ENT_NOQUOTES) {
  return htmlspecialchars($input, $quotes);
}

// and a little rippage from theme.inc

/**
 * Generate the themed representation of a Drupal object.
 *
 * All requests for themed functions must go through this function. It examines
 * the request and routes it to the appropriate theme function. If the current
 * theme does not implement the requested function, then the current theme
 * engine is checked. If neither the engine nor theme implement the requested
 * function, then the base theme function is called.
 *
 * For example, to retrieve the HTML that is output by theme_page($output), a
 * module should call theme('page', $output).
 *
 * @param $function
 *   The name of the theme function to call.
 * @param ...
 *   Additional arguments to pass along to the theme function.
 * @return
 *   An HTML string that generates the themed output.
 */
function theme() {
  global $theme;
  global $theme_engine;
  
  $args = func_get_args();
  $function = array_shift($args);

  if (($theme != '') && function_exists($theme .'_'. $function)) {
    // call theme function
    return call_user_func_array($theme .'_'. $function, $args);
  }
  elseif (($theme != '') && isset($theme_engine) && function_exists($theme_engine .'_'. $function)) {
    // call engine function
    return call_user_func_array($theme_engine .'_'. $function, $args);
  }
  elseif (function_exists('theme_'. $function)){
    // call Drupal function
    return call_user_func_array('theme_'. $function, $args);
  }
}

/**
 * Return a themed form element.
 *
 * @param $title the form element's title
 * @param $value the form element's data
 * @param $description the form element's description or explanation
 * @param $id the form element's ID used by the &lt;label&gt; tag
 * @param $required a boolean to indicate whether this is a required field or not
 * @param $error a string with an error message filed against this form element
 *
 * @return a string representing the form element
 */
function theme_form_element($title, $value, $description = NULL, $id = NULL, $required = FALSE, $error = FALSE) {

  $output  = "<div class=\"form-item\">\n";
  $required = $required ? theme('mark') : '';

  if ($title) {
    if ($id) {
      $output .= " <label for=\"$id\">$title:</label>$required<br />\n";
    }
    else {
      $output .= " <label>$title:</label>$required<br />\n";
    }
  }

  $output .= " $value\n";

  if ($description) {
    $output .= " <div class=\"description\">$description</div>\n";
  }

  $output .= "</div>\n";

  return $output;
}

/**
 * Return a themed error message.
 * REMOVE: this function is deprecated an no longer used in core.
 *
 * @param $message
 *   The error message to be themed.
 *
 * @return
 *   A string containing the error output.
 */
function theme_error($message) {
  return '<div class="error">'. $message .'</div>';
}

/**
 * Return a themed list of items.
 *
 * @param $items
 *   An array of items to be displayed in the list.
 * @param $title
 *   The title of the list.
 * @return
 *   A string containing the list output.
 */
function theme_item_list($items = array(), $title = NULL) {
  $output = '<div class="item-list">'."\n";
  if (isset($title)) {
    $output .= '<h3>'. $title .'</h3>'."\n";
  }

  if (isset($items)) {
    $output .= '<ul>'."\n";
    foreach ($items as $item) {
      $output .= '  <li>'. $item .'</li>'."\n";
    }
    $output .= '</ul>'."\n";
  }
  $output .= '</div>';
  return $output;
}

// looks like we need to rip stuff from bootstrap.inc too

/**
 * Since request_uri() is only available on Apache, we generate an
 * equivalent using other environment vars.
 */
function request_uri() {

  if (isset($_SERVER['REQUEST_URI'])) {
    $uri = $_SERVER['REQUEST_URI'];
  }
  else {
    if (isset($_SERVER['argv'])) {
      $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
    }
    else {
      $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
    }
  }
  
  return check_url($uri);
} 

/**
 * Prepare user input for use in a URI.
 *
 * We replace ( and ) with their entity equivalents to prevent XSS attacks.
 */
function check_url($uri) {
  $uri = htmlspecialchars($uri, ENT_QUOTES);

  $uri = strtr($uri, array('(' => '&040;', ')' => '&041;'));

  return $uri;
}

/**
 * Here.. have a function!
 */
function variable_get() {
  return FALSE;
}


// All the functions below this point are truly original as in that they did not
// originally come from drupal.  They may have been ripped of from somewhere else,
// though.  If so, the source is acknowledged accordingly.

function install_base_uri() {
  $url = $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  return substr($url, 0, strrpos($url, '/'));
}



/**
 * Find out if settings.php has already been written by seeing if all
 * the variables defined in it still have the default values that
 * come with Drupal/Civicrm "out of the box".
 *
 * @return
 *   A boolean.
 */
function install_conf_written_before() {
  global $db_url, $db_prefix, $base_url;
  //  include_once('sites/default/settings.php');
  return (!(($db_url == '') &&
            ($db_prefix == '') &&
            ($base_url == '')));
}


function install_checklist($current_step = '') {
  // do something that generates the checklist html
  if ($current_step == 'Introduction' || $current_step == 'Installation already completed!') {
    return '';
  }
  $install_steps = install_steps();
  $install_steps_index = array_flip($install_steps);
  $index = isset($install_steps_index[$current_step]) ? $install_steps_index[$current_step] : 0;
  $list_html = "  <div id=\"secondary-content\">\n";
  $list_html .= "    <h2 class=\"hide\">Setup Process</h2>\n"; 
  $list_html .= "    <ol class=\"procedure-list\">\n";
  for ($step_number = 1; $step_number < count($install_steps); $step_number++) {
    if ($step_number < $index) {
      $list_html .= '      <li class="complete">'; 
    }
    elseif ($step_number > $index) {
      $list_html .= '      <li class="incomplete">';
    }
    else {
      $list_html .= '      <li class="incomplete active">';
    }
    $list_html .= '<span>'. $install_steps[$step_number]. '</span>';
    $list_html .= "</li>\n";
  }
  $list_html .= "    </ol>\n";
  $list_html .= "  </div>\n";
  return $list_html;
}


function install_steps() {
  return array('Introduction', 'CMS check-up', 'Server check-up', 'Database setup', 'Finishing up');
}


/**
 * This function was copied from the PHP.net website's documentation for the ini_get() function
 *
 * @return
 *   An integer.
 */
function return_bytes($val) {
   $val = trim($val);
   $last = $val{strlen($val)-1};
   switch($last) {
     case 'k':
     case 'K':
       return (int) $val * 1024;
       break;
     case 'm':
     case 'M':
       return (int) $val * 1048576;
       break;
     default:
       return $val;
   }
}


/**
 * This function returns a message about PHP's memory limit in regard to the installation process as
 * well as messages about other non-critical but important server-level prerequisites.
 *
 * @return
 *   A string
 *
 */
function install_warning_messages() {
  $messages = '';
  if (!function_exists('memory_get_usage')) {
    $messages .= _install_msg_successful('Your server has enough memory to run Civicrm.');
  }
  else {
    $memory_limit = ini_get('memory_limit');
    if (return_bytes($memory_limit) < return_bytes('24M')) {
      $messages .= _install_msg_warning('Our memory check indicates that you currently have a memory limit of '. $memory_limit .' which is less than the recommended 24M.  You may proceed with the installation, but it is <em>strongly</em> recommended that you configure your PHP to provide you with at least 24M of memory.', NULL, 'http://www.civicrmlabs.org/installer_help/081/memory_check');
    }
    else {
      $messages .= _install_msg_successful('PHP has at least the recommended amount of memory for Civicrm to run.');
    }
  }
  
  $base_uri = install_base_uri();
  $hostname = $_SERVER['HTTP_HOST'];
  
  if (strcasecmp($hostname, 'localhost') == 0 || substr($hostname, 0, 4) == '192.') {
    $messages .= _install_msg_notice('You appear to be installing Civicrm on a private IP address or on \'localhost\'. If you would like your site to be publicly accessible, you need to restart this installer from a public domain or IP.', NULL, 'http://www.civicrmlabs.org/installer_help/081/url_warning');
  }

  if (ini_get('error_reporting') == E_ALL) {
    $messages .= _install_msg_warning('The installer has detected that your server\'s PHP is configured to report all levels of errors.  This can be a problem if you are running a production site.  It is recommended that you lower your level of error reporting to exclude PHP notices at the very least.', NULL, 'http://www.civicrmlabs.org/installer_help/081/error_reporting');
  } 
  
  return $messages;
}


function install_database_form($edit = array()) {
    $edit['db_path'] = isset($edit['db_path']) ? $edit['db_path'] : '';
    $edit['db_user'] = isset($edit['db_user']) ? $edit['db_user'] : '';
    $edit['db_pass'] = isset($edit['db_pass']) ? $edit['db_pass'] : '';
    $edit['db_host'] = isset($edit['db_host']) ? $edit['db_host'] : '';
    //$edit['db_db'] = isset($edit['db_db']) ? $edit['db_db'] : '';
    $edit['db_prefix'] = isset($edit['db_prefix']) ? $edit['db_prefix'] : '';
    $form = form_textfield('Mysql path', 'db_path', $edit['db_path'], 40, 255, '<span class="tip"><strong>Tip</strong>: Enter your Database path. eg: /opt/mysql4/</span>', NULL, TRUE);
    $form .= form_textfield('User name', 'db_user', $edit['db_user'], 20, 64, '<span class="tip"><strong>Tip</strong>: Allowed characters include letters, digits, \'_\', and \'-\' with a maximum of 16 characters.</span>', NULL, TRUE);
    $form .= form_password('Password', 'db_pass', $edit['db_pass'] , 20, 64, '<span class="tip"><strong>Tip</strong>:  Allowed characters include <strong>letters</strong>, <strong>digits</strong>, and any of the following: -_!#$%^&*()+={}[]|&lt;&gt;;:?,.~</span>', NULL, TRUE);
    $form .= form_textfield('Host', 'db_host', $edit['db_host'] ? $edit['db_host'] : 'localhost', 20, 64, '<span class="tip"><strong>Tip</strong>: If your database is on the same machine as your Civicrm installation, just enter "localhost". Leaving this item blank will default the value to "localhost".</span>', NULL, TRUE);
    $form .= form_textfield('Database name', 'db_db', $edit['db_db'], 20, 64, NULL, NULL, TRUE);
    $form .= form_textfield('Table prefix', 'db_prefix', $edit['db_prefix'], 20, 64, '<span class="tip"><strong>Tip</strong>:  If you are using a shared database account or only have one database account, you may wish to prefix your database tables to avoid a "naming collision." For example: "my_new_site_".</span>');
    //$form .= form_checkbox('Install US zipcode database?', 'use_us_zipcodes', 1, isset($edit['use_us_zipcodes']) ? $edit['use_us_zipcodes'] : 1, '<span class="tip"><strong>Tip</strong>: <a href="javascript:alert(\'zipcode help!\');">Certain features</a> require the list of US zipcodes. Check this box to install.</span>');
    //$form .= form_checkbox('Install Civicrm Site Configuration Guide?', 'install_docs', 1, isset($edit['install_docs']) ? $edit['install_docs'] : 0, 'Check this box to install documentation for configuring your site.');
    //$form .= form_hidden('db_db', 'civicrm');
    $form .= form_hidden('db_type', 'mysql');
    if (isset($_POST['op']) && ($_POST['op'] == 'Retry table creation' || $_POST['op'] == 'Create tables')) {
        $form .= form_submit('Retry table creation');
        //    $form .= form_submit('Cancel installation');
    }
    else {
        $form .= form_submit('Create tables');
    }
    return form($form);
}

function install_test_dbinfo(&$edit, &$error_status) {
  $edit['db_host'] = strlen($edit['db_host']) ? $edit['db_host'] : 'localhost';
  $edit['db_user'] = isset($edit['db_user']) ? trim($edit['db_user']) : '';
  $edit['db_pass'] = isset($edit['db_pass']) ? trim($edit['db_pass']) : '';
  $edit['db_prefix'] = isset($edit['db_prefix']) ? trim($edit['db_prefix']) : '';
  
  if ($edit['db_prefix']) {
    $edit['db_prefix'] = _install_add_prefix_underscore($edit['db_prefix']);
  }
  
  $dummy_array = array();
  if ((!preg_match('/[A-Za-z0-9_-]+/', $edit['db_user'], $dummy_array)) && !(strlen($edit['db_pass']) > 16 || strlen($edit['db_pass']) < 1 )) {
    $error_status = 'invalid_user';
    return FALSE;
  }
  
  if ((!preg_match('/[A-Za-z0-9#@!$%^&*()+={[|\];<>?,.~:_-]+/', $edit['db_pass'], $dummy_array))) {
    $error_status = 'invalid_pass';
    return FALSE;
  }
  
  $connection = @mysql_connect($edit['db_host'], $edit['db_user'], $edit['db_pass']);
  if (!$connection) {
    $error_status = 'connection';
    return FALSE;
  }
  
  $db_exists = mysql_select_db($edit['db_db'], $connection);
  if (!$db_exists) {
    $error_status = 'database_name';
    return FALSE;    
  }
 
  if ($edit['db_prefix']=='') {
    $error_status = 'prefix_name';
    return FALSE;    
  }

  $collision_check = _install_tablenames_collide($connection, $edit);
  if ($collision_check) {
    $error_status = $collision_check;
    return FALSE;
  }
  
  if (! is_readable($edit['db_path'])) {
      $error_status = 'db_path_check';
      return  FALSE;
  }

  return TRUE;
}


function install_CMS_form($edit = array()) {
    $edit['cms_path'] = isset($edit['cms_path']) ? $edit['cms_path'] : '';
  
    $form = form_textfield('CMS path', 'cms_path', $edit['cms_path'], 40, 255, '<span class="tip"><strong>Tip</strong>: Enter your CMS path. eg: /opt/apache/htdocs/drupal</span>', NULL, TRUE);

    if (isset($_POST['op']) && ($_POST['op'] == 'Retry' || $_POST['op'] == 'Proceed')) {
        $form .= form_submit('Retry');
    }
    else {
        $form .= form_submit('Proceed');
    }
    return form($form);
}

function install_test_cmsinfo(&$edit, &$error_status) {
  $edit['cms_path'] = isset($edit['cms_path']) ? trim($edit['cms_path']) : '';
  
  $dummy_array = array();

  if ( !strlen(trim($edit['cms_path'])) || !is_dir($edit['cms_path']) ) {
      $error_status = 'invalid_cms_path';
      return FALSE;
  }
  
  return TRUE;
}

function _install_add_prefix_underscore($prefix) {
  if ($prefix) {
    if (substr(strrev($prefix), 0, 1) == '_') {
      return $prefix;
    }
    else {
      return $prefix . '_';
    }
  }
}


/**
 * Queries database to see if table name collision occurs given DB info submitted in $edit parameter.
 *
 * Returns string:
 *   'failure' if query for existing tables in the database $edit['db_db'] does not go through for some reason
 *        (e.g., lack of permissions for the account to execute a "show tables" query).
 *   '' if no collision occurs.
 *   'collision' if collision occurs.
 */
function _install_tablenames_collide($connection, $edit) {
  $table_names = array('access', 'accesslog', 'aggregator_category', 'aggregator_category_feed', 'aggregator_category_item', 'aggregator_feed', 'aggregator_item', 'authmap', 'blocks', 'book', 'boxes', 'buddylist', 'cache', 'comments', 'contact', 'contact_data', 'contact_search', 'contact_sources', 'directory', 'event', 'event_field_data', 'event_item', 'files', 'filter_formats', 'filters', 'flexinode_data', 'flexinode_field', 'flexinode_type', 'form_fields', 'forms', 'forum', 'history', 'htmlarea', 'image', 'listhandler', 'location_node', 'location_user', 'locales_meta', 'locales_source', 'locales_target', 'mailhandler', 'menu', 'moderation_filters', 'moderation_roles', 'moderation_votes', 'node', 'node_access', 'node_comment_statistics', 'node_counter', 'node_import_mappings', 'notify', 'page', 'permission', 'phplist_config', 'phplist_contact_searches', 'phplist_list', 'phplist_listmessage', 'phplist_listuser', 'phplist_message', 'phplist_rssitem', 'phplist_sendprocess', 'phplist_user_attribute', 'phplist_user_rss', 'phplist_user_user', 'phplist_usermessage', 'poll', 'poll_choices', 'privatemsg', 'privatemsg_archive', 'privatemsg_folder', 'profile_fields', 'profile_values', 'role', 'rsvp', 'rsvp_event', 'rsvp_to_user', 'rsvp_user_prefs', 'search_index', 'sequences', 'sessions', 'story_item', 'survey', 'survey_fields', 'survey_responses', 'system', 'term_data', 'term_hierarchy', 'term_node', 'term_relation', 'term_synonym', 'trackback_received', 'trackback_sent', 'url_alias', 'users', 'users_roles', 'variable', 'vocabulary', 'volunteer', 'volunteer_contact_event', 'watchdog', 'zipcodes');
  
  if ($edit['db_prefix']) {
    foreach ($table_names as $key => $value) {
      $table_names[$key] = $edit['db_prefix'] . $value;
    }
  }
  
  $existing_tables = array();
  $result = mysql_query('SHOW TABLES FROM '. trim($edit['db_db']));
  if (!(is_resource($result) && get_resource_type($result) == 'mysql result')) {
    return 'failure';
  }

  while($row = mysql_fetch_array($result)) {
    $existing_tables[] = $row[0];
  }
  
  if (count(array_intersect($table_names, $existing_tables))) {
    return 'collision';
  }
  else {
    return '';
  }
}

function _install_help_link($link) {
  return '<a href="'. $link .'" target="_blank">What does this mean?</a>';
}

function _install_msg_successful($message, $title = 'Success!') {
  $output  = '            <div class="message message-success">'."\n";
  $output .= '              <p>'."\n";
  $output .= '                 <strong>'. $title .'</strong>'."\n";
  $output .= '                   '. $message ."\n";
  $output .= '              </p>'."\n";
  $output .= '            </div>'."\n";
  return $output;
}


function _install_msg_failed($message, $title = 'Whoops!', $link = NULL) {
  if (!$title) {
    $title = 'Whoops!'; 
  }
  $output  = '            <div class="message message-error">'."\n";
  $output .= '              <p>'."\n";
  $output .= '                 <strong>'. $title .'</strong>'."\n";
  $output .= '                 '. $message ."\n";
  
  if ($link) {
    $output .= '                 '. _install_help_link($link) ."\n";
  }
  
  $output .= '              </p>'."\n";
  $output .= '            </div>'."\n";
  return $output;
}


function _install_msg_warning($message, $title = 'Warning', $link = NULL) {
  if (!$title) {
    $title = 'Warning';
  }
  $output  = '            <div class="message message-warning">'."\n";
  $output .= '              <p>'."\n";
  $output .= '                 <strong>'. $title .':</strong> '."\n";
  $output .= '                 '. $message ."\n";
  
  if ($link) {
    $output .= '                 '. _install_help_link($link) ."\n";
  }
  
  $output .= '              </p>'."\n";
  $output .= '            </div>'."\n";
  return $output;
}


function _install_msg_notice($message, $title = 'Notice', $link = NULL) {
  if (!$title) {
    $title = 'Notice';
  }
  $output  = '            <div class="message message-notice">'."\n";
  $output .= '              <p>'."\n";
  $output .= '                 <strong>'. $title .':</strong> '."\n";
  $output .= '                 '. $message ."\n";
  
  if ($link) {
    $output .= '                 '. _install_help_link($link) ."\n";
  }
  
  $output .= '              </p>'."\n";
  $output .= '            </div>'."\n";
  return $output;
}


function _install_msg_important($message, $title = 'Important', $link = NULL) {
  if (!$title) {
    $title = 'Important';
  }
  $output  = '            <div class="message message-important">'."\n";
  $output .= '              <p>'."\n";
  $output .= '                 <strong>'. $title .':</strong> '."\n";
  $output .= '                 '. $message ."\n";
  
  if ($link) {
    $output .= '                 '. _install_help_link($link) ."\n";
  }
  
  $output .= '              </p>'."\n";
  $output .= '            </div>'."\n";
  return $output;
}


function install_write_conf($edit) {
  $fp = fopen('sites/default/settings.php', 'w');
  fwrite($fp, _install_create_conf_file($edit));
  fclose($fp);
}


function _install_db_query($edit, $dump) {
  $insert_into_sequences = 'INSERT INTO '. $edit['db_prefix'] .'sequences';
  
  foreach (preg_split('/;\n/', preg_replace('/^--[^\n]*\n/', '', preg_replace('/(CREATE TABLE|CREATE INDEX|INSERT INTO|REPLACE) (\w+)/', '\1 '. $edit['db_prefix'] .'\2', $dump))) as $query) {
    if ($query = trim($query)) {
      // For insert statements into the {sequences} table, the string value being inserted also itself needs to be prefixed.
      // That's what the preg_replace shown directly below does.
      if ($edit['db_prefix'] && substr_count($query, $insert_into_sequences)) {
        $query = preg_replace("/INSERT INTO ([a-zA-Z0-9_]*)sequences VALUES \('([a-zA-Z_]*)'(.*)/", 'INSERT INTO \1sequences VALUES (\'\1\2\''.'\3', $query);
      }
      _db_query($query);
    }
  }
}

function _install_save_cms(&$edit){
  if (!isset($edit)) {
    $edit = array();
  }
  $_SESSION['cms_path'] = $edit['cms_path'];
  return;
}

function _setup_config(&$edit)
{
    // get the working directory of the CiviCRM module
    $flag = 1;
    getcwd();
    chdir('../'); 
    $crm_base_path = getcwd();
    $crm_module_path = $crm_base_path.'/modules/';    
    chdir('install');
    $cms_base_folder = $_SESSION['cms_path'];
    $cms_base_folder = explode(DIRECTORY_SEPARATOR,$cms_base_folder);
    //print_r($cms_base_folder);
    for( $i=0; $i < count($cms_base_folder); $i++) {
        if($cms_base_folder[$i] == "htdocs") {
            $flag = 0;
        }
        if($flag == 0) {
            if($cms_base_folder[$i+1] =='') {
                continue ;
            }
            $subpath .= "/".$cms_base_folder[$i+1];
        }
    }
    
    //$path = explode(DIRECTORY_SEPARATOR,$subpath);
    //echo implode(DIRECTORY_SEPARATOR,$path);
    //echo $subpath;
    $cms_base_folder = $subpath.DIRECTORY_SEPARATOR;

    //get the base directory for CMS system
    //$cms_base_folder = 'drupal';

    // build the db dsn
    $dsn = 'mysql://'.$edit['db_user'].':'.$edit['db_pass'].'@'.$edit['db_host'].DIRECTORY_SEPARATOR.$edit['db_db'].'?new_link=true';   

    //$file_contents = str_replace('DATABASE_DSN', $dsn, $file_contents );

    //build the config page
    $params['user_home'] = $crm_base_path;
    $params['cms_base']  = $cms_base_folder;
    $params['db_dsn']    = $dsn;

    $file_contents = _setup_config_file($params);

    // write the config file
    $handle = fopen($crm_module_path.'config.inc.php','w+');
    fwrite($handle,$file_contents);
    fclose($handle);
    system('chmod +x '.$crm_module_path.'config.inc.php');

}

function _setup_run(&$edit) 
{

    // get the CiviCRM bin path
    getcwd();
    chdir('../'); 
    $crm_base_path = getcwd();
    $crm_bin_path = $crm_base_path.'/bin/';    
    // read the bak config file
    $handle = fopen($crm_bin_path.'setup.sh.txt','r');
    $contents = fread($handle, filesize($crm_bin_path.'setup.sh.txt'));
    fclose($handle);
    
    // replace the tags defined in configure file
    $file_contents = str_replace('PASSWORD', $edit['db_pass'], $contents );

    // write the config file
    $handle = fopen($crm_bin_path.'setup.sh','w+');
    fwrite($handle,$file_contents);
    fclose($handle);

    system('chmod +x '.$crm_bin_path.'setup.sh');
    
    chdir('install');
}

function _setup_link() {

    $db_path         = $_SESSION['db_path'];
    $db_user         = $_SESSION['db_user'];
    $db_pass         = $_SESSION['db_pass'];
    $db_db           = $_SESSION['db_db'];
    $cms_path        = $_SESSION['cms_path'];
    $cms_module_path = $_SESSION['cms_path'].'/modules/';
    $cms_sql_path    = $_SESSION['cms_path'].'/sql/';
     
    $crm_path = getcwd();
    chdir($crm_path); 
    chdir("../");
    $crm_path = getcwd();
    $crm_module_path = $crm_base_path;    
    chdir($cms_module_path);
    if(is_link($cms_module_path.'civicrm')) {
        exec('unlink '.$cms_module_path. 'civicrm');
    }

    exec('ln -s '.$crm_path.' '.$cms_module_path.'civicrm');
    chdir($crm_path."/sql");
    system($db_path.'/bin/mysql -u '.$db_user.' -p'.$db_pass.' '.$db_db.' < Contacts.sql',$var1);
    system($db_path.'/bin/mysql -u '.$db_user.' -p'.$db_pass.' '.$db_db.' < FixedData.sql',$var);
    chdir($crm_path);
}

function _setup_config_file (&$params) 
{
    $contents = '<?php'."\n";

    $contents .= 'global $user_home;'."\n";
    $contents .= '// this is the path where you have installed the civicrm code'."\n";
    $contents .= '$user_home = \''.$params['user_home'].'\' ;'."\n\n";

    $contents .= '// these variables define the absolute urls to access drupal and civicrm'."\n";
    $contents .= '// note that the trailing slash is important'."\n";
    $contents .= 'define( \'CRM_HTTPBASE\'    , \''.$params['cms_base'].                 '\' );'."\n";
    $contents .= 'define( \'CRM_RESOURCEBASE\', \''.$params['cms_base'].'modules/civicrm/\');'."\n\n";

    $contents .= '// the new_link option is super important if u r reusing the same user id across both drupal and civicrm'."\n";
    $contents .= 'define( \'CRM_DSN\'         , \''.$params['db_dsn'].'\' );'."\n\n";

    $contents .= 'define( \'CRM_LC_MESSAGES\' , \'en_US\' );'."\n\n";

    $contents .= '// this is used for formatting the various date displays. Change this to match'."\n";
    $contents .= '// your locale if different.'."\n";
    $contents .= 'define( \'CRM_DATEFORMAT_DATETIME\', \'%B %E%f, %Y %l:%M %P\' );'."\n";
    $contents .= 'define( \'CRM_DATEFORMAT_FULL\', \'%B %E%f, %Y\' );'."\n";
    $contents .= 'define( \'CRM_DATEFORMAT_PARTIAL\', \'%B %Y\' );'."\n";
    $contents .= 'define( \'CRM_DATEFORMAT_YEAR\', \'%Y\' );'."\n";
    $contents .= 'define( \'CRM_DATEFORMAT_QF_DATE\', \'%b %d %Y\' );'."\n";
    $contents .= 'define( \'CRM_DATEFORMAT_QF_DATETIME\', \'%b %d %Y, %I : %M %P\' );'."\n\n";

    $contents .= 'define( \'CRM_SMTP_SERVER\' , \'YOUR SMTP SERVER\' );'."\n\n";

    $contents .= 'include_once \'config.main.php\';'."\n\n";

    $contents .= '?>'."\n";

    return $contents;
}

function _install_dump_and_serve(&$edit) {
    if (!isset($edit)) {
        $edit = array();
    }
    
    if (count($edit)) {
        $_SESSION['db_user'] = $edit['db_user'];
        $_SESSION['db_pass'] = $edit['db_pass'];
        $_SESSION['db_host'] = $edit['db_host'];
        $_SESSION['db_db']   = $edit['db_db'];
        $_SESSION['db_path'] = $edit['db_path'];
        $_SESSION['db_prefix'] = $edit['db_prefix'];
    } else {
        $edit['db_user'] = $_SESSION['db_user'];
        $edit['db_pass'] = $_SESSION['db_pass'];
        $edit['db_host'] = $_SESSION['db_host'];
        $edit['db_db']   = $_SESSION['db_db'];
        $edit['db_prefix']   = $_SESSION['db_prefix'];
    }
    
    _setup_config($edit);
    //_setup_run($edit);
    _setup_config_file($edit);
    _setup_link();

    print install_step_page('Finishing up');
    exit();
}


function _install_get_next_query($handle) {
  $query = '';
  if (!$handle) {
    return NULL;
  }
  while ($nextline = fgets($handle)) {
    if (!(substr(trim($nextline),0,2) == '--' || trim($nextline) == '')) {
      $query .= (strlen($query) ? "\n" : '') . $nextline;
      if (substr(trim(strrev($query)), 0, 1) == ';') {
        return $query;
      }
    }
  }
  
  return $query;
}


/**
 * A page to serve when dumping files.
 *
 * Not sure if I need it yet.
 */
function _install_dump_interstitial_page($percentage) {
  $output = '';
  $output .= install_page_header('Setting up database...', $percentage);
  $output .= install_checklist('Database setup');
  $output .= install_step_main_content('Setting up database...', $percentage);
  $output .= install_page_footer();
  print $output;
}


function install_already_installed_page() {
  $output  = '';
  $output .= install_page_header('Civicrm has already been installed.');
  $output .= install_checklist('Installation already completed!');
  $output .= install_step_main_content('Installation already completed!');
  $output .= install_page_footer();
  return $output;
}


function install_step_page($title) {
  $output  = '';
  $output .= install_page_header($title);
  $output .= install_checklist($title);
  $output .= install_step_main_content($title);
  $output .= install_page_footer();
  return $output;
}


function install_step_main_content($title, $percent = NULL) {
  $edit = isset($_POST['edit']) ? $_POST['edit'] : NULL;

  $output  = "\n\n";
  $output .= '    <div id="container">'."\n\n";
  $output .= '      <div id="main-content">'."\n\n";
  if ($title == 'Server check-up' || $title == 'CMS check-up' || $title == 'Database setup' || $title == 'Finishing up' ) {  
    $output .= '        <h2 class="step-title hide">'. $title .'</h2>'."\n\n";
  } else {
    $output .= '        <h2 class="step-title">'. $title .'</h2>'."\n\n";
  }
  if (!$edit) {
    $output .= install_step_pretext($title);   // don't output pretext if there are messages       
  }
  $output .= install_step_messages($title);
  $output .= install_step_explanation($title, $percent);
  $output .= '        <div class="install-form">'."\n\n";
  $output .= install_step_form($title);
  $output .= '        </div>'."\n\n";
  
  $output .= '      </div>'."\n\n";  // closes <div id="main-content">
  $output .= '    </div>'."\n\n";  // closes <div id="container">
  return $output;
}

function install_step_pretext($title) {
  $output = "\n";
  switch ($title) {
  case 'Introduction':
      $output .= ''."\n";
      break;
  case 'CMS check-up':
      $output .= '<p>Enter the absolute path of your CMS. </p>';
      break;
  case 'Server check-up':
      $output .= '<p>We\'re going to run a few checks to make sure that your server is setup correctly for Civicrm. Here we go!</p>';
      break;
  case 'Database setup':
      $output .= '<p>Ok, now that your server is all set, we\'re going to setup the connection to your database. If you need help, feel free to refer to these <a href="javascript:alert(\'detailed instructions\');">detailed instructions</a>.</p>';
      $output .= '<p>Let\'s start with your database username and password:</p>';
      break;
  case 'Setting up database...':
      $output .= "\n";
      break;
      case 'Finishing up';
      $output .= "\n";
      break;
  }
  return $output ."\n\n";
}

function install_step_explanation($title, $percent = NULL) {
    $output = "\n";
    switch ($title) {
    case 'Introduction':
        $output .= '        <p>Welcome to Civicrm! We\'re dedicated to making your experience as pleasant as possible and encourage you to send us your feedback  (once the installation process has been completed!).</p>'."\n";
        $output .= '        <p>But, before we begin, we need to take care of a few nerdy details:'."\n";
        $output .= '        </p>'."\n\n";
        $output .=          theme('item_list', array('First, you will need a <strong>valid username and password</strong> for your database. ',
                          'Second, you will need to know drupal path', 
                                                     'Finally, you need to <strong>set some file permissions</strong>: <ul style="margin-bottom: 1.2em;">
                          <li><code>modules/config.inc.php</code> must be writable</li>
                          <li>the <code>CRM-Home/modules/</code> directory must be writable and readable</li> 
                          <li>the <code>bin/</code> directory must be writable and readable</li> 
                          <!--li>the <code>sql/</code> directory must be writable and readable</li> 
                          <li>In your <code>CMS/modules/<code> directory must be writable.</li--></ul>'))."\n";
        break;
    case 'CMS check-up':
        $output .=          theme('item_list', array('Your <code>CMS path/modules/<code> directory must be writable.'))."\n";

        $output .= ''."\n";
        break;
    case 'Server check-up':
      $output .= ''."\n";
      break;
    case 'Database setup':
      break;
    case 'Setting up database...':
      $output .= "\n\n";
      $output .= '     <p>The database for your Civicrm site is currently being populated. Please wait as this process may take several minutes...</p>'."\n";
      $output .= '     <h3>'. $percent .'% done...</h3>'."\n";
      $output .= '     <div class="progressbar">'."\n";
      $output .= '       <div style="width: '. $percent .'%;" class="foreground"></div>'."\n";
      $output .= '     </div>'."\n";
      break;
    case 'Finishing up';
      $output .= "\n";
      break;
  }
  return $output ."\n\n";
}


// If nothing else, this form generates buttons for the next step of the installation process.
function install_step_form($title) {
    $form = '';
    switch ($title) {
    case 'Introduction':
        $form .= '        '. form(form_button("Ok, let's go!")) ."\n";
        break;
    case 'CMS check-up':
        $edit = isset($_POST['edit']) ? $_POST['edit'] : NULL;
        if ($edit) {
            $form .= install_CMS_form($edit);
        } else {
          $form .= install_CMS_form();
        }
        
        break;
    case 'Server check-up':
        $status = _install_check_server();
        if ($status['server_ok']) {
            $form .= form(form_button('Database setup'));
            //        $form .= form(form_button('Configure database'));
        }
        else {
            $form .= form(form_button('Whoops, try again!'));
        }
        break;
    case 'Database setup':
        $edit = isset($_POST['edit']) ? $_POST['edit'] : NULL;
        if ($edit) {
            $form .= install_database_form($edit);
        }
        else {
            $form .= install_database_form();
        }
        break;
    case 'Finishing up':
        // $button = form_button('Configure site...');
        // The form here is not really meant to post any data to the Civicrm configuration
        // wizard page (which is the default page that shows up at {'http://'. install_base_uri() .'/'}
        // Be careful if modifying configure.module, wizard.inc, or the index.php sequence of pages (and
        // associated logic.
        //$form .= form($button, 'POST', 'http://'. install_base_uri() .'/');
        break;
    case 'Installation already completed!':
        $button = '<input type="submit" class="form-button" value="Go to site">'."\n";
        $form .= form($button, 'GET', 'http://'. install_base_uri() .'/')."\n";
        break;
    default:
        $form .= "\n\n";
        break;
    }
    return $form;  
}


function install_step_messages($title, $edit = array()) {
    $messages = "\n";
    switch ($title) {
    case 'Installation already completed!':
        $messages .= '          <div class="messages">'."\n";
        $messages .= _install_msg_failed('The installation process has detected that Civicrm has already been installed.  You are only allowed to run the installation process once for an installation.');
        $messages .= '          </div>'."\n";
        break;
    case 'CMS check-up':
        $edit = isset($_POST['edit']) ? $_POST['edit'] : NULL;
        if ($edit) {
            $failed_status = '';
            
            if (!install_test_cmsinfo($edit, $failed_status)) {
                $messages .= "\n".'          <div class="messages">'."\n";
                if ($failed_status == 'invalid_cms_path') {
                    $messages .= _install_msg_failed('Please enter valid CMS path.', NULL, '');
                    $messages .= "\n          </div>\n";
                }
            }
        }
        break;
     
    case 'Server check-up':
      $status = _install_check_server();
      $messages .= $status['messages'];
      break;
    case 'Database setup':
      $edit = isset($_POST['edit']) ? $_POST['edit'] : NULL;
      if ($edit) {
        $failed_status = '';
        
        if (!install_test_dbinfo($edit, $failed_status)) {
          $messages .= "\n".'          <div class="messages">'."\n";
          
          if ($failed_status == 'invalid_user' || $failed_status == 'invalid_pass') {
              if ($failed_status == 'invalid_user') {
                  $messages .= _install_msg_failed('The user name you submitted for connecting to the database is not compatible with Civicrm.  Please select one that is 1-16 characters along and consists only of letters, digits, \'_\', and \'-\'.  If your database account has been set up with this user name, we recommend that you change it to the format described above if you wish to install Civicrm with this installer.', NULL, 'http://www.civicrmlabs.org/installer_help/081/invalid_dbuser');
              }
              if ($failed_status == 'invalid_pass') {
                  $messages .= _install_msg_failed('The password you submitted for connecting to the database is not compatible with Civicrm.  Please select one that consists only of letters, digits, \'_\', and \'-\'.', NULL, 'http://www.civicrmlabs.org/installer_help/081/invalid_dbpass');
              }
              $messages .= "\n          </div>\n";
          }
          else {
              if ($failed_status == 'connection') {
                  $messages .= _install_msg_failed('The installer was not able to create a connection to the database with the information you submitted.  Please make sure your database hostname, username, and password are correct.', NULL, 'http://www.civicrmlabs.org/installer_help/081/db_connection');
                  $messages .= "\n          </div>\n";
              }
              else {
                  $messages .= _install_msg_successful('The installer was able to connect to the database with the database hostname, username, and password that you submitted.');
                  
                  if ($failed_status == 'database_name') {
                      $messages .= _install_msg_failed('There is no database by the name <em>'. $edit['db_db'] .'</em>.  '. 'Please make sure you have the correct database name.', NULL, '');
                      $messages .= "\n          </div>\n";
                  }else 
                      
                      if ($failed_status == 'prefix_name') {
                      $messages .= _install_msg_failed('Please make sure you have entered the prefix for database tables.', NULL, '');
                      $messages .= "\n          </div>\n";
                      
                  }
                  else {
                      $messages .= _install_msg_successful('The installer was able to access the database specified by the database name you gave.');
                      
                      if ($failed_status == 'collision') {
                          $collision_msg = 'The installer encountered a naming collision. ';
                          
                          if (trim($edit['db_prefix']) != '') {
                              $collision_msg .= ' You should select a prefix different from <em>'. $edit['db_prefix'] .'</em> since table names with this prefix already exist. ';
                          }
                          else {
                              $collision_msg .= ' You should enter a prefix since one or more non-prefixed table names refer to existing tables. ';
                          }
                          $messages .= _install_msg_failed($collision_msg, NULL, 'http://www.civicrmlabs.org/installer_help/081/prefix_collision');
                      }
                      else { // By process of elimination, error is failure to run "show tables" in _install_tablenames_collide.
                          $messages .= _install_msg_failed('The installer could not query the database to see if conflicting table names already exist.  This problem may be occurring due to a lack of privileges for your database account.', NULL, 'http://www.civicrmlabs.org/installer_help/081/prefix_collision');
                      }

                      if ($failed_status == 'db_path_check') {
                          $messages .= _install_msg_failed('The <code>'.$edit['db_path'].'/</code> directory is not readable.', NULL, '');
                      }
                      
                      $messages .= "\n          </div>\n";
                  }
              }
          }
        }
      }
      break;
    case 'Finishing up':
      if ($_POST['op'] == 'Cancel installation') {
        $messages .= '          <div class="messages">'."\n";
        $messages .= _install_msg_failed('You have chosen to abort the installation process. You may run this installation process at a later time or, if you wish, attempt to install Civicrm manually at a later time.');
        $messages .= '          </div>'."\n";
      }
      else {
        $messages .= '          <div class="messages">'."\n";
        $messages .= _install_msg_successful('Your installation is complete. You are now ready to proceed to your site\'s configuration.');
        $messages .= _install_msg_important('You now need to <strong style="color:#005FB9;">make sure to turn off write permissions to <code>modules/config.inc.php</code></strong>.');
        $messages .= '          </div>'."\n";
      }
      break;
    default:
      break;
  }
  return $messages;
}


function _install_check_server() {
    $status = array('server_ok' => TRUE, 'messages' => '');
    $status['messages'] .= '          <div class="messages">'."\n";
    $status['messages'] .= '            '. install_warning_messages() ."\n";
        
    // need to clear PHP's stat cache for files since they might be changing between page serves
    clearstatcache();
    /*      
     // check for safe mode
  if (ini_get('safe_mode')) {
    $status['messages'] .= _install_msg_failed('Your web-server\'s PHP is configured with "safe_mode" set to "On". Civicrm relies on "safe_mode" being off for several functions.', NULL, 'http://www.civicrmlabs.org/home/installer_help/081/safe_mode');
    // TODO: make a page to link to for this error message
  }
  else {
    $status['messages'] .= _install_msg_successful('PHP\'s "safe_mode" is set to "Off".');
  }
  */
  /*
  // check that 'config.inc.php' is writable
  if (!is_writable('../modules/config.inc.php')) {
    $status['server_ok'] = FALSE;
    $status['messages'] .= _install_msg_failed('<code>/modules/config.inc.php</code> is not writeable.', NULL, '') ."\n";
  }
  else {
    $status['messages'] .= _install_msg_successful('<code>/modules/config.inc.php</code> is writable. <p>Please <strong>make sure you turn off write permissions to this file when you are done with the installation</strong>. (We\'ll remind you at the end of the installation.)</p>');
  }
  */
  
  // check that the ../modules directory is writable
    if (!(is_writable('../modules') && is_readable('../modules'))) {
        $status['server_ok']  = FALSE;
        $status['messages'] .= _install_msg_failed('The <code>CRM-Home/modules/</code> directory is not readable and writeable.', NULL, '');
    }
    else {
        $status['messages'] .= _install_msg_successful('The <code>CRM-Home/modules/</code> directory is readable and writable.');
    }
    /*
    // check that the ../bin directory is writable
    if (!(is_writable('../bin') && is_readable('../bin'))) {
        $status['server_ok']  = FALSE;
        $status['messages'] .= _install_msg_failed('The <code>bin/</code> directory is not readable and writeable.', NULL, '');
    }
    else {
        $status['messages'] .= _install_msg_successful('The <code>bin/</code> directory is readable and writable.');
    }
    */
    
    // check that the ../sql directory is writable
    if (!is_readable('../sql')) {
        $status['server_ok']  = FALSE;
        $status['messages'] .= _install_msg_failed('The <code>sql/</code> directory is not readable and writeable.', NULL, '');
    }
    else {
        $status['messages'] .= _install_msg_successful('The <code>sql/</code> directory is readable and writable.');
    }
    
   

    $cms_path = $_SESSION['cms_path'];
    // check that the ../bin directory is writable
    if (!(is_writable($cms_path.'/modules') && is_readable($cms_path.'/modules'))) {
        $status['server_ok']  = FALSE;
        $status['messages'] .= _install_msg_failed('The <code>'.$cms_path.'/modules</code> directory is not readable and writeable.', NULL, '');
    }
    else {
        $status['messages'] .= _install_msg_successful('The <code>'.$cms_path.'/modules</code> directory is readable and writable.');
    }

    $status['messages'] .= "          </div>\n";
    return $status;
}


// *************************************************************************************
// This is where the actual HTTP requests of the install procedure actually
// start getting processed.  Everything above this are function defs for
// functions that are referenced below.
// *************************************************************************************

//include_once('sites/default/settings.php');

ini_set('session.save_handler', 'files');
session_start();

if (install_conf_written_before()) {
  print install_already_installed_page();
  exit;
}

$op = isset($_POST['op']) ? $_POST['op'] : NULL;
$op = $op ? $op : (isset($_GET['op']) ? $_GET['op'] : NULL);

if (!$op) {
  print install_step_page('Introduction');
}
else {
    switch ($op) {
        // Some clients (browsers) might send back a "\'" "'"
    case "Ok, let\'s go!":
    case "Ok, let's go!":
    case 'CMS check-up':
        // case 'Enter you CMS path':
        print install_step_page('CMS check-up');
        break;
    case 'Proceed':
    case 'Retry':
        $edit = $_POST['edit'];
        $failed_message = '';

        if (install_test_cmsinfo($edit, $failed_message)) {
            $cms_path = $edit['cms_path'];
            _install_save_cms($edit);
            print install_step_page('Server check-up');
        }
        else {
            print install_step_page('CMS check-up');
        }
        break;

    case 'Whoops, try again!':
        print install_step_page('Server check-up');
        //session_destroy();
        break;
    case 'Database setup':
        // case 'Configure database':
        print install_step_page('Database setup');
        break;
    case 'Create tables':
    case 'Retry table creation':
        $edit = $_POST['edit'];
        $failed_message = '';
        if (install_test_dbinfo($edit, $failed_message)) {
            $db_url = $edit['db_type'] .'://'. $edit['db_user'] .':'. $edit['db_pass'] .'@'. $edit['db_host'] .'/'. $edit['db_db'];
            $db_prefix = $edit['db_prefix'];
            //install_create_tables($edit);
            _install_dump_and_serve($edit);
            //install_write_conf($edit);
            //print install_step_page('Finishing up');
        }
        else {
            print install_step_page('Database setup');
        }
        break;
    case 'continue_dump':
        $edit['db_user'] = $_SESSION['db_user'];
        $edit['db_pass'] = $_SESSION['db_pass'];
        $edit['db_host'] = $_SESSION['db_host'];
        $edit['db_prefix'] = $_SESSION['db_prefix'];
        $edit['db_db'] = $_SESSION['db_db'];
        $edit['db_type'] = $_SESSION['db_type'];
        // $edit['use_us_zipcodes'] = $_SESSION['use_us_zipcodes'];
        $edit['install_docs'] = $_SESSION['install_docs'];
        $db_url = $edit['db_type'] .'://'. $edit['db_user'] .':'. $edit['db_pass'] .'@'. $edit['db_host'] .'/'. $edit['db_db'];
        $db_prefix = $edit['db_prefix'];
        if (!isset($_SESSION['continue_dump']) || !$_SESSION['continue_dump']) {
            header('Location: http://'. install_base_uri() .'/index.php');
            exit();
        }
        _install_dump_and_serve($edit);
        break;
    case 'Cancel installation':
        print install_step_page('Finishing up');
        break;
    }
}

?>
