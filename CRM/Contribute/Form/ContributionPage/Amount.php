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

require_once 'CRM/Contribute/Form/ContributionPage.php';

/**
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Contribute_Form_ContributionPage_Amount extends CRM_Contribute_Form_ContributionPage 
{
    /** 
     * Constants for number of options for data types of multiple option. 
     */ 
    const NUM_OPTION = 11;
        
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        // do u want to allow a free form text field for amount 
        $this->addElement('checkbox', 'is_allow_other_amount', ts('Allow other amounts' ), null, array( 'onclick' => "minMax(this);" ) );  
        $this->add('text', 'min_amount', ts('Minimum Amount'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $this->addRule( 'min_amount', ts( 'Please enter a valid money value (e.g. 9.99).' ), 'money' );

        $this->add('text', 'max_amount', ts('Maximum Amount'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $this->addRule( 'max_amount', ts( 'Please enter a valid money value (e.g. 99.99).' ), 'money' );

        $default = array( );
        for ( $i = 1; $i <= self::NUM_OPTION; $i++ ) {
            // label 
            $this->add('text', "label[$i]", ts('Label'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_OptionValue', 'label')); 
 
            // value 
            $this->add('text', "value[$i]", ts('Value'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_OptionValue', 'value')); 
            $this->addRule("value[$i]", ts('Please enter a valid money value for this field (e.g. 99.99).'), 'money'); 

            // default
            $default[] = $this->createElement('radio', null, null, null, $i); 
        }

        $this->addGroup( $default, 'default' );
        
        $this->addElement('checkbox', 'amount_block_is_active', ts('Contribution Amounts section enabled'), null, array( 'onclick' => "amountBlock(this);" ) );

        $this->addElement('checkbox', 'is_monetary', ts('Execute real-time monetary transactions') );
        
        require_once "CRM/Contribute/BAO/ContributionPage.php";
        
        //check if selected payment processor supports recurring payment
        if ( CRM_Contribute_BAO_ContributionPage::checkRecurPaymentProcessor( $this->_id ) ) {
            $this->addElement( 'checkbox', 'is_recur', ts('Enable recurring payments'), null, 
                               array('onclick' => "return showHideByValue('is_recur',true,'recurFields','table-row','radio',false);") );
            require_once 'CRM/Core/OptionGroup.php';
            $this->addCheckBox( 'recur_frequency_unit', ts('Supported recurring units'), 
                                CRM_Core_OptionGroup::values( 'recur_frequency_units' ),
                                null, null, null, null,
                                array( '&nbsp;&nbsp;', '&nbsp;&nbsp;', '&nbsp;&nbsp;', '<br/>' ) );
            $this->addElement('checkbox', 'is_recur_interval', ts('Support recurring intervals') );
        }
        
        // add pay later options
        $this->addElement('checkbox', 'is_pay_later', ts( 'Enable pay later option?' ),
                          null, array( 'onclick' => "payLater(this);" ) );
        $this->addElement('textarea', 'pay_later_text', ts( 'Pay later label' ),  
                          CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_ContributionPage', 'pay_later_text' ),
                          false );
        $this->addElement('textarea', 'pay_later_receipt', ts( 'Pay later instructions' ),  
                          CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_ContributionPage', 'pay_later_receipt' ),
                          false );
        
        //CiviPledge fields.
        $config =& CRM_Core_Config::singleton( );
        if ( in_array('CiviPledge', $config->enableComponents) ) {
            $this->assign('CiviPledge', true );
            require_once 'CRM/Core/OptionGroup.php';
            $this->addElement( 'checkbox', 'is_pledge_active', ts('Enable Pledges for this contribution page') , 
                               null, array( 'onclick' => "pledgeBlock(this);" ) );
            $this->addCheckBox( 'pledge_frequency_unit', ts( 'Supported pledge frequencies' ), 
                                CRM_Core_OptionGroup::values("recur_frequency_units"),
                                null, null, null, null,
                                array( '&nbsp;&nbsp;', '&nbsp;&nbsp;', '&nbsp;&nbsp;', '<br/>' ));
            $this->addElement( 'checkbox', 'is_pledge_interval', ts('Allow Frequency Intervals'),
                               null, array( 'onclick' => "pledgeInterval(this);" ) );
            $this->addElement( 'text', 'initial_reminder_day', ts('Send Initial Reminder'), array('size'=>3) );
            $this->addElement( 'text', 'max_reminders', ts('Send up to'), array('size'=>3) );
            $this->addElement( 'text', 'additional_reminder_day', ts('Send additional reminders'), array('size'=>3) );
            
            $pledgeFee = array( );
            $pledgeFee[] = HTML_QuickForm::createElement( 'radio', null, ts('Pledge Frequency Interval'), 
                                                          'I want to make a one-time contribution', 0 );
            $pledgeFee[] = HTML_QuickForm::createElement( 'radio', null, ts('Pledge Frequency Interval'), 
                                                          'I pledge to contribute this amount every', 1 );
            $this->addGroup( $pledgeFee, 'pledge_frequency_interval', ts('Pledge Frequency Interval'), array( '<br />' ) );
            $this->addElement( 'text', 'installments', ts('Installments'), array('size'=>3) ); 
            $this->addElement( 'text', 'pledge_frequency', ts('Pledge Frequency'), array('size'=>3) );
            
            //Frequency unit drop-down label suffixes switch from *ly to *(s)
            $frequencyUnits = CRM_Core_OptionGroup::values('recur_frequency_units', null, null, null, null, 'name');
            foreach ( $frequencyUnits as $key => $value ) {
                $frequencyUnits[$key] = $value. '(s)';
            }
            $this->addElement( 'select', 'frequency_unit', ts( 'Frequency Unit' ), $frequencyUnits );
        }
        
        $this->addFormRule( array( 'CRM_Contribute_Form_ContributionPage_Amount', 'formRule' ) );
        
        parent::buildQuickForm( );
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
        $defaults = parent::setDefaultValues( );
        
        $title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', $this->_id, 'title' );
        CRM_Utils_System::setTitle(ts('Contribution Amounts (%1)', array(1 => $title)));
       
        require_once 'CRM/Core/OptionGroup.php'; 
        CRM_Core_OptionGroup::getAssoc( "civicrm_contribution_page.amount.{$this->_id}", $defaults );
        
        if ( isset( $defaults['default_amount_id'] ) && CRM_Utils_Array::value( 'value', $defaults ) ) {
            foreach ( $defaults['value'] as $i => $v ) {
                if ( $defaults['amount_id'][$i] == $defaults['default_amount_id'] ) {
                    $defaults['default'] = $i;
                    break;
                }
            }
        }

        if ( ! isset( $defaults['pay_later_text'] ) ||
             empty( $defaults['pay_later_text'] ) ) {
            $defaults['pay_later_text'] = ts( 'I will send payment by check' );
        }

        return $defaults;
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
    static function formRule( &$fields, &$files, $options ) 
    {  
        $errors = array( );  

        $minAmount = CRM_Utils_Array::value( 'min_amount', $fields );
        $maxAmount = CRM_Utils_Array::value( 'max_amount', $fields );
        if ( ! empty( $minAmount) && ! empty( $maxAmount ) ) {
            $minAmount = CRM_Utils_Rule::cleanMoney( $minAmount );
            $maxAmount = CRM_Utils_Rule::cleanMoney( $maxAmount );
            if ( (float ) $minAmount > (float ) $maxAmount ) {
                $errors['min_amount'] = ts( 'Minimum Amount should be less than Maximum Amount' );
            }
        }

        if ( isset( $fields['is_pay_later'] ) ) {
            if ( empty( $fields['pay_later_text'] ) ) {
                $errors['pay_later_text'] = ts( 'Please enter the text for the \'pay later\' checkbox displayed on the contribution form.' );
            }
            if ( empty( $fields['pay_later_receipt'] ) ) {
                $errors['pay_later_receipt'] = ts( 'Please enter the instructions to be sent to the contributor when they choose to \'pay later\'.' );
            }
        }
        
        if ( isset( $fields['is_recur'] ) ) {
            if ( empty( $fields['recur_frequency_unit'] ) ) {
                $errors['recur_frequency_unit'] = ts( 'Atleast one option needs to be checked.' );
            }
        }        
        
        //validation for pledge fields.
        if ( CRM_Utils_array::value( 'is_pledge_active', $fields ) ) {
            //check if we have 'Allow Frequency Intervals' and 'Pledge Frequency Interval'.
            if ( CRM_Utils_array::value( 'is_pledge_interval', $fields ) &&
                 CRM_Utils_array::value( 'pledge_frequency_interval', $fields ) ) {
                if ( is_numeric( $fields['installments'] ) ) {
                    //installments should be > 1
                    if ( $fields['installments'] < 1 ) {
                        $errors['installments'] = ts( 'Installments field must be > 1' ); 
                    } else if ( $fields['installments'] ==  1 ) {
                        $errors['installments'] = ts('Pledges consist of multiple scheduled payments. Select one-time contribution if you want to make your gift in a single payment.');
                    }
                } else if ( !empty( $fields['installments'] ) ) {
                    //installments should be numeric.
                    $errors['installments'] = ts("Please enter a valid Installments.");
                } else {
                    //installments is  required.
                    $errors['installments'] = ts( 'Installments is required field.' ); 
                }
            }
        }
        
        return $errors;
    }
 
    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
        // get the submitted form values.
        $params = $this->controller->exportValues( $this->_name );
        
        $params['id']                    = $this->_id;
        $params['is_allow_other_amount'] = CRM_Utils_Array::value('is_allow_other_amount', $params, false);
        
        $params['min_amount'] = CRM_Utils_Rule::cleanMoney( $params['min_amount'] );
        $params['max_amount'] = CRM_Utils_Rule::cleanMoney( $params['max_amount'] );

        $params['is_pay_later'] = CRM_Utils_Array::value('is_pay_later', $params, false );

        // if there are label / values, create custom options for them
        $labels  = CRM_Utils_Array::value( 'label'  , $params );
        $values  = CRM_Utils_Array::value( 'value'  , $params );
        $default = CRM_Utils_Array::value( 'default', $params ); 
        
        $params['amount_block_is_active']  = CRM_Utils_Array::value( 'amount_block_is_active', $params, false );
        $params['is_monetary']  = CRM_Utils_Array::value( 'is_monetary', $params ,false );
        $params['is_recur']  = CRM_Utils_Array::value( 'is_recur', $params ,false);

        if ( $params['is_recur'] ) {
            require_once 'CRM/Core/BAO/CustomOption.php';
            $params['recur_frequency_unit'] = 
                implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, 
                         array_keys( $params['recur_frequency_unit'] ) );
            $params['is_recur_interval'] = CRM_Utils_Array::value( 'is_recur_interval', $params ,false );
        } else {
            $params['recur_frequency_unit'] = "null";
            $params['is_recur_interval']    = "null";
        }

        $options = array( );
        
        for ( $i = 1; $i < self::NUM_OPTION; $i++ ) {
            if ( isset( $values[$i] ) &&
                 ( strlen( trim( $values[$i] ) ) > 0 ) ) {
                $options[] = array( 'label'      => trim( $labels[$i] ),
                                    'value'      => CRM_Utils_Rule::cleanMoney( trim( $values[$i] ) ),
                                    'weight'     => $i,
                                    'is_active'  => 1,
                                    'is_default' => $default == $i );
            }
        }
        
        $params['default_amount_id'] = null;
        CRM_Core_OptionGroup::createAssoc( "civicrm_contribution_page.amount.{$this->_id}",
                                           $options,
                                           $params['default_amount_id'] );
        
        require_once 'CRM/Contribute/BAO/ContributionPage.php';
        $dao = CRM_Contribute_BAO_ContributionPage::create( $params );
        
        if ( $params['is_pledge_active'] ) {
            $pledge = array();
            require_once "CRM/Core/Session.php";
            foreach ( array('frequency_unit', 'installments', 'max_reminders', 'initial_reminder_day', 'additional_reminder_day' ) as $key ) {
                $pledge[$key] = $params[$key];    
            }
            
            $session =& CRM_Core_Session::singleton();
            $pledge['contact_id'] = $session->get( 'userID' );
            $pledge['contribution_page_id'] = $this->_id;
            $pledge['contribution_type_id'] = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage', 
                                                                           $this->_id, 
                                                                           'contribution_type_id' );
            
            $pledge['frequency_interval']   = $params['pledge_frequency_interval'];
            $pledge['frequency_day']        = 1;
            $pledge['create_date']          = $pledge['start_date']=  date("Ymd");
              
            require_once 'CRM/Pledge/BAO/Pledge.php';
            CRM_Pledge_BAO_Pledge::add($pledge);
        }
       
    }
    
    /** 
     * Return a descriptive name for the page, used in wizard header 
     * 
     * @return string 
     * @access public 
     */ 
    public function getTitle( ) 
    {
        return ts( 'Amounts' );
    }
    
}

