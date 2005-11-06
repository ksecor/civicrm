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
 * This class generates form components for Contribution Mode
 * 
 */
class CRM_Contribute_Form_Preview extends CRM_Core_Form
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
        $this->_id = $this->get( 'id' ); 
        
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

        $this->buildCustom( $this->_values['custom_pre_id'] , 'pre'  );
        $this->buildCustom( $this->_values['custom_post_id'], 'post' );

        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_ContributionMode', 'name' ) );
        $this->addRule( 'name', ts('Please enter a valid contribution mode name.'), 'required' );
        $this->addRule( 'name', ts('Name already exists in Database.'), 'objectExists', array( 'CRM_Contribute_DAO_ContributionMode', $this->_id ) );
        
        $this->add('text', 'description', ts('Description'), CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_ContributionMode', 'description' ) );

        $this->add('checkbox', 'is_active', ts('Enabled?'));

        if ($this->_action == CRM_Core_Action::UPDATE && CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionMode', $this->_id, 'is_reserved' )) { 
            $this->freeze(array('name', 'description', 'is_active' ));
        }
        
    }

    function buildAmount( ) {
        $elements = array( );

        // first build the radio boxes
        if ( ! empty( $this->_values['label'] ) ) {
            for ( $index = 1; $index <= count( $this->_values['label'] ); $index++ ) {
                $elements[] =& $this->createElement('radio', null, '',
                                                    '$' . $this->_values['value'][$index] . ' ' . $this->_values['label'][$index],
                                                    $this->_values['value'][$index] );
            }
        }

        if ( $this->_values['is_allow_other_amount'] ) {
            $textAmount =& $this->createElement('text',
                                                null,
                                                ts('Other Amount') );
            $elements[] =& $textAmount;
        }

        $this->addGroup( $elements, 'amount', ts('Amount'), '<br />' );
    }
    
    /**  
     * Function to add the custom fields
     *  
     * @return None  
     * @access public  
     */ 
    function buildCustom( $profileId, $customPosition ) { 
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

        $this->add('text', 
                   'state_province',
                   ts('State / Province'), 
                   array( 'size' => 30, 'maxlength' => 60 ) ); 

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
                   ts('Credit Card Number'), 
                   array( 'size' => 20, 'maxlength' => 20 ) );

        $this->add('text',
                   'cvv2',
                   ts('Credit Card Verification Number'),
                   array( 'size' => 5, 'maxlength' => 10 ) );

        $this->add( 'date',
                    'credit_card_exp_date',
                    ts('Credit Card Expiration Date'),
                    CRM_Core_SelectValues::date( 'fixed' ) );

        $creditCardType = array( 'Visa'       => 'Visa'      ,
                                 'MasterCard' => 'MasterCard',
                                 'Discover'   => 'Discover'  ,
                                 'Amex'       => 'Amex' );
        $this->addElement( 'select', 
                           'credit_card_type', 
                           ts('Credit Card Type'),  
                           $creditCardType );


        $this->_expressButtonName = $this->getButtonName( 'next', 'express' );
        $this->add('submit',
                   $this->_expressButtonName,
                   ts( 'Contribute via PayPal' ),
                   array( 'class' => 'form-submit' ) );
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        require_once 'CRM/Contribute/BAO/ContributionMode.php';
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Contribute_BAO_ContributionMode::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected contribution mode has been deleted.') );
        } else { 

            $params = $ids = array( );
            // store the submitted values in an array
            $params = $this->exportValues();
            
            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $ids['contributionMode'] = $this->_id;
            }
            
            $contributionMode = CRM_Contribute_BAO_ContributionMode::add($params, $ids);
            CRM_Core_Session::setStatus( ts('The contribution mode "%1" has been saved.', array( 1 => $contributionMode->name )) );
        }
    }
}

?>
