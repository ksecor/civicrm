<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 * Redefine the upload action.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/QuickForm/Action.php';

class CRM_Core_QuickForm_Action_Upload extends CRM_Core_QuickForm_Action {

    /**
     * the array of uploaded file names
     * @var array
     */
    protected $_uploadNames;

    /**
     * The directory to store the uploaded files
     * @var string
     */
    protected $_uploadDir   ;
    
    /**
     * class constructor
     *
     * @param object $stateMachine reference to state machine object
     * @param string $uploadDir    directory to store the uploaded files
     * @param array  $uploadNames  element names of the various uploadable files
     * @return object
     * @access public
     */
    function __construct( &$stateMachine, $uploadDir, $uploadNames ) {
        parent::__construct( $stateMachine );

        $this->_uploadDir    =  $uploadDir;
        $this->_uploadNames  =  $uploadNames;
    }

    /**
     * upload and move the file if valid to the uploaded directory
     *
     * @param object $page       the CRM_Core_Form object
     * @param object $data       the QFC data container
     * @param string $pageName   the name of the page which index the data container with
     * @param string $uploadName the name of the uploaded file
     *
     * @return void
     * @access private
     */
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
                $uniqID = md5(uniqid(rand(), true));
                $info   = pathinfo($value['name']);
                $basename = substr($info['basename'], 0, -(strlen($info['extension']) + ($info['extension'] == '' ? 0 : 1)));
                $newName = $basename . "_{$uniqID}." . $info['extension'];
                $status = $element->moveUploadedFile( $this->_uploadDir, $newName );
                if ( ! $status ) {
                    CRM_Utils_System::statusBounce( ts( 'We could not move the uploaded file %1 to the upload directory %2. Please verify that the CIVICRM_IMAGE_UPLOADDIR setting points to a valid path which is writable by your web server.', array( 1 => $value['name'], 2 => $this->_uploadDir ) ) );
                }
                if (!empty($data['values'][$pageName][$uploadName])) {
                    @unlink($this->_uploadDir . $data['values'][$pageName][$uploadName]);
                }
                
                $data['values'][$pageName][$uploadName] = $this->_uploadDir . $newName;
            }
        }
    }

    /**
     * Processes the request.
     *
     * @param  object    $page       CRM_Core_Form the current form-page
     * @param  string    $actionName Current action name, as one Action object can serve multiple actions
     *
     * @return void
     * @access public
     */
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

        foreach ($this->_uploadNames as $name) {
            $this->upload( $page, $data, $pageName, $name );
        }
        
        $state =& $this->_stateMachine->getState( $pageName );
        if ( empty($state) ) {
            return $page->handle('display');
        }
        
        // the page is valid, process it before we jump to the next state
        $page->postProcess( );

        $state->handleNextState( $page );
    }

}

?>