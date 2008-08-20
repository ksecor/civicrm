<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Admin/Form/Setting.php';

/**
 * This class generates form components for Error Handling and Debugging
 * 
 */
class CRM_Admin_Form_Setting_UpdateConfigBackend extends CRM_Admin_Form_Setting
{  
    protected $_oldBaseDir;
    protected $_oldBaseURL;
  
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        CRM_Utils_System::setTitle(ts('Settings - Update Directory Path and URL'));

        $config =& CRM_Core_Config::singleton( );

        if ( $config->userFramework == 'Joomla' ) {
            $this->_oldBaseURL = substr( $config->userFrameworkResourceURL,
                                         0, -45 );
        } else {
            if ( strpos( $config->userFrameworkResourceURL,
                         'sites/all/modules' ) == false ) {
                CRM_Core_Error::statusBounce( ts( 'This function only works when CiviCRM is installed in sites/all/modules' ) );
            }
            $this->_oldBaseURL = substr( $config->userFrameworkResourceURL,
                                         0, -26 );
        }
        $this->assign( 'oldBaseURL', $this->_oldBaseURL );
        
        // 15 characters from end is /civicrm/upload/
        $this->_oldBaseDir = substr( $config->uploadDir,
                                    0, -15 ); 

        $this->assign( 'oldBaseDir',
                       $this->_oldBaseDir );
                               
        $this->add( 'text', 'newBaseURL', ts( 'New Base URL' ), null, true );
        $this->add( 'text', 'newBaseDir', ts( 'New Base Directory' ), null, true );
 
        $this->addFormRule( array( 'CRM_Admin_Form_Setting_UpdateConfigBackend', 'formRule' ) );

        parent::buildQuickForm();     
    }

    function setDefaultValues( ) 
    {
        if ( ! $this->_defaults ) {
            parent::setDefaultValues( );

            $this->_defaults['newBaseURL'] = $this->_oldBaseURL;
            $this->_defaults['newBaseDir'] = $this->_oldBaseDir;
        }
        return $this->_defaults;
    }

    static function formRule(&$fields) {
        $tmpDir = trim( $fields['newBaseDir'] );

        $errors = array( );
        if ( ! is_writeable( $tmpDir ) ) {
            $errors['newBaseDir'] = ts( '%1 directory does not exist or cannot be written by webserver',
                                        array( 1 => $tmpDir ) );
        }
        return $errors;
    }

    function postProcess( ) {
        // redirect to admin page after saving
        $session =& CRM_Core_Session::singleton();
        $session->pushUserContext( CRM_Utils_System::url( 'civicrm/admin') );

        $params = $this->controller->exportValues( $this->_name );

        $newValues = str_replace( array( $this->_oldBaseURL, $this->_oldBaseDir ),
                                  array( trim( $params['newBaseURL'] ),
                                         trim( $params['newBaseDir'] ) ),
                                  $this->_defaults );

        parent::commonProcess( $newValues );

        parent::rebuildMenu( );
    }

}


