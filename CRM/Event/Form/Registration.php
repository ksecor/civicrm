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

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_Registration extends CRM_Core_Form
{

    /**
     * the id of the event we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_id;
    
    /**
     * the values for the contribution db object
     *
     * @var array
     * @protected
     */
    public $_values;

    /**
     * The params submitted by the form and computed by the app
     *
     * @var array
     * @protected
     */
    protected $_params;

    /** 
     * The fields involved in this contribution page
     * 
     * @var array 
     * @protected 
     */ 
    protected $_fields;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
        $this->_id = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String', $this, false );
        
        $this->_values = $this->get( 'values' );
        $this->_fields = $this->get( 'fields' );
        
        if ( ! $this->_values ) {
            // get all the values from the dao object
            $this->_values = array( );
            
            //retrieve event information
            $params = array( 'id' => $this->_id );
            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::retrieve($params, $this->_values['event']);
            
            //retrieve custom information
            require_once 'CRM/Core/BAO/CustomOption.php'; 
            CRM_Core_BAO_CustomOption::getAssoc( 'civicrm_event', $this->_id, $this->_values['event']['custom'] );
            
            $this->_values['event']['feeLevel'] = CRM_Core_BAO_CustomOption::getCustomOption( $this->_id, true, 'civicrm_event' );
            
            $params = array( 'event_id' => $this->_id );
            require_once 'CRM/Event/BAO/EventPage.php';
            CRM_Event_BAO_EventPage::retrieve($params, $this->_values['event_page']);
            
            if ( $this->_values['event']['is_monetary'] ) {
                $this->setCreditCardFields( );
            }

            $this->set( 'values', $this->_values );
            $this->set( 'fields', $this->_fields );
        }
    }

    /** 
     * create all fields needed for a credit card transaction
     *                                                           
     * @return void 
     * @access public 
     */ 
    function setCreditCardFields( ) {
        
        $this->_fields['first_name']  = array( 'htmlType'   => 'text', 
                                              'name'       => 'first_name', 
                                              'title'      => ts('First Name'), 
                                              'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                              'is_required'=> true );
        
        $this->_fields['middle_name'] = array( 'htmlType'   => 'text', 
                                               'name'       => 'middle_name', 
                                               'title'      => ts('Middle Name'), 
                                               'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                               'is_required'=> false );
        
        $this->_fields['last_name']   = array( 'htmlType'   => 'text', 
                                               'name'       => 'last_name', 
                                               'title'      => ts('Last Name'), 
                                               'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                               'is_required'=> true );
                                         
        $this->_fields['street_address'] = array( 'htmlType'   => 'text', 
                                                  'name'       => 'street_address', 
                                                  'title'      => ts('Street Address'), 
                                                  'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                                  'is_required'=> true );
        
        $this->_fields['city'] = array( 'htmlType'   => 'text', 
                                        'name'       => 'city', 
                                        'title'      => ts('City'), 
                                        'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                        'is_required'=> true );
                                         
        $this->_fields['state_province_id'] = array( 'htmlType'   => 'select', 
                                                     'name'       => 'state_province_id', 
                                                     'title'      => ts('State / Province'), 
                                                     'attributes' => array( '' => ts( '- select -' ) ) +
                                                     CRM_Core_PseudoConstant::stateProvince( ),
                                                     'is_required'=> true );
        
        $this->_fields['postal_code'] = array( 'htmlType'   => 'text', 
                                               'name'       => 'postal_code', 
                                               'title'      => ts('Postal Code'), 
                                               'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                               'is_required'=> true );
        
        $this->_fields['country_id'] = array( 'htmlType'   => 'select', 
                                              'name'       => 'country_id', 
                                              'title'      => ts('Country'), 
                                              'attributes' => array( '' => ts( '- select -' ) ) + 
                                              CRM_Core_PseudoConstant::country( ),
                                              'is_required'=> true );
        
        $this->_fields['credit_card_number'] = array( 'htmlType'   => 'text', 
                                                      'name'       => 'credit_card_number', 
                                                      'title'      => ts('Card Number'), 
                                                      'attributes' => array( 'size' => 20, 'maxlength' => 20 ), 
                                                      'is_required'=> true );
                                         
        $this->_fields['cvv2'] = array( 'htmlType'   => 'text', 
                                        'name'       => 'cvv2', 
                                        'title'      => ts('Security Code'), 
                                        'attributes' => array( 'size' => 5, 'maxlength' => 10 ), 
                                        'is_required'=> true );
        
        $this->_fields['credit_card_exp_date'] = array( 'htmlType'   => 'date', 
                                                        'name'       => 'credit_card_exp_date', 
                                                        'title'      => ts('Expiration Date'), 
                                                        'attributes' => CRM_Core_SelectValues::date( 'creditCard' ),
                                                        'is_required'=> true );
        
        require_once 'CRM/Contribute/PseudoConstant.php';
        $creditCardType = array( ''           => '- select -') + CRM_Contribute_PseudoConstant::creditCard( );
        $this->_fields['credit_card_type'] = array( 'htmlType'   => 'select', 
                                                    'name'       => 'credit_card_type', 
                                                    'title'      => ts('Card Type'), 
                                                    'attributes' => $creditCardType,
                                                    'is_required'=> true );
    }

}
?>
