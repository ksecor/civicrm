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

class CRM_Contribute_Form_AdditionalInfo 
{
    /** 
     * Function to build the form for Premium Information. 
     * 
     * @access public 
     * @return None 
     */ 
    function buildPremium( &$form )
    { 
        //premium section
        $form->add( 'hidden', 'hidden_Premium', 1 );
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
        $sel =& $form->addElement('hierselect', "product_name", ts('Premium'),'onclick="showMinContrib();"');
        $js = "<script type='text/javascript'>\n";
        $formName = 'document.forms.' . $form->getName( );
        
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
        $form->add( 'hidden', 'hidden_AdditionalDetail', 1 );
        
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
        $form->add( 'hidden', 'hidden_Honoree', 1 );
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
     * Function to build the form for PaymentReminders Information. 
     * 
     * @access public 
     * @return None 
     */ 
    function buildPaymentReminders( &$form )
    { 
        //PaymentReminders section
        $form->add( 'hidden', 'hidden_PaymentReminders', 1 );
        $form->add( 'text', 'initial_reminder_day', ts('Send Initial Reminder'), array('size'=>3) );
        $this->addRule('initial_reminder_day', ts('Please enter a valid reminder day.'), 'positiveInteger');
        $form->add( 'text', 'max_reminders', ts('Send up to'), array('size'=>3) );
        $this->addRule('max_reminders', ts('Please enter a valid No. of reminders.'), 'positiveInteger');
        $form->add( 'text', 'additional_reminder_day', ts('Send additional reminders'), array('size'=>3) );
        $this->addRule('additional_reminder_day', ts('Please enter a valid additional reminder day.'), 'positiveInteger');
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

        $customData = array( );
        foreach ( $params as $key => $value ) {
            if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID( $key ) ) {
                CRM_Core_BAO_CustomField::formatCustomField( $customFieldId, $customData,
                                                             $value, 'Contribution', null, $params['id'] );
            }
        }
        
        if ( ! empty($customData) ) {
            $formatted['custom'] = $customData;
        }
        
        //special case to handle if all checkboxes are unchecked
        $customFields = CRM_Core_BAO_CustomField::getFields( 'Contribution' );
        
        if ( !empty($customFields) ) {
            foreach ( $customFields as $k => $val ) {
                if ( in_array ( $val[3], array ('CheckBox','Multi-Select') ) &&
                     ! CRM_Utils_Array::value( $k, $formatted['custom'] ) ) {
                    CRM_Core_BAO_CustomField::formatCustomField( $k, $formatted['custom'],
                                                                 '', 'Contribution', null, $params['id'] );
                }
            }
        }
    }
    
    /** 
     * Function to send email receipt.
     * 
     * @form object  of Contribution form.
     * @param array  $params (reference ) an assoc array of name/value pairs.
     * @$ccContribution boolen,  is it credit card contribution.
     * @access public. 
     * @return None.
     */ 
    function emailReceipt( &$form, &$params, $ccContribution = false )
    {
        // Retrieve Contribution Type Name from contribution_type_id
        $params['contributionType_name'] = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionType',
                                                                        $params['contribution_type_id'] );         
        
        // retrieve payment instrument name
        $paymentInstrumentGroup = array();
        $paymentInstrumentGroup['name'] = 'payment_instrument';
        require_once 'CRM/Core/BAO/OptionGroup.php';
        CRM_Core_BAO_OptionGroup::retrieve($paymentInstrumentGroup, $paymentInstrumentGroup);
        $paymentInstrument = array();
        $paymentInstrument['value']            = $params['payment_instrument_id'];      
        $paymentInstrument['option_group_id']  = $paymentInstrumentGroup['id'];
        require_once 'CRM/Core/BAO/OptionValue.php';
        CRM_Core_BAO_OptionValue::retrieve($paymentInstrument, $paymentInstrument);
        $params['paidBy'] = $paymentInstrument['label'];
        
        // retrieve individual prefix value for honoree
        if ( CRM_Utils_Array::value( 'hidden_Honoree', $params ) ) {
            $individualPrefixGroup = array();
            $individualPrefixGroup['name'] = 'individual_prefix';
            require_once 'CRM/Core/BAO/OptionGroup.php';
            CRM_Core_BAO_OptionGroup::retrieve($individualPrefixGroup, $individualPrefixGroup);
            $individualPrefix = array();
            $individualPrefix['value']            = $params['honor_prefix_id'];      
            $individualPrefix['option_group_id']  = $individualPrefixGroup['id'];
            require_once 'CRM/Core/BAO/OptionValue.php';
            CRM_Core_BAO_OptionValue::retrieve($individualPrefix,$individualPrefix );
            $params['honor_prefix'] = $individualPrefix['label'];
            $honor  = CRM_Core_PseudoConstant::honor( ); 
            $params["honor_type"] = $honor[$params["honor_type_id"]];
        }
        
        // retrieve premium product name and assigned fulfilled
        // date to template
        if ( CRM_Utils_Array::value( 'hidden_Premium', $params ) ) {
            $params['product_option'] = $form->_options[$params['product_name'][0]][$params['product_name'][1]];
            require_once 'CRM/Contribute/DAO/Product.php';
            $productDAO =& new CRM_Contribute_DAO_Product();
            $productDAO->id = $params['product_name'][0];
            $productDAO->find(true);
            $params['product_name'] = $productDAO->name;
            $params['product_sku']  = $productDAO->sku;
            $this->assign('fulfilled_date', CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($params['fulfilled_date'])));
        }
        
        $this->assign( 'ccContribution', $ccContribution );
        if ( $ccContribution ) {
            //build the name.
            $name = CRM_Utils_Array::value( 'billing_first_name', $params );
            if ( CRM_Utils_Array::value( 'billing_middle_name', $params ) ) {
                $name .= " {$params['billing_middle_name']}";
            }
            $name .= ' ' . CRM_Utils_Array::value( 'billing_last_name', $params );
            $name = trim( $name );
            $this->assign( 'billingName', $name );
            
            //assign the address formatted up for display
            $addressParts  = array( "street_address-{$form->_bltID}",
                                    "city-{$form->_bltID}",
                                    "postal_code-{$form->_bltID}",
                                    "state_province-{$form->_bltID}",
                                    "country-{$form->_bltID}");
            $addressFields = array( );
            foreach ( $addressParts as $part) {
                list( $n, $id ) = explode( '-', $part );
                $addressFields[$n] = CRM_Utils_Array::value( $part, $params );
            }
            require_once 'CRM/Utils/Address.php';
            $this->assign('address', CRM_Utils_Address::format( $addressFields ) );
            
            $date = CRM_Utils_Date::format( $params['credit_card_exp_date'] );  
            $date = CRM_Utils_Date::mysqlToIso( $date ); 
            $this->assign( 'credit_card_type', CRM_Utils_Array::value( 'credit_card_type', $params ) );
            $this->assign( 'credit_card_exp_date', $date );
            $this->assign( 'credit_card_number',
                           CRM_Utils_System::mungeCreditCard( $params['credit_card_number'] ) );
        } else {
            //offline contribution
            //Retrieve the name and email from receipt is to be send
            $params['receipt_from_name'] = $form->userDisplayName;
            $params['receipt_from_email']= $form->userEmail;
            // assigned various dates to the templates
            $form->assign('receive_date', CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($formValues['receive_date'])));
            $form->assign('receipt_date', CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($formValues['receipt_date'])));
            $form->assign('thankyou_date', CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($formValues['thankyou_date'])));
            $form->assign('cancel_date', CRM_Utils_Date::MysqlToIso(CRM_Utils_Date::format($formValues['cancel_date'])));
            
            //retrieve custom data
            if ( CRM_Utils_Array::value( 'hidden_custom', $params ) ) {
                $showCustom = 0;
                $customData = array( );
                foreach ( $params as $key => $value ) {
                    if ( $customFieldId = CRM_Core_BAO_CustomField::getKeyID($key) ) {
                        $fieldID['id'] = $customFieldId;
                        CRM_Core_BAO_CustomField::retrieve( $fieldID, $customData);
                        $customField[$customData['label']] = $value;
                        if ($value) {
                            $showCustom = 1;
                        }
                    }
                }
                $form->assign('showCustom',$showCustom);
                $form->assign_by_ref('customField',$customField);
            }
        }
        
        $form->assign_by_ref( 'formValues', $params );
        require_once 'CRM/Contact/BAO/Contact.php';
        list( $contributorDisplayName, 
              $contributorEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $params['contact_id'] );
        $template =& CRM_Core_Smarty::singleton( );
        $message = $template->fetch( 'CRM/Contribute/Form/Message.tpl' );
        $session =& CRM_Core_Session::singleton( );
        $userID = $session->get( 'userID' );
        list( $userName, $userEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $userID );
        $receiptFrom = '"' . $userName . '" <' . $userEmail . '>';
        $subject = ts('Contribution Receipt');
        require_once 'CRM/Utils/Mail.php';
        CRM_Utils_Mail::send( $receiptFrom,
                              $contributorDisplayName,
                              $contributorEmail,
                              $subject,
                              $message);
        
    }
    
}

?>