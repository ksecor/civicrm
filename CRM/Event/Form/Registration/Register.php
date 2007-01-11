<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org. If you have questions       |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/Form/Registration.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_Registration_Register extends CRM_Event_Form_Registration
{

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
        parent::preProcess( );
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

        $this->assign('eventPage', $this->_values['event_page']);
        $this->assign('paidEvent', $this->_values['event']['is_monetary']);

        if ( $this->_values['event']['is_monetary'] ) {
            $this->buildAmount( );
            $this->buildCreditCard( );
        }

        require_once 'CRM/Core/BAO/UFJoin.php';
        $customField =& new CRM_Core_DAO_UFJoin();
        $customField->entity_id    = $this->_id;
        $customField->entity_table = 'civicrm_event';
        $customField->find();

        $ufJoinParams = array( 'entity_table' => 'civicrm_event',
                               'entity_id'    => $this->_id,
                               'weight'       => 1 );
        $custom_pre_id = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );
        $ufJoinParams['weight'] = 2;
        $custom_post_id = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );
       
        while( $customField->fetch() ) {
            if( $custom_pre_id ){
                $this->buildCustom( $customField->uf_group_id, 'customPre'  );
            }
            if( $custom_post_id ){
                $this->buildCustom( $customField->uf_group_id, 'customPost' );
            }
        }
        
        // if payment is via a button only, dont display continue
        if ( $config->paymentBillingMode != CRM_Contribute_Payment::BILLING_MODE_BUTTON || !$this->_values['event']['is_monetary']) {
            $this->addButtons(array( 
                                    array ( 'type'      => 'next', 
                                            'name'      => ts('Continue >>'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    ) 
                              );
        }
    }
    
    function setDefaultValues( ) {
        // check if the user is registered and we have a contact ID
        $session =& CRM_Core_Session::singleton( );
        $contactID = $session->get( 'userID' );
        if ( $contactID ) {
            $options = array( );
            $fields = array( );
            require_once "CRM/Core/BAO/CustomGroup.php";
            $removeCustomFieldTypes = array ('Contribution');
            foreach ( $this->_fields as $name => $dontCare ) {
                //don't set custom data Used for Contribution (CRM-1344)
                if ( substr( $name, 0, 7 ) == 'custom_' ) {  
                    $id = substr( $name, 7 );
                    if ( ! CRM_Core_BAO_CustomGroup::checkCustomField( $id, $removeCustomFieldTypes )) {
                        continue;
                    }
                }
                $fields[$name] = 1;
            }
            $fields['state_province'] = $fields['country'] = $fields['email'] = 1;

            require_once 'CRM/Core/BAO/UFGroup.php';
            CRM_Core_BAO_UFGroup::setProfileDefaults( $contactID, $fields, $this->_defaults );
        }
        return $this->_defaults;
    }

    /**
     * build the radio/text form elements for the amount field
     *
     * @return void
     * @access private
     */
    public function buildAmount( ) {
        $elements = array( );
        if ( ! empty( $this->_values['custom']['label'] ) ) {
            require_once 'CRM/Utils/Money.php';
            for ( $index = 1; $index <= count( $this->_values['custom']['label'] ); $index++ ) {
                $elements[] =& $this->createElement('radio', null, '',
                                                    CRM_Utils_Money::format($this->_values['custom']['value'][$index]) . ' ' . 
                                                    $this->_values['custom']['label'][$index], 
                                                    $this->_values['custom']['amount_id'][$index] );
            }
            $this->addGroup( $elements, 'amount', ts('Fee Level'), '<br />' );
        }
    }
    
    /**  
     * Function to add the custom fields
     *  
     * @return None  
     * @access public  
     */ 
    function buildCustom( $id, $name ) {
        if ( $id ) {
            require_once 'CRM/Core/BAO/UFGroup.php';
            require_once 'CRM/Profile/Form.php';
            $session =& CRM_Core_Session::singleton( );
            $contactID = $session->get( 'userID' );
            if ( $contactID ) {
                if ( CRM_Core_BAO_UFGroup::filterUFGroups($id)  ) {
                    $fields = CRM_Core_BAO_UFGroup::getFields( $id, false,CRM_Core_Action::ADD ); 
                    $this->assign( $name, $fields );
                    foreach($fields as $key => $field) {
                        CRM_Core_BAO_UFGroup::buildProfile($this, $field,CRM_Profile_Form::MODE_CREATE);
                        $this->_fields[$key] = $field;
                    }
                }
            } else {
                $fields = CRM_Core_BAO_UFGroup::getFields( $id, false,CRM_Core_Action::ADD ); 
                $this->assign( $name, $fields );
                foreach($fields as $key => $field) {
                    CRM_Core_BAO_UFGroup::buildProfile($this, $field,CRM_Profile_Form::MODE_CREATE);
                    $this->_fields[$key] = $field;
                }
            }
        }
    }
    

    /** 
     * Function to add all the credit card fields
     * 
     * @return None 
     * @access public 
     */
    function buildCreditCard( ) {
        $config =& CRM_Core_Config::singleton( );
        if ( $config->paymentBillingMode & CRM_Contribute_Payment::BILLING_MODE_FORM ) {
            foreach ( $this->_fields as $name => $field ) {
                $this->add( $field['htmlType'],
                            $field['name'],
                            $field['title'],
                            $field['attributes'] );
            }

            $this->addRule( 'cvv2', ts( 'Please enter a valid value for your card security code. This is usually the last 3-4 digits on the card\'s signature panel.' ), 'integer' );

            $this->addRule( 'credit_card_exp_date', ts('Select a valid date greater than today.'), 'currentDate');
        }            
            
        if ( $config->paymentBillingMode & CRM_Contribute_Payment::BILLING_MODE_BUTTON ) {
            $this->_expressButtonName = $this->getButtonName( 'next', 'express' );
            $this->add('image',
                       $this->_expressButtonName,
                       $config->paymentExpressButton,
                       array( 'class' => 'form-submit' ) );
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
        $config =& CRM_Core_Config::singleton( );
    
        // we first reset the confirm page so it accepts new values
        $this->controller->resetPage( 'Confirm' );

        // get the submitted form values. 
        $params = $this->controller->exportValues( $this->_name ); 

        $params['currencyID']     = $config->defaultCurrency;
        //$params['payment_action'] = 'Sale'; 

        $params['amount'] = $this->_values['custom']['value'][array_search( $params['amount'], $this->_values['custom']['amount_id'])];
        $this->set( 'amount', $params['amount'] ); 
        
        // generate and set an invoiceID for this transaction
        $invoiceID = $this->get( 'invoiceID' );
        if ( ! $invoiceID ) {
            $invoiceID = md5(uniqid(rand(), true));
        }
        $this->set( 'invoiceID', $invoiceID );
        
        $payment =& CRM_Contribute_Payment::singleton( $this->_mode ); 
        // default mode is direct
        $this->set( 'contributeMode', 'direct' ); 

        if ( $config->paymentBillingMode & CRM_Contribute_Payment::BILLING_MODE_BUTTON ) {
            //get the button name  
            $buttonName = $this->controller->getButtonName( );  
            if ($buttonName == $this->_expressButtonName || 
                $buttonName == $this->_expressButtonName . '_x' || 
                $buttonName == $this->_expressButtonName . '_y' ) { 
                $this->set( 'contributeMode', 'express' ); 
                
                //$donateURL = CRM_Utils_System::url( 'civicrm/contribute', '_qf_Contribute_display=1' ); 
                $params['cancelURL' ] = CRM_Utils_System::url( 'civicrm/admin/event/register', '_qf_Register_display=1', true, null, false ); 
                $params['returnURL' ] = CRM_Utils_System::url( 'civicrm/admin/event/register', '_qf_Confirm_display=1&rfp=1', true, null, false ); 
                $params['invoiceID' ] = $invoiceID;
                
                $token = $payment->setExpressCheckout( $params ); 
                if ( is_a( $token, 'CRM_Core_Error' ) ) { 
                    CRM_Core_Error::displaySessionError( $token ); 
                    CRM_Utils_System::redirect( $params['cancelURL' ] );
                } 
                
                $this->set( 'token', $token ); 
                
                if ( $this->_mode == 'test' ) {
                    $paymentURL = "https://" . $config->paymentPayPalExpressTestUrl . "/cgi-bin/webscr?cmd=_express-checkout&token=$token"; 
                } else {
                    $paymentURL = "https://" . $config->paymentPayPalExpressUrl . "/cgi-bin/webscr?cmd=_express-checkout&token=$token"; 
                }
                
                CRM_Utils_System::redirect( $paymentURL ); 
            }
        } else if ( $config->paymentBillingMode & CRM_Contribute_Payment::BILLING_MODE_NOTIFY ) {
            $this->set( 'contributeMode', 'notify' );
        }
    }//end of function
    
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Event Registration');
    }
    
}
?>
