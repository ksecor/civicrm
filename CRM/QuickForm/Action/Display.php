<?php

require_once 'HTML/QuickForm/Action/Display.php';

require_once 'CRM/Config.php';

class CRM_QuickForm_Action_Display extends HTML_QuickForm_Action_Display {
  protected $_stateMachine;

  static $_requiredTemplate = null;
  static $_errorTemplate    = null;
    
  function CRM_QuickForm_Action_Display( &$stateMachine ) {
    $this->_stateMachine =& $stateMachine;
  }

  function _renderForm($page) {
    $config = CRM_Config::singleton();

    $this->_setRenderTemplates($page);
    $template = SmartyTemplate::singleton($config->templateDir, $config->templateCompileDir);
    $template->clear_all_assign();
    $template->assign('form',  $page->toSmarty());
    $content = $template->fetch( $page->getTemplateFileName(), $config->templateDir );
    $this->_stateMachine->setContent($content);
    return;
  }

  function _setRenderTemplates($page) {
    if ( self::$_requiredTemplate === null ) {
      $this->initializeTemplates();
    }

    $renderer = $page->getRenderer();
    
    $renderer->setRequiredTemplate( self::$_requiredTemplate );
    $renderer->setErrorTemplate   ( self::$_errorTemplate    );
  }

  function initializeTemplates() {
    if ( self::$_requiredTemplate !== null ) {
      return;
    }

    $config = CRM_Config::singleton();
    self::$_requiredTemplate = file_get_contents( $config->templateDir . '/themes/form_label.tpl' );
    self::$_errorTemplate    = file_get_contents( $config->templateDir . '/themes/form_error.tpl' );
  }

}

?>
