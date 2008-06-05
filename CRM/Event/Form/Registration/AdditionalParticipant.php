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
class CRM_Event_Form_Registration_AdditionalParticipant extends CRM_Event_Form_Registration
{
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
          parent::preProcess( );
          CRM_Utils_System::setTitle( 'Register Additional Participant' );
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

        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre'  );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost' );

        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => 'Continue >>',
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true
                                        ),
                                
                                array ( 'type'      => 'back',
                                        'name'      => ts('<< Go Back')),
                                )
                          );

        $this->addFormRule( array( 'CRM_Event_Form_Registration_AdditionalParticipant', 'formRule' ),
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
        return null; 
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
        if ( $this->_values['event']['is_monetary'] ) {
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
                    
        }else {
            if ( $this->_values['event']['default_role_id'] ) {
                $params['participant_role_id'] = $this->_values['event']['default_role_id'];
            }
           
        }
        if ( ! isset( $params['participant_role_id'] ) && $this->_values['event']['default_role_id'] ) {
            $params['participant_role_id'] = $this->_values['event']['default_role_id'];
        }
        $this->_params  = array ();
        $this->_params =  $this->get( 'params' );
        if ( CRM_Utils_Array::value( 'is_pay_later', $this->_params[0] ) ) {
            $params['is_pay_later']  = 1;
        }
        
        //build the params array.
        $updateParams= false;
        $paramsKey = null;
        if ( is_array( $this->_params ) ) {
            foreach ( $this->_params as $key => $value ) {
                if ( $value['email-5'] == $params['email-5'] ) {
                    $updateParams= true;
                    $paramsKey = $key;
                    break;
                }
            }
        }
       
        //add participant fields in params.
        if ( $updateParams ) {
            $this->_params[$paramsKey] = $params;
        } else {
            $this->_params[] = $params;
        }
        
        $this->set( 'params', $this->_params );

        //to check whether call processRegistration() 
        if ( !$this->_values['event']['is_monetary'] && CRM_Utils_Array::value( 'additional_participants', $this->_params[0] ) ) {
            $participant =  $this->_params[0]['additional_participants'] + 1;
            if ( count($this->_params) == $participant ) {
                require_once 'CRM/Event/Form/Registration/Register.php';
              CRM_Event_Form_Registration_Register::processRegistration(  $this->_params,  null );
            }
        }
    }

    function &getPages( &$controller )
    {
        $details = array( );
        $i = 0;
        $session =& CRM_Core_Session::singleton( );
        $additional = $session->get('addParticipant');
        
        for ( ; $i < $additional; $i++ ) {
            $details["Participant-{$i}"] = array( 'className' => 'CRM_Event_Form_Registration_AdditionalParticipant', 
                                                  'title'   => "Participant $i"
                                                  );
        }
                
        if ( ! $details ) {
            $details = array( );
        }
        return $details;
    } 

}
?>