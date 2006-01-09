<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 * This is our base form. It is part of the Form/Controller/StateMachine
 * trifecta. Each form is associated with a specific state in the state
 * machine. Each form can also operate in various modes
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'HTML/QuickForm/Page.php';

require_once 'CRM/Utils/Rule.php';
require_once 'CRM/Utils/Request.php';
require_once 'CRM/Core/Smarty.php';
require_once 'CRM/Core/Form/Renderer.php';
require_once 'CRM/Core/SelectValues.php';

class CRM_Core_Form extends HTML_QuickForm_Page {

    /**
     * The state object that this form belongs to
     * @var object
     */
    protected $_state;

    /**
     * The name of this form
     * @var string
     */
    protected $_name;

    /**
     * The mode of operation for this form
     * @var int
     */
    protected $_action;

    /**
     * the renderer used for this form
     *
     * @var object
     */
    protected $_renderer;

    /**
     * cache the smarty template for efficiency reasons
     *
     * @var CRM_Core_Smarty
     */
    static protected $_template;

    /**
     * constants for attributes for various form elements
     * attempt to standardize on the number of variations that we 
     * use of the below form elements
     *
     * @var const string
     */
    const
        ATTR_SPACING = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

    /**
     * All checkboxes are defined with a common prefix. This allows us to
     * have the same javascript to check / clear all the checkboxes etc
     * If u have multiple groups of checkboxes, you will need to give them different
     * ids to avoid potential name collision
     *
     * @var const string / int
     */
    const
        CB_PREFIX     = 'mark_x_',
        CB_PREFIY     = 'mark_y_',
        CB_PREFIZ     = 'mark_z_',
        CB_PREFIX_LEN = 7;

    /**
     * Constructor for the basic form page
     *
     * We should not use QuickForm directly. This class provides a lot
     * of default convenient functions, rules and buttons
     *
     * @param object    $state     State associated with this form
     * @param enum      $action      The mode the form is operating in (None/Create/View/Update/Delete)
     * 
     * @return object
     * @access public
     */

    function __construct($state = null, $action = CRM_Core_Action::NONE, $method = 'post' ) {
        $this->_name  = CRM_Utils_String::getClassName(CRM_Utils_System::getClassName($this));
        $this->HTML_QuickForm_Page( $this->_name, $method );
    
        $this->_state   = $state;
        $this->_action  = $action;

        $this->registerRules( );

        // let the constructor initialize this, should happen only once
        if ( ! isset( self::$_template ) ) {
            self::$_template =& CRM_Core_Smarty::singleton( );
        }

    }

    /**
     * register all the standard rules that most forms potentially use
     *
     * @return void
     * @access private
     *
     */
    function registerRules( ) {
        static $rules = array( 'title', 'variable', 'qfVariable', 
                               'phone', 'integer', 'query',
                               'url', 'date', 'qfDate', 'asciiFile', 'htmlFile',
                               'objectExists', 'postalCode', 'money','positiveInteger' );

        foreach ( $rules as $rule ) {
            $this->registerRule( $rule, 'callback', $rule, 'CRM_Utils_Rule' );
        }
    }

    /**
     * Simple easy to use wrapper around addElement. Deal with
     * simple validation rules
     *
     * @param string type of html element to be added
     * @param string name of the html element
     * @param string display label for the html element
     * @param string attributes used for this element.
     *               These are not default values
     * @param bool   is this a required field
     *
     * @return object    html element, could be an error object
     * @access public
     *
     */
    function &add($type, $name, $label,
                  $attributes = '', $required   = false ) {
        $element =& $this->addElement($type, $name, $label, $attributes);
        if (HTML_QuickForm::isError($element)) {
            CRM_Core_Error::fatal(HTML_QuickForm::errorMessage($element));
        }
        
        if ( $required ) {
            $error = $this->addRule($name, ts('%1 is a required field.', array(1 => $label)) , 'required');
            if (HTML_QuickForm::isError($error)) {
                CRM_Core_Error::fatal(HTML_QuickForm::errorMessage($element));
            }
        }
    
        return $element;
    }
  
    /**
     * This function is called before buildForm. Any pre-processing that
     * needs to be done for buildForm should be done here
     *
     * This is a virtual function and should be redefined if needed
     *
     * @access public
     * @return void
     *
     */
    function preProcess() {
    }

    /**
     * This function is called after the form is validated. Any
     * processing of form state etc should be done in this function.
     * Typically all processing associated with a form should be done
     * here and relevant state should be stored in the session
     *
     * This is a virtual function and should be redefined if needed
     * 
     * @access public
     * @return void
     *
     */
    function postProcess() {
    }

    /**
     * This virtual function is used to build the form. It replaces the
     * buildForm associated with QuickForm_Page. This allows us to put 
     * preProcess in front of the actual form building routine
     *
     * @access public
     * @return void
     *
     */
    function buildQuickForm() {
    }

    /**
     * This virtual function is used to set the default values of
     * various form elements
     *
     * access        public
     * @return array reference to the array of default values
     *
     */
    function setDefaultValues( ) {
    }

    /**
     * This is a virtual function that adds group and global rules to
     * the form. Keeping it distinct from the form to keep code small
     * and localized in the form building code
     *
     * @access public
     * @return void
     *
     */
    function addRules() {
    }

    /**
     * Core function that builds the form. We redefine this function
     * here and expect all CRM forms to build their form in the function
     * buildQuickForm.
     *
     */
    function buildForm() {
        $this->_formBuilt = true;

        $this->preProcess();

        $this->buildQuickForm();

        $defaults =& $this->setDefaultValues( );
        if ( ! empty( $defaults ) ) {
            $this->setDefaults( $defaults );
        }

        $this->addRules();
    }

    /**
     * Add default Next / Back buttons 
     *
     * @param array   array of associative arrays in the order in which the buttons should be
     *                displayed. The associate array has 3 fields: 'type', 'name' and 'isDefault'
     *                The base form class will define a bunch of static arrays for commonly used
     *                formats
     *
     * @return void
     *
     * @access public
     *
     */
    function addButtons( $params ) {
        $prevnext = array( );
        $spacing = array( );
        foreach ( $params as $button ) {
            $js = CRM_Utils_Array::value( 'js', $button );
            $isDefault = CRM_Utils_Array::value( 'isDefault', $button, false );
            if ( $isDefault ) {
                $attrs = array( 'class' => 'form-submit default' );
            } else {
                $attrs = array( 'class' => 'form-submit' );
            }

            if ( $js ) {
                $attrs = array_merge( $js, $attrs );
            }

            if ( $button['type'] === 'reset' ) {
                $prevnext[] =& $this->createElement( $button['type'], 'reset', $button['name'], $attrs );
            } else {
                if ( CRM_Utils_Array::value( 'subName', $button ) ) {
                    $buttonName = $this->getButtonName( $button['type'], $button['subName'] );
                } else {
                    $buttonName = $this->getButtonName( $button['type'] );
                }
                $prevnext[] =& $this->createElement( 'submit', $buttonName, $button['name'], $attrs );
            }
            if ( CRM_Utils_Array::value( 'isDefault', $button ) ) {
                $this->setDefaultAction( $button['type'] );
            }
            
            // hack - addGroup uses an array to express variable spacing, read from the last element
            $spacing[] = CRM_Utils_Array::value('spacing', $button, self::ATTR_SPACING);
        }
        $this->addGroup( $prevnext, 'buttons', '', $spacing, false );
    }

    /**
     * getter function for Name
     *
     * @return string
     * @access public
     */     
    function getName() {
        return $this->_name;
    }
   
    /**
     * getter function for State
     *
     * @return object
     * @access public
     */     
    function &getState() {
        return $this->_state;
    }

    /**
     * getter function for StateType
     *
     * @return int
     * @access public
     */     
    function getStateType( ) {
        return $this->_state->getType( );
    }

    /**
     * getter function for title. Should be over-ridden by derived class
     *
     * @return string
     * @access public
     */     
    function getTitle( ) {
        return 'ERROR: Title is not Set';
    }
	
    /**
     * getter function for link.
     *
     * @return string
     * @access public
     */     
    function getLink( ) {
        return '';
    }
	
    /**
     * boolean function to determine if this is a one form page
     *
     * @return boolean
     * @access public
     */     
    function isSimpleForm() {
        return $this->_state->getType( ) & ( CRM_Core_State::START | CRM_Core_State::FINISH );
    }

    /**
     * getter function for Form Action
     *
     * @return string
     * @access public
     */     
    function getFormAction() {
        return $this->_attributes['action'];
    }
	
    /**
     * setter function for Form Action
     *
     * @param string
     * @return void
     * @access public
     */     
    function setFormAction($action) {
        $this->_attributes['action'] = $action;
    }

    /**
     * render form and return contents
     * 
     * @return string
     * @access public
     */  
    function toSmarty() {
        $renderer =& $this->getRenderer();
        $this->accept($renderer);
        $content = $renderer->toArray();
        $content['formName'] = $this->getName();
        return $content;
    }

    /** 
     * getter function for renderer. If renderer is not set
     * create one and initialize it  
     *
     * @return object
     * @access public
     */
    function &getRenderer() {
        if (! isset($this->_renderer)) {
            $this->_renderer =& CRM_Core_Form_Renderer::singleton( );
        }
        return $this->_renderer;
    }
  
    /**
     * Use the form name to create the tpl file name
     *
     * @return string
     * @access public
     */
    function getTemplateFileName() {
        return (str_replace('_', DIRECTORY_SEPARATOR, CRM_Utils_System::getClassName($this)) . ".tpl");
    }

    /**
     * Error reporting mechanism
     *
     * @param string  $message Error Message
     * @param int     $code    Error Code
     * @param CRM_Core_DAO $dao     A data access object on which we perform a rollback if non - empty
     * @return void
     * @access public
     */
    function error( $message, $code = null, $dao = null ) {
        if ( $dao ) {
            $dao->query( 'ROLLBACK' );
        }

        $error =& CRM_Core_Error::singleton();
        
        $error->push( $code, $message );
    }
  
    /**
     * Store the variable with the value in the form scope
     *
     * @param  string name  : name  of the variable
     * @param  mixed  value : value of the variable
     *
     * @access public
     * @return void
     *
     */
    function set( $name, $value ) {
        $this->controller->set( $name, $value );
    }

    /**
     * Get the variable from the form scope
     *
     * @param  string name  : name  of the variable
     *
     * @access public
     * @return mixed
     *
     */
    function get( $name ) {
        return $this->controller->get( $name );
    }

    /**
     * getter for action
     *
     * @return int
     * @access public
     */
    function getAction( ) {
        return $this->_action;
    }

    /**
     * setter for action
     *
     * @param  int  $action the mode we want to set the form
     *
     * @return void
     * @access public
     */
    function setAction( $action ) {
        $this->_action = $action;
    }

    /**
     * assign value to name in template
     *
     * @param array|string $name  name  of variable
     * @param mixed $value value of varaible
     *
     * @return void
     * @access public
     */
    function assign( $var, $value = null) {
        self::$_template->assign($var, $value);
    }

    /**
     * assign value to name in template by reference
     *
     * @param array|string $name  name  of variable
     * @param mixed $value value of varaible
     *
     * @return void
     * @access public
     */
    function assign_by_ref( $var, &$value ) {
        self::$_template->assign_by_ref($var, $value);
    }

}

?>
