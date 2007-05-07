<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

require_once 'CRM/Event/Form/ManageEvent.php';
require_once 'CRM/Event/BAO/EventPage.php';

/**
 * This class generates form components for Event Fees
 * 
 */
class CRM_Event_Form_ManageEvent_Fee extends CRM_Event_Form_ManageEvent
{

    /** 
     * Constants for number of options for data types of multiple option. 
     */ 
    const NUM_OPTION = 11;
    
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) 
    {
        parent::preProcess( );
    }

    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return None
     */
    function setDefaultValues( )
    {  
        $defaults = parent::setDefaultValues( );
        $eventId = $this->_id;

        $params = array( 'event_id' => $eventId );
        CRM_Event_BAO_EventPage::retrieve( $params, $defaults );
        $eventPageId = $defaults['id'];
        
        if ( isset( $eventPageId ) ) {
            require_once 'CRM/Core/BAO/PriceSet.php';
            $price_set_id = CRM_Core_BAO_PriceSet::getFor( 'civicrm_event_page', $eventPageId );
            if ( $price_set_id ) {
                $defaults['price_set_id'] = $price_set_id;
            } else {
                require_once 'CRM/Core/BAO/CustomOption.php'; 
                CRM_Core_BAO_CustomOption::getAssoc( 'civicrm_event_page', $eventPageId, $defaults );
            }
        }
        $defaults['id'] = $eventPageId;

        if ( CRM_Utils_Array::value( 'value', $defaults ) ) {
            foreach ( $defaults['value'] as $i => $v ) {
                if ( $defaults['amount_id'][$i] == $defaults['default_fee_id'] ) {
                    $defaults['default'] = $i;
                    break;
                }
            }
        }

        if ( !isset($defaults['default']) ) {
            $defaults['default'] = 1;
        }

        if ( !isset($defaults['is_monetary']) ) {
            $defaults['is_monetary'] = 1;
        }

        require_once 'CRM/Core/ShowHideBlocks.php';
        $this->_showHide =& new CRM_Core_ShowHideBlocks( );
        if ( !$defaults['is_monetary'] ) {
            $this->_showHide->addHide( 'event-fees' );
        }
        if ( isset($defaults['price_set_id']) ) {
            $this->_showHide->addHide( 'map-field' );
        }
        $this->_showHide->addToTemplate( );
        
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
        $this->addYesNo('is_monetary', ts('Paid Event'),null, null,array('onclick' =>"return showHideByValue('is_monetary','0','event-fees','block','radio',false);"));
        
        require_once 'CRM/Contribute/PseudoConstant.php';
        $this->add('select', 'contribution_type_id',ts( 'Contribution Type' ),
                   array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( ) );
        
        $this->add('text','fee_label',ts('Fee Label'));

        require_once 'CRM/Core/BAO/PriceSet.php';
        $this->add('select', 'price_set_id', ts( 'Price Set' ),
            array( '' => ts( '- none -' )) + CRM_Core_BAO_PriceSet::getAssoc( ),
            null, array('onchange' => "return showHideByValue('price_set_id', '', 'map-field', 'block', 'select', false);")
        );

        $default = array( );
        for ( $i = 1; $i <= self::NUM_OPTION; $i++ ) {
            // label 
            $this->add('text', "label[$i]", ts('Label'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'label')); 
            // value 
            $this->add('text', "value[$i]", ts('Value'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'value')); 
            $this->addRule("value[$i]", ts('Please enter a valid money value for this field (e.g. 99.99).'), 'money'); 
            
            // default
            $default[] = $this->createElement('radio', null, null, null, $i); 
        }
        
        $this->addGroup( $default, 'default' );

        parent::buildQuickForm();
    }
    
    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        $this->addFormRule( array( 'CRM_Event_Form_ManageEvent_Fee', 'formRule' ) );
    }

    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$values ) 
    {
        if ( $values['is_monetary'] ) {
            //check if contribution type is selected
            if ( !$values['contribution_type_id'] ) {
                $errorMsg['contribution_type_id'] = "Please select contribution type.";
            }
            
            //check for the event fee label (mandatory)
            if ( !$values['fee_label'] ) {
                $errorMsg['fee_label'] = "Please enter the fee label for the paid event.";
            }
            
            //check fee label and amount
            $check = 0;
            foreach ( $values['label'] as $key => $val ) {
                if ( trim($val) && trim($values['value'][$key]) ) {
                    $check++;
                    break;
                }
            }

            if ( !$check && !$values['price_set_id'] ) {
                if ( !$values['label'][1] ) {
                    $errorMsg['label[1]'] = "Please enter Fee Label.";
                }
                if ( !$values['value'][1] ) {
                    $errorMsg['value[1]'] = "Please enter Fee Amount.";
                }
            }
            
        }
        
        if ( !empty($errorMsg) ) {
            return $errorMsg;
        }

        return true;
    }
    
    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
        $params = $ids = array();
        
        $params = $this->exportValues( );
        $params['event_id'] = $ids['event_id'] = $this->_id;

        require_once 'CRM/Core/BAO/PriceSet.php';
        // delete all the prior label values in the custom options table
        // and delete a price set if one exists
        if ( $this->_action & CRM_Core_Action::UPDATE ){
            $eventPageId = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage', $this->_id, 'id', 'event_id' );
            if ( ! CRM_Core_BAO_PriceSet::removeFrom( 'civicrm_event_page', $eventPageId ) ) {
                $dao =& new CRM_Core_DAO_CustomOption( );
                $dao->entity_table = 'civicrm_event_page'; 
                $dao->entity_id    = $eventPageId; 
                if($dao->find( )){
                    $dao->delete( );
                }
            }
        } else {
            //add record in event page 
            $eventPage = CRM_Event_BAO_EventPage::add( $params );
            $eventPageId = $eventPage->id;
        }

        if ( $params['is_monetary'] ) {
            if ( $params['price_set_id'] ) {
                CRM_Core_BAO_PriceSet::addTo( 'civicrm_event_page', $eventPageId, $params['price_set_id'] );
            } else {
                // if there are label / values, create custom options for them
                $labels  = CRM_Utils_Array::value( 'label'  , $params );
                $values  = CRM_Utils_Array::value( 'value'  , $params );
                $default = CRM_Utils_Array::value( 'default', $params ); 

                if ( ! CRM_Utils_System::isNull( $labels ) && ! CRM_Utils_System::isNull( $values )) {
                    for ( $i = 1; $i < self::NUM_OPTION; $i++ ) {
                        if ( ! empty( $labels[$i] ) && ! CRM_Utils_System::isNull( $values[$i] ) ) {
                            $dao =& new CRM_Core_DAO_CustomOption( );
                            $dao->label        = trim( $labels[$i] );
                            $dao->value        = CRM_Utils_Rule::cleanMoney( trim( $values[$i] ) );
                            $dao->entity_table = 'civicrm_event_page';
                            $dao->entity_id    = $eventPageId;
                            $dao->weight       = $i;
                            $dao->is_active    = 1;
                            $dao->save( );
                            if ( $default == $i ) {
                                $params['default_fee_id'] = $dao->id;
                            }
                        }
                    }
                }
            }
        } else {
            $params['contribution_type_id'] = '';
        }

        //update events table
        require_once 'CRM/Event/BAO/Event.php';
        CRM_Event_BAO_Event::add($params, $ids);

        //update event page record with default fee id
        CRM_Event_BAO_EventPage::add( $params );
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle( ) 
    {
        return ts('Event Fees');
    }

}
?>
