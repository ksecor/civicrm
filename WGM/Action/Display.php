<?php

require_once 'HTML/QuickForm/Action/Display.php';

require_once 'WGM/Config.php';

class WGM_Action_Display extends HTML_QuickForm_Action_Display {
  protected $_stateMachine;

  function WGM_Action_Display( &$stateMachine ) {
    $this->_stateMachine =& $stateMachine;
  }

  function _renderForm($page) {
    $config = WGM_Config::instance();

    $this->_setRenderTemplates($page);
    $template = SmartyTemplate::instance($config->templateDir, $config->templateCompileDir);
    $template->clear_all_assign();
    $template->assign('form',  $page->toSmarty());
    $content = $template->fetch( $page->getTemplateFileName() );
    $this->_stateMachine->setContent($content);
    return;
  }

  function _setRenderTemplates($page) {
    $renderer = $page->getRenderer();

    $renderer->setRequiredTemplate('
			{if $error}
				<span class="wgm-error">{$label|upper}</span>
			{else}
				{$label}
				{if $required}
					<span class="wgm-error" size="1">*</span>
				{/if}
			{/if}' );

    $renderer->setErrorTemplate('
			{if $error}
				<span class="wgm-error">{$error}</span><br/>
			{/if}
			{$html}' );
  }
}

?>
