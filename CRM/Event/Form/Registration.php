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
     * the mode that we are in
     * 
     * @var string
     * @protect
     */
    public $_mode;

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
        
        // current mode
        $this->_mode = ( $this->_action == 1024 ) ? 'test' : 'live';
        
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
            CRM_Core_BAO_CustomOption::getAssoc( 'civicrm_event', $this->_id, $this->_values['custom'] );

            // get the profile ids
            require_once 'CRM/Core/BAO/UFJoin.php'; 
            $ufJoinParams = array( 'entity_table' => 'civicrm_event',   
                                   'entity_id'    => $this->_id,   
                                   'weight'       => 1 ); 
            $this->_values['custom_pre_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams ); 
            $ufJoinParams['weight'] = 2; 
            $this->_values['custom_post_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );
            
            $params = array( 'event_id' => $this->_id );
            require_once 'CRM/Event/BAO/EventPage.php';
            CRM_Event_BAO_EventPage::retrieve($params, $this->_values['event_page']);
            
            if ( $this->_values['event']['is_monetary'] ) {
                $this->setCreditCardFields( );
            }

            $this->set( 'values', $this->_values );
            $this->set( 'fields', $this->_fields );
        }

        $this->_contributeMode = $this->get( 'contributeMode' );
        $this->assign( 'contributeMode', $this->_contributeMode );

        // setting CMS page title
        CRM_Utils_System::setTitle($this->_values['event']['title']);  
        $this->assign( 'title', $this->_values['event']['title'] );
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

    /** 
     * assign the minimal set of variables to the template
     *                                                           
     * @return void 
     * @access public 
     */ 
    function assignToTemplate( ) {
        $name = $this->_params['first_name'];
        if ( CRM_Utils_Array::value( 'middle_name', $this->_params ) ) {
            $name .= " {$this->_params['middle_name']}";
        }
        $name .= " {$this->_params['last_name']}";
        $this->assign( 'name', $name );
        $this->set( 'name', $name );

        $vars = array( 'amount', 'currencyID', 'credit_card_type', 
                       'trxn_id', 'amount_level', 'receive_date' );
        
        foreach ( $vars as $v ) {
            if ( CRM_Utils_Array::value( $v, $this->_params ) ) {
                $this->assign( $v, $this->_params[$v] );
            }
        }

        // assign the address formatted up for display
        $addressParts  = array('street_address', 'city', 'postal_code', 'state_province', 'country');
        $addressFields = array();
        foreach ($addressParts as $part) {
            $addressFields[$part] = $this->_params[$part];
        }
        require_once 'CRM/Utils/Address.php';
        $this->assign('address', CRM_Utils_Address::format($addressFields));

        if ( $this->_contributeMode == 'direct' ) {
            $date = CRM_Utils_Date::format( $this->_params['credit_card_exp_date'] );
            $date = CRM_Utils_Date::mysqlToIso( $date );
            $this->assign( 'credit_card_exp_date', $date );
            $this->assign( 'credit_card_number',
                           CRM_Utils_System::mungeCreditCard( $this->_params['credit_card_number'] ) );
        }

        //$this->assign( 'email', $this->_values['event_page'][''] );

        // also assign the receipt_text
        $this->assign( 'receipt_text', $this->_values['event_page']['confirm_email_text'] );
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

}
?>
