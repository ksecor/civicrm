<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
require_once 'CRM/Contribute/PseudoConstant.php';

class CRM_Contribute_Form_ContributionPage_Settings extends CRM_Contribute_Form_ContributionPage {

    /**
    * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return void
     */
    function setDefaultValues()
    {
        if ( $this->_id ) {
            $title = CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_ContributionPage',
                                                  $this->_id,
                                                  'title' );
            CRM_Utils_System::setTitle( ts( 'Title and Settings (%1)',
                                            array( 1 => $title ) ) );
        } else {
            CRM_Utils_System::setTitle( ts( 'Title and Settings' ) );
        }
        return parent::setDefaultValues();
    }
    
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
        $this->addRule( 'title', ts( 'Page title can not be longer than 255 characters and must consist of letters, numbers, spaces and simple punctuation characters.' ), 'longTitle' );

        $this->add('select', 'contribution_type_id',
                   ts( 'Contribution Type' ),
                   CRM_Contribute_PseudoConstant::contributionType( ),
                   true );
        $paymentProcessor =& CRM_Core_PseudoConstant::paymentProcessor( );
        if ( count($paymentProcessor) ) {
            $this->assign('paymentProcessor',$paymentProcessor);
        }
        $this->add( 'select', 'payment_processor_id',
                    ts( 'Payment Processor' ),
                    array(''=>ts( '-select-' )) + $paymentProcessor );

        // intro_text and footer_text
        $this->add('textarea', 'intro_text', ts('Introductory Message'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'intro_text'), true);
        $this->add('textarea', 'footer_text', ts('Footer Message'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'footer_text'), false);

        // collect goal amount
        $this->add('text', 'goal_amount', ts('Goal Amount'), array( 'size' => 8, 'maxlength' => 12 ) ); 
        $this->addRule( 'goal_amount', ts( 'Please enter a valid money value (e.g. 99.99).' ), 'money' );
        
        // is this page active ?
        $this->addElement('checkbox', 'is_active', ts('Is this Online Contribution Page Active?') );

        // should the honor be enabled
        $this->addElement('checkbox', 'honor_block_is_active', ts( 'Honoree Section Enabled' ),null,array('onclick' =>"showHonor()") );
        
        $this->add('text', 'honor_block_title', ts('Honoree Section Title'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'honor_block_title') );

        $this->add('textarea', 'honor_block_text', ts('Honoree Introductory Message'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'honor_block_text') );

        // add optional start and end dates
        $this->add('date', 'start_date',
                   ts('Start Date'),
                   CRM_Core_SelectValues::date('datetime'),
                   false,
                   array('onchange' => 'defaultDate(this);'));  
        $this->addRule('start_date', ts('Please select a valid start date.'), 'qfDate');

        $this->add('date', 'end_date',
                   ts('End Date / Time'),
                   CRM_Core_SelectValues::date('datetime') );
        $this->addRule('end_date', ts('Please select a end valid date.'), 'qfDate');

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

        // we do this in case the user has hit the forward/back button
        if ( $this->_id ) {
            $params['id'] = $this->_id;
        }

        $params['domain_id']             = CRM_Core_Config::domainID( );
        $params['is_active']             = CRM_Utils_Array::value('is_active'            , $params, false);
        $params['is_credit_card_only']   = CRM_Utils_Array::value('is_credit_card_only'  , $params, false);
        $params['honor_block_is_active'] = CRM_Utils_Array::value('honor_block_is_active', $params, false);
        $params['start_date']            = CRM_Utils_Date::format( $params['start_date'] );
        $params['end_date'  ]            = CRM_Utils_Date::format( $params['end_date'] );

        if( !$params['honor_block_is_active'] ) {
            $params['honor_block_title'] = null;
            $params['honor_block_text'] = null;
        }

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

