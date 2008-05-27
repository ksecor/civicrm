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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/Form/Registration.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_AdditionalParticipant extends CRM_Event_Form_Registration
{
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
        parent::preProcess( );
        
        // lineItem isn't set until Register postProcess
    }
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    
    public function buildQuickForm( ) 
    {  
        $config =& CRM_Core_Config::singleton( );
        $this->add( 'text',
                    "email-{$this->_bltID}",
                    ts( 'Email Address' ),
                    array( 'size' => 30, 'maxlength' => 60 ), true );
        
        if ( $this->_values['event']['is_monetary'] ) {
            require_once 'CRM/Event/Form/Registration/Register.php';
            CRM_Event_Form_Registration_Register::buildAmount( $this );
        }
        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => 'Continue >>',
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true
                                        ),
                                
                                array ( 'type'      => 'Previous',
                                        'name'      => ts('<< Go Back')),
                                )
                          );

        $this->addFormRule( array( 'CRM_Event_Form_Registration_RegisterAdditionalParticipant', 'formRule' ),
                            $this );
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
    static function formRule(&$fields, &$files, &$self) 
    {
        //check for atleast one pricefields should be selected
        if ( $fields['priceSetId'] ) {
            $priceField = new CRM_Core_DAO_PriceField( );
            $priceField->price_set_id = $fields['priceSetId'];
            $priceField->find( );
            
            $check = array( );
            
            while ( $priceField->fetch( ) ) {
                if ( ! empty( $fields["price_{$priceField->id}"] ) ) {
                    $check[] = $priceField->id; 
                }
            }
            
            if ( empty( $check ) ) {
                $errors['_qf_default'] = ts( "Select atleast one option from Event Fee(s)" );
            }
        }
        if ( $self->_values['event']['is_monetary'] ) {
        
            // return if this is express mode
            $config =& CRM_Core_Config::singleton( );

            //validation for the user who attemp the amount value zero
            //is an already member
            $session =& CRM_Core_Session::singleton( );
            $userID  = $session->get( 'userID' );
            if ( $fields['priceSetId'] ) { 
                $zeroAmount = array( );
                foreach( $fields as $key => $val  )  {
                    if ( substr( $key, 0, 6 ) == 'price_' ){
                        if ( is_array( $val) ) {
                            foreach( $val as $keys => $vals  )  {
                                $zeroAmount[] = $keys;
                            }
                        } else {
                            $zeroAmount[] = $val;
                        }
                    }
                } 
                foreach( $zeroAmount as $keyes => $values  )  {
                    if( $values && !$userID && 
                        CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', $values, 'value', 'id' ) == 0 ) {
                        $errors['amount'] =  ts( "The Zero amount facility is only for the valid members" );
                    }
                }
            } else {
                $zeroAmount = $fields['amount'];
                if ( $zeroAmount && !$userID && 
                     CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue',$zeroAmount, 'value', 'id' ) == 0 ) {
                    $errors['amount'] =  ts( "The Zero amount facility is only for the valid members" );
                }
            }
            // also return if paylater mode or zero fees for valid members
            if ( CRM_Utils_Array::value( 'is_pay_later', $fields ) ) {
                if ( $fields['priceSetId'] ) { 
                    foreach( $fields as $key => $val  )  {
                        if ( substr( $key, 0, 6 ) == 'price_' && $val != 0) {
                            return empty( $errors ) ? true : $errors;
                        }
                    }
                } else {
                    return empty( $errors ) ? true : $errors;
                }
            } else if ( $fields['priceSetId'] ) { 
                $check = array( );
                foreach( $fields as $key => $val  )  {
                    if ( substr( $key, 0, 6 ) == 'price_' && $val != 0) {
                        if ( is_array( $val) ) {
                            foreach( $val as $keys => $vals  )  {
                                $check[] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', $keys, 'value');
                            }
                        } else {
                            $check[] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', $val, 'value');
                        }
                    }
                }
                $level = count ( $check );
                $j = null;
                for ($i = 0; $i < $level; $i++ ) {
                    if ( $check[$i] == 0 ) {
                        $j++;
                    }   
                }
                if ( $j == $level && isset( $j ) ) {
                    return empty( $errors ) ? true : $errors;
                } 
            } else if ( $zeroAmount ) {
                if ( CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', $zeroAmount, 'value', 'id' ) == 0 ) {
                    return empty( $errors ) ? true : $errors;
                }
            }
            //is pay later and priceset is used avoid credit card and
            //billing address validation  
            if ( CRM_Utils_Array::value( 'is_pay_later', $fields ) && $fields['priceSetId'] ) {
                return empty( $errors ) ? true : $errors;
            }
            
            foreach ( $self->_fields as $name => $fld ) {
                if ( $fld['is_required'] &&
                     CRM_Utils_System::isNull( CRM_Utils_Array::value( $name, $fields ) ) ) {
                    $errors[$name] = ts( '%1 is a required field.', array( 1 => $fld['title'] ) );
                    
                }
            }
        }
        return empty( $errors ) ? true : $errors;
    }  
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params = $this->controller->exportValues( $this->_name ); 
        if ($this->_values['event']['is_monetary']) {
            if ( empty( $params['priceSetId'] ) ) {
                $params['amount_level'] = $this->_values['custom']['label'][array_search( $params['amount'], 
                                                                                          $this->_values['custom']['amount_id'])];
                
                $params['amount']       = $this->_values['custom']['value'][array_search( $params['amount'], 
                                                                                          $this->_values['custom']['amount_id'])];
            } else {
                $lineItem = array( );
                require_once 'CRM/Event/Form/Registration/Register.php';
                CRM_Event_Form_Registration_Register::processPriceSetAmount( $this->_values['custom']['fields'], $params, $lineItem );
            }
            $this->_params                = $params;
        }else {
            if ( $this->_values['event']['default_role_id'] ) {
                $params['participant_role_id'] = $this->_values['event']['default_role_id'];
            }
            $this->_params                = $params;
        }
    }
}
?>