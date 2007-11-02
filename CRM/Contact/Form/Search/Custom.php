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

    protected $_customClass = null;

    public function preProcess( ) {
        require_once 'CRM/Contact/BAO/SearchCustom.php';

        $csID = CRM_Utils_Request::retrieve( 'csid', 'Integer', $this );
        $ssID = CRM_Utils_Request::retrieve( 'ssID', 'Integer', $this );
        $gID  = CRM_Utils_Request::retrieve( 'gid' , 'Integer', $this );

        list( $this->_customSearchID,
              $this->_customSearchClass,
              $formValues ) = CRM_Contact_BAO_SearchCustom::details( $csID, $ssID, $gID );
        
        if (! $this->_customSearchID ) {
            CRM_Core_Error::fatal( 'Could not get details for custom search' );
        }

        if ( ! empty( $formValues ) ) {
            $this->_formValues = $formValues;
        }

        // use the custom selector
        require_once 'CRM/Contact/Selector/Custom.php';
        $this->_selectorName = 'CRM_Contact_Selector_Custom';

        $this->set( 'customSearchID'   , $this->_customSearchID    );
        $this->set( 'customSearchClass', $this->_customSearchClass );

        parent::preProcess( );

        // instantiate the new class
        eval( '$this->_customClass = new ' . $this->_customSearchClass . '( $this->_formValues );' );

    }

    function setDefaultValues( ) {
        return $this->_formValues;
    }

    function buildQuickForm( ) {
        $this->_customClass->buildForm( $this );

        parent::buildQuickForm( );
    }

    function getTemplateFileName( ) {
        $fileName = $this->_customClass->templateFile( );
        return $fileName ? $fileName : parent::getTemplateFileName( );
    }

    function postProcess( ) 
    {
        $session =& CRM_Core_Session::singleton();
        $session->set('isCustom', '1');

        // get user submitted values
        // get it from controller only if form has been submitted, else preProcess has set this
        if ( ! empty( $_POST ) ) {
            $this->_formValues = $this->controller->exportValues($this->_name);

            $this->_formValues['customSearchID'   ] = $this->_customSearchID   ;
            $this->_formValues['customSearchClass'] = $this->_customSearchClass;

            // also reset the sort by character
            $this->_sortByCharacter = null;
            $this->set( 'sortByCharacter', null );
        }            

        parent::postProcess( );
    }

    public function getTitle( ) {
        return ts('Custom Search');
    }
}

?>
