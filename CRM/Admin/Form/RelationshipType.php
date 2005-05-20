<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Admin/Form.php';

/**
 * This class generates form components for Relationship Type
 * 
 */
class CRM_Admin_Form_RelationshipType extends CRM_Admin_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {

        /*
        // store the referer url
        $rd = 0;
        $rd = CRM_Utils_Request::retrieve( 'rd', $this, false, 0 );
        
        if ($rd) {
            echo $strReturnPath = $_SERVER['HTTP_REFERER'];
            echo "<br>======<br>"; 
            echo $lngCrmPosition = strpos($strReturnPath, 'civicrm');
            echo "<br>======<br>"; 
            echo $lngActionPosition = strpos($strReturnPath, 'action');
            $strReturnPath = substr($strReturnPath, $lngCrmPosition, $lngActionPosition);
            echo $_SESSION['returnPath'] = $strReturnPath;
        }
        */
        
        $this->applyFilter('__ALL__', 'trim');
  
        $this->add('text', 'name_a_b'       , ts('Relationship Label-A to B')       ,
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'name_a_b' ) );
        $this->addRule( 'name_a_b', ts('Please enter a valid Relationship Label for A to B.'), 'required' );

        $this->add('text', 'name_b_a'       , ts('Relationship Label-B to A')       ,
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'name_b_a' ) );
      
        // add select for contact type
        $this->add('select', 'contact_type_a', ts('Contact Type A '), CRM_Core_SelectValues::$contactType);
        $this->add('select', 'contact_type_b', ts('Contact Type B '), CRM_Core_SelectValues::$contactType);

        $this->add('text', 'description', ts('Description'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_RelationshipType', 'description' ) );
        $this->add('checkbox', 'is_active', ts('Enabled?'));

        parent::buildQuickForm( );

        if ( $this->_mode & self::MODE_VIEW ) {
            $this->freeze( );
            $this->addElement('button', 'done', 'Done', array('onClick' => "location.href='civicrm/admin/reltype?reset=1&action=browse'"));
        }
  
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params = array();
        $ids    = array();
        
        // store the submitted values in an array
        $params = $this->exportValues();
        $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );
        
        if ($this->_mode & self::MODE_UPDATE ) {
            $ids['relationshipType'] = $this->_id;
        }    
        
        CRM_Contact_BAO_RelationshipType::add($params, $ids);

        CRM_Core_Session::setStatus( ts('The Relationship Type has been saved.') );
    }//end of function
}

?>
