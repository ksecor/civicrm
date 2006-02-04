<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form/ContributionPage.php';
require_once 'CRM/Contribute/PseudoConstant.php';

/**
 * form to process actions on Premiums
 */
class CRM_Contribute_Form_ContributionPage_Premium extends CRM_Contribute_Form_ContributionPage {
    
    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return void
     */
    function setDefaultValues()
    {
        //parent::setDefaultValues();
        
        $defaults = array();
        if ( isset($this->_id ) ) {
            $dao =& new CRM_Contribute_DAO_Premium();
            $dao->entity_table = 'civicrm_contribution_page';
            $dao->entity_id = $this->_id; 
            $dao->find(true);
            CRM_Core_DAO::storeValues( $dao,$defaults );
        }
        

        return $defaults;
    }
    

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->addElement('checkbox', 'premiums_active', ts('Premiums Section Enabled?') );
        
        $this->addElement('text', 'premiums_intro_title', ts('Title'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_Premium', 'premiums_intro_title'));
        
        $this->addRule('premiums_intro_title',ts('Plese Eneter the Title'),'required');
        // intro_text
        $this->add('textarea', 'premiums_intro_text', ts('Introductory Message'), 'rows=5, cols=50', true );

        $this->add('text','premiums_contact_email',ts('Contact Email '),CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_Premium', 'premiums_contact_email')); 

        
        $this->add('text','premiums_contact_phone',ts('Contact Phone'),CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_Premium', 'premiums_contact_phone'));

        // is this group active ?
       
        $session =& CRM_Core_Session::singleton();
        $single = $session->get('singleForm');
        if ( $single ) {
            $this->addButtons(array(
                                    array ( 'type'      => 'next',
                                            'name'      => ts('Save'),
                                            'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                            'isDefault' => true   ),
                                    array ( 'type'      => 'cancel',
                                            'name'      => ts('Cancel') ),
                                    )
                              );
        } else {
            parent::buildQuickForm( );
        }
        //$session->set('single', false );
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

        $dao =& new CRM_Contribute_DAO_Premium();
        $dao->entity_table = 'civicrm_contribution_page';
        $dao->entity_id = $this->_id; 
        $dao->find(true);
        $premiumID = $dao->id;
        if ( $premiumID ) {
            $params['id'] = $premiumID;
        }

        $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );
        $params['entity_table']  = 'civicrm_contribution_page';
        $params['entity_id']     =  $this->_id;
       
        $dao =& new CRM_Contribute_DAO_Premium();
        $dao->copyValues($params);
        $dao->save();

    }

    /** 
     * Return a descriptive name for the page, used in wizard header 
     * 
     * @return string 
     * @access public 
     */ 
    public function getTitle( ) {
        return ts( 'Configure Premiums' );
    }
}
?>
