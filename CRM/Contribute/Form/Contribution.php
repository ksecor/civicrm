<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Contribute/PseudoConstant.php';
require_once 'CRM/Core/BAO/CustomGroup.php';

/**
 * This class generates form components for processing a ontribution 
 * 
 */
class CRM_Contribute_Form_Contribution extends CRM_Core_Form
{
    /**
     * the id of the contribution that we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_id;

    /**
     * the id of the contact associated with this contribution
     *
     * @var int
     * @protected
     */
    protected $_contactID;

    /**
     * is this contribution associated with an online
     * financial transaction
     *
     * @var boolean
     * @protected 
     */ 
    protected $_online = false;

    /**
     * Store the tree of custom data and fields
     *
     * @var array
     */
    protected $_groupTree;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    public function preProcess()  
    {  
        // action
        $this->_action = CRM_Utils_Request::retrieve( 'action', $this, false, 'add' );
        $this->assign( 'action'  , $this->_action   ); 

        $this->_id        = CRM_Utils_Request::retrieve( 'id', $this );

        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return;
        }

        // current contribution id
        if ( $this->_id ) {
            require_once 'CRM/Contribute/DAO/FinancialTrxn.php';
            $trxn =& new CRM_Contribute_DAO_FinancialTrxn( );
            $trxn->entity_table = 'civicrm_contribution';
            $trxn->entity_id    = $this->_id;
            if ( $trxn->find( true ) ) {
                $this->_online = true;
            }
        }

        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', $this );

        $this->_groupTree =& CRM_Core_BAO_CustomGroup::getTree( 'Contribution', $this->_id, 0 );
        CRM_Core_BAO_CustomGroup::buildQuickForm( $this, $this->_groupTree, 'showBlocks1', 'hideBlocks1' );
        
    }

    function setDefaultValues( ) {
        $defaults = array( );

        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return $defaults;
        }

        if ( $this->_id ) {
            $ids = array( );
            $params = array( 'id' => $this->_id );
            CRM_Contribute_BAO_Contribution::getValues( $params, $defaults, $ids );
            $this->_contactID = $defaults['contact_id'];
        } else {
            $now = date("Y-m-d");
            $defaults['receive_date'] = $now;
            $defaults['receipt_date'] = $now;
        }
        
        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, false, false );
        }
        return $defaults;
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

        if ( $this->_action & CRM_Core_Action::DELETE ) {
            $this->addButtons(array( 
                                    array ( 'type'      => 'next', 
                                            'name'      => ts('Delete'), 
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                            'isDefault' => true   ), 
                                    array ( 'type'      => 'cancel', 
                                            'name'      => ts('Cancel') ), 
                                    ) 
                              );
            return;
        }

        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Contribution' );

        $element =& $this->add('select', 'contribution_type_id', 
                               ts( 'Contribution Type' ), 
                               array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( ),
                               true );
        if ( $this->_online ) {
            $element->freeze( );
        }

        $element =& $this->add('select', 'payment_instrument_id', 
                               ts( 'Paid By' ), 
                               array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::paymentInstrument( )
                               );
        if ( $this->_online ) {
            $element->freeze( );
        }

        // add various dates
        $element =& $this->add('date', 'receive_date', ts('Received'), CRM_Core_SelectValues::date('manual', 3, 1), false );         
        $this->addRule('receive_date', ts('Select a valid date.'), 'qfDate');
        if ( $this->_online ) {
            $element->freeze( );
        }

        $this->addElement('date', 'receipt_date', ts('Receipt Sent'), CRM_Core_SelectValues::date('manual', 3, 1)); 
        $this->addRule('receipt_date', ts('Select a valid date.'), 'qfDate');

        $this->addElement('date', 'thankyou_date', ts('Thank-you Sent'), CRM_Core_SelectValues::date('manual', 3, 1)); 
        $this->addRule('thankyou_date', ts('Select a valid date.'), 'qfDate');

        $this->addElement('date', 'cancel_date', ts('Cancelled'), CRM_Core_SelectValues::date('manual', 3, 1)); 
        $this->addRule('cancel_date', ts('Select a valid date.'), 'qfDate');

        $this->add('textarea', 'cancel_reason', ts('Cancellation Reason'), $attributes['cancel_reason'] );

        // add various amounts
        $element =& $this->add( 'text', 'non_deductible_amount', ts('Non-deductible Amount'),
                                $attributes['non_deductible_amount'] );
        $this->addRule('non_deductible_amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }

        $element =& $this->add( 'text', 'total_amount', ts('Total Amount'),
                                $attributes['total_amount'], true );
        $this->addRule('total_amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }

        $element =& $this->add( 'text', 'fee_amount', ts('Fee Amount'),
                                $attributes['fee_amount'] );
        $this->addRule('fee_amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }

        $element =& $this->add( 'text', 'net_amount', ts('Net Amount'),
                                $attributes['net_amount'] );
        $this->addRule('net_amount', ts('Please enter a valid amount.'), 'money');
        if ( $this->_online ) {
            $element->freeze( );
        }

        $element =& $this->add( 'text', 'trxn_id', ts('Transaction ID'), 
                                $attributes['trxn_id'] );
        if ( $this->_online ) {
            $element->freeze( );
        }

        $element =& $this->add( 'text', 'invoice_id', ts('Invoice ID'), 
                                $attributes['invoice_id'] );
        if ( $this->_online ) {
            $element->freeze( );
        }

        $element =& $this->add( 'text', 'source', ts('Source'),
                                $attributes['source'] );
        if ( $this->_online ) {
            $element->freeze( );
        }
        
        $this->add('textarea', 'note', ts('Notes'), $attributes['note'] );

        $this->addButtons(array( 
                                array ( 'type'      => 'next', 
                                        'name'      => ts('Save'), 
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
                                        'isDefault' => true   ), 
                                array ( 'type'      => 'cancel', 
                                        'name'      => ts('Cancel') ), 
                                ) 
                          );

        $this->addFormRule( array( 'CRM_Contribute_Form_Contribution', 'formRule' ), $this );

        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->freeze( );
        }
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
        return $errors;
    }


    /** 
     * Function to process the form 
     * 
     * @access public 
     * @return None 
     */ 
    public function postProcess()  
    { 
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            require_once 'CRM/Contribute/BAO/Contribution.php';
            CRM_Contribute_BAO_Contribution::deleteContribution( $this->_id );
            return;
        }

        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );

        $config =& CRM_Core_Config::singleton( );

        $params = array( );
        $ids    = array( );

        $params['contact_id'] = $this->_contactID;
        $params['currency'  ] = $config->defaultCurrency;

        $fields = array( 'contribution_type_id',
                         'payment_instrument_id',
                         'non_deductible_amount',
                         'total_amount',
                         'fee_amount',
                         'net_amount',
                         'trxn_id',
                         'invoice_id',
                         'cancel_reason',
                         'source',
                         'note' );

        foreach ( $fields as $f ) {
            $params[$f] = CRM_Utils_Array::value( $f, $formValues );
        }

        $dates = array( 'receive_date',
                        'receipt_date',
                        'thankyou_date',
                        'cancel_date' );
        foreach ( $dates as $d ) {
            if ( ! CRM_Utils_System::isNull( $formValues[$d] ) ) {
                $params[$d] = CRM_Utils_Date::format( $formValues[$d] );
            }
        }

        $ids['contribution'] = $params['id'] = $this->_id;

        $contribution =& CRM_Contribute_BAO_Contribution::create( $params, $ids );

        // do the updates/inserts
        CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $formValues );
        CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree, 'Contribution', $contribution->id);
    }

}

?>
