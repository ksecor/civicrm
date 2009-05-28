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

class CRM_Report_Form_Register extends CRM_Core_Form {

    public function preProcess()  
    {  


    }
    function setDefaultValues( ) 
    {
        $defaults = array();
        return $defaults;
    }
    public function buildQuickForm( )  
    {
        
        $this->add( 'text', 'label', ts('Report Title'), array( 'size'=> 40 ), true );
        $this->add( 'text', 'value', ts('Report URL'),   array( 'size'=> 40 ), true );
        $this->add( 'text', 'name',  ts('Report Class'), array( 'size'=> 40 ), true );
        
        require_once 'CRM/Core/Component.php';
        $this->_components = CRM_Core_Component::getComponents();
        //unset the report component
        unset($this->_components['CiviReport']);

        $components = array();
        foreach( $this->_components as $name => $object ) {
            $components[$object->componentID] = $object->info['translatedName'];
        }

        $this->add( 'select', 'component_id', ts('Component'),   array(''=>ts( '- select -' )) + $components );     
        
        $this->addButtons(array( 
                                array ( 'type'      => 'upload',
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
        
        $this->addFormRule( array( 'CRM_Report_Form_Register', 'formRule' ), $this );
    }
    static function formRule( &$fields, &$files, $self ) 
    {  
        $errors = array( ); 
        return $errors;
    } 
      
    /** 
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    public function postProcess( )  
    {   
        // get the submitted form values.  
        $params = $this->controller->exportValues( $this->_name );
    }     
}
?>