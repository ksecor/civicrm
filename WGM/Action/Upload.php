<?php
// $Id: Upload.class.php,v 1.4 2004/05/24 23:38:51 lobo Exp $

require_once 'HTML/QuickForm/Action/Next.php';

class WGM_Action_Upload extends HTML_QuickForm_Action {
  protected $_stateMachine;
  protected $_uploadNames;
  protected $_uploadDir   ;

  function WGM_Action_Upload( &$stateMachine, $uploadDir, $uploadNames ) {
    $this->_stateMachine =& $stateMachine;
    $this->_uploadDir    =  $uploadDir;
    $this->_uploadNames  =  $uploadNames;
  }

  function upload( &$page, &$data, $pageName, $uploadName ) {
    if ( empty( $uploadName ) ) {
      return;
    }

    // get the element containing the upload
    $element =& $page->getElement( $uploadName );
    if ( 'file' == $element->getType( ) ) {
      if ($element->isUploadedFile()) {
        // rename the uploaded file with a unique number at the end
        $value = $element->getValue();
        $newName = uniqid( "${value['name']}." );
        $element->moveUploadedFile( $this->_uploadDir, $newName );
        if (!empty($data['values'][$pageName][$uploadName])) {
          @unlink($this->_uploadDir . $data['values'][$pageName][$uploadName]);
        }
        
        $data['values'][$pageName][$uploadName] = $this->_uploadDir . $newName;
      }
    }
  }

  function perform(&$page, $actionName) {
    // like in Action_Next 
    $page->isFormBuilt() or $page->buildForm(); 

    $pageName =  $page->getAttribute('name'); 
    $data     =& $page->controller->container(); 
    $data['values'][$pageName] = $page->exportValues(); 
    $data['valid'][$pageName]  = $page->validate(); 
    
    if (!$data['valid'][$pageName]) { 
      return $page->handle('display'); 
    } 

    foreach ($name as $uploadNames) {
      $this->upload( $page, $data, $pageName, $name );
    }

    // redirect to next page
    $state = $this->_stateMachine->_states[$pageName];
    if ( empty($state) ) {
      return $page->handle('display');
    }
    
    // the page is valid, process it before we jump to the next state
    $page->postProcess( );

    $state->getNextState( $page );
  }

}

?>
