<?php

require_once 'HTML/QuickForm/Controller.php';

require_once 'CRM/Validate.php';
require_once 'CRM/Form/Renderer.php';

require_once(realpath('themes/engines/smarty/SmartyTemplate.php'));

class CRM_Form extends HTML_QuickForm_Page {

  /**#@+
   * @access protected
   * @var object
   */

  /**
   * The state object that this form belongs to
   */
  protected $_state;

  /**
   * The name of this form
   */
  protected $_name;

  /**
   * The mode of operation for this form
   */
  protected $_mode;

  /**
   * constants for attributes for various form elements
   * attempt to standardize on the number of variations that we 
   * use of the below form elements
   */
  const
    ATTR_TEXT                  = 'size=30 maxlength=60'      ,
    ATTR_TEXT_TINY             = 'size=10 maxlength=10'      ,
    ATTR_TEXT_SMALL            = 'size=30 maxlength=30'      ,
    ATTR_TEXT_LARGE            = 'size=65 maxlength=100'     ,
    ATTR_TEXTAREA              = 'rows=10 cols=65'           ,
    ATTR_TEXTAREA_NOWRAP       = 'rows=10 cols=65 wrap="off"',
    ATTR_TEXTAREA_SMALL        = 'rows=4 cols=60'            ,
    ATTR_TEXTAREA_SMALL_NOWRAP = 'rows=4 cols=60 wrap="off"' ,
    ATTR_PHONE_TEXT            = 'size=22 maxlength=22'      ,
    ATTR_EXT_TEXT              = 'size=4 maxlength=6'        ,

    ATTR_SPACING               = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
  
    MODE_NONE                  = 0,
    MODE_CREATE                = 1,
    MODE_VIEW                  = 2,
    MODE_UPDATE                = 4,
    MODE_DELETE                = 8;

  /**
   * All checkboxes are defined with a common prefix. This allows us to
   * have the same javascript to check / clear all the checkboxes etc
   * If u have multiple groups of checkboxes, you will need to give them different
   * ids to avoid potential name collision
   */
  const
    CB_PREFIX     = 'mark_x_',
    CB_PREFIX_LEN = 7,
    CB_PREFIY     = 'mark_y_',
    CB_PREFIY_LEN = 7,
    CB_PREFIZ     = 'mark_z_',
    CB_PREFIZ_LEN = 7;

   /**
   * Constructor for the basic form page
   *
   * We should not use QuickForm directly. This class provides a lot
   * of default convenient functions, rules and buttons
   *
   * @param string    $name      Form Name
   * @param object    $state     State associated with this form
   * @param enum      $mode      The mode the form is operating in (None/Create/View/Update/Delete)
   * 
   * @access public
   * @throws CRM_Error
   */

  function __construct($name = '', $state = null, $mode = self::MODE_NONE ) {
    $this->HTML_QuickForm_Page( $name );

    $this->_name  = $name;
    $this->_state = $state;
    $this->_mode  = $mode;

    $this->registerRules( );
  }

  /**
   * register all the standard rules that most forms potentially use
   */
  function registerRules( ) {
    $this->registerRule( 'name'       , 'callback', 'name'       , 'CRM_Validate' );
    $this->registerRule( 'variable'   , 'callback', 'variable'   , 'CRM_Validate' );
    $this->registerRule( 'phoneNumber', 'callback', 'phoneNumber', 'CRM_Validate' );
    $this->registerRule( 'queryString', 'callback', 'queryString', 'CRM_Validate' );
    $this->registerRule( 'url'        , 'callback', 'url'        , 'CRM_Validate' );
  }

  /**
   * Simple easy to use addElement function
   */
  function add($type, $name, $label,
               $attributes = '',
               $required   = false,
               $validator  = null,
               $validator_label = null ) {
    // localize the label
    $label = t($label);

    $element = $this->addElement($type, $name, $label, $attributes);
    if (HTML_QuickForm::isError($element)) {
      CRM_Error::abort(HTML_QuickForm::errorMessage($element));
    }

    if ( $required ) {
      $error = $this->addRule($name, t(' is a required field') , 'required');
      if (HTML_QuickForm::isError($error)) {
        CRM_Error::abort(HTML_QuickForm::errorMessage($element));
      }
    }

    if( isset( $validator ) ) {
      if( $validator_label === null ) {
        $validator_label = t(' must be valid');
      }
      $error = $this->addRule($name, $validator_label, $validator);
      if (HTML_QuickForm::isError($error)) {
        CRM_Error::abort(HTML_QuickForm::errorMessage($element));
      }
    }
   
    return $element;
  }
  
  /**
   * add a select element to the form
   *
   * @param string Is the name of the form element
   * @param array  is a value=>label array of the values to add to the drop down
   *
   * @throws CRM_Error
   *
   */
  function addSelect($name, $values) {
    foreach ( $values as $value => $label) {
      $error = $this->createElement('select', null, null, $label, $value);
      if (HTML_QuickForm::isError($error)) {
        CRM_Error::abort(HTML_QuickForm::errorMessage($element));
      }
    }
  }

  /**
   * This function is called before buildForm. Any pre-processing that
   * needs to be done for buildForm should be done here
   *
   * This is a virtual function and should be redefined if needed
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
   */
  function postProcess() {
  }

  /**
   * This virtual function is used to build the form. It replaces the
   * buildForm associated with QuickForm_Page. This allows us to put 
   * preProcess in front of the actual form building routine
   *
   */
  function buildQuickForm() {
  }

  /**
   * This is a virtual function that adds group and global rules to
   * the form. Keeping it distinct from the form to keep code small
   * and localized in the form building code
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

    $this->addRules();
  }

  /**
   * Add default Next / Back buttons 
   *
   * @param boolean cancel     - if set a cancel button is also added
   * @param string  nextName   - name of next button
   * @param string  backName   - name of back button
   * @param string  cancelName - name of cancel button
   * 
   * @return void
   *
   * @access public
   *
   */
  function addDefaultButtons( $params ) {
    
    $prevnext = array( );
    $keys = array_keys($params);
    sort($keys);
    foreach ( $keys as $index ) {
      list( $type, $name, $default ) = $params[$index];

      // internationalize the name
      $name = t($name);
      if ( $type === 'reset' ) {
        $prevnext[] =& $this->createElement( $type, null, $name );
      } else {
        $prevnext[] =& $this->createElement( 'submit', $this->getButtonName($type), $name );
      }
      if ( $default ) {
        $this->setDefaultAction( $type );
      }
       
      $this->addGroup( $prevnext, 'buttons', '', self::ATTR_SPACING, false );
    }
  }
     
  function getName() {
    return $this->_name;
  }
   
  function getState() {
    return $this->_state;
  }

  function getStateType( ) {
    return $this->_state->getType( );
  }

  function isOneState( ) {
    return $this->_state->getType( ) & ( CRM_State::SFINAL | CRM_State::INITIAL );
  }

  function getDisplayName( ) {
    return '(Display Name is not Set)';
  }
	
  function getFormAction() {
    return $this->_attributes['action'];
  }
	
  function setFormAction($action) {
    $this->_attributes['action'] = $action;
  }
  
  function toSmarty() {
    $renderer = $this->getRenderer();
    $this->accept($renderer);
    $form = $renderer->toArray();
    $form['formName'] = $this->getName();
    return $form;
  }
  
  function getRenderer() {
    if (isset($this->renderer)) {
      return $this->renderer;
    }
    else {
      $template = SmartyTemplate::instance();
      // TODO: find a better solution then this
      $this->renderer = new CRM_Form_Renderer($template);
      return $this->renderer;
    }
  }
  
  function getTemplateFileName() {
    $className    = get_class( $this );
    $templateName = str_replace( '_', '/', $className ) . '.tpl';
    return $templateName;
  }

}

?>