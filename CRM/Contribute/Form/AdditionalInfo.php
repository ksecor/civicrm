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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Contribute/PseudoConstant.php';
require_once 'CRM/Core/BAO/CustomGroup.php';
require_once 'CRM/Contribute/Form/Contribution.php';

class CRM_Contribute_Form_AdditionalInfo extends CRM_Contribute_Form_Contribution {
    
    /** 
     * Function to build the form for Premium Information. 
     * 
     * @access public 
     * @return None 
     */ 
    function buildPremium( &$form )
    { 
        //premium section
        $form->add( 'hidden', 'hidden_buildPremium', 1 );
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
        if ( $form->_name == 'AdditionalInfo' ) {
            $formName = 'document.forms.' . 'Contribution';
        } else {
            $formName = 'document.forms.' . $form->_name;   
        }
        for ( $k = 1; $k < 2; $k++ ) {
            if ( ! isset ($defaults['product_name'][$k] )|| (! $defaults['product_name'][$k] ) )  {
                $js .= "{$formName}['product_name[$k]'].style.display = 'none';\n"; 
            }
        }
        
        $sel->setOptions(array($sel1, $sel2 ));
        $js .= "</script>\n";
        $form->assign('initHideBoxes', $js);
        $form->addElement('date', 'fulfilled_date', ts('Fulfilled'), CRM_Core_SelectValues::date('activityDate'));
        $form->addRule( 'fulfilled_date', ts('Select a valid date.'), 'qfDate'); 
        
        $form->addElement('text', 'min_amount', ts('Minimum Contribution Amount'));
        
    }
    
    /** 
     * Function to build the form for Additional Details. 
     * 
     * @access public 
     * @return None 
     */ 
    function buildAdditionalDetail( &$form )
    { 
        //Additional information section
        $form->add( 'hidden', 'hidden_buildAdditionalDetail', 1 );
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Contribution' );
        
        $form->addElement('date', 'thankyou_date', ts('Thank-you Sent'), CRM_Core_SelectValues::date('activityDate')); 
        $form->addRule('thankyou_date', ts('Select a valid date.'), 'qfDate');
        // add various amounts
        $element =& $form->add( 'text', 'non_deductible_amount', ts('Non-deductible Amount'),
                                $attributes['non_deductible_amount'] );
        $form->addRule('non_deductible_amount', ts('Please enter a valid amount.'), 'money');
        
        if ( $form->_online ) {
            $element->freeze( );
        }
        $element =& $form->add( 'text', 'fee_amount', ts('Fee Amount'), 
                                $attributes['fee_amount'] );
        $form->addRule('fee_amount', ts('Please enter a valid amount.'), 'money');
        if ( $form->_online ) {
            $element->freeze( );
        }
        $element =& $form->add( 'text', 'net_amount', ts('Net Amount'),
                                $attributes['net_amount'] );
        $form->addRule('net_amount', ts('Please enter a valid amount.'), 'money');
        if ( $form->_online ) {
            $element->freeze( );
        }
        $element =& $form->add( 'text', 'invoice_id', ts('Invoice ID'), 
                                $attributes['invoice_id'] );
        if ( $form->_online ) {
            $element->freeze( );
        } 
        $form->add('textarea', 'note', ts('Notes'),array("rows"=>4,"cols"=>60) );
        
        if ( $form->_name == 'Contribution' || $form->_name == 'AdditionalInfo' ) {
            $element =& $form->add( 'text', 'trxn_id', ts('Transaction ID'), 
                                    $attributes['trxn_id'] );
            if ( $form->_online ) {
                $element->freeze( );
            } else {
                $form->addRule( 'trxn_id',
                                ts( 'This Transaction ID already exists in the database. Include the account number for checks.' ),
                                'objectExists', 
                                array( 'CRM_Contribute_DAO_Contribution', $form->_id, 'trxn_id' ) );
            }
        }
    }
    
    /** 
     * Function to build the form for Honoree Information. 
     * 
     * @access public 
     * @return None 
     */ 
    function buildHonoree( &$form )
    { 
        //Honoree section
        $form->add( 'hidden', 'hidden_buildHonoree', 1 );
        $honor = CRM_Core_PseudoConstant::honor( ); 
        foreach ( $honor as $key => $var) {
            $honorTypes[$key] = HTML_QuickForm::createElement('radio', null, null, $var, $key);
        }
        $form->addGroup($honorTypes, 'honor_type_id', null);
        $form->add('select','honor_prefix_id',ts('Prefix') ,array('' => ts('- prefix -')) + CRM_Core_PseudoConstant::individualPrefix());
        $form->add('text','honor_first_name',ts('First Name'));
        $form->add('text','honor_last_name',ts('Last Name'));
        $form->add('text','honor_email',ts('Email'));
        $form->addRule( "honor_email", ts('Email is not valid.'), 'email' );
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
    static function formRule( &$fields, &$files, $self ) 
    {  
        $errors = array( ); 
        
        if ( isset( $fields["honor_type_id"] ) ) {
            if ( !((  CRM_Utils_Array::value( 'honor_first_name', $fields ) && 
                      CRM_Utils_Array::value( 'honor_last_name' , $fields )) ||
                   CRM_Utils_Array::value( 'honor_email' , $fields ) )) {
                $errors['hidden_buildHonoree'] = ts('Honor First Name and Last Name OR an email should be set.');
            }
        }
        return $errors;
    }
    
    /** 
     * Function to process the Premium Information 
     * 
     * @access public 
     * @return None 
     */ 
    function processPremium( &$params, $contributionID, $premiumID = null, &$options = null )
    {
        require_once 'CRM/Contribute/DAO/ContributionProduct.php';
        $dao = & new CRM_Contribute_DAO_ContributionProduct();
        $dao->contribution_id = $contributionID;
        $dao->product_id      = $params['product_name'][0];
        $dao->fulfilled_date  = CRM_Utils_Date::format($params['fulfilled_date']);
        $dao->product_option  = $options[$params['product_name'][0]][$params['product_name'][1]];
        if ($premiumID) {
            $premoumDAO = & new CRM_Contribute_DAO_ContributionProduct();
            $premoumDAO->id  = $premiumID;
            $premoumDAO->find(true);
            if ( $premoumDAO->product_id == $params['product_name'][0] ) {
                $dao->id = $premiumID;
                $premium = $dao->save();
            } else {
                $premoumDAO->delete();
                $premium = $dao->save();
            }
        } else {
            $premium = $dao->save();
        } 
    }
    
    /** 
     * Function to process the Note 
     * 
     * @access public 
     * @return None 
     */ 
    function processNote( &$params, $contactID, $contributionID, $contributionNoteID = null )
    {
        //process note
        require_once 'CRM/Core/BAO/Note.php';
        $noteParams = array('entity_table' => 'civicrm_contribution', 
                            'note'         => $params['note'], 
                            'entity_id'    => $contributionID,
                            'contact_id'   => $contactID
                            );
        $noteID = array();
        if ( $contributionNoteID ) {
            $noteID = array( "id" => $contributionNoteID );
            $noteParams['note'] = $noteParams['note'] ? $noteParams['note'] : "null";
        } 
        CRM_Core_BAO_Note::add( $noteParams, $noteID );
    }
    
    /** 
     * Function to process the Common data 
     *  
     * @access public 
     * @return None 
     */ 
    function postProcessCommon( &$params, &$formatted )
    {
        $fields = array( 'non_deductible_amount',
                         'total_amount',
                         'fee_amount',
                         'net_amount',
                         'trxn_id',
                         'invoice_id',
                         'honor_type_id'
                         );
        foreach ( $fields as $f ) {
            $formatted[$f] = CRM_Utils_Array::value( $f, $params );
        }
        
        foreach ( array( 'non_deductible_amount', 'total_amount', 'fee_amount', 'net_amount' ) as $f ) {
            $formatted[$f] = CRM_Utils_Rule::cleanMoney( $params[$f] );
        }
        
        if ( ! CRM_Utils_System::isNull( $params['thankyou_date'] ) ) {
            $formatted['thankyou_date']['H'] = '00';
            $formatted['thankyou_date']['i'] = '00';
            $formatted['thankyou_date']['s'] = '00';
            $formatted['thankyou_date'] = CRM_Utils_Date::format( $params['thankyou_date'] );
        } else {
            $formatted['thankyou_date'] = 'null';
        }
        
        if ( CRM_Utils_Array::value( 'honor_type_id', $params ) ) {
            require_once 'CRM/Contribute/BAO/Contribution.php';
            if ( $this->_honorID ) {
                $honorId = CRM_Contribute_BAO_Contribution::createHonorContact( $params , $this->_honorID );
            } else {
                $honorId = CRM_Contribute_BAO_Contribution::createHonorContact( $params );
            }
            $formatted["honor_contact_id"] = $honorId;
        } else {
            $formatted["honor_contact_id"] = 'null';
        } 
    }
    
}

?>