<?php

require_once 'WGM/DAO.php';

class WGM_Config {
  /*
   * application wide config options, with defaults (should be production defaults)
   *
   */
  public $dsn;
  public $daoDebugLvl		  = 0;
  public $smartyDir           = '/opt/local/lib/php/Smarty/';
  public $templateDir		  = './templates';
  public $templateCompileDir  = './templates_c';
  public $smtpServer          = 'outbound2.groundspring.org';
  public $extendedIncludePath = null;
  public $mainMenu            = 'http://localhost/lobo/drupal/';
  public $DAOFactoryClass	  = 'WGM_BAO_Factory';

  private static $_instance = null;

  static function instance($key = 'wgm') {
    if (self::$_instance === null ) {
      self::$_instance = new WGM_Config($key);
    }
    return self::$_instance;
  }

  function __construct() {
    if (defined('WGM_DSN')) {
      $this->dsn = WGM_DSN;
    }

    if (defined('WGM_DAO_DEBUG_LVL') ) {
      $this->daoDebugLvl = WGM_DAO_DEBUG_LVL;
    }

    if (defined('WGM_DAO_FACTORY_CLASS') ) {
      $this->DAOFactoryClass = WGM_DAO_FACTORY_CLASS;
    }

    if (defined('WGM_SMARTYDIR')) {
      $this->smartyDir = WGM_SMARTYDIR;
    }

    if (defined('WGM_TEMPLATEDIR')) {
      $this->templateDir = WGM_TEMPLATEDIR;
    }

    if (defined('WGM_TEMPLATE_COMPILEDIR')) {
      $this->templateCompileDir = WGM_TEMPLATE_COMPILEDIR;
    }

    if (defined('WGM_EXTENDED_INCLUDEPATH')) {
      $this->extendedIncludePath = WGM_EXTENDED_INCLUDEPATH;
    }
        
    if ( defined( 'WGM_SMTPSERVER' ) ) {
      $this->smtpServer = WGM_SMTPSERVER;
    }

    if ( defined( 'WGM_MAINMENU' ) ) {
      $this->mainMenu = WGM_MAINMENU;
    }

  }

  function init() {
    $this->initDataObject();
  }

  function initDataObject() {
    WGM_DataObject::init(
                         $this->dsn, 
                         $this->daoDebugLvl);

    $factoryClass = $this->DAOFactoryClass;
    WGM_Utils::import($factoryClass);
    WGM_DAO::setFactory(new $factoryClass());
  }

} // end WGM_Configuration

?>