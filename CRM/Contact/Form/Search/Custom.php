<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

require_once 'CRM/Contact/Form/Search.php';

class CRM_Contact_Form_Search_Custom extends CRM_Contact_Form_Search {

    public function preProcess( ) {
        $this->_customSearchID = CRM_Utils_Request::retrieve( 'csid', 'Integer', $this, true );
        
        // check that the csid exists in the db along with the right file
        // and implements the right interface
        require_once 'CRM/Core/OptionGroup.php';
        $this->_customSearchClass = CRM_Core_OptionGroup::getLabel( 'custom_search',
                                                                    $this->_customSearchID );
        if ( ! $this->_customSearchClass ) {
            CRM_Core_Error::fatal( 'Could not find implementation file record for custom search' );
        }

        $customSearchFile = str_replace( '_',
                                         DIRECTORY_SEPARATOR,
                                         $this->_customSearchClass ) . '.php';
        
        $error = include_once( $customSearchFile );
        if ( $error == false ) {
            CRM_Core_Error::fatal( 'Could not find implementation file for custom search' );
        }
             
        // use the custom selector
        require_once 'CRM/Contact/Selector/Custom.php';
        $this->_selectorName = 'CRM_Contact_Selector_Custom';

        $this->set( 'customSearchID'   , $this->_customSearchID    );
        $this->set( 'customSearchClass', $this->_customSearchClass );

        parent::preProcess( );
    }

    function buildQuickForm( ) {
        eval( $this->_customSearchClass . '::buildForm( $this );' );

        $this->buildQuickFormCommon();
    }
}

?>
