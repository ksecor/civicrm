<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

class CRM_Standalone_Form_Register extends CRM_Core_Form {

    protected $_profileID;

    protected $_fields = array( );
    

    function preProcess( ) {
        // pick the first profile ID that has user register checked
        require_once 'CRM/Core/BAO/UFGroup.php';
        $ufGroups =& CRM_Core_BAO_UFGroup::getModuleUFGroup('User Registration');

        if ( count( $ufGroups ) > 1 ) {
            CRM_Core_Error::fatal( ts( 'You have more than one profile that has been enabled for user registration.' ) );
        }

        foreach ( $ufGroups as $id => $dontCare ) {
            $this->_profileID = $id;
        }
    }

    function buildQuickForm( ) {

        $this->add( 'text',
                    'user_unique_id', 
                    ts( 'OpenID' ),
                    CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Contact', 'user_unique_id' ),
                    true );

        $fields = CRM_Core_BAO_UFGroup::getFields( $this->_profileID,
                                                   false,
                                                   CRM_Core_Action::ADD );
        $this->assign( 'custom', $fields );
        
        require_once 'CRM/Profile/Form.php';
        foreach ( $fields as $key => $field ) {
            CRM_Core_BAO_UFGroup::buildProfile( $this,
                                                $field,
                                                CRM_Profile_Form::MODE_CREATE );
            $this->_fields[$key] = $field;
        }
    }


}

?>
