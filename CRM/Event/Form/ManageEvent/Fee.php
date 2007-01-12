<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/Form/ManageEvent.php';
require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for Fee
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
        $eventID = $this->get('eventID');

        if ( isset( $eventID ) ) {
            require_once 'CRM/Core/BAO/CustomOption.php'; 
            CRM_Core_BAO_CustomOption::getAssoc( 'civicrm_event', $eventID, $defaults );
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
        $this->addYesNo('is_monetary', ts('Paid Event'),null, null,array('onclick' =>"return showHideByValue('is_monetary','','contributionType|map-field','block','radio',false);"));
        
        require_once 'CRM/Contribute/PseudoConstant.php';
        $this->add('select', 'contribution_type_id',ts( 'Contribution Type' ),
                   array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( ) );
        
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
                $errorMsg['contribution_type_id'] = "Please select contribution type.";;
            }
            
            //check fee label and amount
            $check = 0;
            foreach ( $values['label'] as $key => $val ) {
                if ( trim($val) && trim($values['value'][$key]) ) {
                    $check++;
                    break;
                }
            }

            if ( !$check ) {
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
        $params = $id = array();
        
        $id['event_id'] = $this->_id;
        
        $params = $this->exportValues( );

        require_once 'CRM/Event/BAO/Event.php';
        CRM_Event_BAO_Event::add($params ,$id);

        // delete all the prior label values in the custom options table
        $dao =& new CRM_Core_DAO_CustomOption( );
        $dao->entity_table = 'civicrm_event'; 
        $dao->entity_id    = $this->_id; 
        $dao->delete( );

        // if there are label / values, create custom options for them
        $labels  = CRM_Utils_Array::value( 'label'  , $params );
        $values  = CRM_Utils_Array::value( 'value'  , $params );
        $default = CRM_Utils_Array::value( 'default', $params ); 
        if ( ! CRM_Utils_System::isNull( $labels ) && ! CRM_Utils_System::isNull( $values ) ) {
            for ( $i = 1; $i < self::NUM_OPTION; $i++ ) {
                if ( ! empty( $labels[$i] ) && !empty( $values[$i] ) ) {
                    $dao =& new CRM_Core_DAO_CustomOption( );
                    $dao->label        = trim( $labels[$i] );
                    $dao->value        = CRM_Utils_Rule::cleanMoney( trim( $values[$i] ) );
                    $dao->entity_table = 'civicrm_event';
                    $dao->entity_id    = $this->_id;
                    $dao->weight       = $i;
                    $dao->is_active    = 1;
                    $dao->save( );
                }
            }
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
        return ts('Event Fee');
    }

}
?>
