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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Contribute_Form_Confirm extends CRM_Core_Form {

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_contributeMode = $this->get( 'contributeMode' );
        $this->assign( 'contributeMode', $this->_contributeMode );

        if ( $contributeMode == 'express' ) {
            $nullObject = null;
            // rfp == redirect from paypal
            $rfp = CRM_Utils_Request::retrieve( 'rfp', $nullObject, false, null, 'GET' );
            if ( $rfp ) {
                require_once 'CRM/Utils/Payment/PayPal.php'; 
                $paypal =& CRM_Utils_Payment_PayPal::singleton( );
                $this->_params = $paypal->getExpressCheckoutDetails( $this->get( 'token' ) );
                
                // set a few other parameters for PayPal
                $this->_params['token']          = $this->get( 'token' );
                $this->_params['payment_action'] = 'Sale';
                $this->_params['amount'        ] = $this->get( 'amount' );
                $this->_params['currencyID'    ] = 'USD';
                $this->set( 'getExpressCheckoutDetails', $this->_params );
            } else {
                $this->_params = $this->get( 'getExpressCheckoutDetails' );
            }
        } else {
            $this->_params = $this->controller->exportValues( 'Contribution' );

            $this->_params['state_province'] = CRM_Core_PseudoConstant::stateProvinceAbbreviation( $this->_params['state_province_id'] ); 
            $this->_params['country']        = CRM_Core_PseudoConstant::countryIsoCode( $this->_params['country_id'] ); 
            $this->_params['year'   ]        = $this->_params['credit_card_exp_date']['Y'];  
            $this->_params['month'  ]        = $this->_params['credit_card_exp_date']['M'];  
            $this->_params['ip_address']     = $_SERVER['REMOTE_ADDR']; 
            $this->_params['payment_action'] = 'Sale';
            $this->_params['currencyID'    ] = 'USD';
        }

        $this->set( 'transactionParams', $this->_params );
    }

    static function assignToTemplate( &$self, &$params ) {
        $name = $params['first_name'];
        if ( CRM_Utils_Array::value( 'middle_name', $params ) ) {
            $name .= " {$params['middle_name']}";
        }
        $name .= " {$params['last_name']}";
        $self->assign( 'name', $name );

        $vars = array( 'street1', 'city', 'postal_code', 'state_province', 'country', 'credit_card_type' );
        foreach ( $vars as $v ) {
            $self->assign( $v, $params[$v] );
        }

        $self->assign( 'credit_card_exp_date', CRM_Utils_Date::format( $params['credit_card_exp_date'], '/' ) );
        $self->assign( 'credit_card_number',
                       CRM_Utils_System::mungeCreditCard( $params['credit_card_number'] ) );
        
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        self::assignToTemplate( $this, $this->_params );

        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Confirm Contribution'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );

    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return void
     */
    function setDefaultValues()
    {
        $defaults = array();
        return $defaults;
    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
        require_once 'CRM/Utils/Payment/PayPal.php';
        $paypal =& CRM_Utils_Payment_PayPal::singleton( );
        if ( $this->_contributeMode == 'express' ) {
            $result =& $paypal->doExpressCheckout( $this->_params );
        } else {
            $result =& $paypal->doDirectPayment( $this->_params );
        }
    }
}

?>
