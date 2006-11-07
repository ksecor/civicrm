<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components generic to CiviCRM settings
 * 
 */
class CRM_Admin_Form_Setting extends CRM_Core_Form
{

    /**
     * This function sets the default values for the form.
     * default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( );
        
        require_once "CRM/Core/BAO/Setting.php";
        CRM_Core_BAO_Setting::retrieve($defaults);

        global $base_url;
        $config =& CRM_Core_Config::singleton( );
        if ( $config->templateCompileDir ) {
            $path = substr($config->templateCompileDir, 0, -12);
        }

        //set defaults if not set in db
        if ( !$defaults['userFrameworkResourceURL'] ) {
            $defaults['userFrameworkResourceURL'] = $base_url. "/modules/civicrm/"; 
        }

        if ( !$defaults['imageUploadDir'] ) {
            $defaults['imageUploadDir'] = $path . "persist/contribute/";
        }
        
        if ( !$defaults['customFileUploadDir'] ) {
            $defaults['customFileUploadDir'] = $path . "upload/custom/";
        }

        if ( !$defaults['uploadDir'] ) {
            $defaults['uploadDir'] = $path . "upload/";
        }

        if ( !$defaults['paymentExpressButton'] ) {
            $defaults['paymentExpressButton'] = 'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif';
        }
        
        //CRM_Core_Error::debug('def', $defaults);
        
        return $defaults;
    }


  
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = array();
        $params = $this->controller->exportValues($this->_name);

        require_once "CRM/Core/BAO/Setting.php";
        CRM_Core_BAO_Setting::add($params);

        CRM_Core_Session::setStatus( ts('Global settings has been saved.') );
    }
}

?>