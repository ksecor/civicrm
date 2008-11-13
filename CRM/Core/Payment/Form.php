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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Core_Payment_Form {
    /** 
     * create all fields needed for a credit card transaction
     *                                                           
     * @return void 
     * @access public 
     */ 
    function setCreditCardFields( &$form ) {
        $bltID = $form->_bltID;

        $form->_fields['billing_first_name'] = array( 'htmlType'   => 'text', 
                                                      'name'       => 'billing_first_name', 
                                                      'title'      => ts('Billing First Name'),
                                                      'cc_field'   => true,
                                                      'attributes' => array( 'size' => 30, 'maxlength' => 60, 'autocomplete' => 'off' ),
                                                      'is_required'=> true );
        
        $form->_fields['billing_middle_name'] = array( 'htmlType'   => 'text', 
                                                       'name'       => 'billing_middle_name', 
                                                       'title'      => ts('Billing Middle Name'), 
                                                       'cc_field'   => true,
                                                       'attributes' => array( 'size' => 30, 'maxlength' => 60, 'autocomplete' => 'off' ), 
                                                       'is_required'=> false );
        
        $form->_fields['billing_last_name'] = array( 'htmlType'   => 'text', 
                                                     'name'       => 'billing_last_name', 
                                                     'title'      => ts('Billing Last Name'), 
                                                     'cc_field'   => true,
                                                     'attributes' => array( 'size' => 30, 'maxlength' => 60, 'autocomplete' => 'off' ), 
                                                     'is_required'=> true );
                                         
        $form->_fields["street_address-{$bltID}"] = array( 'htmlType'   => 'text', 
                                                           'name'       => "street_address-{$bltID}",
                                                           'title'      => ts('Street Address'), 
                                                           'cc_field'   => true,
                                                           'attributes' => array( 'size' => 30, 'maxlength' => 60, 'autocomplete' => 'off' ), 
                                                           'is_required'=> true );
                                         
        $form->_fields["city-{$bltID}"] = array( 'htmlType'   => 'text', 
                                                 'name'       => "city-{$bltID}",
                                                 'title'      => ts('City'), 
                                                 'cc_field'   => true,
                                                 'attributes' => array( 'size' => 30, 'maxlength' => 60, 'autocomplete' => 'off' ), 
                                                 'is_required'=> true );
                                         
        $form->_fields["state_province_id-{$bltID}"] = array( 'htmlType'   => 'select', 
                                                              'name'       => "state_province_id-{$bltID}",
                                                              'title'      => ts('State / Province'), 
                                                              'cc_field'   => true,
                                                              'attributes' => array( '' => ts( '- select -' ) ) +
                                                              CRM_Core_PseudoConstant::stateProvince( ),
                                                              'is_required'=> true );
                                         
        $form->_fields["postal_code-{$bltID}"] = array( 'htmlType'   => 'text', 
                                                        'name'       => "postal_code-{$bltID}",
                                                        'title'      => ts('Postal Code'), 
                                                        'cc_field'   => true,
                                                        'attributes' => array( 'size' => 30, 'maxlength' => 60, 'autocomplete' => 'off' ), 
                                                        'is_required'=> true );
                                         
        $form->_fields["country_id-{$bltID}"] = array( 'htmlType'   => 'select', 
                                                       'name'       => "country_id-{$bltID}", 
                                                       'title'      => ts('Country'), 
                                                       'cc_field'   => true,
                                                       'attributes' => array( '' => ts( '- select -' ) ) + 
                                                       CRM_Core_PseudoConstant::country( ),
                                                       'is_required'=> true );
                                         
        $form->_fields['credit_card_number'] = array( 'htmlType'   => 'text', 
                                                      'name'       => 'credit_card_number', 
                                                      'title'      => ts('Card Number'), 
                                                      'cc_field'   => true,
                                                      'attributes' => array( 'size' => 20, 'maxlength' => 20, 'autocomplete' => 'off' ), 
                                                      'is_required'=> true );
                                         
        $form->_fields['cvv2'] = array( 'htmlType'   => 'text', 
                                        'name'       => 'cvv2', 
                                        'title'      => ts('Security Code'), 
                                        'cc_field'   => true,
                                        'attributes' => array( 'size' => 5, 'maxlength' => 10, 'autocomplete' => 'off' ), 
                                        'is_required'=> true );
                                         
        $form->_fields['credit_card_exp_date'] = array( 'htmlType'   => 'date', 
                                                        'name'       => 'credit_card_exp_date', 
                                                        'title'      => ts('Expiration Date'), 
                                                        'cc_field'   => true,
                                                        'attributes' => CRM_Core_SelectValues::date( 'creditCard' ),
                                                        'is_required'=> true );

        require_once 'CRM/Contribute/PseudoConstant.php';
        $creditCardType = array( ''           => '- select -') + CRM_Contribute_PseudoConstant::creditCard( );
        $form->_fields['credit_card_type'] = array( 'htmlType'   => 'select', 
                                                    'name'       => 'credit_card_type', 
                                                    'title'      => ts('Card Type'), 
                                                    'cc_field'   => true,
                                                    'attributes' => $creditCardType,
                                                    'is_required'=> true );
    }

    /** 
     * Function to add all the credit card fields
     * 
     * @return None 
     * @access public 
     */
    function buildCreditCard( &$form, $useRequired = false ) {
        require_once 'CRM/Core/Payment.php';

        if ( $form->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_FORM) {
            foreach ( $form->_fields as $name => $field ) {
                if ( isset( $field['cc_field'] ) &&
                     $field['cc_field'] ) {
                    $form->add( $field['htmlType'],
                                $field['name'],
                                $field['title'],
                                $field['attributes'],
                                $useRequired ? $field['is_required'] : false );
                }
            }

            $form->addRule( 'cvv2',
                            ts( 'Please enter a valid value for your card security code. This is usually the last 3-4 digits on the card\'s signature panel.' ),
                            'integer' );

            $form->addRule( 'credit_card_exp_date',
                            ts('Credit card expiration date cannot be a past date.'),
                            'currentDate', true );
        }            
            
        if ( $form->_paymentProcessor['billing_mode'] & CRM_Core_Payment::BILLING_MODE_BUTTON ) {
            $form->_expressButtonName = $form->getButtonName( $form->buttonType( ), 'express' );
            $form->add('image',
                       $form->_expressButtonName,
                       $form->_paymentProcessor['url_button'],
                       array( 'class' => 'form-submit' ) );
        }
    }

    /**
     * function to map address fields
     *
     * @return void
     * @static
     */
    static function mapParams( $id, &$src, &$dst, $reverse = false ) {
        static $map = null;
        if ( ! $map ) {
            $map = array( 'first_name'             => 'billing_first_name'        ,
                          'middle_name'            => 'billing_middle_name'       ,
                          'last_name'              => 'billing_last_name'         ,
                          'email'                  => "email-$id"                 ,
                          'street_address'         => "street_address-$id"        ,
                          'supplemental_address_1' => "supplemental_address_1-$id",
                          'city'                   => "city-$id"                  ,
                          'state_province'         => "state_province-$id"        ,
                          'postal_code'            => "postal_code-$id"           ,
                          'country'                => "country-$id"               ,
                          );
        }
        
        foreach ( $map as $n => $v ) {
            if ( ! $reverse ) {
                if ( isset( $src[$n] ) ) {
                    $dst[$v] = $src[$n];
                }
            } else {
                if ( isset( $src[$v] ) ) {
                    $dst[$n] = $src[$v];
                }
            }
        }
    }

}

