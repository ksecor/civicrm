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
 * $Id: Field.php 1419 2005-06-10 12:18:04Z shot $
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Contact/BAO/Contact.php';

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
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_gid = CRM_Utils_Request::retrieve('gid', $this);
        $this->_id  = CRM_Utils_Request::retrieve('id' , $this);
       

        $this->_fields =& CRM_Contact_BAO_Contact::importableFields( );

        $this->_selectFields = array( );
        foreach ($this->_fields as $name => $field ) {
            if ( $name ) {
                // lets skip note for now since we dont support it
                if ( $name == 'note' ) {
                    continue;
                }
                $this->_selectFields[$name] = $field['title'];
            }
        }
        // lets add group and tag to this list
        $this->_selectFields['groups'] = ts('CiviCRM Groups');
        $this->_selectFields['tags'  ] = ts('CiviCRM Tags');
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues()
    {
        $defaults = array();
        
        if (isset($this->_id)) {
            $params = array('id' => $this->_id);
            CRM_Core_BAO_UFField::retrieve($params, $defaults);

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
         
        if($this->_action & CRM_Core_Action::DELETE) {
            $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Delete Profile Field '),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
            return;

        }
        
        // lets trim all the whitespace
        $this->applyFilter('__ALL__', 'trim');

        //hidden field to catch the group id in profile
        $this->add('hidden', 'group_id', $this->_gid);
        
        //hidden field to catch the field id in profile
        $this->add('hidden', 'field_id', $this->_id);

        // field name
        $this->add( 'select', 'field_name', ts('CiviCRM Field Name'), $this->_selectFields, true );
        $this->add( 'select', 'visibility', ts('Visibility'        ), CRM_Core_SelectValues::ufVisibility( ), true );

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
        $this->add( 'checkbox', 'is_view'        , ts( 'View Only?'                    ) );
        $this->add( 'checkbox', 'is_registration', ts( 'Display in Registration Form?' ) );
        $this->add( 'checkbox', 'is_match'       , ts( 'Key to Match Contacts?'        ) );
        
        // add buttons
        $this->addButtons(array(
                                array ('type'      => 'next',
                                       'name'      => ts('Save'),
                                       'isDefault' => true),
                                array ('type'      => 'cancel',
                                       'name'      => ts('Cancel')),
                                )
                          );

        $this->addFormRule( array( 'CRM_UF_Form_Field', 'formRule' ) );

        // if view mode pls freeze it with the done button.
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
            $this->addElement('button', 'done', ts('Done'), array('onClick' => "location.href='civicrm/admin/uf/group/field?reset=1&action=browse&gid=" . $this->_gid . "'"));
        }
    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Core_BAO_UFField::del($this->_id);
            CRM_Core_Session::setStatus(ts('Selected Profile Field has been deleted.'));
            return;
        }
        
        // store the submitted values in an array
        $params = $this->controller->exportValues('Field');

        // set values for custom field properties and save
        $ufField                 =& new CRM_Core_DAO_UFField();
        $ufField->field_name     = $params['field_name'];
        $ufField->listings_title = $params['listings_title'];
        $ufField->visibility     = $params['visibility'];
        $ufField->help_post      = $params['help_post'];

        $ufField->is_required     = CRM_Utils_Array::value( 'is_required'    , $params, false );
        $ufField->is_active       = CRM_Utils_Array::value( 'is_active'      , $params, false );
        $ufField->in_selector     = CRM_Utils_Array::value( 'in_selector'    , $params, false );
        //$ufField->weight          = CRM_Utils_Array::value( 'weight'         , $params, false );
        $ufField->is_view         = CRM_Utils_Array::value( 'is_view'        , $params, false );
        $ufField->is_registration = CRM_Utils_Array::value( 'is_registration', $params, false );
        $ufField->is_match        = CRM_Utils_Array::value( 'is_match'       , $params, false );

        if ($this->_action & CRM_Core_Action::UPDATE) {
            $ufField->id = $this->_id;
        }

        // fix for CRM-316
        if ($this->_action & CRM_Core_Action::UPDATE) {

            $uf =& new CRM_Core_DAO_UFField();
            $uf->id = $this->_id;
            $uf->find();

            
            if ( $uf->fetch() && $uf->weight != CRM_Utils_Array::value( 'weight', $params, false ) ) {
                    
                $searchWeight =& new CRM_Core_DAO_UFField();
                $searchWeight->uf_group_id = $this->_gid;
                $searchWeight->weight = CRM_Utils_Array::value( 'weight', $params, false );
                
                if ( $searchWeight->find() ) {                   
                    
                    $tempDAO =& new CRM_Core_DAO();
                    $query = "SELECT id FROM civicrm_uf_field WHERE weight >= ". $searchWeight->weight ." AND uf_group_id = ".$this->_gid;
                    $tempDAO->query($query);

                    $fieldIds = array();
                    while($tempDAO->fetch()) {
                        $fieldIds[] = $tempDAO->id; 
                    }
                    
                    if ( !empty($fieldIds) ) {
                        $ufDAO =& new CRM_Core_DAO();
                        $updateSql = "UPDATE civicrm_uf_field SET weight = weight + 1 WHERE id IN ( ".implode(",", $fieldIds)." ) ";
                        $ufDAO->query($updateSql);                    
                    }
                }
            }                
             
            $ufField->weight = CRM_Utils_Array::value( 'weight', $params, false );
            
        } else {
            $uf =& new CRM_Core_DAO_UFField();
            $uf->uf_group_id = $this->_gid;
            $uf->weight = CRM_Utils_Array::value( 'weight', $params, false );
            
            if ( $uf->find() ) {
                $tempDAO =& new CRM_Core_DAO();
                $query = "SELECT id FROM civicrm_uf_field WHERE weight >= ". CRM_Utils_Array::value( 'weight', $params, false ) ." AND uf_group_id = ".$this->_gid;
                $tempDAO->query($tempDAO);

                $fieldIds = array();                
                while($tempDAO->fetch()) {
                    $fieldIds[] = $tempDAO->id;                
                }                

                if ( !empty($fieldIds) ) {
                    $ufDAO =& new CRM_Core_DAO();
                    $updateSql = "UPDATE civicrm_uf_field SET weight = weight + 1 WHERE id IN ( ".implode(",", $fieldIds)." ) ";
                    $ufDAO->query($updateSql);
                }
            }

            $ufField->weight = CRM_Utils_Array::value( 'weight', $params, false );
        }


        // need the FKEY - uf group id
        $ufField->uf_group_id = $this->_gid;

        $ufField->save();
        
        $name = $this->_selectFields[$ufField->field_name];
        CRM_Core_Session::setStatus(ts('Your civicrm profile field "%1" has been saved.', array(1 => $name)));
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

        $errors = array( );
        if ( $is_view && $is_registration ) {
            $errors['is_registration'] = 'View Only cannot be selected if this field is to be included on the registration form';
        }
        if ( $is_view && $is_required ) {
            $errors['is_view'] = 'A View Only field cannot be required';
        }
        
        if (CRM_Core_Action::ADD && empty($fields['field_id'])) {
            $fieldName = $fields['field_name'];
            $groupId = $fields['group_id'];
            $query = "SELECT count(*) FROM civicrm_uf_field WHERE uf_group_id = " . CRM_Utils_Type::escape($groupId, 'Integer')  . " AND field_name = '" . CRM_Utils_Type::escape($fieldName, 'String') . "'";

            if ( CRM_Core_DAO::singleValueQuery( $query ) > 0 ) {
                $errors['field_name'] = 'Duplicate Field Name choosen. Select different field name';
            }
        }

        return empty($errors) ? true : $errors;
    }
}

?>
