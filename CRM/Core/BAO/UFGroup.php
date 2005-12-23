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

require_once 'CRM/Core/Controller/Simple.php';
require_once 'CRM/Core/DAO/UFGroup.php';
require_once 'CRM/Core/DAO/UFField.php';
require_once 'CRM/Contact/BAO/Contact.php';

/**
 *
 */
class CRM_Core_BAO_UFGroup extends CRM_Core_DAO_UFGroup 
{
    const 
        PUBLIC_VISIBILITY   = 1,
        ADMIN_VISIBILITY    = 2,
        LISTINGS_VISIBILITY = 4;

    /**
     * cache the match clause used in this transaction
     *
     * @var string
     */
    static $_matchFields = null;
    
    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Core_BAO_UFGroup object
     * @access public
     * @static
     */
    static function retrieve(&$params, &$defaults)
    {
        return CRM_Core_DAO::commonRetrieve( 'CRM_Core_DAO_UFGroup', $params, $defaults );
    }
    
    /**
     * Get the form title.
     *
     * @param int $id id of uf_form
     * @return string title
     *
     * @access public
     * @static
     *
     */
    public static function getTitle( $id )
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFGroup', $id, 'title' );
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive($id, $is_active) {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_UFGroup', $id, 'is_active', $is_active );
    }

    /**
     * get all the registration fields
     *
     * @param int $action   what action are we doing
     * @param int $mode     mode 
     *
     * @return array the fields that are needed for registration
     * @static
     * @access public
     */
    static function getRegistrationFields( $action, $mode ) {
        if ( $mode & CRM_Profile_Form::MODE_REGISTER) {
            $ufGroups =& CRM_Core_BAO_UFGroup::getModuleUFGroup('User Registration');
        } else {
            $ufGroups =& CRM_Core_BAO_UFGroup::getModuleUFGroup('Profile');  
        }
        
        if (!is_array($ufGroups)) {
            return false;
        }
        
        $fields = array( );

        foreach ( $ufGroups as $id => $title ) {
            $subset = self::getFields( $id, true, $action );

            // we do not allow duplicates. the first field is the winner
            foreach ( $subset as $name => $field ) {
                if ( ! CRM_Utils_Array::value( $name, $fields ) ) {
                    $fields[$name] = $field;
                }
            }
        }
        return $fields;
    }

    /** 
     * get all the listing fields 
     * 
     * @param int  $action            what action are we doing 
     * @param int  $visibility        visibility of fields we are interested in
     * @param bool $considerSelector  whether to consider the in_selector parameter
     * 
     * @return array the fields that are listings related
     * @static 
     * @access public 
     */ 
    static function getListingFields( $action, $visibility, $considerSelector = false, $ufGroupId = null ) {
        if ($ufGroupId) {
            $subset = self::getFields( $ufGroupId, false, $action, false, $visibility );
            if ($considerSelector) {
                // drop the fields not meant for the selector
                foreach ($subset as $name => $field) {
                    if ( ! $field['in_selector'] ) {
                        unset($subset[$name]);
                    }
                }
            }
            $fields = $subset ; 
        } else {
            $ufGroups =& CRM_Core_PseudoConstant::ufGroup( ); 
            
            $fields = array( ); 
            foreach ( $ufGroups as $id => $title ) { 
                $subset = self::getFields( $id, false, $action, false, $visibility );
                if ($considerSelector) {
                    // drop the fields not meant for the selector
                    foreach ($subset as $name => $field) {
                        if (!$field['in_selector']) unset($subset[$name]);
                    }
                }
                $fields = array_merge( $fields, $subset ); 
            } 
        }
        return $fields; 
    } 

    /**
     * get the title of the group which contributes the largest number of fields
     * to the registered entries
     *
     * @return string title of the registered group
     * @static
     * @access public
     */
    static function getRegisteredTitle( ) {
        $ufGroups =& CRM_Core_PseudoConstant::ufGroup( ); 

        $size  = -1;
        $title = null;
        foreach ( $ufGroups as $id => $value ) { 
            $subset = self::getFields( $id, true, $action ); 
            if ( count( $subset ) > $size ) {
                $size  = count( $subset );
                $title = $value;
            }
        }
        return $title;
    }

    /**
     * get all the fields that belong to the group with the named title
     *
     * @param int $id       the id of the UF group
     * @param int $register are we interested in registration fields
     * @param int $action   what action are we doing
     * @param int $match    are we interested in match fields
     * @param int $visibility visibility of fields we are interested in
     *
     * @return array the fields that belong to this title
     * @static
     * @access public
     */
    static function getFields( $id, $register = false, $action = null, $match = false, $visibility = null ) {
        //get location type
        $locationType = array( );
        $locationType =& CRM_Core_PseudoConstant::locationType();

        $group =& new CRM_Core_DAO_UFGroup( );

        $group->id = $id;
        if ( $group->find( true ) ) {

            $where = "WHERE uf_group_id = {$group->id} AND is_active = 1";
            if ( $visibility ) {
                $clause = array( );
                if ( $visibility & self::PUBLIC_VISIBILITY ) {
                    $clause[] = 'visibility = "Public User Pages"';
                }
                if ( $visibility & self::ADMIN_VISIBILITY ) {
                    $clause[] = 'visibility = "User and User Admin Only"';
                }
                if ( $visibility & self::LISTINGS_VISIBILITY ) {
                    $clause[] = 'visibility = "Public User Pages and Listings"';
                }
                if ( ! empty( $clause ) ) {
                    $where .= ' AND ( ' . implode( ' OR ' , $clause ) . ' ) ';
                }
            }

            $query =  "SELECT * FROM civicrm_uf_field $where ORDER BY weight, field_name"; 

            $field =& CRM_Core_DAO::executeQuery( $query );

            $fields = array( );
            $importableFields =& CRM_Contact_BAO_Contact::importableFields( );
            $importableFields['group']['title'] = ts('Group(s)');
            $importableFields['group']['where'] = null;
            $importableFields['tag'  ]['title'] = ts('Tag(s)');
            $importableFields['tag'  ]['where'] = null;
            
            while ( $field->fetch( ) ) {
                if ( ( $field->is_view && $action == CRM_Core_Action::VIEW ) || ! $field->is_view ) {
                    $name  = $title = $locType = $phoneType = '';
                    $name  = $field->field_name;
                    $title = $importableFields[$field->field_name]['title']; 
                    if ($field->location_type_id) {
                        $name    .= '-'.$field->location_type_id;
                        $locType  = ' (' . $locationType[$field->location_type_id] . ') ';
                    }
                    if ($field->phone_type) {
                        $name      .= '-'.$field->phone_type;
                        
                        if ($field->phone_type != ts('Phone')) { // this hack is to prevent Phone Phone (work)
                            $phoneType  = '-' . $field->phone_type;
                        }
                    }
                    
                    $title .= $phoneType . $locType;

                    $fields[$name] =
                        array('name'             => $name,
                              'groupTitle'       => $group->title,
                              'groupHelpPre'     => $group->help_pre,
                              'groupHelpPost'    => $group->help_post,
                              'title'            => $title,
                              'where'            => $importableFields[$field->field_name]['where'],
                              'attributes'       => CRM_Core_DAO::makeAttribute( $importableFields[$field->field_name] ),
                              'is_required'      => $field->is_required,
                              'is_view'          => $field->is_view,
                              'is_match'         => $field->is_match,
                              'help_post'        => $field->help_post,
                              'visibility'       => $field->visibility,
                              'in_selector'      => $field->in_selector,
                              'default'          => $field->default_value,
                              'rule'             => CRM_Utils_Array::value( 'rule', $importableFields[$field->field_name] ),
                              'options_per_line' => $importableFields[$field->field_name]['options_per_line'],
                              'location_type_id' => $field->location_type_id,
                              'phone_type'       => $field->phone_type,
                              'group_id'         => $group->id  
                              );
                }
            }

            return $fields;
        }
        return null;
    }

    /**
     * check the data validity
     *
     * @param int    $userID    the user id that we are actually editing
     * @param string $title     the title of the group we are interested in
     * @pram  boolean $register is this the registrtion form
     * @param int    $action  the action of the form
     *
     * @return boolean   true if form is valid
     * @static
     * @access public
     */
    static function isValid( $userID, $title, $register = false, $action = null ) {
        $session =& CRM_Core_Session::singleton( );

        if ( $register ) {
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Dynamic', ts('Dynamic Form Creator'), $action );
            $controller->set( 'gid'     , $group->id );
            $controller->set( 'id'      , $userID );
            $controller->set( 'register', 1 );
            $controller->process( );
            return $controller->validate( );
        } else {
            // make sure we have a valid group
            $group =& new CRM_Core_DAO_UFGroup( );
            
            $group->title     = $title;
            $group->domain_id = CRM_Core_Config::domainID( );
            
            if ( $group->find( true ) && $userID ) {
                require_once 'CRM/Core/Controller/Simple.php';
                $controller =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Dynamic', ts('Dynamic Form Creator'), $action );
                $controller->set( 'gid'     , $group->id );
                $controller->set( 'id'      , $userID );
                $controller->set( 'register', 0 );
                $controller->process( );
                return $controller->validate( );
            }
            return true;
        }
    }

    /**
     * get the html for the form that represents this particular group
     *
     * @param int     $userID   the user id that we are actually editing
     * @param string  $title    the title of the group we are interested in
     * @param int     $action   the action of the form
     * @param boolean $register is this the registration form
     * @param boolean $reset    should we reset the form?
     *
     * @return string       the html for the form
     * @static
     * @access public
     */
    static function getEditHTML( $userID, $title, $action = null, $register = false, $reset = false ) {
        $session =& CRM_Core_Session::singleton( );

        if ( $register ) {
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Dynamic', ts('Dynamic Form Creator'), $action );
            if ( $reset ) {
                // hack to make sure we do not process this form
                unset( $_POST['_qf_default'] );
                unset( $_REQUEST['_qf_default'] );
                $controller->reset( );
            }
            $controller->set( 'id'      , $userID );
            $controller->set( 'register', 1 );
            $controller->process( );
            $controller->setEmbedded( true );
            $controller->run( );

            $template =& CRM_Core_Smarty::singleton( );
            return trim( $template->fetch( 'CRM/Profile/Form/Dynamic.tpl' ) );
        } else {

            // make sure we have a valid group
            $group =& new CRM_Core_DAO_UFGroup( );
            
            $group->title     = $title;
            $group->domain_id = CRM_Core_Config::domainID( );

            if ( $group->find( true ) && $userID ) {
                require_once 'CRM/Core/Controller/Simple.php';
                $controller =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Dynamic', ts('Dynamic Form Creator'), $action );
                if ( $reset ) {
                    $controller->reset( );
                }
                $controller->set( 'gid'     , $group->id );
                $controller->set( 'id'      , $userID );
                $controller->set( 'register', 0 );
                $controller->process( );
                $controller->setEmbedded( true );
                $controller->run( );
                
                $template =& CRM_Core_Smarty::singleton( );
                return trim( $template->fetch( 'CRM/Profile/Form/Dynamic.tpl' ) );
            }
        }
        return '';
    }

    /**
     * Get the UF match clause 
     *
     * @param array   $params  the list of values to be used in the where clause
     * @param boolean $flatten should we flatten the input params
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     *
     * @return string the where clause to include in a sql query
     * @static
     * @access public
     */
    static function getMatchClause( $params, &$tables, $flatten = false ) {
        if ( $flatten && is_array( $params['location'] ) ) {
            $params['email'] = array();
            $params['phone'] = array();
            $params['im']    = array();
            
            foreach($params['location'] as $loc) {
                foreach (array('email', 'phone', 'im') as $key) {
                    if (is_array($loc[$key])) {
                        foreach ($loc[$key] as $value) {
                            if ( ! empty( $value[$key] ) ) {
                                $value[$key] = strtolower( $value[$key] );
                                $params[$key][] = 
                                    '"' . addslashes($value[$key]) . '"';
                            }
                        }
                    }
                }
            }
            
            foreach (array('email', 'phone', 'im') as $key) {
                if (count($params[$key]) == 0) {
                    unset($params[$key]);
                }
            }
            
            foreach ( array( 'street_address', 'supplemental_address_1', 'supplemental_address_2',
                             'state_province_id', 'postal_code', 'country_id' ) as $fld ) {
                if ( ! empty( $params['location'][1]['address'][$fld] ) ) {
                    $params[$fld] = $params['location'][1]['address'][$fld];
                }
            }
        }
        
        if ( ! self::$_matchFields ) {
            $ufGroups =& CRM_Core_PseudoConstant::ufGroup( );

            self::$_matchFields = array( );
            foreach ( $ufGroups as $id => $title ) {
                $subset = self::getFields( $id, false, CRM_Core_Action::VIEW, true );
                self::$_matchFields = array_merge( self::$_matchFields, $subset );
            }
        }

        if ( empty( self::$_matchFields ) ) {
            return null;
        }
        
        require_once 'CRM/Contact/BAO/Query.php';
        return CRM_Contact_BAO_Query::getWhereClause( $params, self::$_matchFields, $tables, true );
    }

    /**
     * searches for a contact in the db with similar attributes
     *
     * @param array $params the list of values to be used in the where clause
     * @param int    $id          the current contact id (hence excluded from matching)
     * @param boolean $flatten should we flatten the input params
     *
     * @return contact_id if found, null otherwise
     * @access public
     * @static
     */
    public static function findContact( &$params, $id = null, $flatten = false ) {
        $tables = array( );
        $clause = self::getWhereClause( $params, $tables );
        $emptyClause = 'civicrm_contact.domain_id = ' . CRM_Core_Config::domainID( );
        if ( ! $clause || trim( $clause ) === trim( $emptyClause ) ) {
            return null;
        }
        return CRM_Contact_BAO_Contact::matchContact( $clause, $tables, $id );
    }

    /**
     * Given a contact id and a field set, return the values from the db
     * for this contact
     *
     * @param int     $id       the contact id
     * @param array   $fields   the profile fields of interest
     * @param array   $values   the values for the above fields

     * @return void
     * @access public
     * @static
     */
    public static function getValues( $id, &$fields, &$values ) {
        $options = array( );

        // get the contact details (hier)
        $returnProperties =& CRM_Contact_BAO_Contact::makeHierReturnProperties( $fields );
        $params  = array( 'id' => $id );
        $query   =& new CRM_Contact_BAO_Query( $params, $returnProperties, $fields );
        $options =& $query->_options;

        $details = $query->searchQuery( );
        if ( ! $details->fetch( ) ) {
            return;
        }

        require_once 'CRM/Core/PseudoConstant.php'; 
        $locationTypes = CRM_Core_PseudoConstant::locationType( );

        //start of code to set the default values
        foreach ($fields as $name => $field ) {
            $index   = $field['title'];
            $params[$index] = $values[$index] = '';
            if ( $details->$name ) {
                //to handle custom data (checkbox) to be written
                // to handle gender / suffix / prefix
                if ( in_array( $name, array( 'gender', 'individual_prefix', 'individual_suffix' ) ) ) {
                    $params[$index] = $values[$index] = $details->$name;
                } else if ( in_array( $name, array( 'state_province', 'country' ) ) ) {
                    $values[$index] = $details->$name;
                    $idx = $name . '_id';
                    $params[$index] = $details->$idx;
                } else if ( substr($name, 0, 7) === 'do_not_' ) {  
                    if ($details->$name) {
                        $values[$index] = '[ x ]';
                    }
                } else if ( $name == 'group' ) {
                    $groups = CRM_Contact_BAO_GroupContact::getContactGroup( $id, 'Added' );
                    $title = array( );
                    $ids   = array( );
                    foreach ( $groups as $g ) {
                        if ( $g['visibility'] != 'User and User Admin Only' ) {
                            $title[] = $g['title'];
                            if ( $g['visibility'] == 'Public User Pages and Listings' ) {
                                $ids[] = $g['group_id'];
                            }
                        }
                    }
                    $values[$index] = implode( ', ', $title );
                    $params[$index] = implode( ',' , $ids   );
                } else if ( $name == 'tag' ) {
                    require_once 'CRM/Core/BAO/EntityTag.php';
                    $entityTags =& CRM_Core_BAO_EntityTag::getTag('civicrm_contact', $id );
                    $allTags    =& CRM_Core_PseudoConstant::tag();
                    $title = array( );
                    foreach ( $entityTags as $tagId ) {
                        $title[] = $allTags[$tagId];
                    }
                    $values[$index] = implode( ', ', $title );
                    $params[$index] = implode( ',' , $entityTags );
                } else {
                    require_once 'CRM/Core/BAO/CustomField.php';
                    if ( $cfID = CRM_Core_BAO_CustomField::getKeyID($name)) {
                        $params[$index] = $details->$name;
                        $values[$index] = CRM_Core_BAO_CustomField::getDisplayValue( $details->$name, $cfID, $options );
                    } else {
                        $values[$index] = $details->$name;
                    }
                }
            } else if ( strpos( $name, '-' ) !== false ) {
                list( $fieldName, $id ) = explode( '-', $name );
                $locationTypeName = CRM_Utils_Array::value( $id, $locationTypes );
                if ( ! $locationTypeName ) {
                    continue;
                }

                $detailName = "{$locationTypeName}-{$fieldName}";
                if ( in_array( $fieldName, array( 'phone', 'im', 'email' ) ) ) {
                    $detailName .= '-1';
                }
                
                if ( in_array( $fieldName, array( 'state_province', 'country' ) ) ) {
                    $values[$index] = $details->$detailName;
                    $idx = $detailName . '_id';
                    $params[$index] = $details->$idx;
                } else {
                    $values[$index] = $params[$index] = $details->$detailName;
                }
            }
        
            if ( $field['visibility'] == "Public User Pages and Listings" &&
                 CRM_Utils_System::checkPermission( 'access CiviCRM Profile Listings' ) ) {
                
                if ( CRM_Utils_Array::value( $index, $params ) === null ) {
                    $params[$index] = $values[$index];
                }
                if ( empty( $params[$index] ) ) {
                    continue;
                }
                $fieldName = $field['name'];
                $url = CRM_Utils_System::url( 'civicrm/profile',
                                              'reset=1&gid=' . $field['group_id'] .'&'. 
                                              urlencode( $fieldName ) .
                                              '=' .
                                              urlencode( $params[$index] ) );
                if ( ! empty( $values[$index] ) ) {
                    $values[$index] = '<a href="' . $url . '">' . $values[$index] . '</a>';
                }
            }
        }
    }
    
     /**
     * Delete the profile Group.
     *
     * @param int id profile Id 
     * 
     * @return void
     *
     * @access public
     * @static
     *
     */
    public static function del($id) { 
        
        //check wheter this group contains  any profile fields
        $profileField = & new CRM_Core_DAO_UFField();
        $profileField->uf_group_id = $id;
        $profileField->find();
        while($profileField->fetch()) {
            return -1;
        }
        
        //check wheter this group is used by any module(check uf join records)
        $ufJoin = & new CRM_Core_DAO_UFJoin();
        $ufJoin->uf_group_id = $id;
        $ufJoin->find();
        while($ufJoin->fetch()) {
            return 0;
        }
        
        //delete profile group
        $group = & new CRM_Core_DAO_UFGroup();
        $group->id = $id; 
        $group->delete();
        return 1;
    }

    /**
     * build a form for the given UF group
     *
     * @param int           $id        the group id
     * @param CRM_Core_Form $form      the form element
     * @param string        $name      the name that we should store the fields as
     * @param array         $allFields reference to the array where all the fields are stored
     *
     * @return void
     * @static
     * @access public
     */
    public static function buildQuickForm( $id, &$form, $name, &$allFields) {
        $fields =& CRM_Core_BAO_UFGroup::getFields( $id, false, $action );

        foreach ( $fields as $n => $fld ) {
            if ( ! array_key_exists( $n, $allFields ) ) {
                $allFields[$n] = $fld;
            }
        }

        $form->assign( $name, $fields );
        foreach ( $fields as $name => $field ) {
            $required = $field['is_required'];

            if ( substr($field['name'],0,14) === 'state_province' ) {
                $form->add('select', $name, $field['title'],
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince(), $required);
            } else if ( substr($field['name'],0,7) === 'country' ) {
                $form->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::country(), $required);
            } else if ( $field['name'] === 'birth_date' ) {  
                $form->add('date', $field['name'], $field['title'], CRM_Core_SelectValues::date('birth') );  
            } else if ( $field['name'] === 'gender' ) {  
                $genderOptions = array( );   
                $gender = CRM_Core_PseudoConstant::gender();   
                foreach ($gender as $key => $var) {   
                    $genderOptions[$key] = HTML_QuickForm::createElement('radio', null, ts('Gender'), $var, $key);   
                }   
                $form->addGroup($genderOptions, $field['name'], $field['title'] );  
            } else if ( $field['name'] === 'individual_prefix' ){
                $form->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::individualPrefix());
            } else if ( $field['name'] === 'individual_suffix' ){
                $form->add('select', $name, $field['title'], 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::individualSuffix());
            } else if ( $field['name'] === 'group' ) {
                require_once 'CRM/Contact/Form/GroupTag.php';
                CRM_Contact_Form_GroupTag::buildGroupTagBlock($form, 0,
                                                              CRM_Contact_Form_GroupTag::GROUP);
            } else if ( $field['name'] === 'tag' ) {
                require_once 'CRM/Contact/Form/GroupTag.php';
                CRM_Contact_Form_GroupTag::buildGroupTagBlock($form, 0,
                                                              CRM_Contact_Form_GroupTag::TAG );
            } else if (substr($field['name'], 0, 6) === 'custom') {
                $customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name']);
                CRM_Core_BAO_CustomField::addQuickFormElement($form, $name, $customFieldID, $inactiveNeeded, false);
                if ($required) {
                    $form->addRule($name, ts('%1 is a required field.', array(1 => $field['title'])) , 'required');
                }
            } else {
                $form->add('text', $name, $field['title'], $field['attributes'], $required );
            }

            if ( $field['rule'] ) {
                $form->addRule( $name, ts( 'Please enter a valid %1', array( 1 => $field['title'] ) ), $field['rule'] );
            }
        }
    }

    /**
     * function to add the UF Group
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids) {
        
        $params['is_active'] = CRM_Utils_Array::value('is_active', $params, false);

        $ufGroup             =& new CRM_Core_DAO_UFGroup();
        $ufGroup->domain_id  = CRM_Core_Config::domainID( );
        $ufGroup->copyValues($params); 
                
        $ufGroup->id = CRM_Utils_Array::value( 'ufgroup', $ids );;
        $ufGroup->save();
        return $ufGroup;
    }    

    /**
     * function to get match clasue for dupe checking
     *
     * @param array   $params  the list of values to be used in the where clause
     * @param  array $tables (reference ) add the tables that are needed for the select clause
     *
     * @return string the where clause to include in a sql query
     * @static
     * @access public
     *
     */
    public function getWhereClause($params ,&$tables)
    {
        require_once 'CRM/Core/DAO/DupeMatch.php';

        if(is_array($params)) {
            if (is_array($params['location'])) {
                $params['email'] = array();
                $params['phone'] = array();
                $params['im']    = array();
            
                foreach($params['location'] as $loc) {
                    foreach (array('email', 'phone', 'im') as $key) {
                        if (is_array($loc[$key])) {
                            foreach ($loc[$key] as $value) {
                                if ( ! empty( $value[$key] ) ) {
                                    $value[$key] = strtolower( $value[$key] );
                                    $params[$key][] = 
                                        '"' . addslashes($value[$key]) . '"';
                                }
                            }
                        }
                    }
                }
            }
            foreach (array('email', 'phone', 'im') as $key) {
                if (count($params[$key]) == 0) {
                    unset($params[$key]);
                }
            }
            
            foreach ( array( 'street_address', 'supplemental_address_1', 'supplemental_address_2',
                             'state_province_id', 'postal_code', 'country_id' ) as $fld ) {
                if ( ! empty( $params['location'][1]['address'][$fld] ) ) {
                    $params[$fld] = $params['location'][1]['address'][$fld];
                }
            }
        }
        $importableFields =  CRM_Contact_BAO_Contact::importableFields( );
        
        $dupeMatchDAO = & new CRM_Core_DAO_DupeMatch();
        $dupeMatchDAO->find();
        while($dupeMatchDAO->fetch()) {
            $rule = explode('AND',$dupeMatchDAO->rule);
            foreach ( $rule as $name ) {
                $name  = trim( $name );
                $fields[$name] = array('name'             => $name,
                                       'title'            => $importableFields[$name]['title'],
                                       'where'            => $importableFields[$name]['where'],
                                       );
            }
        }

        require_once 'CRM/Contact/BAO/Query.php';
        return CRM_Contact_BAO_Query::getWhereClause( $params, $fields, $tables, true );
    }
    
    /**
     * Function to make uf join entries for an uf group
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param int   $ufGroupId    ufgroup id
     *
     * @return none
     * @access public
     * @static
     */
    static function createUFJoin( &$params, $ufGroupId ) 
    {
        $groupTypes = $params['uf_group_type'];
        
        // get ufjoin records for uf group
        $ufGroupRecord =& CRM_Core_BAO_UFGroup::getUFJoinRecord($ufGroupId);
        
        // get the list of all ufgroup types
        $allUFGroupType =& CRM_Core_SelectValues::ufGroupTypes( );
        
        // this fix is done to prevent warning generated by array_key_exits incase of empty array is given as input
        if (!is_array($groupTypes)) {
            $groupTypes = array( );
        }
        
        // this fix is done to prevent warning generated by array_key_exits incase of empty array is given as input
        if (!is_array($ufGroupRecord)) {
            $ufGroupRecord = array();
        }
        
        // check which values has to be inserted/deleted for contact
        foreach ($allUFGroupType as $key => $value) {
            $joinParams = array( );
            $joinParams['uf_group_id'] = $ufGroupId;
            $joinParams['module'     ] = $key;
            //$joinParams['weight'     ] = $params['weight'];
            if (array_key_exists($key, $groupTypes) && !in_array($key, $ufGroupRecord )) {
                // insert a new record
                CRM_Core_BAO_UFGroup::addUFJoin($joinParams);
            } else if (!array_key_exists($key, $groupTypes) && in_array($key, $ufGroupRecord) ) {
                // delete a record for existing ufgroup
                CRM_Core_BAO_UFGroup::delUFJoin($joinParams);
            } 
        }

        //update the weight for remaining group
        CRM_Core_BAO_UFGroup::updateWeight($params['weight'], $ufGroupId);
    }

    /**
     * Function to get the UF Join records for an ufgroup id
     *
     * @params int $ufGroupId uf group id
     * @params int $displayName if set return display name in array
     * @params int $status if set return module other than default modules (User Account/User registration/Profile)
     *
     * @return array $ufGroupJoinRecords 
     *
     * @access public
     * @static
     */
    public static function getUFJoinRecord( $ufGroupId = null, $displayName = null, $status = null ) 
    {
        if ($displayName) { 
            $UFGroupType = array( );
            $UFGroupType = CRM_Core_SelectValues::ufGroupTypes( );
        }
        
        $ufJoin = array();
        require_once 'CRM/Core/DAO/UFJoin.php';
        $dao =& new CRM_Core_DAO_UFJoin( );
        
        if ($ufGroupId) {
            $dao->uf_group_id = $ufGroupId;
        }
        
        $dao->find( );
        
        while ($dao->fetch( )) {
            if (!$displayName) { 
                $ufJoin[$dao->id] = $dao->module;
            } else {
                if ( $UFGroupType[$dao->module] ) {
                    if (!$status) { //skip the default modules
                        $ufJoin[$dao->id] = $UFGroupType[$dao->module];
                    }
                } else {
                    $ufJoin[$dao->id] = $dao->module;
                }
            }
        }
        return $ufJoin;
    }

    
    /**
     * Function takes an associative array and creates a ufjoin record for ufgroup
     *
     * @param array $params (reference) an assoc array of name/value pairs
     *
     * @return object CRM_Core_BAO_UFJoin object
     * @access public
     * @static
     */
    static function addUFJoin( &$params ) 
    {
        require_once 'CRM/Core/DAO/UFJoin.php';
        
        $ufJoin =& new CRM_Core_DAO_UFJoin( );
        $ufJoin->copyValues( $params );
        $ufJoin->save( );
        return $ufJoin;
    }

    /**
     * Function to delete the uf join record for an uf group 
     *
     * @param array  $params (reference) an assoc array of name/value pairs
     *
     * @access public
     * @static
     */
    static function delUFJoin( &$params ) 
    {
        require_once 'CRM/Core/DAO/UFJoin.php';
        $ufJoin =& new CRM_Core_DAO_UFJoin( );
        $ufJoin->copyValues( $params );
        $ufJoin->delete( );
    }

    /**
     * Function to get the weight for ufjoin record
     *
     * @param int $ufGroupId  if $ufGroupId get update weight or add weight
     *
     * @return int $weight
     * @static
     */
    static function getWeight ( $ufGroupId = null ) 
    {
        //calculate the weight
        require_once 'CRM/Core/DAO.php';
        $dao =& new CRM_Core_DAO( );
        if ( !$ufGroupId ) {
            $queryString = "SELECT ( MAX(civicrm_uf_join.weight)+1) as new_weight
                            FROM civicrm_uf_join 
                            WHERE module='User Registration' OR module='User Account' OR module='Profile'";
        } else {
            $queryString = "SELECT MAX(civicrm_uf_join.weight) as new_weight
                            FROM civicrm_uf_join
                            WHERE civicrm_uf_join.uf_group_id = " . CRM_Utils_Type::escape($ufGroupId, 'Integer'); 

            /*  ." 
                              AND (civicrm_uf_join.module='User Registration'
                                    OR civicrm_uf_join.module='User Account'
                                    OR civicrm_uf_join.module='Profile')";*/
        }
        
        $dao->query($queryString);
        $dao->fetch();
        return ($dao->new_weight) ? $dao->new_weight : 1; 
    }


    /**
     * Function to get the uf group for a module
     *
     * @param string $moduleName module name 
     * $param int    $count no to increment the weight
     *
     * @return array $ufGroups array of ufgroups for a module
     * @access public
     * @static
     */
    public static function getModuleUFGroup( $moduleName = null, $count = 0) 
    {
        require_once 'CRM/Core/DAO.php';

        $dao =& new CRM_Core_DAO( );
        $queryString = 'SELECT civicrm_uf_group.id as id, civicrm_uf_group.title as title,
                               civicrm_uf_join.weight as weight, civicrm_uf_group.is_active as is_active
                        FROM civicrm_uf_group
                        LEFT JOIN civicrm_uf_join on ( civicrm_uf_group.id = civicrm_uf_join.uf_group_id )
                        WHERE civicrm_uf_group.domain_id = ' . CRM_Core_Config::domainID( ); 
        if ($moduleName) {
            $queryString .= ' AND civicrm_uf_group.is_active = 1 
                              AND civicrm_uf_join.module ="' . CRM_Utils_Type::escape($moduleName, 'String') .'" ';
        }
        
        $queryString .= ' ORDER BY civicrm_uf_join.weight, civicrm_uf_group.title';
        $dao->query($queryString);

        $ufGroups = array( );
        while ($dao->fetch( )) {
            $ufGroups[$dao->id]['name'     ] = $dao->title;
            $ufGroups[$dao->id]['title'    ] = $dao->title;
            $ufGroups[$dao->id]['weight'   ] = $dao->weight + $count;
            $ufGroups[$dao->id]['is_active'] = $dao->is_active;
        }

        return $ufGroups;
    }
    
    /**
     * Function to update the weight for a UFGroup
     * 
     * @param int $weight weight for a UFGroup
     * @param int $ufGroupId uf Group Id
     *
     * @static
     */
    static function updateWeight($weight, $ufGroupId) 
    {

        //get the current uf group records in uf join table
        $ufJoin =& new CRM_Core_DAO_UFJoin();
        $ufJoin->uf_group_id = $ufGroupId;

        $ufJoinRecords = array();
        $ufJoin->find();
        while ($ufJoin->fetch()) {
            $ufJoinRecords[] = $ufJoin->id;
            $oldWeight       = $ufJoin->weight;
        }
        
        $check = 0;
        if ( $weight != $oldWeight )  {
            $check++;
            // get the groups whose weight is less than new/updated group
            $daoObj =& new CRM_Core_DAO();
            $query = "SELECT id
                      FROM civicrm_uf_join
                      WHERE (module = 'User Registration' OR module='User Account' OR module='Profile')

                        AND weight = ". CRM_Utils_Type::escape($weight, 'Integer');
            //AND uf_group_id <> ". CRM_Utils_Type::escape($ufGroupId, 'Integer') ."

            $daoObj->query($query);
            while ($daoObj->fetch()) {
                $check = 0;
            }
        }

        if ( !$check ) {
            // get the groups whose weight is less than new/updated group
            $dao =& new CRM_Core_DAO();
            $query = "SELECT id
                      FROM civicrm_uf_join
                      WHERE (module = 'User Registration' OR module='User Account' OR module='Profile')

                        AND weight >= ". CRM_Utils_Type::escape($weight, 'Integer');

            //AND uf_group_id <> ". CRM_Utils_Type::escape($ufGroupId, 'Integer'). "
        
            $dao->query($query);
            
            $fieldIds = array();                
            while ($dao->fetch()) {
                $fieldIds[] = $dao->id;                
            }                
            
            //update the record with weight + 1
            if ( !empty($fieldIds) ) {
                $ufDAO =& new CRM_Core_DAO();
                $updateSql = "UPDATE civicrm_uf_join SET weight = weight + 1 WHERE id IN ( ".implode(",", $fieldIds)." ) ";
                $ufDAO->query($updateSql);
            }
        }
        
        //set the weight for the current uf group
        if ( !empty($ufJoinRecords) ) {
            $ufJoinDAO =& new CRM_Core_DAO();
            $queryString = "UPDATE civicrm_uf_join SET weight = ". CRM_Utils_Type::escape($weight, 'Integer')."
                          WHERE id IN ( ".implode(",", $ufJoinRecords)." ) ";
            $ufJoinDAO->query($queryString);
        }
    }
}

?>
