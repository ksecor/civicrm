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
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/ContributionPage.php';
require_once 'CRM/Contribute/PseudoConstant.php';

/**
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Contribute_Form_ContributionPage_Settings extends CRM_Contribute_Form_ContributionPage {

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->_first = true;

        // name
        $this->add('text', 'title', ts('Title'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'title'), true);

        $this->add('select', 'contribution_type_id',
                   ts( 'Contribution Type' ),
                   CRM_Contribute_PseudoConstant::contributionType( ) );

        // intro_text and footer_text
        $this->add('textarea', 'intro_text', ts('Introductory Message'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'intro_text'), true);
        $this->add('textarea', 'footer_text', ts('Footer Message'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'footer_text'), false);

        // collect goal amount
        $this->add('text', 'goal_amount', ts('Goal Amount'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $this->addRule( 'goal_amount', ts( 'Please enter a valid money value (e.g. 99.99).' ), 'money' );
        
        // should the thermometer be enabled
        $this->addElement('checkbox', 'is_thermometer', ts( 'Should a thermometer block be displayed during a contribution?' ) );
        // thermometer block title
        $this->add('text', 'thermometer_title', ts('Thermometer Title'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'thermometer_title') );

        // is this group active ?
        $this->addElement('checkbox', 'is_active', ts('Is this Online Contribution Page Active?') );

        $this->addFormRule(array('CRM_Contribute_Form_ContributionPage_Settings', 'formRule'));

        parent::buildQuickForm( );
    }
    /**
    * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule(&$params)
    {
        $errors = array( );
        if ( ! $params['goal_amount'] && $params['is_thermometer'] ) {
            $errors['goal_amount'] = ts('You must enter a contribution page Goal Amount if you want to track progress by enabling the Progress Thermometer block.');
        }
        
        return empty($errors) ? true : $errors;
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

        // we do this in case the user has hit the forward/back button
        if ( $this->_id ) {
            $params['id'] = $this->_id;
        }

        $params['domain_id']             = CRM_Core_Config::domainID( );
        $params['is_active']             = CRM_Utils_Array::value('is_active'            , $params, false);
        $params['is_thermometer']        = CRM_Utils_Array::value('is_thermometer'       , $params, false);
        $params['is_credit_card_only']   = CRM_Utils_Array::value('is_credit_card_only'  , $params, false);

        require_once 'CRM/Contribute/BAO/ContributionPage.php';
        $dao =& CRM_Contribute_BAO_ContributionPage::create( $params );

        $this->set( 'id', $dao->id );
    }

    /** 
     * Return a descriptive name for the page, used in wizard header 
     * 
     * @return string 
     * @access public 
     */ 
    public function getTitle( ) {
        return ts( 'Title and Settings' );
    }
}
?>
