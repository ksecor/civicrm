<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/ContributionPage.php';

/**
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Contribute_Form_ContributionPage_Amount extends CRM_Contribute_Form_ContributionPage {
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
        $this->addElement('checkbox', 'is_allow_other_amount', ts('Allow Other Amounts?' ) ); 
 
        $this->add('text', 'min_amount', ts('Minimum Contribution Amount'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $this->add('text', 'max_amount', ts('Maximum Contribution Amount'), array( 'size' => 8, 'maxlength' => 8 ) ); 

        for ( $i = 1; $i <= self::NUM_OPTION; $i++ ) {
            // label 
            $this->add('text', "label[$i]", ts('Label'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'label')); 
 
            // value 
            $this->add('text', "value[$i]", ts('Value'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomOption', 'value')); 
            $this->addRule("value[$i]", ts('Please enter a valid value for this field.'), 'integer'); 
        }

        parent::buildQuickForm( );
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
        $params['domain_id']             = CRM_Core_Config::domainID( );
        $params['is_allow_other_amount'] = CRM_Utils_Array::value('is_allow_other_amount', $params, false);

        require_once 'CRM/Contribute/BAO/ContributionPage.php';
        $dao = CRM_Contribute_BAO_ContributionPage::create( $params );

        // if there are label / values, create custom options for them
        $labels = CRM_Utils_Array::value( 'label', $params );
        $values = CRM_Utils_Array::value( 'value', $params );
        if ( ! CRM_Utils_System::isNull( $labels ) && ! CRM_Utils_System::isNull( $values ) ) {
            require_once 'CRM/Core/DAO/CustomOption.php';

            for ( $i = 1; $i < self::NUM_OPTION; $i++ ) {
                if ( ! empty( $labels[$i] ) && !empty( $values[$i] ) ) {
                    $dao =& new CRM_Core_DAO_CustomOption( );
                    $dao->label        = trim( $labels[$i] );
                    $dao->value        = trim( $values[$i] );
                    $dao->entity_table = 'civicrm_contribution_page';
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
    public function getTitle( ) {
        return ts( 'Contribution Amounts' );
    }

}
?>
