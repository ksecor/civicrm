<?php

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Action/Direct.php';

require_once 'CRM/QuickForm/Action/Back.php';
require_once 'CRM/QuickForm/Action/Cancel.php';
require_once 'CRM/QuickForm/Action/Display.php';
require_once 'CRM/QuickForm/Action/Done.php';
require_once 'CRM/QuickForm/Action/Jump.php';
require_once 'CRM/QuickForm/Action/Next.php';
require_once 'CRM/QuickForm/Action/Process.php';
require_once 'CRM/QuickForm/Action/Refresh.php';
require_once 'CRM/QuickForm/Action/Submit.php';
require_once 'CRM/QuickForm/Action/Upload.php';

require_once 'CRM/StateMachine.php';

class CRM_Controller extends HTML_QuickForm_Controller {
  protected $_stateMachine;
  protected $_stateNames;

  /**
   * This caches the content for the display system
   */
  protected $_content;

  /**
   * All CRM single or multi page pages should inherit from this class. This
   * class extends the basic controller and adds additional useful functionality
   *
   * @param string  name of the controller
   * @param boolean whether controller is modal
   *
   * @access public
   *   
   * @return void
   *
   */
  function __construct( $name, $modal ) {
    $this->HTML_QuickForm_Controller( $name, $modal );

    // if the request has a reset value, initialize the controller session
    if ( $_GET['reset'] ) {
      $this->container( true );
    }

  }

  /**
   * Process the request, overrides the default QFC run method
   * This routine actually checks if the QFC is modal and if it
   * is the first invalid page, if so it call the requested action
   * if not, it calls the display action on the first invalid page
   * avoids the issue of users hitting the back button and getting
   * a broken page
   *
   * This run is basically a composition of the original run and the
   * jump action
   *
   */
  function run( ) {
    // the names of the action and page should be saved
    $this->_actionName = $this->getActionName();
    list($pageName, $action) = $this->_actionName;
 
    if ( $this->isModal( ) ) {
      if ( ! $this->isValid( $pageName ) ) {
        $pageName = $this->findInvalid( );
        $action   = 'display';
      }
    }

    // note that based on action, control might not come back!!
    // e.g. if action is a valid JUMP, u basically do a redirect
    // to the appropriate place
    $this->_pages[$pageName]->handle($action);

    return $pageName;
  }

  /**
   * Helper function to add a jump to all the states as direct jumps. Helps
   * if we need to have links / submit buttons to goto one specific page.
   * Not needed for all forms, but does not hurt to have something like this 
   * standard for all forms
   *
   * @param array names of all the states that compose this wizard
   * @access private
   * @return void
   *
   */
  function addDirectActions( $stateNames ) {
    foreach ( $stateNames as $name ) {
      $this->addAction( $name, new HTML_QuickForm_Action_Direct( ) );
    }
  }

  /**
   * Helper function to add all the needed default actions. Note that the framework
   * redefines all of the default QFC actions
   *
   * @param string   directory to store all the uploaded files
   * @param array    names for the various upload buttons (note u can have more than 1 upload)
   *
   * @access private
   * @return void
   *
   */
  function addDefaultActions( $uploadDirectory = null, $uploadNames = null ) {
    $this->addAction('display', new CRM_QuickForm_Action_Display($this->_stateMachine));
    $this->addAction('next'   , new CRM_QuickForm_Action_Next   ($this->_stateMachine));
    $this->addAction('back'   , new CRM_QuickForm_Action_Back   ($this->_stateMachine));
    $this->addAction('process', new CRM_QuickForm_Action_Process($this->_stateMachine));
    $this->addAction('cancel' , new CRM_QuickForm_Action_Cancel ($this->_stateMachine));
    $this->addAction('refresh', new CRM_QuickForm_Action_Refresh($this->_stateMachine));
    $this->addAction('done'   , new CRM_QuickForm_Action_Done   ($this->_stateMachine));
    $this->addAction('jump'   , new CRM_QuickForm_Action_Jump   ($this->_stateMachine));
    $this->addAction('submit' , new CRM_QuickForm_Action_Submit ($this->_stateMachine));

    if ( ! empty( $uploadDirectory ) ) {
      $this->addAction('upload' ,
                       new CRM_QuickForm_Action_Upload ($this->_stateMachine,
                                                        $uploadDirectory,
                                                        $uploadNames));
    }
    
  }

  function getStateMachine( ) {
    return $this->_stateMachine;
  }

 function setStateMachine( $stateMachine) {
    $this->_stateMachine = $stateMachine;
  }

  /**
   * add pages to the controller
   *
   */
  function addPages( $stateMachine, $states = null, $mode = CRM_Form::MODE_NONE ) {
    $stateNames = array( );

    if ( ! $states ) {
      $states = $stateMachine->getStatesDescription( );
    }

    foreach ( $states as $state ) {
      $className    = $state;
      $classString  = CRM_String::getLastTuple( $className );
      $stateNames[] = $classString;

      CRM_Utils::import( $className );

      $page = new $className( $classString,
                              $stateMachine->find( $classString ),
                              $mode );
      $this->addPage( $page );
    }

    $this->addDirectActions( $stateNames );

  }

  /**
   * QFC does not provide native support to have different 'submit' buttons.
   * We introduce this notion to QFC by using button specific data. Thus if
   * we have two submit buttons, we could have one displayed as a button and
   * the other as an image, both are of type 'submit'.
   *
   * @param string name of the button
   *
   * @access public
   * @return string the value of the button data (null if not present)
   *
   */
  function getButtonData( $buttonName ) {
    $data =& $this->container();
    
    $buttonStore =& $data['_qf_button_data'];

    return CRM_Array::value( $buttonName, $buttonStore );
  }

  /**
   * The above button data is actually stored in the session by QFC.
   * It is super important to reset this data once you have retrieved it.
   * We avoid doing it in the above routine in case the user calls the
   * getButtonData function multiple times
   *
   * @access public
   * @return void
   *
   */
  function resetButtonData( ) {
    $data =& $this->container();

    $data['_qf_button_data'] = array( );
  }

  /**
   * function to destroy all the session state of the controller.
   *
   * @access public
   * @return void
   */
  function reset( ) {
    $this->container( true );
  }

  /**
   * virtual function to do any processing of data.
   * Sometimes it is useful for the controller to actually process data.
   * This is typically used when we need the controller to figure out
   * what pages are potentially involved in this wizard. (this is dynamic
   * and can change based on the arguments
   *
   */
  function process( ) {
  }

  function setContent(&$content) {
    $this->_content =& $content;
  }

  function &getContent() {
    return $this->_content;
  }

}

?>