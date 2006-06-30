<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Member/Form.php';

/**
 * This class generates form components for Membership Type
 * 
 */
class CRM_Member_Form_MembershipType extends CRM_Member_Form
{
    /**
     * max number of contacts we will display for membership-organisation
     */
    const MAX_CONTACTS = 50;

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );

        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }

        $this->applyFilter('__ALL__', 'trim');
        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipType', 'name' ) );

        $this->addRule( 'name', ts('A membership type with this name already exists. Please select another name.'), 
                        'objectExists', array( 'CRM_Member_DAO_MembershipType', $this->_id ) );
        $this->add('text', 'description', ts('Description'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipType', 'description' ) );
        $this->add('text', 'minimum_fee', ts('Minimum Fee'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipType', 'minimum_fee' ) );
        $this->add('select', 'duration_unit', ts('Duration') . ' ', CRM_Core_SelectValues::unitList('duration'));
        //period type
        $this->addElement('select', 'period_type', ts('Period Type'), 
                          CRM_Core_SelectValues::periodType( ), array( 'onChange' => 'showHidePeriodSettings()'));

        $this->add('text', 'duration_interval', ts('Duration Interval'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipType', 'duration_interval' ) );

        $memberOrg =& $this->add('text', 'member_org', ts('Membership Organization'), 'size=30 maxlength=120' );
        //start day
        $this->add('date', 'fixed_period_start_day', ts('Fixed Period Start Day'), 
                   CRM_Core_SelectValues::date('custom', 3, 1, 'M d'), false);
        
        //rollover day
        $this->add('date', 'fixed_period_rollover_day', ts('Fixed Period Rollover Day'), 
                   CRM_Core_SelectValues::date('custom', 3, 1, 'M d'), false);
        
        $this->add('hidden','action',$this->_action); //required in form rule

        require_once 'CRM/Contribute/PseudoConstant.php';
        $this->add('select', 'contribution_type_id', ts( 'Contribution Type' ), 
                   array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( ), false );

        require_once 'CRM/Contact/BAO/Relationship.php';
        $relTypeInd =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Individual');
        $this->add('select', 'relationship_type_id', ts('Relationship Type'),  array('' => ts('- select -')) + $relTypeInd);

        $this->add( 'select', 'visibility', ts('Visibility'), CRM_Core_SelectValues::memberVisibility( ) );
        $this->add('text', 'weight', ts('Weight'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_MembershipType', 'weight' ) );
        $this->add('checkbox', 'is_active', ts('Enabled?'));

        $searchRows            = $this->get( 'searchRows'    );
        $searchCount           = $this->get( 'searchCount'   );
        $searchDone            = $this->get( 'searchDone' );

        if ( $searchRows ) {
            $checkBoxes = array( );
            $chekFlag = 0;
            foreach ( $searchRows as $id => $row ) {
                $checked = '';
                if (!$chekFlag) {
                    $checked = array( 'checked' => null);
                    $chekFlag++;
                }
                
                $checkBoxes[$id] = $this->createElement('radio',null, null,null,$id, $checked );
            }
            
            $this->addGroup($checkBoxes, 'contact_check');
            $this->assign('searchRows', $searchRows );
        }

        $this->assign('searchCount', $searchCount);
        $this->assign('searchDone', $searchDone);
        
        if ( $searchDone ) {
            $searchBtn = ts('Search Again');
        } elseif ( $this->_action & CRM_Core_Action::UPDATE ) {
            $searchBtn = ts('Change');
        } else {
            $searchBtn = ts('Search');
        }
        
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $memberOrg->freeze();
            if ( $searchDone ) {
                $memberOrg->unfreeze();
            }
        }
        $this->addElement( 'submit', $this->getButtonName('refresh'), $searchBtn, array( 'class' => 'form-submit' ) );
        
        $this->addFormRule(array('CRM_Member_Form_MembershipType', 'formRule'));
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
    public function formRule( &$params ) {
        $errors = array( );

        if ( !$params['_qf_MembershipType_refresh'] ) {
            if ( !$params['name'] ) {
                $errors['name'] = "Please enter a membership type name.";
            }
            if ( !$params['contribution_type_id'] ) {
                $errors['contribution_type_id'] = "Please enter the contribution type.";
            }
            if ( !$params['contact_check'] && $params['action']!= CRM_Core_Action::UPDATE ) {
                $errors['member_org'] = "Please select the membership organization";
            }
            if ( $params['period_type'] == 'fixed' ) {
                if ( !$params['fixed_period_start_day'] ) {
                    $errors['fixed_period_start_day'] = "Please enter the 'Fixed period start day'.";
                }
            }
            $periods = array('fixed_period_start_day', 'fixed_period_rollover_day');
            foreach ( $periods as $per ) {
                if ($params[$per]['M'] || $params[$per]['d']) {
                    $mon = $params[$per]['M'];
                    $dat = $params[$per]['d'];
                    if (!($mon && $dat)) {
                        $errors[$per] = "Please enter a valid 'Fixed period start day'.";
                    }
                }
            }
        }
        return empty($errors) ? true : $errors;
    }
       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        require_once 'CRM/Member/BAO/MembershipType.php';
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Member_BAO_MembershipType::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected membership type has been deleted.') );
        } else { 
            $params = $ids = array( );
            $params = $this->exportValues();

            $this->set( 'searchDone', 0 );
            if ( CRM_Utils_Array::value( '_qf_MembershipType_refresh', $_POST ) ) {
                $this->search( $params );
                $this->set( 'searchDone', 1 );
                return;
            }
            if ( $params['relationship_type_id'] ) {
                $relationId = explode( '_', $params['relationship_type_id'] );
                $params['relationship_type_id'] = $relationId[0];
            }
            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $ids['membershipType'] = $this->_id;
            }
            
            $periods = array('fixed_period_start_day', 'fixed_period_rollover_day');
            foreach ( $periods as $per ) {
                if ($params[$per]['M'] && $params[$per]['d']) {
                    $mon = $params[$per]['M'];
                    $dat = $params[$per]['d'];
                    $mon = ( $mon < 9) ? '0'.$mon : $mon; 
                    $dat = ( $dat < 9) ? '0'.$dat : $dat; 
                    $params[$per] = $mon . $dat;
                } else {
                    $params[$per] = null;
                }
            }
            $ids['memberOfContact'] = $params['contact_check'];
            $membershipType = CRM_Member_BAO_MembershipType::add($params, $ids);
            CRM_Core_Session::setStatus( ts('The membership type "%1" has been saved.', array( 1 => $membershipType->name )) );
        }
    }

    /**
     * This function is to get the result of the search for membership organisation.
     *
     * @param  array $params  This contains elements for search criteria
     *
     * @access public
     * @return None
     *
     */
    function search(&$params) {
        //max records that will be listed
        $searchValues = array();
        $searchValues[] = array( 'sort_name', 'LIKE', $params['member_org'], 0, 1 );
        
        $searchValues[] = array( 'contact_type', '=', 'organization', 0, 0 );

        // get the count of contact
        $contactBAO  =& new CRM_Contact_BAO_Contact( );
        $query =& new CRM_Contact_BAO_Query( $searchValues );
        $searchCount = $query->searchQuery(0, 0, null, true );
        $this->set( 'searchCount', $searchCount );
        if ( $searchCount <= self::MAX_CONTACTS ) {
            // get the result of the search
            $result = $query->searchQuery(0, self::MAX_CONTACTS, null);

            $config =& CRM_Core_Config::singleton( );
            $searchRows = array( );

            while($result->fetch()) {
                $contactID = $result->contact_id;

                $searchRows[$contactID]['id'] = $contactID;
                $searchRows[$contactID]['name'] = $result->sort_name;
                $searchRows[$contactID]['city'] = $result->city;
                $searchRows[$contactID]['state'] = $result->state;
                $searchRows[$contactID]['email'] = $result->email;
                $searchRows[$contactID]['phone'] = $result->phone;

                $contact_type = '<img src="' . $config->resourceBase . 'i/contact_';

                $contact_type .= 'org.gif" alt="' . ts('Organization') . '" height="16" width="18" />';

                $searchRows[$contactID]['type'] = $contact_type;
            }
            $this->set( 'searchRows' , $searchRows );
        } else {
            // resetting the session variables if many records are found
            $this->set( 'searchRows' , null );
        }
    }

}

?>
