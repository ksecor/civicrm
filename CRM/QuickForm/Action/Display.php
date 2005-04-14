<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 * Redefine the display action.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/QuickForm/Action.php';

require_once 'CRM/Config.php';

class CRM_QuickForm_Action_Display extends CRM_QuickForm_Action {

    /**
     * the template to display the required "red" asterick
     * @var string
     */
    static $_requiredTemplate = null;

    /**
     * the template to display error messages inline with the form element
     * @var string
     */
    static $_errorTemplate    = null;
    
    /**
     * class constructor
     *
     * @param object $stateMachine reference to state machine object
     *
     * @return object
     * @access public
     */
    function __construct( &$stateMachine ) {
        parent::__construct( $stateMachine );
    }

    /**
     * Processes the request.
     *
     * @param  object    $page       CRM_Form the current form-page
     * @param  string    $actionName Current action name, as one Action object can serve multiple actions
     *
     * @return void
     * @access public
     */
    function perform(&$page, $actionName) {

        // CRM_Error::le_method();

        $pageName = $page->getAttribute('id');
        // If the original action was 'display' and we have values in container then we load them
        // BTW, if the page was invalid, we should later call validate() to get the errors
        list(, $oldName) = $page->controller->getActionName();
        if ('display' == $oldName) {
            // If the controller is "modal" we should not allow direct access to a page
            // unless all previous pages are valid (see also bug #2323)
            if ($page->controller->isModal() && !$page->controller->isValid($page->getAttribute('id'))) {
                $target =& $page->controller->getPage($page->controller->findInvalid());
                return $target->handle('jump');
            }
            $data =& $page->controller->container();
            if (!empty($data['values'][$pageName])) {
                $page->loadValues($data['values'][$pageName]);
                $validate = false === $data['valid'][$pageName];
            }
        }
        // set "common" defaults and constants
        $page->controller->applyDefaults($pageName);
        $page->isFormBuilt() or $page->buildForm();
        // if we had errors we should show them again
        if (isset($validate) && $validate) {
            $page->validate();
        }

        // CRM_Error::ll_method();

        $f = $this->_renderForm($page);
        return $f;
    }

    /**
     * render the page using a custom templating
     * system
     *
     * @param object  $page the CRM_Form page
     *
     * @return void
     * @access public
     */
    function _renderForm(&$page) {
        $config  = CRM_Config::singleton ();
        $session = CRM_Session::singleton();

        $this->_setRenderTemplates($page);
        $template = SmartyTemplate::singleton($config->templateDir, $config->templateCompileDir);

        $template->assign_by_ref( 'config' , $config  );
        $template->assign_by_ref( 'session', $session );
        $template->register_function ( 'crmURL', array( 'CRM_System', 'crmURL' ) );

        // We could do something real smart out here and actually figure out the real tpl to call
        // rather than go thru this indirection. TODO
        $template->assign( 'mode'   , $page->getMode( ) );
        $template->assign( 'tplFile', $page->getTemplateFileName() ); 
        $template->assign('form',  $page->toSmarty());
        $content = $template->fetch( 'CRM/index.tpl', $config->templateDir );
        $this->_stateMachine->setContent($content);
        return;
    }

    /**
     * set the various rendering templates
     *
     * @param object  $page the CRM_Form page
     *
     * @return void
     * @access public
     */
    function _setRenderTemplates(&$page) {
        if ( self::$_requiredTemplate === null ) {
            $this->initializeTemplates();
        }

        $renderer = $page->getRenderer();
    
        $renderer->setRequiredTemplate( self::$_requiredTemplate );
        $renderer->setErrorTemplate   ( self::$_errorTemplate    );
    }

    /**
     * initialize the various templates
     *
     * @param object  $page the CRM_Form page
     *
     * @return void
     * @access public
     */
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