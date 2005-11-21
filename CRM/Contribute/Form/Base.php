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
     * the id of the contribution page that we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_int;

    /**
     * the values for the contribution db object
     *
     * @var array
     * @protected
     */
    protected $_values;

    /**
     * the default values for the form
     *
     * @var array
     * @protected
     */
    protected $_defaults;

    /**
     * The params submitted by the form and computed by the app
     *
     * @var array
     * @protected
     */
    protected $_params;

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

        $this->_values = $this->get( 'values' );
        if ( ! $this->_values ) {
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
            
            $this->set( 'values', $this->_values );
        }

        $this->_contributeMode = $this->get( 'contributeMode' );
        $this->assign( 'contributeMode', $this->_contributeMode ); 

        $this->_defaults = array( );
    }

    function setDefaultValues( ) {
        return $this->_defaults;
    }

    function assignToTemplate( ) {
        $name = $this->_params['first_name'];
        if ( CRM_Utils_Array::value( 'middle_name', $this->_params ) ) {
            $name .= " {$this->_params['middle_name']}";
        }
        $name .= " {$this->_params['last_name']}";
        $this->assign( 'name', $name );

        $vars = array( 'amount', 'currencyID',
                       'street1', 'city', 'postal_code',
                       'state_province', 'country', 'credit_card_type', 'trxn_id' );

        foreach ( $vars as $v ) {
            if ( CRM_Utils_Array::value( $v, $this->_params ) ) {
                $this->assign( $v, $this->_params[$v] );
            }
        }

        $this->assign( 'credit_card_exp_date', CRM_Utils_Date::format( $this->_params['credit_card_exp_date'], '/' ) );
        $this->assign( 'credit_card_number',
                       CRM_Utils_System::mungeCreditCard( $this->_params['credit_card_number'] ) );
        
    }

}

?>
