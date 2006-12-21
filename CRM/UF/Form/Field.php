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
 * $Id: Field.php 1419 2005-06-10 12:18:04Z shot $
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Contact/BAO/Contact.php';

require_once 'CRM/Core/BAO/UFField.php';

/**
 * form to process actions on the field aspect of Custom
 */
class CRM_UF_Form_Field extends CRM_Core_Form {
    /**
     * the uf group id saved to the session for an update
     *
     * @var int
     * @access protected
     */
    protected $_gid;

    /**
     * The field id, used when editing the field
     *
     * @var int
     * @access protected
     */
    protected $_id;

    /**
     * The set of fields that we can view/edit in the user field framework
     *
     * @var array
     * @access protected
     */
    
    protected $_fields;

    /**
     * the title for field
     *
     * @var int
     * @access protected
     */
    protected $_title;

    /**
     * The set of fields sent to the select element
     *
     * @var array
     * @access protected
     */
    protected $_selectFields;

    /**
     * to store fields with if locationtype exits status 
     *
     * @var array
     * @access protected
     */
    protected $_hasLocationTypes;
    
    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_gid = CRM_Utils_Request::retrieve('gid', 'Positive',
                                                  $this);
        $this->_id  = CRM_Utils_Request::retrieve('id' , 'Positive',
                                                  $this);

        if($this->_action & CRM_Core_Action::UPDATE) {
            $this->_fields =& CRM_Contact_BAO_Contact::importableFields('All', true, true);
        } else {
            $this->_fields =& CRM_Contact_BAO_Contact::importableFields('All', true);
        }
    
        $this->_fields = array_merge (CRM_Contribute_BAO_Contribution::getContributionFields(), $this->_fields);

        if ( CRM_Core_Permission::access( 'Quest' ) ) {
            require_once 'CRM/Quest/BAO/Student.php';
            $this->_fields = array_merge (CRM_Quest_BAO_Student::exportableFields(), $this->_fields);
        }

        $this->_selectFields = array( );
        foreach ($this->_fields as $name => $field ) {
            // lets skip note for now since we dont support it
            if ( $name == 'note' ) {
                continue;
            }
            $this->_selectFields    [$name] = $field['title'];
            $this->_hasLocationTypes[$name] = $field['hasLocationType'];
        }

        // lets add group and tag to this list
        $this->_selectFields['group'] = ts('Group(s)');
        $this->_selectFields['tag'  ] = ts('Tag(s)');
       
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {

        if($this->_action & CRM_Core_Action::DELETE) {
            $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Delete Profile Field'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
            return;

        }

        if (isset($this->_id)) {
            $params = array('id' => $this->_id);
            CRM_Core_BAO_UFField::retrieve($params, $defaults);
            
            $specialFields = array ('street_address','supplemental_address_1', 'supplemental_address_2', 'city', 'postal_code', 'postal_code_suffix', 'geo_code_1', 'geo_code_2', 'state_province', 'country', 'phone', 'email', 'im', 'location_name' );

            if ( !$defaults['location_type_id'] && in_array($defaults['field_name'], $specialFields)  ) {
                $defaults['location_type_id'] = 0;
            }
            
            $defaults[ 'field_name' ] = array ($defaults['field_type'], $defaults['field_name'], $defaults['location_type_id'], $defaults['phone_type']);
            $this->_gid = $defaults['uf_group_id'];
        } else {
            $defaults['is_active'] = 1;
        }
        
        if ($this->_action & CRM_Core_Action::ADD) {
            $uf =& new CRM_Core_DAO();
            $sql = "SELECT weight FROM civicrm_uf_field  WHERE uf_group_id = ". $this->_gid ." ORDER BY weight  DESC LIMIT 0, 1"; 
            $uf->query($sql);
            while( $uf->fetch( ) ) {
                $defaults['weight'] = $uf->weight + 1;
            }
            
            if ( empty($defaults['weight']) ) {
                $defaults['weight'] = 1;
            }
        }
        
        // lets trim all the whitespace
        $this->applyFilter('__ALL__', 'trim');

        //hidden field to catch the group id in profile
        $this->add('hidden', 'group_id', $this->_gid);
        
        //hidden field to catch the field id in profile
        $this->add('hidden', 'field_id', $this->_id);

        $fields = array();
        $fields['Individual'  ] =& CRM_Contact_BAO_Contact::exportableFields('Individual');
        $fields['Household'   ] =& CRM_Contact_BAO_Contact::exportableFields('Household');
        $fields['Organization'] =& CRM_Contact_BAO_Contact::exportableFields('Organization');

        $config =& CRM_Core_Config::singleton( );
        if ( !$config->includeCounty ) {
            unset( $fields['Individual'  ]['county']);
            unset( $fields['Household'   ]['county']);
            unset( $fields['Organization']['county']);
        }

        if ( CRM_Core_Permission::access( 'Quest' ) ) {
            require_once 'CRM/Quest/BAO/Student.php';
            $fields['Student']      =& CRM_Quest_BAO_Student::exportableFields();
        }

        if ( CRM_Core_Permission::access( 'CiviContribute' ) ) {
            $contribFields =& CRM_Contribute_BAO_Contribution::getContributionFields();
            if ( ! empty( $contribFields ) ) {
                $fields['Contribution'] =& $contribFields;
            }
        }

        if ( CRM_Core_Permission::access( 'CiviEvent' ) ) {
            require_once 'CRM/Event/BAO/Query.php';
            $participantFields =& CRM_Event_BAO_Query::getParticipantFields();
            if ( ! empty( $participantFields ) ) {
                $fields['Participant'] =& $participantFields;
            }
        }

        $noSearchable = array();
        foreach ($fields as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $this->_mapperFields[$key][$key1] = $value1['title'];
                $hasLocationTypes[$key][$key1]    = $value1['hasLocationType'];

                //for hiding the 'is searchable' field for 'File' custom data
                if ( ($value1['data_type'] == 'File') && ($value1['html_type'] == 'File') ) {
                    if (!in_array($value1['title'], $noSearchable)) {
                        $noSearchable[] = $value1['title'];
                    }
                }
            }
        }
        $this->assign('noSearchable', $noSearchable);

        require_once 'CRM/Core/BAO/LocationType.php';
        $this->_location_types  =& CRM_Core_PseudoConstant::locationType();
        $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();
        
       /* FIXME: dirty hack to make the default option show up first.  This
        * avoids a mozilla browser bug with defaults on dynamically constructed
        * selector widgets. */
        
        if ($defaultLocationType) { 
            $defaultLocation = $this->_location_types[$defaultLocationType->id];
            unset($this->_location_types[$defaultLocationType->id]);
            $this->_location_types = array($defaultLocationType->id => $defaultLocation) +  $this->_location_types;
        }
        
        $this->_location_types = array ('Primary') + $this->_location_types;

        $sel1 = array('' => '-select-') + CRM_Core_SelectValues::contactType();// + array('Student' => 'Students');
        
        if ( CRM_Core_Permission::access( 'Quest' ) ) {
            $sel1['Student'] = 'Students';
        }
        
        if ( CRM_Core_Permission::access( 'CiviEvent' ) ) {
            $sel1['Participant'] = 'Participants';
        }
        
        if ( ! empty( $contribFields ) ) {
            $sel1['Contribution'] = 'Contributions';
        }

        foreach ($sel1 as $key=>$sel ) {
            if ($key) {
                $sel2[$key] = $this->_mapperFields[$key];
            }
        }
        
        $sel3[''] = null;
        $phoneTypes = CRM_Core_SelectValues::phoneType();
     
        foreach ($sel1 as $k=>$sel ) {
            if ($k) {
                foreach ($this->_location_types as $key => $value) {                        
                    $sel4[$k]['phone'][$key] =& $phoneTypes;
                }
            }
        }
      
        foreach ($sel1 as $k=>$sel ) {
            if ($k) {
                if (is_array($this->_mapperFields[$k])) {
                    foreach ($this->_mapperFields[$k]  as $key=>$value) {
                        if ($hasLocationTypes[$k][$key]) {
                            $sel3[$k][$key] = $this->_location_types;
                        } else {
                            $sel3[$key] = null;
                        }
                    }
                }
            }
        }

        $this->_defaults = array();
        $js = "<script type='text/javascript'>\n";
        $formName = "document.{$this->_name}";
      
        $sel =& $this->addElement('hierselect', "field_name", ts('Field Name'), 'onclick="showLabel();"');  
        $formValues = array();
         
        $formValues = $this->exportValues( );

        if ( empty( $formValues ) ) {
            for ( $k = 1; $k < 4; $k++ ) {
                if (!$defaults['field_name'][$k] ) {
                    $js .= "{$formName}['field_name[$k]'].style.display = 'none';\n"; 
                }
            }
        } else {
            if ( !empty($formValues['field_name']) ) {
                foreach ( $formValues['field_name'] as $value) {
                    for ( $k = 1; $k < 4; $k++ ) {
                        if (!$formValues['field_name'][$k]) {
                            $js .= "{$formName}['field_name[$k]'].style.display = 'none';\n"; 
                        } else {
                            $js .= "{$formName}['field_name[$k]'].style.display = '';\n"; 
                        }
                    }
                }
            } else {
                for ( $k = 1; $k < 4; $k++ ) {
                    if ( ! isset($defaults['field_name'][$k]) ) {
                         $js .= "{$formName}['field_name[$k]'].style.display = 'none';\n"; 
                     }
                }
            } 
        }
        
        $sel->setOptions(array($sel1,$sel2,$sel3, $sel4));
        
        $js .= "</script>\n";
        $this->assign('initHideBoxes', $js);
        
        $this->add( 'select', 'visibility', ts('Visibility'), CRM_Core_SelectValues::ufVisibility( ), true );
        
        // should the field appear in selector?
        $this->add('checkbox', 'in_selector', ts('In Selector?'));
       
        // weight
        $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFField', 'weight'), true);
        $this->addRule('weight', ts(' is a numeric field') , 'numeric');
        
        $this->add('textarea', 'help_post', ts('Field Help'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFField', 'help_post'));
        
        // listings title
        $this->add('text', 'listings_title', ts('Listings Title'),
                   CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFField', 'listings_title') );
        $this->addRule('listings_title', ts('Please enter a valid title for this field when displayed in user listings.'), 'title');
        
        $this->add( 'checkbox', 'is_required'    , ts( 'Required?'                     ) );
        $this->add( 'checkbox', 'is_active'      , ts( 'Active?'                       ) );
        $this->add( 'checkbox', 'is_searchable'  , ts( 'Searchable?'                   ) );
        $this->add( 'checkbox', 'is_view'        , ts( 'View Only?'                    ) );
        // $this->add( 'checkbox', 'is_registration', ts( 'Display in Registration Form?' ) );
        //$this->add( 'checkbox', 'is_match'       , ts( 'Key to Match Contacts?'        ) );

        $this->add('text', 'label', ts('Field Label'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_UFField', 'label'));
        
        // add buttons
        $this->addButtons(array(
                                array ('type'      => 'next',
                                       'name'      => ts('Save'),
                                       'isDefault' => true),
                                array ('type'      => 'cancel',
                                       'name'      => ts('Cancel')),
                                )
                          );

        $this->addFormRule( array( 'CRM_UF_Form_Field', 'formRule' ));

        // if view mode pls freeze it with the done button.
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
            $this->addElement('button', 'done', ts('Done'), array('onclick' => "location.href='civicrm/admin/uf/group/field?reset=1&action=browse&gid=" . $this->_gid . "'"));
        }

        $this->setDefaults($defaults);

    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
        if ($this->_action & CRM_Core_Action::DELETE) {
            CRM_Core_BAO_UFField::del($this->_id);
            CRM_Core_Session::setStatus(ts('Selected Profile Field has been deleted.'));
            return;
        }
     
        // store the submitted values in an array
        $params = $this->controller->exportValues('Field');
        $ids = array( );
       
        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $ids['uf_field'] = $this->_id;
        }
        
        $ids['uf_group'] = $this->_gid;
        
        //check for duplicate fields
        if (CRM_Core_BAO_UFField::duplicateField($params, $ids) ) {
            CRM_Core_Session::setStatus(ts('The selected field was not added. It already exists in this profile.'));
            return;
        } else {
            $ufField = CRM_Core_BAO_UFField::add($params,$ids);
            $name = $this->_selectFields[$ufField->field_name];
            CRM_Core_Session::setStatus(ts('Your civicrm profile field "%1" has been saved.', array(1 => $name)));
        }
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
    static function formRule( &$fields ) {
        $is_required     = CRM_Utils_Array::value( 'is_required'    , $fields, false );
        $is_registration = CRM_Utils_Array::value( 'is_registration', $fields, false );
        $is_view         = CRM_Utils_Array::value( 'is_view'        , $fields, false );
        $in_selector     = CRM_Utils_Array::value( 'in_selector'    , $fields, false );
        $visibility      = CRM_Utils_Array::value( 'visibility'     , $fields, false );
        $is_active       = CRM_Utils_Array::value( 'is_active'      , $fields, false );

        $errors = array( );
        if ( $is_view && $is_registration ) {
            $errors['is_registration'] = 'View Only cannot be selected if this field is to be included on the registration form';
        }
        if ( $is_view && $is_required ) {
            $errors['is_view'] = 'A View Only field cannot be required';
        }
        if ( $in_selector && ($visibility != 'Public User Pages and Listings' )) {
            $errors['visibility'] = 'Visibility should be "Public User Pages and Listings" if "In Selector ?" is checked.';
        }
        $fieldName = $fields['field_name'][0];
        if (!$fieldName) {
            $errors['field_name'] = 'Please select a field name';
        }
        
        if ( $in_selector && $fieldName == 'Contribution' ) {
            $errors['in_selector'] = "'In Selector' can NOT be checked for Contribution fields.";
        }
        
        if (! empty($fields['field_id'])) {
            //get custom field id 
            $customFieldId = explode('_', $fieldName);
            if ($customFieldId[0] == 'custom') {
                $customField =& new CRM_Core_DAO_CustomField();
                $customField->id = $customFieldId[1];
                $customField->find(true);
                
                if ( !$customField->is_active && $is_active) {
                    $errors['field_name'] = 'Cannot set this field "Active" since the selected custom field is disabled.';
                }
            }
         }
        return empty($errors) ? true : $errors;
    }

}

?>
