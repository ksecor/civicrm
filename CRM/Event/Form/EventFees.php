<?PHP

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */


/**
 * This class generates form components for processing a participation fee block 
 */
class CRM_Event_Form_EventFees
{
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    static function preProcess( &$form )  
    {
        $form->_eventId    = CRM_Utils_Request::retrieve( 'eventId', 'Positive', $form );
        $form->_pId        = CRM_Utils_Request::retrieve( 'participantId', 'Positive', $form );
        $form->_discountId = CRM_Utils_Request::retrieve( 'discountId', 'Positive', $form );
    }
    
    /**
     * This function sets the default values for the form in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    static function setDefaultValues( &$form ) 
    { 
        $defaults = array( );
        
        if ( $form->_pId ) {
            $ids    = array( );
            $params = array( 'id' => $form->_pId );
            
            require_once "CRM/Event/BAO/Participant.php";
            CRM_Event_BAO_Participant::getValues( $params, $defaults, $ids );
            if ( $form->_action == CRM_Core_Action::UPDATE ) {
                $discounts = array( );
                if ( !empty( $form->_values['discount'] ) ) {
                    foreach( $form->_values['discount'] as $key => $value ) { 
                        $discounts[$key] = $value['name'];
                    }
                }

                if ( $form->_discountId ) {
                    $form->assign( 'discount', $discounts[$defaults[$form->_pId]['discount_id']] );
                }
                
                $form->assign( 'fee_amount', $defaults[$form->_pId]['fee_amount'] );
                $form->assign( 'fee_level', $defaults[$form->_pId]['fee_level'] );
            }
            $defaults[$form->_pId]['send_receipt'] = 0;
        } else {
            $defaults[$form->_pId]['send_receipt'] = 1;
            if ( $form->_eventId ) {
                $defaults[$form->_pId]['receipt_text'] = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event',
                                                                                      $form->_eventId, 
                                                                                      'confirm_email_text'
                                                                                      );
            }
            $today_date = getDate();
            $defaults[$form->_pId]['receive_date']['M'] = $today_date['mon'];
            $defaults[$form->_pId]['receive_date']['d'] = $today_date['mday'];
            $defaults[$form->_pId]['receive_date']['Y'] = $today_date['year'];
        }
        if ( $form->_mode ) {
            $fields = array( );
            
            foreach ( $form->_fields as $name => $dontCare ) {
                $fields[$name] = 1;
            }
            
            $names = array("first_name", "middle_name", "last_name" );
            foreach ($names as $name) {
                $fields[$name] = 1;
            }
            
            $fields["state_province-{$form->_bltID}"] = 1;
            $fields["country-{$form->_bltID}"       ] = 1;
            $fields["email-{$form->_bltID}"         ] = 1;
            $fields["email-Primary"                 ] = 1;
            
            require_once "CRM/Core/BAO/UFGroup.php";
            CRM_Core_BAO_UFGroup::setProfileDefaults( $form->_contactID, $fields, $form->_defaults );
            
            $defaultAddress = array("street_address-5","city-5", "state_province_id-5", "country_id-5","postal_code-5" );
            foreach ($defaultAddress as $name) {
                if ( ! empty( $form->_defaults[$name] ) ) {
                    $defaults[$form->_pId][$name] = $form->_defaults[$name];
                }
            } 
            
            foreach ($names as $name) {
                if ( ! empty( $form->_defaults[$name] ) ) {
                    $defaults[$form->_pId]["billing_" . $name] = $form->_defaults[$name];
                }
            } 
        }

        require_once 'CRM/Core/BAO/PriceSet.php';
        if ( $priceSetId = CRM_Core_BAO_PriceSet::getFor( 'civicrm_event', $form->_eventId ) ) {
            $fields = $priceOptionValues = array( );
            
            if ( CRM_Utils_Array::value( 'fee_level', $defaults[$form->_pId] ) ) {
                $tmp_id = substr( $defaults[$form->_pId]['fee_level'], 
                                  strchr($defaults[$form->_pId]['fee_level'], '.') );
                $eventLevel = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $tmp_id );

                //FIXME we need to reevaluate mapping of price set
                //fields to option group and values.
                //since custom fields option values may get
                //collides with price set option value labels.
                foreach ( $eventLevel as $id => $name ) {
                    $optionValue         = new CRM_Core_BAO_OptionValue( );
                    $optionValue->label  = $name;
                    $optionValue->find( );
                    while ( $optionValue->fetch( ) ) {
                        if ( $optionValue->option_group_id ) {
                            $groupName = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup', 
                                                                      $optionValue->option_group_id, 'name' );
                            
                            //hack to avoid collision of custom fields
                            //option labels with price set fields labels.
                            if ( strpos( $groupName, 'civicrm_price_field.amount' ) === 0 ) {
                                $fieldName = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_PriceField', 
                                                                          substr( $groupName, 27 ), 'label') ;
                                $eventLevel[$id] = array( 'fieldName'   => $fieldName,
                                                          'optionLabel' => $name );
                            }
                        }
                    }
                }
                
                //for the texfield default value
                foreach ( $eventLevel as $id => $values ) {
                    if( !is_array( $values ) ){
                        $textLevel       = explode( ' - ', $values );
                        $eventLevel[$id] = array( 'fieldName'   => CRM_Utils_Array::value( '0', $textLevel ),
                                                  'optionLabel' => CRM_Utils_Array::value( '1', $textLevel ) );
                    }       
                }
                
                require_once 'CRM/Core/BAO/PriceField.php';
                foreach ( $eventLevel as $values ) {
                    $priceField        = new CRM_Core_BAO_PriceField( );
                    $priceField->label = $values['fieldName'];
                    
                    $priceField->find( true );
                    
                    // FIXME: we are not storing qty for text type (for
                    // offline mode). Hence cannot set defaults for Text
                    // type price field
                    if ( $priceField->html_type == 'Text' ) {
                        $defaults[$form->_pId]["price_{$priceField->id}"] = $values['optionLabel'];
                        
                        require_once 'CRM/Core/BAO/PriceField.php';
                        $priceOptions = CRM_Core_BAO_PriceField::getOptions( $priceField->id );
                        foreach ( $priceOptions as $id => $val ) {
                            $textValue = $val['value'];
                            break;
                        }
                        $priceOptionValues[$priceField->id] = $values['optionLabel'] * $textValue;
                        continue;
                    }
                    
                    $optionId = CRM_Core_BAO_PriceField::getOptionId( $values['optionLabel'], $priceField->id );
                    
                    //get the checked optioned values.
                    $optionValue = null;
                    if ( $optionId ) {
                        $optionValue = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', $optionId, 'name' );
                    }
                    
                    //get the total as per fields.
                    if ( CRM_Utils_Array::value( $priceField->id, $priceOptionValues ) === null ) {
                        $priceOptionValues[$priceField->id] = $optionValue;
                    } else {
                        $priceOptionValues[$priceField->id] += $optionValue;
                    }
                    
                    if ( $priceField->html_type == 'CheckBox' ) {
                        $defaults[$form->_pId]["price_{$priceField->id}"][$optionId] = 1;
                        continue;
                    }
                    $defaults[$form->_pId]["price_{$priceField->id}"] = $optionId;
                }
            }
            if ( $form->_action == CRM_Core_Action::ADD ) {
                foreach( $form->_priceSet['fields'] as $key => $val ) {
                    foreach ( $val['options'] as $keys => $values ) {
                        if ( $values['is_default'] ) {
                            if ( $val['html_type'] == 'CheckBox') {
                                $defaults[$form->_pId]["price_{$key}"][$keys] = 1;
                            } else {
                                $defaults[$form->_pId]["price_{$key}"] = $keys;
                            }
                        }
                    }
                }
            }
            //need to build all price set field amount string where price set ids <= current price set
            $query = "select `id` from civicrm_price_field where `price_set_id` = $priceSetId  ORDER BY `id` desc limit 0, 1";
            $maxFieldId = CRM_Core_DAO::singleValueQuery( $query );
            $allFieldValues = array( );
            for ( $count = 1; $count <= $maxFieldId; $count++ ) {
                $allFieldValues[$count] = CRM_Utils_Array::value( $count, $priceOptionValues );
            }
            $form->assign( 'feeString', implode( ',', $allFieldValues ) );
            $form->assign( 'totalAmount', CRM_Utils_Array::value( 'fee_amount', $defaults[$form->_pId] ) );
            if ( $form->_action == CRM_Core_Action::UPDATE ) {
                $fee_level = $defaults[$form->_pId]['fee_level'];
                CRM_Event_BAO_Participant::fixEventLevel( $fee_level );
                $form->assign("fee_level", $fee_level );
                $form->assign( 'fee_amount', CRM_Utils_Array::value( 'fee_amount', $defaults[$form->_pId] ) );
            }
        } else {
            $optionGroupId = null;

            // if user has selected discount use that to set default
            if ( isset( $form->_discountId ) ) {
                $defaults[$form->_pId]['discount_id'] = $form->_discountId;

                //hack to set defaults for already selected discount value
                if ( $form->_action == CRM_Core_Action::UPDATE && !$form->_originalDiscountId ) {
                    $form->_originalDiscountId = $defaults[$form->_pId]['discount_id'];
                    if ( $form->_originalDiscountId ) {
                        $optionGroupId = CRM_Core_DAO::getFieldValue( "CRM_Core_DAO_Discount", 
                                                                      $form->_originalDiscountId,
                                                                      'option_group_id' );
                        $defaults[$form->_pId]['discount_id'] = $form->_originalDiscountId;
                    }
                }
            } 

            if ( ( $form->_action == CRM_Core_Action::ADD ) ) {
                // this case is for add mode, where we show discount automatically
                if ( !isset( $form->_discountId ) ) {
                    require_once 'CRM/Core/BAO/Discount.php';
                    $discountId = CRM_Core_BAO_Discount::findSet( $form->_eventId, 'civicrm_event' );
                } else {
                    $discountId = $form->_discountId;
                }

                if ( $form->_eventId && $discountId ) {
                    $defaultDiscountId = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_Event", 
                                                                      $form->_eventId, 
                                                                      'default_discount_id' );
                    if ( $defaultDiscountId ) {
                        $discountKey = CRM_Core_DAO::getFieldValue( "CRM_Core_DAO_OptionValue", 
                                                                    $defaultDiscountId, 
                                                                    'weight' );
                    }

                    $defaults[$form->_pId]['discount_id'] = $discountId;
                    $defaults[$form->_pId]['amount'] = key(array_slice($form->_values['discount'][$discountId], $discountKey-1, $discountKey, true));

                    $optionGroupId = CRM_Core_DAO::getFieldValue( "CRM_Core_DAO_Discount", 
                                                                  $discountId,
                                                                  'option_group_id' );
                } else {                    
                    if ( $form->_eventId ) {
                        $defaults[$form->_pId]['amount'] = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_Event", 
                                                                                                  $form->_eventId, 
                                                                                                  'default_fee_id' 
                                                                                                  );
                    }
                }
            }

            if ( ($form->_action == CRM_Core_Action::UPDATE ) && 
                 CRM_Utils_Array::value( 'event_id', $defaults[$form->_pId] ) ) {
                if ( ! empty($form->_feeBlock) ) {
                    $feeLevel = CRM_Utils_Array::value('fee_level',
                                                       $defaults[$form->_pId] );
                    $feeAmount = CRM_Utils_Array::value('fee_amount',
                                                        $defaults[$form->_pId] );
                    foreach( $form->_feeBlock as $amountId => $amountInfo ) {
                        if ( $amountInfo['label'] == $feeLevel &&
                             $amountInfo['value'] == $feeAmount ) {
                            $defaults[$form->_pId]['amount'] = $amountInfo['amount_id'];
                        }

                        // if amount is not set do fuzzy matching
                        if ( ! isset( $defaults[$form->_pId]['amount'] ) ) {
                            // if only level use that
                            if ( $amountInfo['label'] == $feeLevel ) {
                                $defaults[$form->_pId]['amount'] = $amountInfo['amount_id'];
                            } else if ( strpos( $feeLevel, $amountInfo['label'] ) !== false ) {
                                $defaults[$form->_pId]['amount'] = $amountInfo['amount_id'];
                            } else if ( $amountInfo['value'] == $feeAmount ) {
                                // if amount matches use that
                                $defaults[$form->_pId]['amount'] = $amountInfo['amount_id'];
                            }
                        }
                    }
                }

                if ( ! isset($defaults[$form->_pId]['amount']) ) {
                    // keeping the old code too
                    if ( ! $optionGroupId ) {
                        $optionGroupId = CRM_Core_DAO::getFieldValue( "CRM_Core_DAO_OptionGroup", 
                                                                      'civicrm_event.amount.' .
                                                                      $defaults[$form->_pId]['event_id'], 
                                                                      'id', 
                                                                      'name' );
                    }

                    $optionParams = array( 'option_group_id' => $optionGroupId,
                                           'label' => CRM_Utils_Array::value('fee_level',
                                                                             $defaults[$form->_pId]) );
                    
                    CRM_Core_BAO_CustomOption::retrieve( $optionParams, $params );
                    $defaults[$form->_pId]['amount'] = $params['id'];
                }
            }
            $form->assign("amountId", $defaults[$form->_pId]['amount'] );
        }
        return $defaults[$form->_pId];

    }
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    static function buildQuickForm( &$form )  
    {
        if ( $form->_eventId ) {
            $form->_isPaidEvent = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Event', $form->_eventId, 'is_monetary' );
            if ( $form->_isPaidEvent ) {
                $form->addElement( 'hidden', 'hidden_feeblock', 1 );
            }
        }
        
        if ( $form->_pId ) { 
            if ( CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_ParticipantPayment', 
                                              $form->_pId, 'contribution_id', 'participant_id' ) ) {
                $form->_online = true;
            }
        }
        
        if ( $form->_isPaidEvent ) {
            require_once "CRM/Event/BAO/Event.php";
            $params = array( 'id' => $form->_eventId );
            CRM_Event_BAO_Event::retrieve( $params, $event );

            //retrieve custom information
            $form->_values = array( );
            require_once "CRM/Event/Form/Registration/Register.php";
            CRM_Event_Form_Registration::initPriceSet($form, $event['id'] );
            CRM_Event_Form_Registration_Register::buildAmount( $form, true, $form->_discountId );
            $form->assign ( 'line_items' , CRM_Utils_Array::value( 'line_items', $form->_values ) );
            $discounts = array( );
            if ( !empty( $form->_values['discount'] ) ) {
                foreach( $form->_values['discount'] as $key => $value ) { 
                    $discounts[$key] = $value['name'];                   
                }
                
                $element = $form->add('select', 'discount_id', 
                                      ts( 'Discount Set' ), 
                                      array( 0 => ts( '- select -' )) + $discounts,
                                      false,
                                      array('onchange' => "buildFeeBlock( {$form->_eventId}, this.value );") );
           
                if ( $form->_online ) {
                    $element->freeze();
                }
            }
            if ( $form->_mode ) {
                require_once 'CRM/Core/Payment/Form.php';
                CRM_Core_Payment_Form::buildCreditCard( $form, true );
            } else if ( !$form->_mode ) {
                $form->addElement('checkbox', 'record_contribution', ts('Record Payment?'), null, 
                                  array('onclick' =>"return showHideByValue('record_contribution','','payment_information','table-row','radio',false);"));
                
                require_once 'CRM/Contribute/PseudoConstant.php';
                $form->add('select', 'contribution_type_id', 
                           ts( 'Contribution Type' ), 
                           array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::contributionType( ) );
                
                $form->add('date', 'receive_date', ts('Received'), CRM_Core_SelectValues::date('activityDate'), false );         
                $form->addRule('receive_date', ts('Select a valid date.'), 'qfDate');
                
                $form->add('select', 'payment_instrument_id', 
                           ts( 'Paid By' ), 
                           array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::paymentInstrument( ),
                           false, array( 'onChange' => "return showHideByValue('payment_instrument_id','4','checkNumber','table-row','select',false);"));
                // don't show transaction id in batch update mode
                $path = CRM_Utils_System::currentPath( );
                $form->assign('showTransactionId', false );
                if ( $path != 'civicrm/contact/search/basic' ) {
                    $form->add('text', 'trxn_id', ts('Transaction ID'));
                    $form->addRule( 'trxn_id', ts('Transaction ID already exists in Database.'),
                                    'objectExists', array( 'CRM_Contribute_DAO_Contribution', $form->_eventId, 'trxn_id' ) );
                    $form->assign('showTransactionId', true );
                }
            
                $form->add('select', 'contribution_status_id',
                           ts('Payment Status'), 
                           CRM_Contribute_PseudoConstant::contributionStatus( )
                           );
                
                $form->add( 'text', 'check_number', ts('Check Number'), 
                            CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Contribution', 'check_number' ) );

            }
        } else {
            $form->add( 'text', 'amount', ts('Event Fee(s)') );
        }
        
        $form->assign("paid", $form->_isPaidEvent );
        
        $form->addElement('checkbox', 
                          'send_receipt', 
                          ts('Send Confirmation?'), null, 
                          array('onclick' =>"return showHideByValue('send_receipt','','notice','block','radio',false);") );
        $form->add('textarea', 'receipt_text', ts('Confirmation Message') );
        
        // Retrieve the name and email of the contact - form will be the TO for receipt email
        if ( $form->_contactID ) {
            list( $form->_contributorDisplayName, 
                 $form->_contributorEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $form->_contactID );
            $form->assign( 'email', $form->_contributorEmail );
        } else {
            //show email block for batch update for event
            $form->assign( 'batchEmail', true );
        }

        require_once "CRM/Core/BAO/Preferences.php";
        $mailingInfo =& CRM_Core_BAO_Preferences::mailingPreferences();
        $form->assign( 'outBound_option', $mailingInfo['outBound_option'] );
    }
}

