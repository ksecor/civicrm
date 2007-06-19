<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
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

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for processing a case
 * 
 */
class CRM_Case_Form_Case extends CRM_Core_Form
{
    /**
     * the id of the case that we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_id;


    /**
     * the id of the contact associated with this contribution
     *
     * @var int
     * @protected
     */
    protected $_contactID;


    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        
    }

    function setDefaultValues( ) 
    {
        $defaults = array( );
        return $defaults;
    }
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    {         

        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->freeze( );
        }
        $this->add('select', 'status',  ts( 'Case Status' ),  
                   array( '' => ts( '-select-' ) ) , true);
        $this->add('select', 'case_type_id',  ts( 'Case Type' ),  
                   array( '' => ts( '-select-' ) ) , true);
        $this->add('select', 'case_sub_type_id',  ts( 'Case Sub Type' ),  
                   array( '' => ts( '-select-' ) ) , true);
        $this->add('select', 'case_violation_type_id',  ts( 'Violation' ),  
                   array( '' => ts( '-select-' ) ) , true);
        $this->add( 'text', 'subject', ts('Subject') );
        $this->add( 'date', 'start_date', ts('Start Date'),
                    CRM_Core_SelectValues::date('datetime' ),
                    true);   
        $this->add( 'date', 'end_date', ts('End Date'),
                    CRM_Core_SelectValues::date('datetime' ),
                    false); 
        $this->add('textarea', 'description', ts('Regarding'));
        $this->addButtons(array( 
                                array ( 'type'      => $buttonType, 
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );
    }
    
    /**  
     * global form rule  
     *  
     * @param array $fields  the input form values  
     * @param array $files   the uploaded files if any  
     * @param array $options additional user data  
     *  
     * @return true if no errors, else array of errors  
     * @access public  
     * @static  
     */  
    static function formRule( &$fields, &$files, $self ) {  
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
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return;
        }
        
        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        
    }
}

?>
