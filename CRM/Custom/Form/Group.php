<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
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
    static function formRule(&$fields, &$files, $options) {
        $errors = array();
        //$extends = array('Activity','Phonecall','Meeting','Group','Contribution');
        $extends = array('Activity','Relationship','Group','Contribution','Membership');
        if(in_array($fields['extends'][0],$extends) && $fields['style'] == 'Tab' ) {
            $errors['style'] = 'Display Style should be Inline for this Class';
        }

        //checks the given custom group doesnot start with digit
        $title = $fields['title']; 
        $asciiValue = ord($title{0});//gives the ascii value
        if($asciiValue>=48 && $asciiValue<=57) {
            $errors['title'] = ts("Group's Name should not start with digit");
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
        $this->addFormRule( array( 'CRM_Custom_Form_Group', 'formRule' ) );
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

        // title
        $this->add('text', 'title', ts('Group Name'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'title'), true);
        $this->addRule( 'title', ts('Name already exists in Database.'),
                        'objectExists', array( 'CRM_Core_DAO_CustomGroup', $this->_id, 'title' ) );
       
        require_once "CRM/Contribute/PseudoConstant.php";
        require_once "CRM/Member/BAO/MembershipType.php";
        $sel1 = CRM_Core_SelectValues::customGroupExtends();
        $sel2= array();
        $sel2['Activity']     = array("" => "-- Any --") + CRM_Core_PseudoConstant::activityType();
        $sel2['Contribution'] = array("" => "-- Any --") + CRM_Contribute_PseudoConstant::contributionType( );
        $sel2['Membership']   = array("" => "-- Any --") + CRM_Member_BAO_MembershipType::getMembershipTypes( false );
        

        $relTypeInd =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Individual');
        $relTypeOrg =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Organization');
        $relTypeHou =  CRM_Contact_BAO_Relationship::getContactRelationshipType(null,'null',null,'Household');
        $allRelationshipType =array();
        $allRelationshipType = array_merge(  $relTypeInd , $relTypeOrg);
        $allRelationshipType = array_merge( $allRelationshipType, $relTypeHou);
        
        $sel2['Relationship'] = array("" => "-- Any --") + $allRelationshipType;
        
        require_once "CRM/Core/Component.php";
        $cSubTypes = CRM_Core_Component::contactSubTypes();
        $contactSubTypes = array();
        foreach($cSubTypes as $key => $value ) {
            $contactSubTypes[$key] = $key;
        }
        $sel2['Contact']  =  array("" => "-- Any --") +$contactSubTypes;
        
        $sel =& $this->addElement('hierselect', "extends", ts('Used For'));
        $sel->setOptions(array($sel1,$sel2));
        $js .= "</script>\n";
        $this->assign('initHideBoxes', $js);
        
        // which entity is this custom data group for ?
        // for update action only allowed if there are no custom values present for this group.
        // $extendsElement = $this->add('select', 'extends', ts('Used For'), CRM_Core_SelectValues::customGroupExtends());
        
        if ($this->_action == CRM_Core_Action::UPDATE) { 
            $sel->freeze();
            $this->assign('gid', $this->_id);
        }
        
        // help text
        $this->add('textarea', 'help_pre',  ts('Pre-form Help'),  CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'help_pre'));
        $this->add('textarea', 'help_post',  ts('Post-form Help'),  CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'help_post'));

        // weight
        $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomGroup', 'weight'), true);
        $this->addRule('weight', ts(' is a numeric field') , 'numeric');

        // display style
        $this->add('select', 'style', ts('Display Style'), CRM_Core_SelectValues::customGroupStyle());
       
        // is this group collapsed or expanded ?
        $this->addElement('checkbox', 'collapse_display', ts('Collapse this group on initial display'));

        // is this group active ?
        $this->addElement('checkbox', 'is_active', ts('Is this Custom Data Group active?') );


        // $this->addFormRule(array('CRM_Custom_Form_Group', 'formRule'));
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
            $defGroup =& new CRM_Core_DAO_CustomGroup();
            $defGroup->domain_id = CRM_Core_Config::domainID( );
            $defGroup->orderBy('weight DESC');
            $defGroup->find( );
            
            if ( $defGroup->fetch() ) {
                $defaults['weight'] = $defGroup->weight + 1;
            } else {
                $defaults['weight'] = 1;
            }
            
        }

        if (isset($this->_id)) {
            $params = array('id' => $this->_id);
            CRM_Core_BAO_CustomGroup::retrieve($params, $defaults);
            
        } else {
            $defaults['is_active'] = 1;
            $defaults['style'] = 'Inline';
        }

        
        $extends = $defaults['extends'];
        unset($defaults['extends']);
        $defaults['extends'][0] = $extends;
        $defaults['extends'][1] = $defaults['extends_entity_column_value'];

        
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
    public function postProcess()
    {
        // get the submitted form values.
        $params = $this->controller->exportValues('Group');
        
        // create custom group dao, populate fields and then save.           
        $group =& new CRM_Core_DAO_CustomGroup();
        $group->title            = $params['title'];
        $group->name             = CRM_Utils_String::titleToVar($params['title']);
        $group->extends          = $params['extends'][0];
        
        if ( ($params['extends'][0] == 'Relationship') && !empty($params['extends'][1])) {
            $group->extends_entity_column_value = str_replace( array('_a_b', '_b_a'), array('', ''), $params['extends'][1]);
        } elseif ( empty($params['extends'][1]) ) {
            $group->extends_entity_column_value = null;
        } else {
            $group->extends_entity_column_value = $params['extends'][1];
        }
        
        $group->style            = $params['style'];
        $group->collapse_display = CRM_Utils_Array::value('collapse_display', $params, false);

        // fix for CRM-316
        if ($this->_action & CRM_Core_Action::UPDATE) {

            $cg =& new CRM_Core_DAO_CustomGroup();
            $cg->id = $this->_id;
            $cg->find();

            if ( $cg->fetch() && $cg->weight != $params['weight'] ) {
                
                $searchWeight =& new CRM_Core_DAO_CustomGroup();
                $searchWeight->domain_id = CRM_Core_Config::domainID( );
                $searchWeight->weight = $params['weight'];
                
                if ( $searchWeight->find() ) {

                    $tempDAO =& new CRM_Core_DAO();
                    $query = "SELECT id FROM civicrm_custom_group WHERE weight >= ". $searchWeight->weight ." AND domain_id = ".CRM_Core_Config::domainID( );
                    $tempDAO->query($query);

                    $groupIds = array();
                    
                    while($tempDAO->fetch()) {
                        $groupIds[] = $tempDAO->id; 
                    }
                    
                    if ( !empty($groupIds) ) {
                        $cgDAO =& new CRM_Core_DAO();
                        $updateSql = "UPDATE civicrm_custom_group SET weight = weight + 1 WHERE id IN ( ".implode(",", $groupIds)." ) ";
                        $cgDAO->query($updateSql);                    
                    }
                }
            }
            
            $group->weight  = $params['weight'];
            
        } else {
            $cg =& new CRM_Core_DAO_CustomGroup();
            $cg->domain_id = CRM_Core_Config::domainID( );
            $cg->weight = $params['weight'];
            
            if ( $cg->find() ) {
                $tempDAO =& new CRM_Core_DAO();
                $query = "SELECT id FROM civicrm_custom_group WHERE weight >= ". $cg->weight ." AND domain_id = ".CRM_Core_Config::domainID( );
                $tempDAO->query($query);
                
                $groupIds = array();
                while($tempDAO->fetch()) {
                    $groupIds[] = $tempDAO->id; 
                }
                
                if ( !empty($groupIds) ) {
                    $cgDAO =& new CRM_Core_DAO();
                    $updateSql = "UPDATE civicrm_custom_group SET weight = weight + 1 WHERE id IN ( ".implode(",", $groupIds)." ) ";
                    $cgDAO->query($updateSql);
                }
                
            }

            $group->weight = $params['weight'];  
        } 
    

        $group->help_pre         = $params['help_pre'];
        $group->help_post        = $params['help_post'];
        $group->is_active        = CRM_Utils_Array::value('is_active', $params, false);
        $group->domain_id        = CRM_Core_Config::domainID( );

        if ($this->_action & CRM_Core_Action::UPDATE) {
            $group->id = $this->_id;
        }
        $group->save();

        if ($this->_action & CRM_Core_Action::UPDATE) {
            CRM_Core_Session::setStatus(ts('Your Group "%1" has been saved.', array(1 => $group->title)));
        } else {
            $url = CRM_Utils_System::url( 'civicrm/admin/custom/group/field', 'reset=1&action=add&gid=' . $group->id);
            CRM_Core_Session::setStatus(ts('Your Group "%1" has been added. You can <a href="%2">add custom fields</a> to this group now.', array(1 => $group->title, 2 => $url)));
        }
    }
}
?>