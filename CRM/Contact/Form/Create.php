<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class gets the name of the file to upload
 */
class CRM_Contact_Form_Create extends CRM_Core_Form 
{
    function buildQuickForm( ) {
        $this->_ncid = CRM_Utils_Request::retrieve( 'ncid', 'Positive', $this );
        if ( $this->_ncid ) {
            $this->assign( 'newContactId', $this->_ncid );
        }
        
        $this->assign( 'dojoIncludes', "dojo.require('dojox.data.QueryReadStore');
                                        dojo.require('dijit.form.Button');
                                        dojo.require('dijit.Dialog');
                                        dojo.require('dijit.form.TextBox'); dojo.require('dojo.parser');" );
        
        $orgAttributes        = array( 'dojoType'     => 'civicrm.FilteringSelect',
                                       'store'        => 'organizationStore',
                                       'style'        => 'width:300px; border: 1px solid #cfcfcf;',
                                       'class'        => 'tundra',
                                       'pageSize'     => 10,
                                       'id'           => 'select_contact'
                                       );
        
        $orgDataURL =  CRM_Utils_System::url( 'civicrm/ajax/search',
                                              "org=1",
                                              false, null, false );
        
        $this->assign('orgDataURL',$orgDataURL );
        $this->addElement('text', 'select_contact', ts('Select Existing Organization'),$orgAttributes );
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                          );
    }
    
    function postProcess( ) {
        // store the submitted values in an array
        // CRM_Core_Error::debug( 'p', $_POST );
        $params = $this->controller->exportValues( $this->_name );
        CRM_Core_Error::debug( 'params', $params );
        exit( );
    }
}
