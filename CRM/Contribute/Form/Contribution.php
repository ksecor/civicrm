<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
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
     * the id of the premium that we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_premiumId;


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
     * Stores all producuct option
     *
     * @var boolean
     * @protected 
     */ 
    protected $_options ;

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
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String',
                                                      $this, false, 'add' );
        $this->assign( 'action'  , $this->_action   ); 

        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                                         $this );

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

        //to get Premium id 
        if( $this->_id ) {
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->contribution_id = $this->_id;
            if ( $dao->find(true) ) {
                $this->_premiumId = $dao->id;
            }
            
        }
        
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive',
                                                         $this );

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
        }
        
        if( isset($this->_groupTree) ) {
            CRM_Core_BAO_CustomGroup::setDefaults( $this->_groupTree, $defaults, false, false );
        }
        $this->assign('showOption',true);
        // for Premium section
        if( $this->_premiumId ) {
            $this->assign('showOption',false);
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->id = $this->_premiumId;
            $dao->find(true);
            //if($this->_options[$dao->product_id];)
            $options = $this->_options[$dao->product_id];
            if ( ! $options ) {
                $this->assign('showOption',true);
            }
            $options_key = CRM_Utils_Array::key($dao->product_option,$options);
            if( $options_key) {
                $defaults['product_name']   = array ( $dao->product_id , trim($options_key) );
            } else {
                $defaults['product_name']   = array ( $dao->product_id);
            }
            $defaults['fulfilled_date'] = $dao->fulfilled_date;
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
        $this->buldPremiumForm($this);
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
            $this->assign("hideCalender" , true );
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
        
        $this->add('textarea', 'note', ts('Notes'),array("rows"=>4,"cols"=>60) );


        $session = & CRM_Core_Session::singleton( );
        $uploadNames = $session->get( 'uploadNames' );
        if ( is_array( $uploadNames ) && ! empty ( $uploadNames ) ) {
            $buttonType = 'upload';
        } else {
            $buttonType = 'next';
        }
        
        $this->addButtons(array( 
                                array ( 'type'      => $buttonType, 
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
        //print_r($formValues);

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
        $currentTime = getDate();        
        foreach ( $dates as $d ) {
            if ( ! CRM_Utils_System::isNull( $formValues[$d] ) ) {
                $formValues[$d]['H'] = $currentTime['hours'];
                $formValues[$d]['i'] = $currentTime['minutes'];
                $formValues[$d]['s'] = '00';
                $params[$d] = CRM_Utils_Date::format( $formValues[$d] );
            }
        }

        $ids['contribution'] = $params['id'] = $this->_id;

        $contribution =& CRM_Contribute_BAO_Contribution::create( $params, $ids );

        // do the updates/inserts
        CRM_Core_BAO_CustomGroup::postProcess( $this->_groupTree, $formValues );
        
        //process premium
        if( $formValues['product_name'][0] ) {
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            $dao = & new CRM_Contribute_DAO_ContributionProduct();
            $dao->contribution_id = $contribution->id;
            $dao->product_id  = $formValues['product_name'][0];
            $dao->fulfilled_date  = CRM_Utils_Date::format($formValues['fulfilled_date']);
            $dao->product_option = $this->_options[$formValues['product_name'][0]][$formValues['product_name'][1]];
            if ($this->_premiumId) {
                $premoumDAO = & new CRM_Contribute_DAO_ContributionProduct();
                $premoumDAO->id  = $this->_premiumId;
                $premoumDAO->find(true);
                if( $premoumDAO->product_id == $formValues['product_name'][0] ) {
                    $dao->id = $this->_premiumId;
                    $premium = $dao->save();
                } else {
                    $premoumDAO->delete();
                    $premium = $dao->save();
                }
            } else {
                $premium = $dao->save();
            }
        }
        CRM_Core_BAO_CustomGroup::updateCustomData($this->_groupTree, 'Contribution', $contribution->id);
    }

    /** 
     * Function to build the form for Premium 
     * 
     * @access public 
     * @return None 
     */ 
    
    function buldPremiumForm( $form)
    {
        require_once 'CRM/Contribute/DAO/Product.php';
        $sel1 = $sel2 = array();
        
        $dao = & new CRM_Contribute_DAO_Product();
        $dao->is_active = 1;
        $dao->find();
        $min_amount = array();
        $sel1[0] = '-select product-';
        while ( $dao->fetch() ) {
            
            $sel1[$dao->id] = $dao->name." ( ".$dao->sku." )";
            $min_amount[$dao->id] = $dao->min_contribution;
            $options = explode(',', $dao->options);
            foreach ($options as $k => $v ) {
                $options[$k] = trim($v);
            }
            if( $options [0] != '' ) {
                $sel2[$dao->id] = $options;
            }
            
            $form->assign('premiums', true );
            
        }
        $form->_options = $sel2;
        $form->assign('mincontribution',$min_amount);
        $sel =& $this->addElement('hierselect', "product_name", ts('Premium'),'onclick="showMinContrib();"');
        $js = "<script type='text/javascript'>\n";
        $formName = 'document.forms.' . $form->_name;
        for ( $k = 1; $k < 2; $k++ ) {
            if (!$defaults['product_name'][$k]) {
                $js .= "{$formName}['product_name[$k]'].style.display = 'none';\n"; 
            }
        }

        $sel->setOptions(array($sel1, $sel2 ));
        $js .= "</script>\n";
        $form->assign('initHideBoxes', $js);

        $form->addElement('date', 'fulfilled_date', ts('Fulfilled'), CRM_Core_SelectValues::date('manual', 3, 1));
        $form->addElement('text', 'min_amount', ts('Minimum Contribution Amount'));
    }

}

?>
