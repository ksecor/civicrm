<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

require_once 'CRM/Core/DAO/UFGroup.php';

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

    static function getRegistrationFields( $action, $mode, $ctype = null ) 
    {
        if ( $mode & CRM_Profile_Form::MODE_REGISTER) {
        $ufGroups =& CRM_Core_BAO_UFGroup::getModuleUFGroup('User Registration');
        } else {
            $ufGroups =& CRM_Core_BAO_UFGroup::getModuleUFGroup('Profile');  
        }
        
        if ( ! is_array( $ufGroups ) ) {
            return false;
        }
        
        $fields = array( );

        require_once "CRM/Core/BAO/UFField.php";
        foreach ( $ufGroups as $id => $title ) {
            if ( CRM_Core_BAO_UFField::checkProfileType($id) ) { // to skip mix profiles
                continue;
            }

            if ( $ctype ) {
                $fieldType = CRM_Core_BAO_UFField::getProfileType( $id );
                if ( $fieldType != $ctype ) {
                    continue;
                }
            }

            $subset = self::getFields( $id, true, $action,
                                       null, null, false, null, true, $ctype );

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
    static function getListingFields( $action,
                                      $visibility,
                                      $considerSelector = false,
                                      $ufGroupId = null,
                                      $searchable = null,
                                      $restrict = null,
                                      $skipPermission = false ) 
    {
        if ($ufGroupId) {
            $subset = self::getFields( $ufGroupId, false, $action,
                                       $visibility, $searchable,
                                       false, $restrict,
                                       $skipPermission );
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
                $subset = self::getFields( $id, false, $action,
                                           $visibility, $searchable,
                                           false, $restrict,
                                           $skipPermission );
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
     * @param string   $restrict     should we restrict based on a specified profile type
     *
     * @return array   the fields that belong to this title
     * @static
     * @access public
     */
    static function getFields( $id, $register = false, $action = null,
                               $visibility = null , $searchable = null,
                               $showAll= false, $restrict = null,
                               $skipPermission = false,
                               $ctype = null ) 
    {
        if ( $restrict ) {
            $query  = "SELECT g.* from civicrm_uf_group g, civicrm_uf_join j 
                            WHERE g.is_active   = 1
                              AND g.id          = %1 
                              AND j.uf_group_id = %1 
                              AND j.module      = %2
                              ";
            $params = array( 1 => array( $id, 'Integer' ),
                             2 => array( $restrict, 'String' ) );
        } else {
            $query  = "SELECT g.* from civicrm_uf_group g WHERE g.is_active = 1 AND g.id = %1 ";
            $params = array( 1 => array( $id, 'Integer' ) );
        }
        
        // add permissioning for profiles only if not registration
        if ( ! $skipPermission ) {
            $permissionClause = CRM_Core_Permission::ufGroupClause( CRM_Core_Permission::VIEW, 'g.' );
            $query .= " AND $permissionClause ";
        }
       
        $group =& CRM_Core_DAO::executeQuery( $query, $params );
        
        $fields = array( );
        if ( $group->fetch( ) ) {
            $where = " WHERE uf_group_id = {$group->id}";
            
            if( $searchable ) {
                $where .= " AND is_searchable = 1"; 
            }
            
            if ( ! $showAll ) {
                $where .= " AND is_active = 1";
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
            require_once 'CRM/Contact/BAO/Contact.php';
            if ( !$showAll ) {
                $importableFields =& CRM_Contact_BAO_Contact::importableFields( "All");
            } else {
                $importableFields =& CRM_Contact_BAO_Contact::importableFields("All", false, true );
            }
            
            $importableFields['group']['title'] = ts('Group(s)');
            $importableFields['group']['where'] = null;
            $importableFields['tag'  ]['title'] = ts('Tag(s)');
            $importableFields['tag'  ]['where'] = null;
            
            $specialFields = array ( 'street_address',
                                     'supplemental_address_1',
                                     'supplemental_address_2',
                                     'city',
                                     'postal_code',
                                     'postal_code_suffix',
                                     'geo_code_1',
                                     'geo_code_2',
                                     'state_province',
                                     'country',
                                     'phone',
                                     'email',
                                     'im',
                                     'location_name' );
            
            //get location type
            $locationType = array( );
            $locationType =& CRM_Core_PseudoConstant::locationType();
            
            require_once 'CRM/Core/BAO/CustomField.php';
            $customFields = CRM_Core_BAO_CustomField::getFieldsForImport( $ctype );
            
            // hack to add custom data for components
            $components = array("Contribution", "Participant","Membership");
            foreach ( $components as $value) {
                $customFields = array_merge($customFields, CRM_Core_BAO_CustomField::getFieldsForImport($value));
            }

            while ( $field->fetch( ) ) {
                $name  = $title = $locType = $phoneType = '';
                $name  = $field->field_name;
                $title = $field->label;
                
                if ($field->location_type_id) {
                    $name    .= "-{$field->location_type_id}";
                    $locType  = " ( {$locationType[$field->location_type_id]} ) ";
                } else {                                                           
                    if ( in_array($field->field_name, $specialFields))  {
                        $name    .= '-Primary'; 
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
                          'where'            => CRM_Utils_Array::value('where',$importableFields[$field->field_name]),
                          'attributes'       => CRM_Core_DAO::makeAttribute( CRM_Utils_Array::value($field->field_name,
                                                                                                    $importableFields) ),
                          'is_required'      => $field->is_required,
                          'is_view'          => $field->is_view,
                          'help_post'        => $field->help_post,
                          'visibility'       => $field->visibility,
                          'in_selector'      => $field->in_selector,
                          'rule'             => CRM_Utils_Array::value( 'rule', $importableFields[$field->field_name] ),
                          'location_type_id' => $field->location_type_id,
                          'phone_type'       => $field->phone_type,
                          'group_id'         => $group->id,
                          'add_to_group_id'  => $group->add_to_group_id,
                          'collapse_display' => $group->collapse_display,
                          'add_captcha'      => $group->add_captcha
                          );

                //adding custom field property 
                if ( substr($name, 0, 6) == 'custom' ) {
                    // if field is not present in customFields, that means the user
                    // DOES NOT HAVE permission to access that field
                    if ( array_key_exists( $name, $customFields ) ) {
                        $fields[$name]['is_search_range' ] = $customFields[$name]['is_search_range'];
                        // fix for CRM-1994
                        $fields[$name]['options_per_line'] = $customFields[$name]['options_per_line']; 
                    } else {
                        unset( $fields[$name] );
                    }
                }
            }
        } else {
            CRM_Core_Error::fatal( ts( 'This profile is not configured for the requested action. Contact the site administrator if you need assistance.' ) );
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
    static function isValid( $userID, $title, $register = false, $action = null ) 
    {
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
                                 $doNotProcess  = false,
                                 $ctype = null ) 
    {
        $session =& CRM_Core_Session::singleton( );

        if ( $register ) {
            require_once "CRM/Core/Controller/Simple.php";
            $controller =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Dynamic',
                                                           ts('Dynamic Form Creator'),
                                                           $action );
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
            $controller->set( 'skipPermission', 1 );
            $controller->set( 'ctype'   , $ctype );
            $controller->process( );
            $controller->setEmbedded( true );
            $controller->run( );

            // we are done processing so restore the POST/REQUEST vars
            if ( ( $reset || $doNotProcess ) && $oldQFDefault ) {
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
                // make sure profileID and ctype match if ctype exists
                if ( $ctype ) {
                    $profileType = CRM_Core_BAO_UFField::getProfileType( $profileID );
                    if ( $profileType != $ctype ) {
                        return null;
                    }
                }

                require_once 'CRM/Core/Controller/Simple.php';
                $controller =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Dynamic',
                                                               ts('Dynamic Form Creator'),
                                                               $action );
                if ( $reset ) {
                    $controller->reset( );
                }
                $controller->set( 'gid'     , $profileID );
                $controller->set( 'id'      , $userID );
                $controller->set( 'register', 0 );
                $controller->set( 'skipPermission', 1 );
                if ( $ctype ) {
                    $controller->set( 'ctype'   , $ctype );
                }
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
                    // get the new email, location is different in Drupal 5 vs Drupal 4.7
                    $config =& CRM_Core_Config::singleton( );
                    if ( $config->userFrameworkVersion >= 5 ) {
                        $email = CRM_Utils_Array::value( 'mail', $_POST );
                    } else {
                        $email = CRM_Utils_Array::value( 'mail',
                                                         CRM_Utils_Array::value( 'edit', $_POST ) );
                    }
                    
                    if ( CRM_Utils_Rule::email( $email ) && ( $email  != $userEmail[1] ) ) {
                        require_once 'CRM/Core/BAO/UFMatch.php';
                        CRM_Core_BAO_UFMatch::updateContactEmail( $userID, $email );
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
    public static function findContact( &$params, $id = null, $flatten = false ) 
    {
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
     * @param int     $id             the contact id
     * @param array   $fields         the profile fields of interest
     * @param array   $values         the values for the above fields
     * @param boolean $searchable     searchable or not
     * @param array   $componentWhere component condition
     *
     * @return void
     * @access public
     * @static
     */
    public static function getValues( $cid, &$fields, &$values, $searchable = true, $componentWhere = null ) 
    {
        $options = array( );
        $studentFields = array( );
        if ( CRM_Core_Permission::access( 'Quest', false ) ) {
            //student fields ( check box ) 
            require_once 'CRM/Quest/BAO/Student.php';
            $studentFields = CRM_Quest_BAO_Student::$multipleSelectFields;
        }
        
        // get the contact details (hier)
        $returnProperties =& CRM_Contact_BAO_Contact::makeHierReturnProperties( $fields );

        $params  = array( array( 'contact_id', '=', $cid, 0, 0 ) );
        
        // add conditions specified by components. eg partcipant_id etc
        if ( !empty($componentWhere) ) {
            $params = array_merge($params, $componentWhere);
        }

        $query   =& new CRM_Contact_BAO_Query( $params, $returnProperties, $fields );
        $options =& $query->_options;
        
        $details = $query->searchQuery( );
        if ( ! $details->fetch( ) ) {
            return;
        }

        $config =& CRM_Core_Config::singleton( );
        
        require_once 'CRM/Core/PseudoConstant.php'; 
        $locationTypes = $imProviders = array( );
        $locationTypes = CRM_Core_PseudoConstant::locationType( );
        $imProviders   = CRM_Core_PseudoConstant::IMProvider( );
        //start of code to set the default values
        foreach ($fields as $name => $field ) { 
            $index   = $field['title'];
            $params[$index] = $values[$index] = '';
            $customFieldName = null;
            if ( $name === 'organization_name' ) {
                require_once "CRM/Contact/BAO/Relationship.php";
                $rel = CRM_Contact_BAO_Relationship::getRelationship($cid);
                krsort($rel);
                foreach ($rel as $k => $v) {
                    if ($v['relation'] == 'Employee of') {
                        $values[$index] = $params[$index] = $v['name'];
                        break;
                    }
                }
            }
            
            if ( isset($details->$name) || $name == 'group' || $name == 'tag') {//hack for CRM-665
                // to handle gender / suffix / prefix
                if ( in_array( $name, array( 'gender', 'individual_prefix', 'individual_suffix' ) ) ) {
                    $values[$index] = $details->$name;
                    $name = $name . '_id';
                    $params[$index] = $details->$name ;
                } else if ( in_array( $name, array( 'state_province', 'country', 'county' ) ) ) {
                    $values[$index] = $details->$name;
                    $idx = $name . '_id';
                    $params[$index] = $details->$idx;
                   
                } else if ( $name === 'preferred_communication_method' ) {
                    $communicationFields = CRM_Core_PseudoConstant::pcm();
                    $pref = array();
                    $compref = array();
                    $pref = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $details->$name );
                    
                    foreach($pref as $k) {
                        if ( $k ) {
                            $compref[] = $communicationFields[$k];
                        }
                    }
                    $params[$index] = $details->$name;
                    $values[$index] = implode( ",", $compref);
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
                    if ( CRM_Core_Permission::access( 'Quest', false ) ) {
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

                                $fileURL = CRM_Core_BAO_CustomField::getFileURL( $cid,
                                                                                 $details->$name,
                                                                                 $details->$customOptionValueId );
                                if ( $fileURL ) {
                                    $params[$index] = $values[$index] = $fileURL;
                                } else {
                                    // 
                                    if (is_numeric( $details->{$name} ) ) {
                                        // check if there is a . and hence a float
                                        if ( strpos( $details->{$name}, '.' ) ) {
                                            $customVal = (float )($details->{$name});
                                        } else {
                                            $customVal = (int ) ($details->{$name});
                                        }
                                    } else {
                                        $customVal = $details->{$name};
                                    }

                                    $params[$index] = $customVal;
                                    $values[$index] = CRM_Core_BAO_CustomField::getDisplayValue( $customVal, $cfID, $options );
                                    if ( CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField', 
                                                                      $cfID, 'is_search_range' ) ) {
                                        $customFieldName = "{$name}_from";
                                    }
                                }
                            } else if ( $name == 'home_URL' &&
                                        ! empty( $details->$name ) ) {
                                $url = CRM_Utils_System::fixURL( $details->$name );
                                $values[$index] = "<a href=\"$url\">{$details->$name}</a>";
                            } else if ( in_array( $name, array('birth_date', 'deceased_date','membership_start_date','membership_end_date','join_date')) ) {
                                $values[$index] = $details->$name;
                                require_once 'CRM/Utils/Date.php';
                                $params[$index] = CRM_Utils_Date::isoToMysql( $details->$name );
                            } else {
                                $values[$index] = $details->$name;
                            }
                        }
                    } 
                }
            } else if ( strpos( $name, '-' ) !== false ) {
                list( $fieldName, $id, $type ) = explode( '-', $name );
                
                if ($id == 'Primary') {
                    // fix for CRM-1543
                    // not sure why we'd every use Primary location type id
                    // we need to fix the source if we are using it
                    // $locationTypeName = CRM_Contact_BAO_Contact::getPrimaryLocationType( $cid ); 
                    $locationTypeName = 1;
                } else {
                    $locationTypeName = CRM_Utils_Array::value( $id, $locationTypes );
                }
                
                if ( ! $locationTypeName ) {
                    continue;
                }
                
                $detailName = "{$locationTypeName}-{$fieldName}";
                $detailName = str_replace( ' ', '_', $detailName );

                if ( in_array( $fieldName, array( 'phone', 'im', 'email' ) ) ) {
                    if ( $type ) {
                        $detailName .= "-{$type}";
                    } else {
                        $detailName .= '-1';
                    }
                }

                if ( in_array( $fieldName, array( 'state_province', 'country', 'county' ) ) ) {
                    $values[$index] = $details->$detailName;
                    $idx = $detailName . '_id';
                    $params[$index] = $details->$idx;
                } else if ( $fieldName == 'im'){
                    $providerId     = $detailName . '-provider_id';
                    $providerName   = $imProviders[$details->$providerId];
                    if ( $providerName ) {
                        $values[$index] = $details->$detailName . " (" . $providerName .")";
                    } else {
                        $values[$index] = $details->$detailName;
                    }
                    $params[$index] = $details->$detailName ;        
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
                $customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name']);
                
                
                if ( !$customFieldName ) { 
                    $fieldName = $field['name'];
                } else {
                    $fieldName = $customFieldName;
                }
                
                if ( CRM_Core_BAO_CustomField::getKeyID($field['name']) ) {
                    $htmlType = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField', $customFieldID, 'html_type', 'id' );
                    if($htmlType == 'Link') {
                        $url =  $params[$index] ;
                    } else{
                         $url = CRM_Utils_System::url( 'civicrm/profilel',
                                              'reset=1&force=1&gid=' . $field['group_id'] .'&'. 
                                              urlencode( $fieldName ) .
                                              '=' .
                                              urlencode( $params[$index] ) );
                    }
                }
                
       
               
                if ( ! empty( $values[$index] ) && $searchable ) {
                    $values[$index] = '<a href="' . $url . '">' . $values[$index] . '</a>';
                }
            }
            if ( $field['visibility'] == "User and User Admin Only"|| $field['visibility'] == "Public User Pages" ) {
 
                $customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name']);
                 if ( CRM_Core_BAO_CustomField::getKeyID($field['name']) ) {
                    $htmlType = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField', $customFieldID, 'html_type', 'id' );
                    if($htmlType == 'Link') {
                          $values[$index] = '<a href="' . $values[$index] . '">' . $values[$index] . '</a>';
                     }
                 }
            }
        }
        
    }

    /**
     * Check if profile Group used by any module.
     *
     * @param int  $id    profile Id 
     * 
     * @return boolean
     *
     * @access public
     * @static
     *
     */
    public static function usedByModule( $id ) 
    {               
        //check whether this group is used by any module(check uf join records)
        $sql = "SELECT id
                 FROM civicrm_uf_join
                 WHERE civicrm_uf_join.uf_group_id=$id" ; 
        
        $dao =& new CRM_Core_DAO( );
        $dao->query( $sql );
        if ( $dao->fetch( ) ) {        
            return true;
        } else {
            return false;
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
    public static function del($id) 
    {                
        //check whether this group contains  any profile fields
        require_once 'CRM/Core/DAO/UFField.php';
        require_once 'CRM/Core/BAO/UFField.php';
        $profileField = & new CRM_Core_DAO_UFField();
        $profileField->uf_group_id = $id;
        $profileField->find();
        while($profileField->fetch()) {
            CRM_Core_BAO_UFField::del($profileField->id);            
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
     * function to add the UF Group
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids) 
    {
        $params['is_active'              ] = CRM_Utils_Array::value( 'is_active'           , $params, false );
        $params['add_captcha'            ] = CRM_Utils_Array::value( 'add_captcha'         , $params, false );
        $params['is_map'                 ] = CRM_Utils_Array::value( 'is_map'              , $params, false );
        $params['is_update_dupe'         ] = CRM_Utils_Array::value( 'is_update_dupe'      , $params, false );
        $params['collapse_display'       ] = CRM_Utils_Array::value( 'collapse_display'    , $params, false );
        $params['limit_listings_group_id'] = CRM_Utils_Array::value( 'group'               , $params        );
        $params['add_to_group_id'        ] = CRM_Utils_Array::value( 'add_contact_to_group', $params        );
        $params['is_edit_link'           ] = CRM_Utils_Array::value( 'is_edit_link'        , $params, false );
        $params['is_uf_link'             ] = CRM_Utils_Array::value( 'is_uf_link'          , $params, false );
        $params['is_cms_user'            ] = CRM_Utils_Array::value( 'is_cms_user'         , $params, false );

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
            if (isset( $params['location'] ) && is_array( $params['location'] ) ) {
                $params['email'] = null;
                $params['phone'] = null;
                $params['im']    = null;
            
                foreach($params['location'] as $locIdx => $loc) {
                    foreach (array('email', 'phone', 'im') as $key) {
                        if ( isset($loc[$key]) && is_array($loc[$key]) ) {
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
                if ( isset( $params[$key] ) && count( $params[$key] ) == 0 ) {
                    unset( $params[$key] );
                }
            }
            
            foreach ( array( 'street_address', 'supplemental_address_1', 'supplemental_address_2',
                             'state_province_id', 'postal_code', 'country_id' ) as $fld ) {
                if ( ! empty( $params['location'][1]['address'][$fld] ) ) {
                    $params[$fld] = $params['location'][1]['address'][$fld];
                }
            }
            unset( $params['location'] );
            
            if (isset( $params['custom'] ) && is_array( $params['custom'] ) ) {
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
	$fields['external_identifier'] =  array('name'             => 'external_identifier',
						'title'            => $importableFields['external_identifier']['title'],
						'where'            => $importableFields['external_identifier']['where'],
						);

        //this is the fix to ignore the groups/ tags for dupe checking CRM-664, since we never use them for dupe checking
        unset( $params['group'] );
        unset( $params['tag']   );
        
        if ( isset( $params["contact_type"] ) && $params["contact_type"] ) {
            $fields["contact_type"] = array("name"  => "contact_type" ,
                                            "title" => "Contact Type",
                                            "where" => "civicrm_contact.contact_type"
                                            );
        }
        
        // also eliminate all the params that are not present in fields
        foreach ( $params as $name => $value ) {
            if ( ! array_key_exists( $name, $fields ) ) {
                unset( $params[$name] );
            }
        }

        if ( empty( $params ) ) {
            return null;
        }

        require_once 'CRM/Contact/BAO/Query.php';
        $params =& CRM_Contact_BAO_Query::convertFormValues( $params, 0, true );
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
        $query = "UPDATE civicrm_uf_join SET weight = %1
                       WHERE  uf_group_id = $ufGroupId";
        $p =array( 1 => array( $params['weight'], 'Integer' ) ); 
        CRM_Core_DAO::executeQuery($query, $p);
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
        $ufJoin = array( );

        while ($dao->fetch( )) {
            if (!$displayName) { 
                $ufJoin[$dao->id] = $dao->module;
            } else {
                if ( isset ( $UFGroupType[$dao->module] ) ) {
                    if (!$status) { //skip the default modules
                        $ufJoin[$dao->id] = $UFGroupType[$dao->module];
                    }
                } else if ( ! CRM_Utils_Array::key($dao->module, $ufJoin) ){ //added for CRM-1475
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
            $ufGroups[$dao->id]['name'     ] = $dao->title;
            $ufGroups[$dao->id]['title'    ] = $dao->title;
            $ufGroups[$dao->id]['weight'   ] = $dao->weight + $count;
            $ufGroups[$dao->id]['is_active'] = $dao->is_active;
        }

        return $ufGroups;
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
    static function filterUFGroups ($ufGroupId, $contactID = null) 
    {
        if ( ! $contactID ) {
           $session =& CRM_Core_Session::singleton( );
           $contactID = $session->get( 'userID' );
        }

        if ($contactID) {
            //get the contact type
            require_once "CRM/Contact/BAO/Contact.php";
            $contactType = CRM_Contact_BAO_Contact::getContactType($contactID);
            
            //match if exixting contact type is same as profile contact type
            require_once "CRM/Core/BAO/UFField.php";
            $profileType = CRM_Core_BAO_UFField::getProfileType($ufGroupId);
            
            //allow special mix profiles for Contribution and Participant
            $specialProfiles = array('Contribution', 'Participant' , 'Membership');

            if ( in_array($profileType, $specialProfiles )  ) {
                return true;
            }
            
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
     * @params int     $contactID  contact id
     *
     * @return null
     * @static
     * @access public
     */
    static function buildProfile( &$form, &$field, $mode, $contactId = null )  
    {
        require_once "CRM/Profile/Form.php";
        require_once "CRM/Core/OptionGroup.php";

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

        $config =& CRM_Core_Config::singleton( );

        if ( substr($fieldName,0,14) === 'state_province' ) {
            $form->add('select', $name, $title,
                       array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince(), $required);
        } else if ( substr($fieldName,0,7) === 'country' ) {
            $form->add('select', $name, $title, 
                       array('' => ts('- select -')) + CRM_Core_PseudoConstant::country(), $required);
        } else if ( substr($fieldName,0,6) === 'county' ) {
            if ( $config->includeCounty ) {
                $form->add('select', $name, $title, 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::county(), $required);
            }
        } else if ( substr($fieldName, 0, 2) === 'im' ) {
            if ( !$contactId ) {
                $form->add('select', $name . '-provider_id', 'IM Provider', 
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::IMProvider(), $required);
            
                if ($view && $mode != CRM_Profile_Form::MODE_SEARCH) {
                    $form->freeze($name."-provider_id");
                }
            }
            $form->add('text', $name, $title, $attributes, $required );
        } else if ( $fieldName === 'birth_date' ) {  
            $form->add('date', $name, $title, CRM_Core_SelectValues::date('birth'), $required );  
        } else if ( $fieldName === 'deceased_date' ) {  
            $form->add('date', $name, $title, CRM_Core_SelectValues::date('birth'), $required );    
        } else if ( in_array($fieldName, array( "membership_start_date","membership_end_date","join_date")) ) {  
            $form->add('date', $name, $title, CRM_Core_SelectValues::date('manual'), $required ); 
        }  else if ($field['name'] == 'membership_type_id' ) { 
            require_once 'CRM/Member/PseudoConstant.php';
            $form->add('select', 'membership_type_id', ts( 'Membership Type' ),
                       array(''=>ts( '-select-' )) + CRM_Member_PseudoConstant::membershipType( ), $required );            
        } else if ($field['name'] == 'status_id' ) { 
            require_once 'CRM/Member/PseudoConstant.php';
            $form->add('select', 'status_id', ts( 'Membership Status' ),
                       array(''=>ts( '-select-' )) + CRM_Member_PseudoConstant::membershipStatus( ), $required );
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
            $communicationFields = CRM_Core_PseudoConstant::pcm();
            foreach ( $communicationFields as $key => $var ) {
                if ( $key == '' ) {
                    continue;
                }
                $communicationOptions[] =& HTML_QuickForm::createElement( 'checkbox', $key, null, $var );
            }
            $form->addGroup($communicationOptions, $name, $title, '<br/>' );
        } else if ($fieldName === 'preferred_mail_format') {
            $form->add('select', $name, $title, CRM_Core_SelectValues::pmf());
        } else if ( $fieldName === 'group' ) {
            require_once 'CRM/Contact/Form/GroupTag.php';
            CRM_Contact_Form_GroupTag::buildGroupTagBlock($form, $contactId,
                                                          CRM_Contact_Form_GroupTag::GROUP,
                                                          true, $required,
                                                          $title, null, $name );
        } else if ( $fieldName === 'tag' ) {
            require_once 'CRM/Contact/Form/GroupTag.php';
            CRM_Contact_Form_GroupTag::buildGroupTagBlock($form, $contactId,
                                                          CRM_Contact_Form_GroupTag::TAG,
                                                          false, $required,
                                                          null, $title, $name );
            
        } else if ( $fieldName === 'home_URL' ) {
            $form->addElement('text', $name, $title,
                              array_merge( CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'home_URL'),
                                           array('onfocus' => "if (!this.value) this.value='http://'; else return false")
                                           ));
            
            $form->addRule($name, ts('Enter a valid Website.'), 'url');
        } else if (substr($fieldName, 0, 6) === 'custom') {
            $customFieldID = CRM_Core_BAO_CustomField::getKeyID($fieldName);
            CRM_Core_BAO_CustomField::addQuickFormElement($form, $name, $customFieldID, $inactiveNeeded, $required, $search, $title);
        } else if ( in_array($fieldName, array('receive_date', 'receipt_date', 'thankyou_date', 'cancel_date' )) ) {  
            $form->add('date', $name, $title, CRM_Core_SelectValues::date('manual', 3, 1), $required );  
            $form->addRule($name, ts('Select a valid date.'), 'qfDate');
        } else if ($fieldName == 'payment_instrument' ) {
            require_once "CRM/Contribute/PseudoConstant.php";
            $form->add('select', $name, ts( 'Paid By' ),
                       array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::paymentInstrument( ), $required );
        } else if ($fieldName == 'contribution_type' ) {
            require_once "CRM/Contribute/PseudoConstant.php";
            $form->add('select', $name, ts( 'Contribution Type' ),
                       array(''=>ts( '-select-' )) + CRM_Contribute_PseudoConstant::contributionType( ), $required);
        } else if ($fieldName == 'participant_register_date' ) {
            require_once "CRM/Event/PseudoConstant.php";
            $form->add('date', $name, $title, CRM_Core_SelectValues::date('birth'), $required );  
        } else if ($fieldName == 'participant_status_id' ) {
            require_once "CRM/Event/PseudoConstant.php";
            $form->add('select', $name, ts( 'Participant Status' ),
                       array(''=>ts( '-select-' )) + CRM_Event_PseudoConstant::participantStatus( ), $required);
        } else if ($fieldName == 'participant_role_id' ) {
            require_once "CRM/Event/PseudoConstant.php";
            $form->add('select', $name, ts( 'Participant Role' ),
                       array(''=>ts( '-select-' )) + CRM_Event_PseudoConstant::participantRole( ), $required);
        } else if ($fieldName == 'scholarship_type_id' ) {
            $form->add('select', $name, $title, array( "" => "-- Select -- " )+ array_flip( CRM_Core_OptionGroup::values( 'scholarship_type', true ) ) );
        } else if ($fieldName == 'applicant_status_id' ) {  
            $form->add('select', $name, $title, array( "" => "-- Select -- " )+ array_flip( CRM_Core_OptionGroup::values( 'applicant_status', true ) ) );
        } else if ($fieldName == 'highschool_gpa_id' ) {
            $form->add('select', $name, $title, array( "" => "-- Select -- ") + CRM_Core_OptionGroup::values( 'highschool_gpa' ) );
        } else if ($fieldName == 'interview_rank' ) {
            require_once "CRM/TMF/BAO/Query.php";
            $ranking = array( );
            require_once "CRM/TMF/BAO/Query.php";
            $ranking = CRM_TMF_BAO_Query::buildNumberSelect(20);
            $form->add('select', $name, $title, array("" => "-- Select -- ")+ $ranking );
        } else {
            $processed = false;
            if ( CRM_Core_Permission::access( 'Quest', false ) ) {
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
        
        if ($view && $mode != CRM_Profile_Form::MODE_SEARCH) {
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
     * @params int     $contactId      contact id
     * @params array   $fields         associative array of fields
     * @params array   $defaults       defaults array
     * @params boolean $singleProfile  true for single profile else false(batch update)
     * @params int     $componentId    id for specific components like contribute, event etc
     *
     * @return null
     * @static
     * @access public
     */
    static function setProfileDefaults( $contactId, &$fields, &$defaults, $singleProfile = true, $componentId = null, $component = null ) 
    {
        if ( ! $componentId ) {
            //get the contact details
            require_once 'CRM/Contact/BAO/Contact.php';
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
                
                require_once 'CRM/Contact/Form/GroupTag.php';
                if ( $name == 'group' ) {                   
                    CRM_Contact_Form_GroupTag::setDefaults( $contactId, $defaults, CRM_Contact_Form_GroupTag::GROUP, $fldName ); 
                }
                if( $name == 'tag' ) {
                    CRM_Contact_Form_GroupTag::setDefaults( $contactId, $defaults, CRM_Contact_Form_GroupTag::TAG, $fldName ); 
                }

                if( $name == 'organization_name' ) {
                    require_once "CRM/Contact/BAO/Relationship.php";
                    $rel = CRM_Contact_BAO_Relationship::getRelationship($contactId);
                    krsort($rel);
                    foreach ($rel as $key => $value) {
                        if ($value['relation'] == 'Employee of') {
                            $defaults[$name] =  $value['name'];
                            break;
                        }
                    }
                }

                if (CRM_Utils_Array::value($name, $details ) || isset( $details[$name] ) ) {
                    //to handle custom data (checkbox) to be written
                    // to handle gender / suffix / prefix
                    if ($name == 'gender') { 
                        $defaults[$fldName] = $details['gender_id'];
                    } else if ($name == 'individual_prefix') {
                        $defaults[$fldName] = $details['individual_prefix_id'];
                    } else if ($name == 'individual_suffix') {
                        $defaults[$fldName] = $details['individual_suffix_id'];
                    } else if ($name == 'preferred_communication_method') {
                        $v = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $details[$name] );
                        foreach ( $v as $item ) {
                            if ($item) {
                                $defaults[$fldName."[$item]"] = 1;
                            }
                        } 
                    } else if ( substr( $name, 0, 7 ) == 'custom_') {
                        //fix for custom fields
                        $customFields = CRM_Core_BAO_CustomField::getFields( $values['Individual'] );

                        // hack to add custom data for components
                        $components = array("Contribution", "Participant","Membership");
                        foreach ( $components as $value) {
                            $customFields = CRM_Utils_Array::crmArrayMerge( $customFields, 
                                                                            CRM_Core_BAO_CustomField::getFieldsForImport($value));
                        }
                        
                        switch( $customFields[substr($name,7,9)][3] ) {
                        case 'Multi-Select':
                            $v = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $details[$name] );
                            foreach ( $v as $item ) {
                                if ($item) {
                                    $defaults[$fldName][$item] = $item;
                                }
                            }
                            break;
                            
                        case 'CheckBox':
                            $v = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $details[$name] );
                            foreach ( $v as $item ) {
                                if ($item) {
                                    $defaults[$fldName][$item] = 1;
                                }
                            }
                            break;
                            
                        default:
                            $defaults[$fldName] = $details[$name];
                            break;
                        }
                    } else{
                        $defaults[$fldName] = $details[$name];
                    }
                } else {
                    list($fieldName, $locTypeId, $phoneTypeId) = CRM_Utils_System::explode( '-', $name, 3 );
                    if ( is_array($details) ) {   
                        foreach ($details as $key => $value) {
                            if ($locTypeId == 'Primary') {
                                $locTypeId = CRM_Contact_BAO_Contact::getPrimaryLocationType( $contactId ); 
                            }

                            if (is_numeric($locTypeId)) {//fixed for CRM-665
                                if ($locTypeId == CRM_Utils_Array::value('location_type_id',$value) ) {
                                    if (CRM_Utils_Array::value($fieldName, $value )) {
                                        //to handle stateprovince and country
                                        if ( $fieldName == 'state_province' ) {
                                            $defaults[$fldName] = $value['state_province_id'];
                                        } else if ( $fieldName == 'county' ) {
                                            $defaults[$fldName] = $value['county_id'];
                                        } else if ( $fieldName == 'country' ) {
                                            $defaults[$fldName] = $value['country_id'];
                                        } else if ( $fieldName == 'phone' ) {
                                            if ($phoneTypeId) {
                                                if ( $value['phone'][$phoneTypeId] ) {
                                                    $defaults[$fldName] = $value['phone'][$phoneTypeId];
                                                }
                                            } else {
                                                $defaults[$fldName] = $value['phone'][1];
                                            }
                                        } else if ( $fieldName == 'email' ) {
                                            //adding the first email (currently we don't support multiple emails of same location type)
                                            $defaults[$fldName] = $value['email'][1];
                                        } else if ( $fieldName == 'im' ) {
                                            //adding the first im (currently we don't support multiple ims of same location type)
                                            $defaults[$fldName] = $value['im'][1];
                                            $defaults[$fldName . "-provider_id"] = $value['im']['1_provider_id'];
                                        } else {
                                            $defaults[$fldName] = $value[$fieldName];
                                        }
                                    }
                                }
                                if ( $fieldName == 'country' ) {
                                    if ( ! isset($value['country_id']) || ! $value['country_id'] ) {
                                        $config =& CRM_Core_Config::singleton();
                                        if ( $config->defaultContactCountry ) {
                                            $defaults[$fldName] = $config->defaultContactCountry;
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
            }
            
            if ( CRM_Core_Permission::access( 'Quest', false ) ) {
                require_once 'CRM/Quest/BAO/Student.php';
                // Checking whether the database contains quest_student table.
                // Now there are two different schemas for core and quest.
                // So if only core schema in use then withought following check gets the DB error.
                $student      = new CRM_Quest_BAO_Student();
                $tableStudent = $student->getTableName();
                
                if ($tableStudent) {
                    //set student defaults
                    CRM_Quest_BAO_Student::retrieve( $details, $studentDefaults, $ids);
                    $studentFields = array( 'educational_interest','college_type','college_interest','test_tutoring');
                    foreach( $studentFields as $fld ) {
                        if ( $studentDefaults[$fld] ) {
                            $values = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR , $studentDefaults[$fld] );
                        }
                        
                        $studentDefaults[$fld] = array();
                        if ( is_array( $values ) ) {
                            foreach( $values as $v ) {
                                $studentDefaults[$fld][$v] = 1;
                            }
                        }
                    }

                    foreach ($fields as $name => $field ) {
                        $fldName = "field[$contactId][$name]";
                        if ( array_key_exists($name,$studentDefaults) ) {
                            $defaults[$fldName] = $studentDefaults[$name];
                        }
                    }
                }
            }
        }  
        
        //Handling Contribution Part of the batch profile 
        if ( CRM_Core_Permission::access( 'CiviContribute' ) && $component == 'Contribute' ) {
            $params = $ids = $values = array();
            $params = array( 'id' => $componentId );
            
            require_once "CRM/Contribute/BAO/Contribution.php";
            CRM_Contribute_BAO_Contribution::getValues( $params, $values,  $ids );

            foreach ($fields as $name => $field ) {
                $fldName = "field[$componentId][$name]";
                if ( $name == 'contribution_type' ) {
                    $defaults[$fldName] = $values['contribution_type_id'];
                } else if ( array_key_exists($name,$values) ) {
                    $defaults[$fldName] = $values[$name];
                }
            }
        }

        //Handling Event Participation Part of the batch profile 
        if ( CRM_Core_Permission::access( 'CiviEvent' ) && $component == 'Event' ) {
               $params = $ids = $values = array();
               $params = array( 'id' => $componentId );
               
               require_once "CRM/Event/BAO/Participant.php";
               CRM_Event_BAO_Participant::getValues( $params, $values,  $ids );

               foreach ($fields as $name => $field ) {
                   $fldName = "field[$componentId][$name]";
                   if ( array_key_exists($name,$values[$componentId]) ) {
                       $defaults[$fldName] = $values[$componentId][$name];
                   } 
               }
        }
        
    }
    
    /**
     * Function to get profiles by type  eg: pure Individual etc
     *
     * @params array  $types      associative array of types eg: types('Individual')
     *
     * @return array  $profiles  associative array of profiles  
     * @static
     * @access public
     */
    static function getProfiles( $types ) 
    {
        require_once "CRM/Core/BAO/UFField.php";
        $profiles = array();
        $ufGroups = CRM_Core_PseudoConstant::ufgroup( );
        foreach ($ufGroups as $id => $title) {
            $ptype = CRM_Core_BAO_UFField::getProfileType($id, false);
            if ( in_array ($ptype, $types) ) {
                $profiles[$id] = $title;
            }
        }
        
        return $profiles;
    }

   /**
     * Function to get default value for Register. 
     *
     * @return $defaults
     * @static
     * @access public
     */
    static function setRegisterDefaults( &$fields, &$defaults )  
    {
        foreach($fields as $name=>$field) {
            if ( substr( $name, 0, 8 ) == 'country-' ) {
                $config =& CRM_Core_Config::singleton();
                if ( $config->defaultContactCountry ) {
                    $defaults[$name] = $config->defaultContactCountry;
                }
            }
        }
        return $defaults;
    }
    
    /**
     * This function is to make a copy of a profile, including
     * all the fields in the profile
     *
     * @param int $id the profile id to copy
     *
     * @return void
     * @access public
     */
    static function copy( $id ) 
    {
        $fieldsToPrefix = array( 'title' => ts( 'Copy of ' ) );
        
        $copy        =& CRM_Core_DAO::copyGeneric( 'CRM_Core_DAO_UFGroup', 
                                                   array( 'id' => $id ), 
                                                   null, 
                                                   $fieldsToPrefix );

        $copyUFJoin  =& CRM_Core_DAO::copyGeneric( 'CRM_Core_DAO_UFJoin', 
                                                   array( 'uf_group_id' => $id ), 
                                                   array( 'uf_group_id' => $copy->id),
                                                   null,
                                                   'entity_table');

        $copyUFField =& CRM_Core_DAO::copyGeneric( 'CRM_Core_BAO_UFField', 
                                                   array( 'uf_group_id' => $id ), 
                                                   array( 'uf_group_id' => $copy->id ) );
        return $copy;
    }

    
    /**
     * Process that send notification e-mails
     *
     * @params int     $contactId      contact id
     * @params array   $values         associative array of name/value pair
     * @return void
     * @access public
     */
    
    static function commonSendMail( $contactID, &$values ) {
        if ( !$contactID || !$values ){
            return;
        }
        
        $template =& CRM_Core_Smarty::singleton( );
                   
        list( $displayName, $email ) = CRM_Contact_BAO_Contact::getEmailDetails( $contactID );
               
        self::profileDisplay( $values['id'] , $values['values'],$template );
        $emailList = explode(',',$values['email']);
        
        $contactLink = CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid=$contactID");
          
        // set details in the template here
        $template->assign( 'email', $email );
        $template->assign( 'displayName',$displayName);            
        $template->assign( 'currentDate',date('r') );
        $template->assign( 'contactLink',$contactLink);
        
        $subject = trim( $template->fetch( 'CRM/UF/Form/NotifySubject.tpl' ) );
        $message = $template->fetch( 'CRM/UF/Form/NotifyMessage.tpl' );             
        $emailFrom = '"' . $displayName . '" <' . $email . '>';
       
        if($message) {
            require_once 'CRM/Utils/Mail.php';
            foreach ( $emailList as $emailTo ) {  
                CRM_Utils_Mail::send( $emailFrom,
                                      "",
                                      $emailTo,
                                      $subject,
                                      $message,
                                      null,
                                      null
                                      );
            }
        }            
    }
    
    
    /**  
     * Given a contact id and a group id, returns the field values from the db
     * for this group and notify email only if group's notify field is
     * set and field values are not empty 
     *  
     * @params $gid      group id
     * @params $cid      contact id
     * @params $params   associative array 
     * @return array
     * @access public  
     */ 
    function checkFieldsEmptyValues( $gid,$cid,$params ) 
    {
        if ( $gid ) {
            require_once 'CRM/Core/BAO/UFGroup.php';
            if ( CRM_Core_BAO_UFGroup::filterUFGroups($gid, $cid) ){
                $values = array( );
                $fields = CRM_Core_BAO_UFGroup::getFields( $gid, false, CRM_Core_Action::VIEW );  
                CRM_Core_BAO_UFGroup::getValues( $cid, $fields, $values , false, $params );

                $count=0;//checks for array with only keys and not values
                foreach ($values as $value) {
                    if ($value) {
                        $count++;
                    }
                } 
                
                $email = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFGroup', $gid, 'notify' );
                $val = array(
                                'id'     => $gid,
                                'values' => $values,
                                'email'  => $email
                                );
                return ($count && $email) ? $val : false;
            }
        } 
    }

    /**  
     * Function to assign uf fields to template
     * 
     * @params int     $gid      group id
     * @params array   $values   associative array of fields 
     * @return void  
     * @access public  
     */ 
    function profileDisplay( $gid,$values,$template ) 
    {
        $groupTitle = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFGroup', $gid, 'title' );
        $template->assign( "grouptitle", $groupTitle );
        if ( count($values) ) {
            $template->assign( 'values', $values );
        }               
    }  
}
?>
