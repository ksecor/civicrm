<?PHP

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
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
        //as when call come from register.php
        if ( !$form->_eventId ) {
            $form->_eventId  = CRM_Utils_Request::retrieve( 'eventId', 'Positive', $form );
        }
        
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
 
        if ( $form->_eventId ) {
            //get receipt text and contribution type
            $returnProperities = array( 'confirm_email_text', 'contribution_type_id' );
            $details = array( );
            CRM_Core_DAO::commonRetrieveAll( 'CRM_Event_DAO_Event', 'id', $form->_eventId, $details, $returnProperities );
            $defaults[$form->_pId]['contribution_type_id'] = $details[$form->_eventId]['contribution_type_id'];
        }
        
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
                //set receipt text
                $defaults[$form->_pId]['receipt_text'] = $details[$form->_eventId]['confirm_email_text'];
            }

            list( $defaults[$form->_pId]['receive_date'] ) = CRM_Utils_Date::setDateDefaults( );
        }

        if ( $form->_mode ) {
            $fields = array( );
            
            foreach ( $form->_fields as $name => $dontCare ) {
                $fields[$name] = 1;
            }

            $names = array( "first_name", "middle_name", "last_name","street_address-{$form->_bltID}",
                            "city-{$form->_bltID}", "postal_code-{$form->_bltID}","country_id-{$form->_bltID}",
                            "state_province_id-{$form->_bltID}"
                            );
            foreach ($names as $name) {
                $fields[$name] = 1;
            }
            
            $fields["state_province-{$form->_bltID}"] = 1;
            $fields["country-{$form->_bltID}"       ] = 1;
            $fields["email-{$form->_bltID}"         ] = 1;
            $fields["email-Primary"                 ] = 1;
            
            require_once "CRM/Core/BAO/UFGroup.php";
            if ( $form->_contactID ) {
                CRM_Core_BAO_UFGroup::setProfileDefaults( $form->_contactID, $fields, $form->_defaults );
            }

            // use primary email address if billing email address is empty
            if ( empty( $form->_defaults["email-{$form->_bltID}"] ) &&
                 ! empty( $form->_defaults["email-Primary"] ) ) {
                $defaults[$form->_pId]["email-{$form->_bltID}"] = $form->_defaults["email-Primary"];
            }
            
            foreach ( $names as $name) {
                if ( ! empty( $form->_defaults[$name] ) ) {
                    $defaults[$form->_pId]["billing_" . $name] = $form->_defaults[$name];
                }
            }
        }

        require_once 'CRM/Price/BAO/Set.php';
        if ( $priceSetId = CRM_Price_BAO_Set::getFor( 'civicrm_event', $form->_eventId ) ) {
            // get price set default values, CRM-4090
            if ( 'CRM_Event_Form_Registration_Register' == CRM_Utils_System::getClassName( $form ) ) {
                $priceSetValues = self::setDefaultPriceSet( $form->_pId, $form->_eventId );
                if ( !empty( $priceSetValues ) ) {
                    $defaults[$form->_pId] = array_merge( $defaults[$form->_pId], $priceSetValues );  
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
                                                                      'default_discount_fee_id' );
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

            if ( CRM_Utils_Array::value( 'event_id', $defaults[$form->_pId] ) 
                 && ( $form->_action == CRM_Core_Action::UPDATE || $form->_allowConfirmation ) ) { 
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

        //CRM-4453
        $form->assign( 'fee_currency', $defaults[$form->_pId]['participant_fee_currency']);
        
        // CRM-4395 
        if ( $contriId = $form->get( 'onlinePendingContributionId' ) ) {
            require_once 'CRM/Contribute/DAO/Contribution.php';
            $contribution =& new CRM_Contribute_DAO_Contribution( );
            $contribution->id = $contriId;
            $contribution->find( true );
            foreach( array('contribution_type_id', 'payment_instrument_id','contribution_status_id', 'receive_date' ) as $f ) {
                $defaults[$form->_pId][$f] = $contribution->$f;
            }
        }
        
        return $defaults[$form->_pId];
    }
    
    /**
     * This function sets the default values for price set.
     * 
     * @access public
     * @return None
     */
    static function setDefaultPriceSet( $participantID, $eventID = null ) 
    {
        $defaults = array( );
        if ( !$eventID && $participantID ) {
            $eventID = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_Participant', $participantID, 'event_id' );
        }
        if ( !$participantID || !$eventID ) {
            return $defaults; 
        }
        
        // get price set ID.
        require_once 'CRM/Price/BAO/Set.php';
        $priceSetID = CRM_Price_BAO_Set::getFor( 'civicrm_event', $eventID );
        if ( !$priceSetID ) {
            return $defaults;  
        }
        
        // use line items for setdefault price set fields, CRM-4090
        require_once 'CRM/Core/BAO/LineItem.php';
        $lineItems[$participantID] = CRM_Core_BAO_LineItem::getLineItems( $participantID );
        
        if ( is_array( $lineItems[$participantID] ) && 
             !CRM_Utils_System::isNull( $lineItems[$participantID] ) ) {
            
            $priceFields = $htmlTypes = $optionValues = array( );
            foreach ( $lineItems[$participantID] as $lineId => $items ) {
                $priceFieldId  = CRM_Utils_Array::value( 'price_field_id', $items );
                $optionGroupId = CRM_Utils_Array::value( 'option_group_id', $items );
                if ( $priceFieldId && $optionGroupId ) {
                    $priceFields[$priceFieldId] = $optionGroupId;
                }
            }
            
            if ( empty( $priceFields ) ) {
                return $defaults;
            }
            
            // get all price set field html types.
            $sql = "
SELECT  id, html_type 
  FROM  civicrm_price_field 
 WHERE  id IN (" .implode( ',', array_keys( $priceFields ) ).')';
            $fieldDAO  = CRM_Core_DAO::executeQuery( $sql );
            while ( $fieldDAO->fetch( ) ) {
                $htmlTypes[$fieldDAO->id] = $fieldDAO->html_type;
            }
            
            $sql = "
SELECT  id, label, name, option_group_id  
  FROM  civicrm_option_value 
 WHERE  option_group_id IN (" .implode( ',', $priceFields ).')';
            $valueDAO  = CRM_Core_DAO::executeQuery( $sql );
            while ( $valueDAO->fetch( ) ) {
                $optionValues[$valueDAO->option_group_id][$valueDAO->id] = array( 'name'  => $valueDAO->name,
                                                                                  'label' => $valueDAO->label );
            }
            
            foreach ( $lineItems[$participantID] as $lineId => $items ) {
                $fieldId  = $items['price_field_id'];
                $htmlType = CRM_Utils_Array::value( $fieldId, $htmlTypes );
                if ( !$htmlType ) continue;
                
                if ( $htmlType == "Text" ) {
                    $defaults["price_{$fieldId}"] = $items['qty'];
                } else {
                    $optionGroupId  = CRM_Utils_Array::value( $fieldId,  $priceFields );
                    $fieldOptValues = CRM_Utils_Array::value( $optionGroupId, $optionValues ); 
                    if ( !is_array( $fieldOptValues ) ) continue; 
                    
                    foreach ( $fieldOptValues as $optionId => $values ) {
                        if ( $values['label'] == $items['label'] &&
                             $values['name']  == $items['unit_price'] ) {
                            if ( $htmlType == "CheckBox" ) {
                                $defaults["price_{$fieldId}"][$optionId] = true;
                            } else {
                                $defaults["price_{$fieldId}"] = $optionId;
                                break;
                            }
                        }
                    }
                }
            }
            
        }
        
        return $defaults;
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
            
            // make sure this is for backoffice registration.
            if ( $form->getName( ) == 'Participant' ) {
                require_once "CRM/Event/BAO/Participant.php";
                $eventfullMsg = CRM_Event_BAO_Participant::eventFullMessage( $form->_eventId, $form->_pId );
                $form->addElement( 'hidden', 'hidden_eventFullMsg', $eventfullMsg, array( 'id' => 'hidden_eventFullMsg' ) );
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
            $lineItem = array();
            if ( !CRM_Utils_System::isNull( CRM_Utils_Array::value( 'line_items', $form->_values ) ) ) {
                $lineItem[] = $form->_values['line_items'];
            }
            $form->assign ( 'lineItem', empty($lineItem)?false:$lineItem );
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
                
                $form->addDate( 'receive_date', ts('Received'), false, array( 'formatType' => 'activityDate') );
                
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
                
                $allowStatuses = array( );
                $statuses = CRM_Contribute_PseudoConstant::contributionStatus( );
                if ( $form->get( 'onlinePendingContributionId' )  ) {
                    $statusNames = CRM_Contribute_PseudoConstant::contributionStatus( null, 'name' );
                    foreach ( $statusNames as $val => $name ) {
                        if ( in_array($name, array('In Progress', 'Overdue')) ) continue;
                        $allowStatuses[$val] = $statuses[$val]; 
                    }
                } else {
                    $allowStatuses = $statuses;
                }
                $form->add('select', 'contribution_status_id',
                           ts('Payment Status'), $allowStatuses );
                
                $form->add( 'text', 'check_number', ts('Check Number'), 
                            CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_Contribution', 'check_number' ) );

            }
        } else {
            $form->add( 'text', 'amount', ts('Event Fee(s)') );
        }
        $form->assign( 'onlinePendingContributionId', $form->get( 'onlinePendingContributionId' ) );
        
        $form->assign("paid", $form->_isPaidEvent );
        
        $form->addElement('checkbox', 
                          'send_receipt', 
                          ts('Send Confirmation?'), null, 
                          array('onclick' =>"return showHideByValue('send_receipt','','notice','table-row','radio',false);") );
        $form->add('textarea', 'receipt_text', ts('Confirmation Message') );
        
        // Retrieve the name and email of the contact - form will be the TO for receipt email ( only if context is not standalone)        
        if ( $form->_context != 'standalone' ) {    
            if ( $form->_contactID ) {
                list( $form->_contributorDisplayName, 
                     $form->_contributorEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $form->_contactID );
                $form->assign( 'email', $form->_contributorEmail );
            } else {
                //show email block for batch update for event
                $form->assign( 'batchEmail', true );
            }
        }

        require_once "CRM/Core/BAO/Preferences.php";
        $mailingInfo =& CRM_Core_BAO_Preferences::mailingPreferences();
        $form->assign( 'outBound_option', $mailingInfo['outBound_option'] );
    }
}

