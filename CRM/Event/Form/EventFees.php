<?PHP

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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
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
        $form->_eventId       = CRM_Utils_Request::retrieve( 'eventId', 'Positive', $form );
        $form->_participantId = CRM_Utils_Request::retrieve( 'participantId', 'Positive', $form );
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

        if ( $form->_participantId ) {
            $ids    = array( );
            $params = array( 'id' => $form->_participantId );
            
            require_once "CRM/Event/BAO/Participant.php";
            CRM_Event_BAO_Participant::getValues( $params, $defaults, $ids );            
            $defaults[$form->_participantId]['send_receipt'] = 0;
        } else {
            $defaults[$form->_participantId]['send_receipt'] = 1;
            $defaults[$form->_participantId]['receipt_text'] = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage',
                                                                                           $form->_eventId, 
                                                                                           'confirm_email_text',
                                                                                           'event_id');
            $today_date = getDate();
            $defaults[$form->_participantId]['receive_date']['M'] = $today_date['mon'];
            $defaults[$form->_participantId]['receive_date']['d'] = $today_date['mday'];
            $defaults[$form->_participantId]['receive_date']['Y'] = $today_date['year'];
        }

        require_once 'CRM/Core/BAO/PriceSet.php';
        if ( $priceSetId = CRM_Core_BAO_PriceSet::getFor( 'civicrm_event_page', $form->_eventId ) ) {
            $fields = array( );
    
            $eventLevel = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, 
                                   substr( $defaults[$form->_participantId]['event_level'], 1, -1 ) );
            foreach ( $eventLevel as $id => $name ) {
                $optionValue         = new CRM_Core_BAO_OptionValue( );
                $optionValue->label  = $name;
                $optionValue->find( true );
                
                if ($optionValue->option_group_id ){
                    $groupName       = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup', $optionValue->option_group_id, 'name' );
                    $fieldName       = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_PriceField', substr( $groupName, -1, 1 ), 'label') ;
                    $eventLevel[$id] = array( 'fieldName'   => $fieldName,
                                              'optionLabel' => $name );
                }
            }
            //for the texfield default value
            foreach ( $eventLevel as $id => $values ) {
                if( !is_array( $values ) ){
                    $textLevel       = explode( ' - ', $values );
                    $eventLevel[$id] = array( 'fieldName'   => $textLevel[0],
                                              'optionLabel' => $textLevel[1] );
                }       
            }
            
            require_once 'CRM/Core/BAO/PriceField.php';
            foreach ( $eventLevel as $values ) {
                $priceField        = new CRM_Core_BAO_PriceField( );
                $priceField->label = $values['fieldName'];
                
                $priceField->find( true );
                
                // FIXME: we are not storing qty for text type (for
                // offline mode). Hence can not set defaults for Text
                // type price field
                if ( $priceField->html_type == 'Text' ) {
                    $defaults[$form->_participantId]["price_{$priceField->id}"] = $values['optionLabel'];
                    continue;
                }
                
                $optionId = CRM_Core_BAO_PriceField::getOptionId( $values['optionLabel'], $priceField->id );
                
                if ( $priceField->html_type == 'CheckBox' ) {
                    $defaults[$form->_participantId]["price_{$priceField->id}"][$optionId] = 1;
                    continue;
                }
                $defaults[$form->_participantId]["price_{$priceField->id}"] = $optionId;
            }
        } else {
            if ( $defaults[$form->_participantId]['event_id'] && ($form->_action == CRM_Core_Action::UPDATE ) ) {
                $optionGroupId = CRM_Core_DAO::getFieldValue( "CRM_Core_DAO_OptionGroup", 
                                                              'civicrm_event_page.amount.' . 
                                                              CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_EventPage", 
                                                                                           $defaults[$form->_participantId]['event_id'],
                                                                                           'id', 'event_id' ), 
                                                              'id', 
                                                              'name' );
                $optionParams = array( 'option_group_id' => $optionGroupId,
                                       'label'           => CRM_Utils_Array::value('event_level',$defaults[$form->_participantId]) );
                
                CRM_Core_BAO_CustomOption::retrieve( $optionParams, $params );
                $defaults[$form->_participantId]['amount'] = $params['id'];
            } elseif ( $defaults[$form->_participantId]['event_id'] && ($form->_action == CRM_Core_Action::ADD ) ) {
                $defaults[$form->_participantId]['amount'] = CRM_Core_DAO::getFieldValue( "CRM_Event_DAO_EventPage", 
                                                                               $defaults[$form->_participantId]['event_id'], 
                                                                               'default_fee_id', 
                                                                               'event_id' );
            }
        }

        return $defaults[$form->_participantId];
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
        }
        if ( $form->_isPaidEvent ) {
            $form->addElement( 'hidden', 'hidden_feeblock', 1 );
            require_once "CRM/Event/BAO/EventPage.php";
            $params = array( 'event_id' => $form->_eventId );
            CRM_Event_BAO_EventPage::retrieve( $params, $eventPage );

            //retrieve custom information
            $form->_values = array( );
            require_once "CRM/Event/Form/Registration/Register.php";
            CRM_Event_Form_Registration::initPriceSet($form, $eventPage['id'] );
            CRM_Event_Form_Registration_Register::buildAmount( $form, false );
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
                       array(''=>ts( '- select -' )) + CRM_Contribute_PseudoConstant::paymentInstrument( )
                       );
            
            $form->add('select', 'contribution_status_id',
                       ts('Payment Status'), 
                       CRM_Contribute_PseudoConstant::contributionStatus( )
                       );
            
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
    }
}

