<?php
/* $Id: drupal_test_case.php,v 1.56 2008/03/17 01:36:33 boombatower Exp $ */

/**
 * Test case for typical Drupal tests.
 * Extends WebTestCase for comfortable browser usage
 * but also implements all UnitTestCase methods, I wish
 * WebTestCase would do this.
 */
class DrupalTestCase extends WebTestCase {
  var $_content;
  var $_originalModules     = array();
  var $_modules             = array();
  var $_cleanupVariables    = array();
  var $_cleanupUsers        = array();
  var $_cleanupRoles        = array();
  var $_cleanupNodes        = array();
  var $_cleanupContentTypes = array();


  function DrupalTestCase($label = NULL) {
    if (! $label) {
      if (method_exists($this, 'get_info')) {
        $info  = $this->get_info();
        $label = $info['name'];
      }
    }
    $this->WebTestCase($label);
  }
  
  /**
   * Creates a node based on default settings.
   *
   * @param settings An array of settings to change from the defaults, in the form of 'body' => 'Hello, world!'
   */
  function drupalCreateNode($settings = array()) {
 
    // Populate defaults array
    $defaults = array(
      'body'      => $this->randomName(32),
      'title'     => $this->randomName(8),
      'comment'   => 2,
      'changed'   => time(),
      'format'    => FILTER_FORMAT_DEFAULT,
      'moderate'  => 0,
      'promote'   => 0,
      'revision'  => 1,
      'log'       => '',
      'status'    => 1,
      'sticky'    => 0,
      'type'      => 'page',
      'revisions' => NULL,
      'taxonomy'  => NULL,
    );
    $defaults['teaser'] = $defaults['body'];
    // If we already have a node, we use the original node's created time, and this
    if (isset($defaults['created'])) {
      $defaults['date'] = format_date($defaults['created'], 'custom', 'Y-m-d H:i:s O');
    }
    
    if (empty($settings['uid'])) {
      global $user;
      $defaults['uid'] = $user->uid;
    }
    $node = ($settings + $defaults);
    $node = (object)$node;
 
    node_save($node);
    
    // small hack to link revisions to our test user
    db_query('UPDATE {node_revisions} SET uid = %d WHERE vid = %d', $node->uid, $node->vid);
    $this->_cleanupNodes[] = $node->nid;
    return $node;
  }

  /**
   * Creates a custom content type based on default settings.
   *
   * @param settings An array of settings to change from the defaults, in the form of 'type' => 'foo'
   */
  function drupalCreateContentType($settings = array()) {
    // find a non-existent random type name.
    do {
      $name = strtolower($this->randomName(3, 'type_'));
    } while (node_get_types('type', $name));

    // Populate defaults array
    $defaults = array(
      'type' => $name,
      'name' => $name,
      'description' => '',
      'help' => '',
      'min_word_count' => 0,
      'title_label' => 'Title',
      'body_label' => 'Body',
      'has_title' => 1,
      'has_body' => 1,
    );
    // imposed values for a custom type
    $forced = array(
      'orig_type' => '',
      'old_type' => '',
      'module' => 'node',
      'custom' => 1,
      'modified' => 1,
      'locked' => 0,
    );
    $type = $forced + $settings + $defaults;
    $type = (object)$type;

    node_type_save($type);
    node_types_rebuild();

    $this->_cleanupContentTypes[] = $type->type;
    return $type;
  }

  /**
   * @abstract Checks to see if we need to send
   * a http-auth header to authenticate
   * when browsing a site.
   *
   * @param status Boolean pass true if you want to know if we are using
   * HTTP-AUTH
   * @return void
   */
  function drupalCheckAuth($status = false) {
    $check = variable_get('simpletest_httpauth', false);
    if( $status ) {
      return $check;
    }
    if( variable_get('simpletest_httpauth', false) ) {
      $html = $this->authenticate(variable_get('simpletest_httpauth_username', ''), variable_get('simpletest_httpauth_pass', ''));
    }
    return $html;
  }

  /**
   * @abstract Broker for the get function
   * adds the authentication headers if necessary
   * @author Earnest Berry III <earnest.berry@gmail.com>
   *
   * @param $path string Drupal path or url to load into internal browser
   * @param array $options Options to be forwarded to url().
   * @return void
   */
  function drupalGet($path, $options = array()) {
    $url = url($path, array_merge($options, array('absolute' => TRUE)));
    $html = $this->_browser->get($url);

    if ($this->drupalCheckAuth(true)) {
      $html .= $this->drupalCheckAuth();
    }

    $this->_content = $this->_browser->getContent();

    return $html;
  }

  /**
   * @abstract Broker for the post function
   * adds the authentication headers if
   * necessary
   * @author Earnest Berry III <earnest.berry@gmail.com>
   *
   * @param url string Url to retch
   * @return void
   */
  function drupalRawPost($action, $edit = array()) {
    $html = $this->_browser->post($action, $edit);

    if( $this->drupalCheckAuth(true) ) {
      $html .= $this->drupalCheckAuth();
    }

    $this->_content = $this->_browser->getContent();

    return $html;
  }



  /**
   * Do a post request on a drupal page.
   * It will be done as usual post request with SimpleBrowser
   * By $reporting you specify if this request does assertions or not
   * Warning: empty ("") returns will cause fails with $reporting
   *
   * @param string  $path
   *   Location of the post form. Either a Drupal path or an absolute path or
   *   NULL to post to the current page.
   * @param array $edit
   *   Field data in an assocative array. Changes the current input fields
   *   (where possible) to the values indicated. A checkbox can be set to
   *   TRUE to be checked and FALSE to be unchecked.
   * @param string $submit
   *   Untranslated value, id or name of the submit button.
   */
  function drupalPost($path, $edit = array(), $submit) {
    if (isset($path)) {
      $ret = $this->drupalGet($path);
      $this->assertTrue($ret, t(' [browser] GET path "@path"', array('@path' => $path)));
    }

    foreach ($edit as $field_name => $field_value) {
      $ret = $this->_browser->setFieldByName($field_name, $field_value)
          || $this->_browser->setFieldById("edit-$field_name", $field_value);
      $this->assertTrue($ret, " [browser] Setting $field_name=\"$field_value\"");
    }

    $ret = $this->_browser->clickSubmit(t($submit))  || $this->_browser->clickSubmitById($submit) || $this->_browser->clickSubmitByName($submit) || $this->_browser->clickImageByName($submit);
    $this->assertTrue($ret, ' [browser] POST by click on ' . t($submit));
    $this->_content = $this->_browser->getContent();
  }

  /**
   *    Follows a link by name.
   *
   *    Will click the first link found with this link text by default, or a
   *    later one if an index is given. Match is case insensitive with
   *    normalized space. The label is translated label. There is an assert
   *    for successful click.
   *    WARNING: Assertion fails on empty ("") output from the clicked link
   *
   *    @param string $label      Text between the anchor tags.
   *    @param integer $index     Link position counting from zero.
   *    @param boolean $reporting Assertions or not
   *    @return boolean/string    Page on success.
   *
   *    @access public
   */
  function clickLink($label, $index = 0) {
    $url_before = str_replace('%', '%%', $this->getUrl());
    $urls = $this->_browser->_page->getUrlsByLabel($label);
    if (count($urls) < $index + 1) {
      $url_target = 'URL NOT FOUND!';
    } else {
      $url_target = str_replace('%', '%%', $urls[$index]->asString());
    }

    $ret = parent::clickLink(t($label), $index);

    $this->assertTrue($ret, ' [browser] clicked link '. t($label) . " ($url_target) from $url_before");

    return $ret;
  }

  /**
   * @TODO: needs documentation
   */
  function drupalGetContent() {
    return $this->_content;
  }

  /**
   * Generates a random string, to be used as name or whatever
   * @param integer $number   number of characters
   * @return random string
   */
  function randomName($number = 4, $prefix = 'simpletest_') {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
    for ($x = 0; $x < $number; $x++) {
        $prefix .= $chars{mt_rand(0, strlen($chars)-1)};
        if ($x == 0) {
            $chars .= '0123456789';
        }
    }
    return $prefix;
  }

  /**
   * Enables a drupal module
   * @param string $name name of the module
   * @return boolean success
   */
  function drupalModuleEnable($name) {
  	if (module_exists($name)) {
      $this->pass(" [module] $name already enabled");
      return TRUE;
    }
    $this->checkOriginalModules();
    if (array_search($name, $this->_modules) === FALSE) {
      $this->_modules[$name] = $name;
      $form_state['values'] = array('status' => $this->_modules, 'op' => t('Save configuration'));
      drupal_execute('system_modules', $form_state);
      
      //rebuilding all caches
      drupal_rebuild_theme_registry();
      node_types_rebuild();
      menu_rebuild();
      cache_clear_all('schema', 'cache');
      module_rebuild_cache();
    }
  }


  /**
   * Disables a drupal module
   * @param string $name name of the module
   * @return boolean success
   */
  function drupalModuleDisable($name) {
    if (!module_exists($name)) {
      $this->pass(" [module] $name already disabled");
      return TRUE;
    }
    $this->checkOriginalModules();
    if (($key = array_search($name, $this->_modules)) !== FALSE) {
      unset($this->_modules[$key]);
      $form_state['values'] = array('status' => $this->_modules, 'op' => t('Save configuration'));
      drupal_execute('system_modules', $form_state);
      
      //rebuilding all caches
      drupal_rebuild_theme_registry();
      node_types_rebuild();
      menu_rebuild();
      cache_clear_all('schema', 'cache');
      module_rebuild_cache();
    }
  }

  /**
   * Retrieves and saves current modules list into $_originalModules and $_modules.
   */
  function checkOriginalModules() {
    if (empty($this->_originalModules)) {
      require_once ('./modules/system/system.admin.inc');
      $form_state = array();
      $form = drupal_retrieve_form('system_modules', $form_state);
      $this->_originalModules = drupal_map_assoc($form['status']['#default_value']);
      $this->_modules = $this->_originalModules;
    }
  }
  
  /**
   * Set a drupal variable and keep track of the changes for tearDown()
   * @param string $name name of the value
   * @param mixed  $value value
   */
  function drupalVariableSet($name, $value) {
    /* NULL variables would anyways result in default because of isset */
    $old_value = variable_get($name, NULL);
    if ($value !== $old_value) {
      variable_set($name, $value);
      /* Use array_key_exists instead of isset so NULL values do not get overwritten */
      if (!array_key_exists($name, $this->_cleanupVariables)) {
        $this->_cleanupVariables[$name] = $old_value;
      }
    }
  }


  /**
   * Create a role / perm combination specified by permissions
   *
   * @param  array $permissions Array of the permission strings
   * @return integer role-id
   */
  function drupalCreateRolePerm($permissions = NULL) {
    if ($permissions === NULL) {
      $permstring = 'access comments, access content, post comments, post comments without approval';
    } else {
      $permstring = implode(', ', $permissions);
    }
    /* Create role */
    $role_name = $this->randomName();
    db_query("INSERT INTO {role} (name) VALUES ('%s')", $role_name);
    $role = db_fetch_object(db_query("SELECT * FROM {role} WHERE name = '%s'", $role_name));
    $this->assertTrue($role, " [role] created name: $role_name, id: " . (isset($role->rid) ? $role->rid : '-n/a-'));
    if ($role && !empty($role->rid)) {
      /* Create permissions */
      db_query("INSERT INTO {permission} (rid, perm) VALUES (%d, '%s')", $role->rid, $permstring);
      $this->assertTrue(db_affected_rows(), ' [role] created permissions: ' . $permstring);
      $this->_cleanupRoles[] = $role->rid;
      return $role->rid;
    } else {
      return false;
    }
  }

  /**
   * Creates a user / role / permissions combination specified by permissions
   *
   * @param  array $permissions Array of the permission strings
   * @return array/boolean false if fails. fully loaded user object with added pass_raw property
   */
  function drupalCreateUserRolePerm($permissions = NULL) {
    /* Create role */
    $rid = $this->drupalCreateRolePerm($permissions);
    if (!$rid) {
      return FALSE;
    }
    /* Create user */
    $ua = array();
    $ua['name']   = $this->randomName();
    $ua['mail']   = $ua['name'] . '@example.com';
    $ua['roles']  = array($rid=>$rid);
    $ua['pass']   = user_password();
    $ua['status'] = 1;

    $u = user_save('', $ua);

    $this->assertTrue(!empty($u->uid), " [user] name: $ua[name] pass: $ua[pass] created");
    if (empty($u->uid)) {
      return FALSE;
    }

    /* Add to cleanup list */
    $this->_cleanupUsers[] = $u->uid;

    /* Add the raw password */
    $u->pass_raw = $ua['pass'];
    return $u;
  }

  /**
   * Logs in a user with the internal browser
   *
   * @param object user object with pass_raw property!
   * @param $submit value of submit button on log in form
   */
  function drupalLoginUser($user = NULL, $submit = 'Log in') {

    $this->drupalGet('user');
    // Going to the page retrieves the cookie, as the browser should save it

    if ($user === NULL) {
      $user = $this->drupalCreateUserRolePerm();
    }

    $edit = array('name' => $user->name, 'pass' => $user->pass_raw);
    $this->drupalPost('user', $edit, $submit);

    $this->assertText( $user->name, ' [login] found name: ' . $user->name);
    $this->assertNoText(t('The username %name has been blocked.', array('%name' => $user->name)), ' [login] not blocked');
    $this->assertNoText(t('The name %name is a reserved username.', array('%name' => $user->name)), ' [login] not reserved');

    return $user;
  }

  /**
   * tearDown implementation, setting back switched modules etc
   */
  function tearDown() {

    if ($this->_modules != $this->_originalModules) {
      $form_state['values'] = array('status' => $this->_originalModules, 'op' => t('Save configuration'));
      drupal_execute('system_modules', $form_state);
      
      //rebuilding all caches
      drupal_rebuild_theme_registry();
      node_types_rebuild();
      menu_rebuild();
      cache_clear_all('schema', 'cache');
      module_rebuild_cache();
    
      $this->_modules = $this->_originalModules; 
    }

    foreach ($this->_cleanupVariables as $name => $value) {
      if (is_null($value)) {
        variable_del($name);
      } else {
        variable_set($name, $value);
      }
    }
    $this->_cleanupVariables = array();
    
    //delete nodes
    foreach ($this->_cleanupNodes as $nid) {
      node_delete($nid);
    }
    $this->_cleanupNodes = array();

    //delete roles
    while (sizeof($this->_cleanupRoles) > 0) {
      $rid = array_pop($this->_cleanupRoles);
      db_query("DELETE FROM {role} WHERE rid = %d",       $rid);
      db_query("DELETE FROM {permission} WHERE rid = %d", $rid);
    }

    //delete users and their content
    while (sizeof($this->_cleanupUsers) > 0) {
      $uid = array_pop($this->_cleanupUsers);
      // cleanup nodes this user created
      $result = db_query("SELECT nid FROM {node} WHERE uid = %d", $uid);
      while ($node = db_fetch_array($result)) {
        node_delete($node['nid']);
      }
      user_delete(array(), $uid);
    }

    //delete content types
    foreach ($this->_cleanupContentTypes as $type) {
      node_type_delete($type);
    }
    $this->_cleanupContentTypes = array();

    //Output drupal warnings and messages into assert messages
    $drupal_msgs = drupal_get_messages();
    foreach($drupal_msgs as $type => $msgs) {
      foreach ($msgs as $msg) {
        $this->assertTrue(TRUE, "$type: $msg");
      }
    }

    parent::tearDown();
  }

  /**
   * Just some info for the reporter
   */
  function run(&$reporter) {
    $arr = array('class' => get_class($this));
    if (method_exists($this, 'get_info')) {
      $arr = array_merge($arr, $this->get_info());
    }
    $reporter->test_info_stack[] = $arr;
    parent::run($reporter);
    array_pop($reporter->test_info_stack);
  }


        /**
         *    Will trigger a pass if the raw text is found on the loaded page
         *    Fail otherwise.
         *    @param string $raw        Raw string to look for
         *    @param string $message    Message to display.
         *    @return boolean           True on pass
         *    @access public
         */
        function assertWantedRaw($raw, $message = "%s") {
          return $this->assertExpectation(
                  new TextExpectation($raw),
                  $this->_browser->getContent(),
                  $message);
        }


        /**
         *    Will trigger a pass if the raw text is NOT found on the loaded page
         *    Fail otherwise.
         *    @param string $raw        Raw string to look for
         *    @param string $message    Message to display.
         *    @return boolean           True on pass
         *    @access public
         */
        function assertNoUnwantedRaw($raw, $message = "%s") {
          return $this->assertExpectation(
                  new NoTextExpectation($raw),
                  $this->_browser->getContent(),
                  $message);
        }
  /* Taken from UnitTestCase */
        /**
         *    Will be true if the value is null.
         *    @param null $value       Supposedly null value.
         *    @param string $message   Message to display.
         *    @return boolean                        True on pass
         *    @access public
         */
        function assertNull($value, $message = "%s") {
            $dumper = &new SimpleDumper();
            $message = sprintf(
                    $message,
                    "[" . $dumper->describeValue($value) . "] should be null");
            return $this->assertTrue(! isset($value), $message);
        }

        /**
         *    Will be true if the value is set.
         *    @param mixed $value           Supposedly set value.
         *    @param string $message        Message to display.
         *    @return boolean               True on pass.
         *    @access public
         */
        function assertNotNull($value, $message = "%s") {
            $dumper = &new SimpleDumper();
            $message = sprintf(
                    $message,
                    "[" . $dumper->describeValue($value) . "] should not be null");
            return $this->assertTrue(isset($value), $message);
        }

        /**
         *    Type and class test. Will pass if class
         *    matches the type name or is a subclass or
         *    if not an object, but the type is correct.
         *    @param mixed $object         Object to test.
         *    @param string $type          Type name as string.
         *    @param string $message       Message to display.
         *    @return boolean              True on pass.
         *    @access public
         */
        function assertIsA($object, $type, $message = "%s") {
            return $this->assertExpectation(
                    new IsAExpectation($type),
                    $object,
                    $message);
        }

        /**
         *    Type and class mismatch test. Will pass if class
         *    name or underling type does not match the one
         *    specified.
         *    @param mixed $object         Object to test.
         *    @param string $type          Type name as string.
         *    @param string $message       Message to display.
         *    @return boolean              True on pass.
         *    @access public
         */
        function assertNotA($object, $type, $message = "%s") {
            return $this->assertExpectation(
                    new NotAExpectation($type),
                    $object,
                    $message);
        }

        /**
         *    Will trigger a pass if the two parameters have
         *    the same value only. Otherwise a fail.
         *    @param mixed $first          Value to compare.
         *    @param mixed $second         Value to compare.
         *    @param string $message       Message to display.
         *    @return boolean              True on pass
         *    @access public
         */
        function assertEqual($first, $second, $message = "%s") {
            return $this->assertExpectation(
                    new EqualExpectation($first),
                    $second,
                    $message);
        }

        /**
         *    Will trigger a pass if the two parameters have
         *    a different value. Otherwise a fail.
         *    @param mixed $first           Value to compare.
         *    @param mixed $second          Value to compare.
         *    @param string $message        Message to display.
         *    @return boolean               True on pass
         *    @access public
         */
        function assertNotEqual($first, $second, $message = "%s") {
            return $this->assertExpectation(
                    new NotEqualExpectation($first),
                    $second,
                    $message);
        }

        /**
         *    Will trigger a pass if the two parameters have
         *    the same value and same type. Otherwise a fail.
         *    @param mixed $first           Value to compare.
         *    @param mixed $second          Value to compare.
         *    @param string $message        Message to display.
         *    @return boolean               True on pass
         *    @access public
         */
        function assertIdentical($first, $second, $message = "%s") {
            return $this->assertExpectation(
                    new IdenticalExpectation($first),
                    $second,
                    $message);
        }

        /**
         *    Will trigger a pass if the two parameters have
         *    the different value or different type.
         *    @param mixed $first           Value to compare.
         *    @param mixed $second          Value to compare.
         *    @param string $message        Message to display.
         *    @return boolean               True on pass
         *    @access public
         */
        function assertNotIdentical($first, $second, $message = "%s") {
            return $this->assertExpectation(
                    new NotIdenticalExpectation($first),
                    $second,
                    $message);
        }

        /**
         *    Will trigger a pass if both parameters refer
         *    to the same object. Fail otherwise.
         *    @param mixed $first           Object reference to check.
         *    @param mixed $second          Hopefully the same object.
         *    @param string $message        Message to display.
         *    @return boolean               True on pass
         *    @access public
         */
        function assertReference(&$first, &$second, $message = "%s") {
            $dumper = &new SimpleDumper();
            $message = sprintf(
                    $message,
                    "[" . $dumper->describeValue($first) .
                            "] and [" . $dumper->describeValue($second) .
                            "] should reference the same object");
            return $this->assertTrue(
                    SimpleTestCompatibility::isReference($first, $second),
                    $message);
        }

        /**
         *    Will trigger a pass if both parameters refer
         *    to different objects. Fail otherwise.
         *    @param mixed $first           Object reference to check.
         *    @param mixed $second          Hopefully not the same object.
         *    @param string $message        Message to display.
         *    @return boolean               True on pass
         *    @access public
         */
        function assertCopy(&$first, &$second, $message = "%s") {
            $dumper = &new SimpleDumper();
            $message = sprintf(
                    $message,
                    "[" . $dumper->describeValue($first) .
                            "] and [" . $dumper->describeValue($second) .
                            "] should not be the same object");
            return $this->assertFalse(
                    SimpleTestCompatibility::isReference($first, $second),
                    $message);
        }

        /**
         *    Will trigger a pass if the Perl regex pattern
         *    is found in the subject. Fail otherwise.
         *    @param string $pattern    Perl regex to look for including
         *                              the regex delimiters.
         *    @param string $subject    String to search in.
         *    @param string $message    Message to display.
         *    @return boolean           True on pass
         *    @access public
         */
        function assertWantedPattern($pattern, $subject, $message = "%s") {
            return $this->assertExpectation(
                    new WantedPatternExpectation($pattern),
                    $subject,
                    $message);
        }

        /**
         *    Will trigger a pass if the Perl regex pattern
         *    is not present in subject. Fail if found.
         *    @param string $pattern    Perl regex to look for including
         *                              the regex delimiters.
         *    @param string $subject    String to search in.
         *    @param string $message    Message to display.
         *    @return boolean           True on pass
         *    @access public
         */
        function assertNoUnwantedPattern($pattern, $subject, $message = "%s") {
            return $this->assertExpectation(
                    new UnwantedPatternExpectation($pattern),
                    $subject,
                    $message);
        }

        /**
         *    Confirms that no errors have occurred so
         *    far in the test method.
         *    @param string $message    Message to display.
         *    @return boolean           True on pass
         *    @access public
         */
        function assertNoErrors($message = "%s") {
            $queue = &SimpleErrorQueue::instance();
            return $this->assertTrue(
                    $queue->isEmpty(),
                    sprintf($message, "Should be no errors"));
        }

        /**
         *    Confirms that an error has occurred and
         *    optionally that the error text matches exactly.
         *    @param string $expected   Expected error text or
         *                              false for no check.
         *    @param string $message    Message to display.
         *    @return boolean           True on pass
         *    @access public
         */
        function assertError($expected = false, $message = "%s") {
            $queue = &SimpleErrorQueue::instance();
            if ($queue->isEmpty()) {
                $this->fail(sprintf($message, "Expected error not found"));
                return;
            }
            list($severity, $content, $file, $line, $globals) = $queue->extract();
            $severity = SimpleErrorQueue::getSeverityAsString($severity);
            return $this->assertTrue(
                    ! $expected || ($expected == $content),
                    "Expected [$expected] in PHP error [$content] severity [$severity] in [$file] line [$line]");
        }

        /**
         *    Confirms that an error has occurred and
         *    that the error text matches a Perl regular
         *    expression.
         *    @param string $pattern   Perl regular expression to
         *                              match against.
         *    @param string $message    Message to display.
         *    @return boolean           True on pass
         *    @access public
         */
        function assertErrorPattern($pattern, $message = "%s") {
            $queue = &SimpleErrorQueue::instance();
            if ($queue->isEmpty()) {
                $this->fail(sprintf($message, "Expected error not found"));
                return;
            }
            list($severity, $content, $file, $line, $globals) = $queue->extract();
            $severity = SimpleErrorQueue::getSeverityAsString($severity);
            return $this->assertTrue(
                    (boolean)preg_match($pattern, $content),
                    "Expected pattern match [$pattern] in PHP error [$content] severity [$severity] in [$file] line [$line]");
        }


}
?>
