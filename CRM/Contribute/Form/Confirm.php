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
        $nullObject = null;
        $rfp = CRM_Utils_Request::retrieve( 'rfp', $nullObject, false, null, 'GET' );
        if ( true ) {
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
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
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
        $result = $paypal->doExpressCheckout( $this->_params );
    }
}

?>
