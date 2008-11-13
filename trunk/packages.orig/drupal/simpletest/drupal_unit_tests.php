<?php
/**
 * Implementes getTestInstances to allow access to the test objects from outside
 */
class DrupalTestSuite extends TestSuite {
  var $_cleanupModules   = array();

  function DrupalTestSuite($label) {
    $this->TestSuite($label);
  }

  /**
   * @return array of instantiated tests that this GroupTests holds
   */
  function getTestInstances() {
    for ($i = 0, $count = count($this->_test_cases); $i < $count; $i++) {
      if (is_string($this->_test_cases[$i])) {
        $class = $this->_test_cases[$i];
        $this->_test_cases[$i] = &new $class();
      }
    }
    return $this->_test_cases; 
  } 
}

class DrupalUnitTests extends DrupalTestSuite {
  /**
   * Constructor
   * @param array   $class_list  list containing the classes of tests to be processed
   *                             default: NULL - run all tests
   */
  function DrupalUnitTests($class_list = NULL) {
    static $classes;
    $this->DrupalTestSuite('Drupal Unit Tests');

    /* Tricky part to avoid double inclusion */
    if (!$classes) {

      $files = array();
      foreach (module_list() as $module) {
        $module_path = drupal_get_path('module', $module);
        if (file_exists($module_path .'/tests/')) {
          $dir = $module_path .'/tests';
          $tests = file_scan_directory($dir, '\.test$');
          $files = array_merge($files, $tests);
        }   
      }   
      $files = array_keys($files);

      $existing_classes = get_declared_classes();
      foreach ($files as $file) {
        include_once($file);
      }
      $classes = array_diff(get_declared_classes(), $existing_classes);
    }
    if (!is_null($class_list)) {
      $classes = $class_list;
    }
    if (count($classes) == 0) {
      $this->addTestCase(new BadGroupTest($test_file, 'No new test cases'));
      return;
    }
    $groups = array();
    foreach ($classes as $class) {
      if (!is_subclass_of($class, 'DrupalTestCase')) {
        continue;
      }
      $this->_addClassToGroups($groups, $class);
    }
    foreach ($groups as $group_name => $group) {
      $group_test = &new DrupalTestSuite($group_name);
      foreach ($group as $key => $v) {
        $group_test->addTestCase($group[$key]);
      }
      $this->addTestCase($group_test);
    }
  }

  /**
   * Adds a class to a groups array specified by the get_info of the group
   * @param array  $groups Group of categorized tests
   * @param string $class  Name of a class
   */
  function _addClassToGroups(&$groups, $class) {
    $test = &new $class();
    if (method_exists($test, 'get_info')) {
      $info = $test->get_info();
      $groups[$info['group']][] = $test;
    }
  }

  /**
   * Invokes run() on all of the held test cases, instantiating
   * them if necessary.
   * The Drupal version uses paintHeader instead of paintGroupStart
   * to avoid collapsing of the very top level.
   *
   * @param SimpleReporter $reporter    Current test reporter.
   * @access public
   */
  function run(&$reporter) {
    cache_clear_all();
    @set_time_limit(0);
    ignore_user_abort(true);

    // Disable devel output, check simpletest settings page
    if (!variable_get('simpletest_devel', false)) {
      $GLOBALS['devel_shutdown'] = FALSE;
    }

    parent::run($reporter);

    // Restores modules
    foreach ($this->_cleanupModules as $name => $status) {
      db_query("UPDATE {system} SET status = %d WHERE name = '%s' AND type = 'module'", $status, $name);
    }
    $this->_cleanupModules = array();

  }

  /**
   * Enables a drupal module
   * @param string $name name of the module
   * @return boolean success
   */
  function drupalModuleEnable($name) {
    if (module_exists($name)) {
      return TRUE;
    }
    include_once './includes/install.inc';
    module_rebuild_cache(); // Rebuild the module cache
    if (drupal_get_installed_schema_version($name, TRUE) == SCHEMA_UNINSTALLED) {
      drupal_install_modules(array($name));
    }
    else {
      $try = module_enable(array($name));
    }

    if(module_exists($name)) {
      if (!isset($this->_cleanupModules[$name])) {
        $this->_cleanupModules[$name] = 0;
        return TRUE;
      }
    }
    else {
      die("required module $name could not be enabled (probably file does not exist)");
    }
  }


  /**
   * Disables a drupal module
   * @param string $name name of the module
   * @return boolean success
   */
  function drupalModuleDisable($name) {
    if (!module_exists($name)) {
      return TRUE;
    }
    /* Update table */
    db_query("UPDATE {system} SET status = 0 WHERE name = '%s' AND type = 'module'", $name);
    if (db_affected_rows()) {
      /* Make sure not overwriting when double switching */
      if (!isset($this->_cleanupModules[$name])) {
        $this->_cleanupModules[$name] = 1;
      }
      /* refresh module_list */
      module_list(TRUE, FALSE);
      return TRUE;
    }
    die("incompatible module $name could not be disabled for unknown reason");
  }
}
