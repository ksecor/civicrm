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
class CRM_Contribute_Form_ContributionPage_ThankYou extends CRM_Contribute_Form_ContributionPage {
    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->registerRule( 'emailList', 'callback', 'emailList', 'CRM_Utils_Rule' );

        // thank you_text
        $this->add('textarea', 'thankyou_text', ts('Thank You Message'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'thankyou_text'), true);
        $this->addElement('checkbox', 'is_email_receipt', ts( 'Email Receipt to Contributor?' ) );
        $this->add('textarea', 'receipt_text', ts('Receipt Message'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'receipt_text') );
        
        $this->add('text', 'cc_receipt', ts('CC Receipt to'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'cc_receipt') );
        $this->addRule( 'cc_receipt', ts('Please enter a valid list of comma delimited email addresses'), 'emailList' );

        $this->add('text', 'bcc_receipt', ts('BCC Copy Receipt to'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_ContributionPage', 'bcc_receipt') );
        $this->addRule( 'bcc_receipt', ts('Please enter a valid list of comma delimited email addresses'), 'emailList' );

        $this->addFormRule( array( 'CRM_Contribute_Form_ContributionPage_ThankYou', 'formRule' ) );

        parent::buildQuickForm( );
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
    static function formRule( &$fields, &$files, $options ) { 
        $errors = array( ); 

        // if is_email_receipt is set, the receipt message must be non-empty
        if ( CRM_Utils_Array::value( 'is_email_receipt', $fields ) ) {
            $message = trim( CRM_Utils_Array::value( 'receipt_text', $fields ) );
            if ( empty( $message ) ) {
                $errors['receipt_text'] = ts( 'A Receipt message must be specified if Email Receipt to Contributor is enabled' );
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

        $params['id'] = $this->_id;
        $params['domain_id']             = CRM_Core_Config::domainID( );
        $params['is_email_receipt']      = CRM_Utils_Array::value('is_email_receipt'     , $params, false);

        require_once 'CRM/Contribute/BAO/ContributionPage.php'; 
        $dao = CRM_Contribute_BAO_ContributionPage::create( $params ); 
    }

    /** 
     * Return a descriptive name for the page, used in wizard header 
     * 
     * @return string 
     * @access public 
     */ 
    public function getTitle( ) {
        return ts( 'Thank-you and Receipting' );
    }
}
?>
