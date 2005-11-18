<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for processing a ontribution 
 * 
 */
class CRM_Contribute_Form_Contribution extends CRM_Core_Form
{

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        // current contribution page id 
        $this->_id = CRM_Utils_Request::retrieve( 'id', $this, true );        

        // get all the values from the dao object
        $params = array('id' => $this->_id); 
        $this->_values = array( );
        CRM_Core_DAO::commonRetrieve( 'CRM_Contribute_DAO_ContributionPage', $params, $this->_values );

        // get the amounts and the label
        require_once 'CRM/Core/BAO/CustomOption.php';  
        CRM_Core_BAO_CustomOption::getAssoc( 'civicrm_contribution_page', $this->_id, $this->_values );

        // get the profile ids
        require_once 'CRM/Core/BAO/UFJoin.php'; 
 
        $ufJoinParams = array( 'entity_table' => 'civicrm_contribution_page',   
                               'entity_id'    => $this->_id,   
                               'weight'       => 1 ); 
        $this->_values['custom_pre_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams ); 
 
        $ufJoinParams['weight'] = 2; 
        $this->_values['custom_post_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );
        $this->assign( 'intro_text', $this->_values['intro_text'] );
        // assigning title to template in case someone wants to use it, also setting CMS page title
        $this->assign( 'title', $this->_values['title'] );
        CRM_Utils_System::setTitle($this->_values['title']);  
    
        $this->_defaults = array( );

    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->applyFilter('__ALL__', 'trim');

        $this->buildCreditCard( );

        $this->buildAmount( );

        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre'  );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost' );

    }

    function buildAmount( ) {
        $elements = array( );

        // first build the radio boxes
        if ( ! empty( $this->_values['label'] ) ) {
            for ( $index = 1; $index <= count( $this->_values['label'] ); $index++ ) {
                $elements[] =& $this->createElement('radio', null, '',
                                                    '$' . $this->_values['value'][$index] . ' ' . $this->_values['label'][$index],
                                                    $this->_values['value'][$index],
                                                    array('onclick'=>'clearAmountOther();'));
                if ( $this->_values['value'][$index] == $this->_values['default_amount'] ) {
                    $this->_defaults["amount"] = $this->_values['value'][$index];
                }
            }
        }

        if ( $this->_values['is_allow_other_amount'] ) {
            $elements[] =& $this->createElement('radio', null, '',
                                                ts('Other Amount'), 'amount_other_radio');

            $this->assign( 'is_allow_other_amount', true );
            $this->addElement('text', 'amount_other',
                       ts('Other Amount'), array( 'size' => 10, 'maxlength' => 10, 'onfocus'=>'useAmountOther();' )
                       );
            $this->addRule( 'amount_other', ts( 'Please enter a valid amount (numbers and decimal point only).' ), 'money' );
        } else {
            $this->assign( 'is_allow_other_amount', false );
        }


        $this->addGroup( $elements, 'amount', ts('Contribution Amount'), '<br />' );
        $this->addRule( 'amount', ts('Amount is a required field'), 'required' );
    }

    function setDefaultValues( ) {
        return $this->_defaults;
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
            CRM_Core_BAO_UFGroup::buildQuickForm( $id, $this, $name );
        }
    }

    /** 
     * Function to add all the credit card fields
     * 
     * @return None 
     * @access public 
     */
    function buildCreditCard( ) {
        $this->add('text', 
                   'email', 
                   ts('Email Address'), 
                   array( 'size' => 30, 'maxlength' => 60 ),
                   true );

        $this->add('text',
                   'first_name',
                   ts('First Name'),
                   array( 'size' => 30, 'maxlength' => 60 ) );

        $this->add('text',
                   'middle_name',
                   ts('Middle Name'),
                   array( 'size' => 30, 'maxlength' => 60 ) );

        $this->add('text',
                   'last_name',
                   ts('Last Name'),
                   array( 'size' => 30, 'maxlength' => 60 ) );

        $this->add('text', 
                   'street1',
                   ts('Street Address'), 
                   array( 'size' => 30, 'maxlength' => 60 ) ); 

        $this->add('text', 
                   'city',
                   ts('City'), 
                   array( 'size' => 30, 'maxlength' => 60 ) ); 

        $this->add('select', 
                   'state_province_id',
                   ts('State / Province'),
                   array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince( ) );

        $this->add('text', 
                   'postal_code',
                   ts('Postal Code'), 
                   array( 'size' => 30, 'maxlength' => 60 ) ); 

        $this->addElement( 'select',
                           'country_id',
                           ts('Country'), 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::country( ) );

        $this->add('text', 
                   'credit_card_number', 
                   ts('Card Number'), 
                   array( 'size' => 20, 'maxlength' => 20 ) );

        $this->add('text',
                   'cvv2',
                   ts('Security Code'),
                   array( 'size' => 5, 'maxlength' => 10 ) );
        $this->addRule( 'cvv2', ts( 'Please enter a valid value for your card security code. This is usually the last 3-4 digits on the card\'s signature panel.' ), 'integer' );

        $this->add( 'date',
                    'credit_card_exp_date',
                    ts('Expiration Date'),
                    CRM_Core_SelectValues::date( 'creditCard' ) );
        $this->addRule( 'credit_card_exp_date', ts('Select a valid date.'), 'qfDate');

        $creditCardType = array( ''           => '- select -',
                                 'Visa'       => 'Visa'      ,
                                 'MasterCard' => 'MasterCard',
                                 'Discover'   => 'Discover'  ,
                                 'Amex'       => 'Amex' );
        
        $this->addElement( 'select', 
                           'credit_card_type', 
                           ts('Card Type'),  
                           $creditCardType,
                           true );

        $this->_expressButtonName = $this->getButtonName( 'next', 'express' );
        $this->add('image',
                   $this->_expressButtonName,
                   'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif',
                   array( 'class' => 'form-submit' ) );

        $this->addButtons(array( 
                                array ( 'type'      => 'next', 
                                        'name'      => ts('Continue >>'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );

        $this->addFormRule( array( 'CRM_Contribute_Form_Contribution', 'formRule' ), $this );
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

        if ( $fields['amount'] == 'amount_other_radio' ) {
            if ( $self->_values['min_amount'] > 0 ) {
                $min = $self->_values['min_amount'];
                if ( $fields['amount_other'] < $min ) {
                    $errors['amount_other'] = ts( 'This amount has to be greater than %1', 
                                                  array ( 1 => $min ) );
                }
            }

            if ( $self->_values['max_amount'] > 0 ) {
                $max = $self->_values['max_amount'];
                if ( $fields['amount_other'] > $max ) {
                    $errors['amount_other'] = ts( 'This amount has to be less than %1', 
                                                  array ( 1 => $max ) );
                }
            }
        }

        // make sure either 
        // return if this is express mode
        if ( CRM_Utils_Array::value( $self->_expressButtonName . '_x', $fields ) ||
             CRM_Utils_Array::value( $self->_expressButtonName . '_y', $fields ) ||
             CRM_Utils_Array::value( $self->_expressButtonName       , $fields ) ) {
            return $errors;
        }
        
        // make sure the required fields are present
        $required = array( 'first_name' => ts( 'First Name' ),
                           'last_name'  => ts( 'Last Name'  ),
                           'street1'    => ts( 'Street Address' ),
                           'city'       => ts( 'City' ),
                           'state_province_id' => ts( 'State / Province' ),
                           'postal_code'    => ts( 'Postal Code' ),
                           'country_id'     => ts( 'Country' ),
                           'credit_card_number' => ts( 'Card Number' ),
                           'cvv2' => ts( 'Card Security Code' ),
                           'credit_card_type' => ts( 'Card Type' ),
                           'credit_card_exp_date' => ts( 'Card Expiration Date' ) );

        foreach ( $required as $item => $name) {
            if ( CRM_Utils_System::isNull( CRM_Utils_Array::value( $item, $fields ) ) ) {
                $error[$item] = ts( "%1 is a required field", array( 1 => $name ) );
            }
        }

        // make sure that credit card number and cvv are valid
        require_once 'CRM/Utils/Rule.php';
        if ( CRM_Utils_Array::value( 'credit_card_type', $fields ) ) {
            if ( CRM_Utils_Array::value( 'credit_card_number', $fields ) &&
                 ! CRM_Utils_Rule::creditCardNumber( $fields['credit_card_number'], $fields['credit_card_type'] ) ) {
                $error['credit_card_number'] = ts( "Please enter a valid Credit Card Number" );
            }
            
            if ( CRM_Utils_Array::value( 'cvv2', $fields ) &&
                 ! CRM_Utils_Rule::cvv( $fields['cvv2'], $fields['credit_card_type'] ) ) {
                $error['cvv2'] =  ts( "Please enter a valid Credit Card Verification Number" );
            }
        }

        return empty( $error ) ? true : $error;
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // get the submitted form values. 
        $params = $this->controller->exportValues( $this->_name ); 
        $params['currencyID']     = 'USD'; 
        $params['payment_action'] = 'Sale'; 
        $params['amount'] = ( $params['amount'] == 'amount_other_radio' ) ? $params['amount_other'] : $params['amount'];

        $this->set( 'amount', $params['amount'] ); 

        require_once 'CRM/Utils/Payment/PayPal.php';                                                                                      
        $paypal =& CRM_Utils_Payment_PayPal::singleton( ); 
  
        //get the button name  
        $buttonName = $this->controller->getButtonName( );  
        if ($buttonName == $this->_expressButtonName || 
            $buttonName == $this->_expressButtonName . '_x' || 
            $buttonName == $this->_expressButtonName . '_y' ) { 
            $this->set( 'contributeMode', 'express' ); 
 
            $donateURL = CRM_Utils_System::url( 'civicrm/contribute', '_qf_Contribute_display=1' ); 
            $params['cancelURL' ] = CRM_Utils_System::url( 'civicrm/contribute/contribution', '_qf_Contribute_display=1', true, null, false ); 
            $params['returnURL' ] = CRM_Utils_System::url( 'civicrm/contribute/contribution', '_qf_Confirm_display=1&rfp=1', true, null, false ); 
             
            $token = $paypal->setExpressCheckout( $params ); 
            if ( is_a( $token, 'CRM_Core_Error' ) ) { 
                CRM_Core_Error::displaySessionError( $token ); 
                CRM_Utils_System::redirect( $params['cancelURL' ] );
            } 

            $this->set( 'token', $token ); 
             
            $paypalURL = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=$token"; 
            CRM_Utils_System::redirect( $paypalURL ); 
        } else { 
            $this->set( 'contributeMode', 'direct' ); 
        } 
    }

}

?>