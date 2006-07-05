<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 * form to process actions on Membership
 */
class CRM_Member_Form_MembershipBlock extends CRM_Contribute_Form_ContributionPage {
    
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
            require_once 'CRM/Member/DAO/MembershipBlock.php';
            $dao =& new CRM_Member_DAO_MembershipBlock();
            $dao->entity_table = 'civicrm_contribution_page';
            $dao->entity_id = $this->_id; 
            $dao->find(true);
            CRM_Core_DAO::storeValues( $dao,$defaults );
        }
        // for membership_types
        $membershipType    = explode(',' , $defaults['membership_types'] );
        $newMembershipType = array();  
        foreach( $membershipType as $k => $v ) {
            $newMembershipType[$v] = 1;
        }
        $defaults['membership_type'] = $newMembershipType;
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
             
        $this->addElement('checkbox', 'is_active', ts('Membership Section Enabled?') );
        
        $this->addElement('text', 'new_title', ts('Title - New Membership'), CRM_Core_DAO::getAttribute('CRM_Member_DAO_MembershipBlock', 'new_title'));
               
        $this->add('textarea', 'new_text', ts('Introductory Message - New Memberships'), 'rows=5, cols=50');
        
        $this->addElement('text', 'renewal_title', ts('Title - Renewals'), CRM_Core_DAO::getAttribute('CRM_Member_DAO_MembershipBlock', 'renewal_title'));
        
        $this->add('textarea', 'renewal_text', ts('Introductory Message - Renewals'), 'rows=5, cols=50');

        $this->addElement('checkbox', 'is_required', ts('Require Membership Signup') );
        $this->addElement('checkbox', 'display_min_fee', ts('Display Minimum Membership Fee') );
        $this->addElement('checkbox', 'is_separate_payment', ts('Separate Membership Payment') );

        require_once 'CRM/Member/BAO/MembershipType.php';
        $membershipTypes = CRM_Member_BAO_MembershipType::getMembershipTypes(); 
        
        $membership        = array();
        $membershipDefault = array();
        foreach ( $membershipTypes as $k => $v ) {
            $membership[]      = HTML_QuickForm::createElement('advcheckbox', $k , null, $v );
            $membershipDefault[] = HTML_QuickForm::createElement('radio',null ,null,null, $k );
        }
       
        $this->addGroup($membership, 'membership_type', ts('Membership Types'));
        $this->addGroup($membershipDefault, 'membership_type_default', ts('Membership Types Default'));
        
        $this->addFormRule(array('CRM_Member_Form_MembershipBlock', 'formRule'));
       
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
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule(&$params, &$files) {
        if ( $params['is_active'] ) {
            $membershipType = array_values($params['membership_type']);
            if ( array_sum($membershipType) == 0 ) {
                $errors['membership_type'] = 'Please select at least one Membership Type to include in the Membership section of this page.';
            }
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

        require_once 'CRM/Member/DAO/MembershipBlock.php';
        $dao =& new CRM_Member_DAO_MembershipBlock();
        $dao->entity_table = 'civicrm_contribution_page';
        $dao->entity_id = $this->_id; 
        $dao->find(true);
        $membershipID = $dao->id;
        if ( $membershipID ) {
            $params['id'] = $membershipID;
        }
        
        $membershipTypes = array();
        foreach( $params['membership_type'] as $k => $v) {
            if ( $v ) {
                $membershipTypes[] = $k;
            }
        }
        
        $params['membership_types']              =  implode(',', $membershipTypes);
        $params['is_required']                   =  CRM_Utils_Array::value( 'is_required', $params, false );
        $params['is_active']                     =  CRM_Utils_Array::value( 'is_active', $params, false );
        $params['display_min_fee']               =  CRM_Utils_Array::value( 'display_min_fee', $params, false );
        $params['is_separate_payment']              =  CRM_Utils_Array::value( 'is_separate_payment', $params, false );
        $params['entity_table']                  = 'civicrm_contribution_page';
        $params['entity_id']                     =  $this->_id;
       
        $dao =& new CRM_Member_DAO_MembershipBlock();
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
        return ts( 'Configure Membership' );
    }
}
?>
