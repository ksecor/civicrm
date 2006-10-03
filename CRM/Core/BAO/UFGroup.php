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
     * @param array $params      (reference) an assoc array of name/value pairs
     * @param array $defaults    (reference) an assoc array to hold the flattened values
     *
     * @return object   CRM_Core_DAO_UFGroup object
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
     * @param int      $id           id of the database record
     * @param boolean  $is_active    value we want to set the is_active field
     *
     * @return Object             CRM_Core_DAO_UFGroup object on success, null otherwise
     * @access public
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

        require_once "CRM/Core/BAO/UFField.php";
        foreach ( $ufGroups as $id => $title ) {
            if ( CRM_Core_BAO_UFField::checkProfileType($id) ) { // to skip mix profiles
                continue;
            }
            
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
     * @param      $ufGroupId
     * @param      $searchable
     * 
     * @return array     the fields that are listings related
     * @static 
     * @access public 
     */ 
    static function getListingFields( $action, $visibility, $considerSelector = false, $ufGroupId = null, $searchable = null ) {
        if ($ufGroupId) {
            $subset = self::getFields( $ufGroupId, false, $action, $visibility, $searchable);
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
                $subset = self::getFields( $id, false, $action, $visibility ,$searchable);
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
     * get all the fields that belong to the group with the name title
     *
     * @param int      $id           the id of the UF group
     * @param int      $register     are we interested in registration fields
     * @param int      $action       what action are we doing
     * @param int      $visibility   visibility of fields we are interested in
     * @param          $searchable
     * @param boolean  $showall
     *
     *
     * @return array   the fields that belong to this title
     * @static
     * @access public
     */
    static function getFields( $id, $register = false, $action = null,
                               $visibility = null , $searchable = null, $showAll= false ) {
        //get location type
        $locationType = array( );
        $locationType =& CRM_Core_PseudoConstant::locationType();

        $fields = array( );

        $customFields = CRM_Core_BAO_CustomField::getFieldsForImport();

        $group =& new CRM_Core_DAO_UFGroup( );
        $group->id = $id;
        $group->is_active = 1;
        if ( $group->find( true ) ) {
            if( $searchable ) {
                $where = "WHERE uf_group_id = {$group->id} AND is_searchable = 1"; 
            } else {
                $where = "WHERE uf_group_id = {$group->id}";
            }

            if ( !$showAll) {
                $where .= " AND is_active = 1";
            } else {
                $where .= " AND 1";
            }
            
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

            $field =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            
            if ( !$showAll ) {
                $importableFields =& CRM_Contact_BAO_Contact::importableFields( "All");
            } else {
                $importableFields =& CRM_Contact_BAO_Contact::importableFields("All", false, true );
            }

            $importableFields['group']['title'] = ts('Group(s)');
            $importableFields['group']['where'] = null;
            $importableFields['tag'  ]['title'] = ts('Tag(s)');
            $importableFields['tag'  ]['where'] = null;

            $specialFields = array ('street_address','supplemental_address_1', 'supplemental_address_2', 'city', 'postal_code', 'postal_code_suffix', 'geo_code_1', 'geo_code_2', 'state_province', 'country', 'phone', 'email', 'im' );

            while ( $field->fetch( ) ) {
                if ( $searchable || 
                     ( $field->is_view && $action == CRM_Core_Action::VIEW ) ||
                     ! $field->is_view ) {
                    $name  = $title = $locType = $phoneType = '';
                    $name  = $field->field_name;
                    $title = $field->label;

                    if ($field->location_type_id) {
                        $name    .= "-{$field->location_type_id}";
                        $locType  = " ( {$locationType[$field->location_type_id]} ) ";
                    } else {                                                           
                        if ( in_array($field->field_name, $specialFields))  {
                            //$name    .= '-Primary';        //Fix for CRM-1155
                            require_once 'CRM/Core/BAO/LocationType.php';
                            $defaultLocation = CRM_Core_BAO_LocationType::getDefault();
                            $name    .= '-' . $defaultLocation->id;
                            $locType  = ' ( Primary ) ';
                        }
                    }

                    if ($field->phone_type) {
                        $name      .= "-{$field->phone_type}";
                        if ( $field->phone_type != 'Phone' ) { // this hack is to prevent Phone Phone (work)
                            $phoneType  = "-{$field->phone_type}";
                        }
                    }

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
                              'group_id'         => $group->id,
                              'add_to_group_id'  => $group->add_to_group_id,
                              'collapse_display' => $group->collapse_display,
                              'add_captcha'      => $group->add_captcha
                              );
                    //adding custom field property 
                    if ( substr($name, 0, 6) == 'custom' ) {
                        $fields[$name]['is_search_range'] = $customFields[$name]['is_search_range'];
                    }
                }
            }
        } else {
            CRM_Core_Error::fatal( ts( 'The requested profile page is currently inactive or does not exist. Please contact the site administrator if you need assistance.' ) );
        }
        return $fields;
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
     * @param int     $userID    the user id that we are actually editing
     * @param string  $title     the title of the group we are interested in
     * @param int     $action    the action of the form
     * @param boolean $register  is this the registration form
     * @param boolean $reset     should we reset the form?
     * @param int     $profileID do we have the profile ID?
     *
     * @return string       the html for the form on success, otherwise empty string
     * @static
     * @access public
     */
    static function getEditHTML( $userID,
                                 $title,
                                 $action = null,
                                 $register = false,
                                 $reset = false,
                                 $profileID = null,
                                 $doNotProcess  = false ) {
        $session =& CRM_Core_Session::singleton( );

        if ( $register ) {
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Dynamic', ts('Dynamic Form Creator'), $action );
            if ( $reset || $doNotProcess ) {
                // hack to make sure we do not process this form
                $oldQFDefault = $_POST['_qf_default'];
                unset( $_POST['_qf_default'] );
                unset( $_REQUEST['_qf_default'] );
                if ( $reset ) {
                    $controller->reset( );
                }
            }
            $controller->set( 'id'      , $userID );
            $controller->set( 'register', 1 );
            $controller->process( );
            $controller->setEmbedded( true );
            $controller->run( );

            // we are done processing so restore the POST/REQUEST vars
            if ( $reset || $doNotProcess ) {
                $_POST['_qf_default'] = $_REQUEST['_qf_default'] = $oldQFDefault;
            }

            $template =& CRM_Core_Smarty::singleton( );
            return trim( $template->fetch( 'CRM/Profile/Form/Dynamic.tpl' ) );
        } else {
            if ( ! $profileID ) {
                // make sure we have a valid group
                $group =& new CRM_Core_DAO_UFGroup( );
                
                $group->title     = $title;
                $group->domain_id = CRM_Core_Config::domainID( );

                if ( $group->find( true ) ) {
                    $profileID = $group->id;
                }
            }

            if ( $profileID ) {
                require_once 'CRM/Core/Controller/Simple.php';
                $controller =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Dynamic', ts('Dynamic Form Creator'), $action );
                if ( $reset ) {
                    $controller->reset( );
                }
                $controller->set( 'gid'     , $profileID );
                $controller->set( 'id'      , $userID );
                $controller->set( 'register', 0 );
                $controller->process( );
                $controller->setEmbedded( true );
                $controller->run( );
                
                $template =& CRM_Core_Smarty::singleton( );
                return trim( $template->fetch( 'CRM/Profile/Form/Dynamic.tpl' ) );
            } else {
                // fix for CRM 701
                require_once 'CRM/Contact/BAO/Contact.php';
                
                $userEmail = CRM_Contact_BAO_Contact::getEmailDetails( $userID );
                
                // if post not empty then only proceed
                if ( ! empty ( $_POST ) ) {
                    if ( CRM_Utils_Rule::email( $_POST['edit']['mail'] ) && ( $_POST['edit']['mail']  != $userEmail[1] ) ) {
                        require_once 'CRM/Core/BAO/UFMatch.php';
                        CRM_Core_BAO_UFMatch::updateContactEmail( $userID, $_POST['edit']['mail'] );
                    }
                }
            }
        }
        return '';
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
        require_once 'CRM/Contact/Form/Search.php';
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
    public static function getValues( $cid, &$fields, &$values ) {
        $options = array( );

        $studentFields = array( );
        if ( CRM_Core_Permission::access( 'Quest' ) ) {
            //student fields ( check box ) 
            require_once 'CRM/Quest/BAO/Student.php';
            $studentFields = CRM_Quest_BAO_Student::$multipleSelectFields;
        }

        // get the contact details (hier)
        $returnProperties =& CRM_Contact_BAO_Contact::makeHierReturnProperties( $fields );

        $params  = array( array( 'contact_id', '=', $cid, 0, 0 ) );
        $query   =& new CRM_Contact_BAO_Query( $params, $returnProperties, $fields );
        $options =& $query->_options;

        $details = $query->searchQuery( );
        if ( ! $details->fetch( ) ) {
            return;
        }

        $config =& CRM_Core_Config::singleton( );
        
        require_once 'CRM/Core/PseudoConstant.php'; 
        $locationTypes = CRM_Core_PseudoConstant::locationType( );
        //start of code to set the default values
        foreach ($fields as $name => $field ) {
            $index   = $field['title'];
            $params[$index] = $values[$index] = '';

            if ( isset($details->$name) || $name == 'group' || $name == 'tag') {//hack for CRM-665
                // to handle gender / suffix / prefix
                if ( in_array( $name, array( 'gender', 'individual_prefix', 'individual_suffix' ) ) ) {
                    $values[$index] = $details->$name;
                    $name = $name . '_id';
                    $params[$index] = $details->$name ;
                } else if ( in_array( $name, array( 'state_province', 'country' ) ) ) {
                    $values[$index] = $details->$name;
                    $idx = $name . '_id';
                    $params[$index] = $details->$idx;
                } else if ( $name == 'group' ) {
                    $groups = CRM_Contact_BAO_GroupContact::getContactGroup( $cid, 'Added', null, false, true );
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
                    $entityTags =& CRM_Core_BAO_EntityTag::getTag('civicrm_contact', $cid );
                    $allTags    =& CRM_Core_PseudoConstant::tag();
                    $title = array( );
                    foreach ( $entityTags as $tagId ) {
                        $title[] = $allTags[$tagId];
                    }
                    $values[$index] = implode( ', ', $title );
                    $params[$index] = implode( ',' , $entityTags );
                } else if (array_key_exists( $name ,$studentFields ) ) {
                    require_once 'CRM/Core/OptionGroup.php';
                    $paramsNew = array($name => $details->$name );
                    if ( $name == 'test_tutoring') {
                        $names = array( $name => array('newName' => $index ,'groupName' => 'test' ));
                    } else if (substr($name, 0, 4) == 'cmr_') { //for  readers group
                        $names = array( $name => array('newName' => $index, 'groupName' => substr($name, 0, -3) ));
                    } else {
                        $names = array( $name => array('newName' => $index, 'groupName' => $name ));
                    }
                    CRM_Core_OptionGroup::lookupValues( $paramsNew, $names, false );
                    $values[$index] = $paramsNew[$index];
                    $params[$index] = $paramsNew[$name];
                } else {
                    $processed = false;
                    if ( CRM_Core_Permission::access( 'Quest' ) ) {
                        require_once 'CRM/Quest/BAO/Student.php';
                        $processed = CRM_Quest_BAO_Student::buildStudentForm( $this, $field );
                    }
                    if ( ! $processed ) {
                        if ( substr($name, 0, 7) === 'do_not_' or substr($name, 0, 3) === 'is_' ) {  
                            if ($details->$name) {
                                $values[$index] = '[ x ]';
                            }
                        } else {
                            require_once 'CRM/Core/BAO/CustomField.php';
                            if ( $cfID = CRM_Core_BAO_CustomField::getKeyID($name)) {
                                
                                $customOptionValueId = "custom_value_{$cfID}_id";

                                $fileId = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomValue', $details->$customOptionValueId, 'file_id', 'id' );
                                if ($fileId) {
                                    $fileType = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_File', $fileId, 'mime_type', 'id' );
                                    if ( $fileType == "image/jpeg" || $fileType =="image/gif" || $fileType =="image/png" ) { 
                                        $url = CRM_Utils_System::url( 'civicrm/file', "reset=1&id=$fileId&eid=$cid" );
                                        $params[$index] = $values[$index] = "<a href='javascript:popUp(\"$url\");'><img src=\"$url\" width=100 height=100/></a>";
                                    } else { // for non image files
                                        $url = CRM_Utils_System::url( 'civicrm/file', "reset=1&id=$fileId&eid=$cid" );
                                        $params[$index] = $values[$index] = "<a href=$url>" . $details->$name ."</a>";
                                    }                                    
                                } else {
                                    $params[$index] = $details->$name;
                                    $values[$index] = CRM_Core_BAO_CustomField::getDisplayValue( $details->$name, $cfID, $options );
                                }
                            } else if ( $name == 'home_URL' &&
                                        ! empty( $details->$name ) ) {
                                $url = CRM_Utils_System::fixURL( $details->$name );
                                $values[$index] = "<a href=\"$url\">{$details->$name}</a>";
                            } else {
                                $values[$index] = $details->$name;
                            }
                        }
                    }
                }
            } else if ( strpos( $name, '-' ) !== false ) {
                list( $fieldName, $id, $type ) = explode( '-', $name );
                
                if ($id == 'Primary') {
                    $id = CRM_Contact_BAO_Contact::getPrimaryLocationType( $cid ); 
                }

                $locationTypeName = CRM_Utils_Array::value( $id, $locationTypes );
                if ( ! $locationTypeName ) {
                    continue;
                }

                $detailName = "{$locationTypeName}-{$fieldName}";
                if ( in_array( $fieldName, array( 'phone', 'im', 'email' ) ) ) {
                    if ( $type ) {
                        $detailName .= "-{$type}";
                    } else {
                        $detailName .= '-1';
                    }
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
                 CRM_Core_Permission::check( 'profile listings and forms' ) ) {
                
                if ( CRM_Utils_System::isNull( $params[$index] ) ) {
                    $params[$index] = $values[$index];
                }
                if ( !isset( $params[$index] ) ) {
		    continue;
                }
                $fieldName = $field['name'];
                $url = CRM_Utils_System::url( 'civicrm/profile',
                                              'reset=1&force=1&gid=' . $field['group_id'] .'&'. 
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
     * @param int  $id    profile Id 
     * 
     * @return boolean
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
        $sql = "SELECT id
                FROM civicrm_uf_join
                WHERE civicrm_uf_join.module 
                   NOT IN ( 'User Registration','User Account','Profile','Search Profile' )
                   AND civicrm_uf_join.uf_group_id =" . CRM_Utils_Type::escape($id, 'Integer'); 
        
        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );
        if ( $dao->fetch( ) ) { 
            return 0;
        }
        
        //delete records from uf join table
        require_once 'CRM/Core/DAO/UFJoin.php';
        $ufJoin = & new CRM_Core_DAO_UFJoin();
        $ufJoin->uf_group_id = $id; 
        $ufJoin->delete();

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
        $params['is_active'              ] = CRM_Utils_Array::value('is_active', $params, false);
        $params['add_captcha'            ] = CRM_Utils_Array::value('add_captcha', $params, false);
        $params['is_map'                 ] = CRM_Utils_Array::value('is_map', $params, false);
        $params['collapse_display'       ] = CRM_Utils_Array::value('collapse_display', $params, false);
        $params['limit_listings_group_id'] = CRM_Utils_Array::value('group', $params);
        $params['add_to_group_id'        ] = CRM_Utils_Array::value('add_contact_to_group', $params);
    
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
                $params['email'] = null;
                $params['phone'] = null;
                $params['im']    = null;
            
                foreach($params['location'] as $locIdx => $loc) {
                    foreach (array('email', 'phone', 'im') as $key) {
                        if (is_array($loc[$key])) {
                            foreach ($loc[$key] as $keyIdx => $value) {
                                if ( ! empty( $value[$key] ) && ! $params[$key] ) {
                                    $params[$key] = $value[$key];
                                    break;
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
            unset( $params['location'] );
            
            if (is_array($params['custom'])) {
                foreach ( $params['custom'] as $key => $value ) {
                    $params['custom_'. $value['custom_field_id'] ] = $value['value'];
                }
            }
            unset( $params['custom'] );
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
        if ( $params["contact_type"] ) {
            $fields["contact_type"] = array("name"  => "contact_type" ,
                                            "title" => "Contact Type",
                                            "where" => "civicrm_contact.contact_type"
                                            );
        }
        //this is the fix to ignore the groups/ tags for dupe checking CRM-664, since we never use them for dupe checking
        unset( $params['group'] );
        unset( $params['tag']   );
        
        // also eliminate all the params that are not present in fields
        foreach ( $params as $name => $value ) {
            if ( ! array_key_exists( $name, $fields ) ) {
                unset( $params[$name] );
            }
        }
        
        
        $params =& CRM_Contact_Form_Search::convertFormValues( $params );
        $whereTables = array( );

        return CRM_Contact_BAO_Query::getWhereClause( $params, $fields, $tables, $whereTables, true );
    }
    
    /**
     * Function to make uf join entries for an uf group
     *
     * @param array $params       (reference) an assoc array of name/value pairs
     * @param int   $ufGroupId    ufgroup id
     *
     * @return void
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
     * @param array  $params    (reference) an assoc array of name/value pairs
     *
     * @return void
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
     * @param int $ufGroupId     if $ufGroupId get update weight or add weight
     *
     * @return int   weight of the UFGroup
     * @access public
     * @static
     */
    static function getWeight ( $ufGroupId = null ) 
    {
        //calculate the weight
        $p = array( );
        if ( !$ufGroupId ) {
            $queryString = "SELECT ( MAX(civicrm_uf_join.weight)+1) as new_weight
                            FROM civicrm_uf_join 
                            WHERE module = 'User Registration' OR module = 'User Account' OR module = 'Profile'";
        } else {
            $queryString = "SELECT MAX(civicrm_uf_join.weight) as new_weight
                            FROM civicrm_uf_join
                            WHERE civicrm_uf_join.uf_group_id = %1";
            $p[1] = array( $ufGroupId, 'Integer' );
        }
        
        $dao =& CRM_Core_DAO::executeQuery( $queryString, $p );
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
                        WHERE civicrm_uf_group.domain_id = %1';
        $p = array( 1 => array( CRM_Core_Config::domainID( ), 'Integer' ) );
        if ($moduleName) {
            $queryString .= ' AND civicrm_uf_group.is_active = 1 
                              AND civicrm_uf_join.module = %2';
            $p[2] = array( $moduleName, 'String' );
        }
        
        $queryString .= ' ORDER BY civicrm_uf_join.weight, civicrm_uf_group.title';
        $dao =& CRM_Core_DAO::executeQuery($queryString, $p);

        $ufGroups = array( );
        while ($dao->fetch( )) {
            $process = false;
            if ( ! $moduleName ) {
                $process = true;
            } else {
                // Don't add ufgroup to array if moduleName is User Account or Profile, UNLESS the ufGroup's fields'
                // contact type matches the contact type of the logged in user.
                if ( $moduleName == 'User Account' || $moduleName == 'Profile' ) {
                    $process = self::filterUFGroups($dao->id);
                } else {
                    $process = true;
                }
            }
            
            if ( $process ) {
                $ufGroups[$dao->id]['name'     ] = $dao->title;
                $ufGroups[$dao->id]['title'    ] = $dao->title;
                $ufGroups[$dao->id]['weight'   ] = $dao->weight + $count;
                $ufGroups[$dao->id]['is_active'] = $dao->is_active;
            }
        }

        return $ufGroups;
    }
    
    /**
     * Function to update the weight for a UFGroup
     * 
     * @param int $weight      weight for a UFGroup
     * @param int $ufGroupId   uf Group Id
     * 
     * @return void
     * @access public
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
            $query = "SELECT id
                      FROM civicrm_uf_join
                      WHERE (module = 'User Registration' OR module='User Account' OR module='Profile')
                        AND weight = %1";
            $p =array( 1 => array( $weight, 'Integer' ) );

            $daoObj =& CRM_Core_DAO::executeQuery($query, $p);
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
                        AND weight >= %1";
            $p =array( 1 => array( $weight, 'Integer' ) );

            $dao =& CRM_Core_DAO::executeQuery($query, $p); 
            
            $fieldIds = array();                
            while ($dao->fetch()) {
                $fieldIds[] = $dao->id;                
            }                
            
            //update the record with weight + 1
            if ( !empty($fieldIds) ) {
                $sql = "UPDATE civicrm_uf_join SET weight = weight + 1 WHERE id IN ( ".implode(",", $fieldIds)." ) ";
                CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
            }
        }
        
        //set the weight for the current uf group
        if ( !empty($ufJoinRecords) ) {
            $query = "UPDATE civicrm_uf_join SET weight = %1
                       WHERE id IN ( ".implode(",", $ufJoinRecords)." ) ";
            $p =array( 1 => array( $weight, 'Integer' ) ); 
            CRM_Core_DAO::executeQuery($query, $p);
        }
    }

    /**
     * Function to filter ufgroups based on logged in user contact type
     *
     * @params int $ufGroupId uf group id (profile id)
     *
     * @return boolean true or false
     * @static
     * @access public
     */
    static function filterUFGroups ($ufGroupId) 
    {
        global $user;

        require_once "CRM/Core/BAO/UFMatch.php";
        $contactId = CRM_Core_BAO_UFMatch::getContactId($user->uid);

        if ($contactId) {
            //get the contact type
            require_once "CRM/Contact/BAO/Contact.php";
            $contactType = CRM_Contact_BAO_Contact::getContactType($contactId);
            
            //match if exixting contact type is same as profile contact type
            require_once "CRM/Core/BAO/UFField.php";
            $profileType = CRM_Core_BAO_UFField::getProfileType($ufGroupId);

            if ($contactType == $profileType) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Function to build profile form
     *
     * @params object  $form       form object
     * @params array   $field      array field properties
     * @params int     $mode       profile mode
     * @params int     $contactId  contact id
     *
     * @return null
     * @static
     * @access public
     */
    static function buildProfile( &$form, &$field, $mode, $contactId = null ) 
    {
        $fieldName  = $field['name'];
        $title      = $field['title'];
        $attributes = $field['attributes'];
        $rule       = $field['rule'];
        $view       = $field['is_view'];

        $required = ( $mode == CRM_Profile_Form::MODE_SEARCH ) ? false : $field['is_required'];
        $search   = ( $mode == CRM_Profile_Form::MODE_SEARCH ) ? true : false;
        
        if ($contactId) {
            $name = "field[$contactId][$fieldName]";
        } else {
            $name = $fieldName;
        }
        
        if ( substr($fieldName,0,14) === 'state_province' ) {
            $form->add('select', $name, $title,
                       array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince(), $required);
        } else if ( substr($fieldName,0,7) === 'country' ) {
            $form->add('select', $name, $title, 
                       array('' => ts('- select -')) + CRM_Core_PseudoConstant::country(), $required);
        } else if ( $fieldName === 'birth_date' ) {  
            $form->add('date', $name, $title, CRM_Core_SelectValues::date('birth'), $required );  
        } else if ( $fieldName === 'deceased_date' ) {  
            $form->add('date', $name, $title, CRM_Core_SelectValues::date('birth'), $required );  
        } else if ( $fieldName === 'gender' ) {  
            $genderOptions = array( );   
            $gender = CRM_Core_PseudoConstant::gender();   
            foreach ($gender as $key => $var) {   
                $genderOptions[$key] = HTML_QuickForm::createElement('radio', null, ts('Gender'), $var, $key);   
            }   
            $form->addGroup($genderOptions, $name, $title );  
            if ($required) {
                $form->addRule($name, ts('%1 is a required field.', array(1 => $title)) , 'required');
            }
        } else if ( $fieldName === 'individual_prefix' ){
            $form->add('select', $name, $title, 
                       array('' => ts('- select -')) + CRM_Core_PseudoConstant::individualPrefix(), $required);
        } else if ( $fieldName === 'individual_suffix' ){
            $form->add('select', $name, $title, 
                       array('' => ts('- select -')) + CRM_Core_PseudoConstant::individualSuffix(), $required);
        } else if ($fieldName === 'preferred_communication_method') {
            $communicationFields = CRM_Core_SelectValues::pcm();
            foreach ( $communicationFields as $key => $var ) {
                if ( $key == '' ) {
                    continue;
                }
                $communicationOptions[] =& HTML_QuickForm::createElement( 'checkbox', $var, null, $key );
            }
            $form->addGroup($communicationOptions, $name, $title, '<br/>' );
        } else if ($fieldName === 'preferred_mail_format') {
            $form->add('select', $name, $title, CRM_Core_SelectValues::pmf());
            /* need to fix for groups and tags
        } else if ( $fieldName === 'group' ) {
            require_once 'CRM/Contact/Form/GroupTag.php';
            CRM_Contact_Form_GroupTag::buildGroupTagBlock($form, $form->_id,
                                                          CRM_Contact_Form_GroupTag::GROUP,
                                                          true, $required,
                                                          $title, null );
        } else if ( $fieldName === 'tag' ) {
            require_once 'CRM/Contact/Form/GroupTag.php';
            CRM_Contact_Form_GroupTag::buildGroupTagBlock($form, $form->_id,
                                                          CRM_Contact_Form_GroupTag::TAG,
                                                          false, $required,
                                                          null, $title
        );
            */
        } else if (substr($fieldName, 0, 6) === 'custom') {
            $customFieldID = CRM_Core_BAO_CustomField::getKeyID($fieldName);
            CRM_Core_BAO_CustomField::addQuickFormElement($form, $name, $customFieldID, $inactiveNeeded, $required, $search, $title);
        } else if ( in_array($fieldName, array('receive_date', 'receipt_date', 'thankyou_date', 'cancel_date' )) ) {  
            $form->add('date', $name, $title, CRM_Core_SelectValues::date('manual', 3, 1), $required );  
            $form->addRule($name, ts('Select a valid date.'), 'qfDate');
        } else if ($fieldName == 'payment_instrument' ) {
            $form->add('select', $name, ts( 'Paid By' ),
                       array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::paymentInstrument( ), $required );
        } else if ($fieldName == 'contribution_type' ) {
            $form->add('select', $name, ts( 'Contribution Type' ),
                       array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( ), $required);
        } else {
            $processed = false;
            if ( CRM_Core_Permission::access( 'Quest' ) ) {
                require_once 'CRM/Quest/BAO/Student.php';
                $processed = CRM_Quest_BAO_Student::buildStudentForm( $form, $fieldName, $title, $contactId );
            }
            if ( ! $processed ) {
                if ( substr($fieldName, 0, 3) === 'is_' or substr($fieldName, 0, 7) === 'do_not_' ) {
                    $form->add('checkbox', $name, $title, $attributes, $required );
                } else {
                    $form->add('text', $name, $title, $attributes, $required );
                }
            }
        }

        if ($view) {
            $form->freeze($name);
        }

        //add the rules
        if ( in_array($fieldName, array('non_deductible_amount', 'total_amount', 'fee_amount', 'net_amount' )) ) {
            $form->addRule($name, ts('Please enter a valid amount.'), 'money');
        }

        if ( $rule ) {
            if (!($rule == 'email'  &&  $mode == CRM_Profile_Form::MODE_SEARCH)) {
                $form->addRule( $name, ts( 'Please enter a valid %1', array( 1 => $title ) ), $rule );
            }
        }
    }
    
    /**
     * Function to set profile defaults
     *
     * @params int     $contactId     contact id
     * @params array   $fields        associative array of fields
     * @params array   $defaults      defaults array
     * @params boolean $singleProfile true for single profile else false(batch update)
     *
     * @return null
     * @static
     * @access public
     */
    static function setProfileDefaults( $contactId, &$fields, &$defaults, $singleProfile = true ) 
    {
        //get the contact details
        list($contactDetails, $options) = CRM_Contact_BAO_Contact::getHierContactDetails( $contactId, $fields );
        $details = $contactDetails[$contactId];
        
        //start of code to set the default values
        foreach ($fields as $name => $field ) {
            //set the field name depending upon the profile mode(single/batch)
            if ( $singleProfile ) {
                $fldName = $name;
            } else {
                $fldName = "field[$contactId][$name]";
            }

            if (CRM_Utils_Array::value($name, $details )) {
                //to handle custom data (checkbox) to be written
                // to handle gender / suffix / prefix
                if ($name == 'gender') { 
                    $defaults[$fldName] = $details['gender_id'];
                } else if ($name == 'individual_prefix') {
                    $defaults[$fldName] = $details['individual_prefix_id'];
                } else if ($name == 'individual_suffix') {
                    $defaults[$fldName] = $details['individual_suffix_id'];
                } else{
                    $defaults[$fldName] = $details[$name];
                }
                
            } else {
                list($fieldName, $locTypeId, $phoneTypeId) = explode('-', $name);
                if ( is_array($details) ) {   
                    foreach ($details as $key => $value) {
                        if ($locTypeId == 'Primary') {
                            $locTypeId = CRM_Contact_BAO_Contact::getPrimaryLocationType( $contactId ); 
                        }
                        
                        if (is_numeric($locTypeId)) {//fixed for CRM-665
                            if ($locTypeId == $value['location_type_id'] ) {
                                if (CRM_Utils_Array::value($fieldName, $value )) {
                                    //to handle stateprovince and country
                                    if ( $fieldName == 'state_province' ) {
                                        $defaults[$fldName] = $value['state_province_id'];
                                    } else if ( $fieldName == 'country' ) {
                                        if (!$value['country_id']) {
                                            $config =& CRM_Core_Config::singleton();
                                            if ( $config->defaultContactCountry ) {
                                                $countryIsoCodes =& CRM_Core_PseudoConstant::countryIsoCode();
                                                $defaultID = array_search($config->defaultContactCountry,
                                                                          $countryIsoCodes);
                                                $defaults[$fldName] = $defaultID;
                                            }
                                        } else {
                                            $defaults[$fldName] = $value['country_id'];
                                        }
                                    } else if ( $fieldName == 'phone' ) {
                                        if ($phoneTypeId) {
                                            $defaults[$fldName] = $value['phone'][$phoneTypeId];
                                        } else {
                                            $defaults[$fldName] = $value['phone'][1];
                                        }
                                    } else if ( $fieldName == 'email' ) {
                                        //adding the first email (currently we don't support multiple emails of same location type)
                                        $defaults[$fldName] = $value['email'][1];
                                    } else if ( $fieldName == 'im' ) {
                                        //adding the first email (currently we don't support multiple ims of same location type)
                                        $defaults[$fldName] = $value['im'][1];
                                    } else {
                                        $defaults[$fldName] = $value[$fieldName];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        if ( CRM_Core_Permission::access( 'Quest' ) ) {
            require_once 'CRM/Quest/BAO/Student.php';
            // Checking whether the database contains quest_student table.
            // Now there are two different schemas for core and quest.
            // So if only core schema in use then withought following check gets the DB error.
            $student      = new CRM_Quest_BAO_Student();
            $tableStudent = $student->geTableName();
            
            if ($tableStudent) {
                //set student defaults
                CRM_Quest_BAO_Student::retrieve( $details, $defaults, $ids);
                $fields = array( 'educational_interest','college_type','college_interest','test_tutoring');
                foreach( $fields as $field ) {
                    if ( $defaults[$field] ) {
                        $values = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR , $defaults[$field] );
                    }
                    
                    $defaults[$field] = array();
                    if ( is_array( $values ) ) {
                        foreach( $values as $v ) {
                            $defaults[$field][$v] = 1;
                        }
                    }
                }
            }
        }    
    }
}

?>
