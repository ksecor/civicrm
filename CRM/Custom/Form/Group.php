<?php

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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Custom_Form_Group extends CRM_Core_Form {

    /**
     * the group id saved to the session for an update
     *
     * @var int
     * @access protected
     */
    protected $_id;

    /**
     * Function to set variables up before form is built
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function preProcess()
    {
        require_once 'CRM/Core/BAO/CustomGroup.php';
        // current group id
        $this->_id = $this->get('id');

        // setting title for html page
        if ($this->_action == CRM_Core_Action::UPDATE) {
            $title = CRM_Core_BAO_CustomGroup::getTitle($this->_id);
            CRM_Utils_System::setTitle(ts('Edit %1', array(1 => $title)));
        } else if ($this->_action == CRM_Core_Action::VIEW) {
            $title = CRM_Core_BAO_CustomGroup::getTitle($this->_id);
            CRM_Utils_System::setTitle(ts('Preview %1', array(1 => $title)));
        } else {
            CRM_Utils_System::setTitle(ts('New Custom Data Group'));
        }
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
    static function formRule(&$fields, &$files, $self) {
        $errors = array();

        $extends = array('Activity','Relationship','Group','Contribution','Membership', 'Event','Participant');
        if(in_array($fields['extends'][0],$extends) && $fields['style'] == 'Tab' ) {
            $errors['style'] = 'Display Style should be Inline for this Class';
        }

        //checks the given custom group doesnot start with digit
        $title = $fields['title']; 
        if ( ! empty( $title ) ) {
            $asciiValue = ord($title{0});//gives the ascii value
            if($asciiValue>=48 && $asciiValue<=57) {
                $errors['title'] = ts("Group's Name should not start with digit");
            } 
        }

        return empty($errors) ? true : $errors;
    }
    
    

    /**
     * This function is used to add the rules (mainly global rules) for form.
     * All local rules are added near the element
     *
     * @param null
     * 
     * @return void
     * @access public
     * @see valid_date
     */
    function addRules( )
    {
        $this->addFormRule( array( 'CRM_Custom_Form_Group', 'formRule' ), $this ); 
    }
    
    /**
     * Function to actually build the form
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->applyFilter('__ALL__', 'trim');
        
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_CustomGroup' );
        
        //title
        $this->add('text', 'title', ts('Group Name'), $attributes['title'], true);
        $this->addRule( 'title',
                        ts( 'Name already exists in Database.' ),
                        'objectExists',
                        array( 'CRM_Core_DAO_CustomGroup', $this->_id, 'title' ) );   
        
        //Fix for code alignment, CRM-3058
        require_once "CRM/Contribute/PseudoConstant.php";
        require_once "CRM/Member/BAO/MembershipType.php";
        require_once 'CRM/Event/PseudoConstant.php';
        require_once "CRM/Contact/BAO/Relationship.php";
        
        $sel1 = array( "" => "-- Select --" ) + CRM_Core_SelectValues::customGroupExtends( );
        $sel2 = array( );
        $activityType    = CRM_Core_PseudoConstant::activityType( false );
        $eventType       = CRM_Core_OptionGroup::values( 'event_type' );
        $membershipType  = CRM_Member_BAO_MembershipType::getMembershipTypes( false );
        $participantRole = CRM_Core_OptionGroup::values( 'participant_role' );
        $relTypeInd      = CRM_Contact_BAO_Relationship::getContactRelationshipType( null, 'null', null, 'Individual' );
        $relTypeOrg      = CRM_Contact_BAO_Relationship::getContactRelationshipType( null, 'null', null, 'Organization' );
        $relTypeHou      = CRM_Contact_BAO_Relationship::getContactRelationshipType( null, 'null', null, 'Household' );

        ksort( $sel1 );
        asort( $activityType );
        asort( $eventType );
        asort( $membershipType );
        asort( $participantRole );
        $allRelationshipType = array();
        $allRelationshipType = array_merge(  $relTypeInd , $relTypeOrg);        
        $allRelationshipType = array_merge( $allRelationshipType, $relTypeHou);

        $sel2['Event']                = array( "" => "-- Any --" ) + $eventType;
        $sel2['Activity']             = array( "" => "-- Any --" ) + $activityType;
        $sel2['Membership']           = array( "" => "-- Any --" ) + $membershipType;
        $sel2['ParticipantRole']      = array( "" => "-- Any --" ) + $participantRole;
        $sel2['ParticipantEventName'] = array( "" => "-- Any --" ) + CRM_Event_PseudoConstant::event( );
        $sel2['ParticipantEventType'] = array( "" => "-- Any --" ) + $eventType;
        $sel2['Contribution']         = array( "" => "-- Any --" ) + CRM_Contribute_PseudoConstant::contributionType( );
        $sel2['Relationship']         = array( "" => "-- Any --" ) + $allRelationshipType;
        
        require_once "CRM/Core/Component.php";
        $cSubTypes = CRM_Core_Component::contactSubTypes();
        
        if ( !empty( $cSubTypes ) ) {
            $contactSubTypes = array( );
            foreach($cSubTypes as $key => $value ) {
                $contactSubTypes[$key] = $key;
            }
            $sel2['Contact']  =  array("" => "-- Any --") + $contactSubTypes;
        } else {
            if( !isset( $this->_id ) ){
                $formName = 'document.forms.' . $this->_name;
                
                $js  = "<script type='text/javascript'>\n";
                $js .= "{$formName}['extends[1]'].style.display = 'none';\n";
                $js .= "</script>";
                $this->assign( 'initHideBlocks', $js );
            }
        }
        
        $sel =& $this->addElement('hierselect',
                                  "extends",
                                  ts('Used For'),
                                  array( 'onClick' => "showHideStyle();",
                                         'name'    => "extends[0]"
                                         ) );
        $sel->setOptions( array( $sel1, $sel2 ) );
        
        if ($this->_action == CRM_Core_Action::UPDATE) { 
            $sel->freeze();
            $this->assign('gid', $this->_id);
        }
        
        // help text
        $this->addWysiwyg( 'help_pre', ts('Pre-form Help'), $attributes['help_pre']);
        $this->addWysiwyg( 'help_post', ts('Post-form Help'), $attributes['help_post']);

        // weight
        $this->add('text', 'weight', ts('Order'), $attributes['weight'], true);
        $this->addRule('weight', ts('is a numeric field') , 'numeric');

        // display style
        $this->add('select', 'style', ts('Display Style'), CRM_Core_SelectValues::customGroupStyle());
       
        // is this group collapsed or expanded ?
        $this->addElement('checkbox', 'collapse_display', ts('Collapse this group on initial display'));

        // is this group active ?
        $this->addElement('checkbox', 'is_active', ts('Is this Custom Data Group active?') );

        // does this group have multiple record?
        $this->addElement('checkbox', 'is_multiple', ts('Does this Custom Data Group allow multiple records?') );

        $this->add('text', 'min_multiple', ts('Minimum number of multiple records'), $attributes['min_multiple'] );
        $this->addRule('min_multiple', ts('is a numeric field') , 'numeric');

        $this->add('text', 'max_multiple', ts('Maximum number of multiple records'), $attributes['max_multiple'] );
        $this->addRule('max_multiple', ts('is a numeric field') , 'numeric');


        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
        
        // views are implemented as frozen form
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
            $this->addElement('button', 'done', ts('Done'), array('onclick' => "location.href='civicrm/admin/custom/group?reset=1&action=browse'"));
        }
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @param null
     * 
     * @return array   array of default values
     * @access public
     */
    function setDefaultValues()
    {
        $defaults = array();
    
        if ($this->_action == CRM_Core_Action::ADD) {
            $defaults['weight'] = CRM_Utils_Weight::getDefaultWeight('CRM_Core_DAO_CustomGroup');

            $defaults['is_multiple'] = $defaults['min_multiple'] = $defaults['max_multiple'] = 0;
        }

        if (isset($this->_id)) {
            $params = array('id' => $this->_id);
            CRM_Core_BAO_CustomGroup::retrieve($params, $defaults);
            
        } else {
            $defaults['is_active'] = 1;
            $defaults['style'] = 'Inline';
        }

        if ( isset ($defaults['extends'] ) ){     
            $extends = $defaults['extends'];
            unset($defaults['extends']);
            $defaults['extends'][0] = $extends;
            $defaults['extends'][1] = CRM_Utils_Array::value( 'extends_entity_column_value',
                                                              $defaults );
        }
        
        return $defaults;
        
    }
    
    /**
     * Process the form
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function postProcess( )
    {
        // get the submitted form values.
        $params = $this->controller->exportValues('Group');
   
        if ($this->_action & CRM_Core_Action::UPDATE) {
            $params['id'] = $this->_id;
        } 
       
        $group = CRM_Core_BAO_CustomGroup::create( $params );
        
        // reset the cache
        require_once 'CRM/Core/BAO/Cache.php';
        CRM_Core_BAO_Cache::deleteGroup( 'contact fields' );
      
        if ($this->_action & CRM_Core_Action::UPDATE) {
            CRM_Core_Session::setStatus(ts('Your custom data group \'%1 \' has been saved.', array(1 => $group->title)));
        } else {
            $url = CRM_Utils_System::url( 'civicrm/admin/custom/group/field', 'reset=1&action=add&gid=' . $group->id);
            CRM_Core_Session::setStatus(ts('Your custom data group \'%1\' has been added. You can add custom fields to this group now.',
                                           array(1 => $group->title)));
            $session =& CRM_Core_Session::singleton( );
            $session->replaceUserContext($url);
        }
    }
}

