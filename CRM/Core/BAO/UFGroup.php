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
 * $Id$
 *
 */

/**
 *
 */
class CRM_Core_BAO_UFGroup extends CRM_Core_DAO_UFGroup {
    const 
        PUBLIC_VISIBILITY   = 1,
        ADMIN_VISIBILITY    = 2,
        LISTINGS_VISIBILITY = 4;

    /**
     * cache the match clause used in this transaction
     *
     * @var string
     */
    static $_matchClause = null;

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
     *
     * @return array the fields that are needed for registration
     * @static
     * @access public
     */
    static function getRegistrationFields( $action ) {
        $ufGroups =& CRM_Core_PseudoConstant::ufGroup( );

        $fields = array( );
        foreach ( $ufGroups as $id => $title ) {
            $subset = self::getFields( $id, true, $action );
            $fields = array_merge( $fields, $subset );
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
    static function getListingFields( $action, $visibility, $considerSelector = false ) {
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
        $group =& new CRM_Core_DAO_UFGroup( );

        $group->id = $id;
        if ( $group->find( true ) ) {
            $field =& new CRM_Core_DAO_UFField( );
            $field->uf_group_id = $group->id;
            $field->is_active   = 1;
            if ( $register ) {
                $field->is_registration = 1;
            }
            if ( $match ) {
                $field->is_match = 1;
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
                $field->whereAdd( implode( ' OR ' , $clause ) );
            }

            $field->orderBy('weight', 'field_name');
            $field->find( );
            $fields = array( );
            $importableFields =& CRM_Contact_BAO_Contact::importableFields( );
            $importableFields['group']['title'] = ts('Group(s)');
            $importableFields['group']['where'] = null;
            $importableFields['tag'  ]['title'] = ts('Tag(s)');
            $importableFields['tag'  ]['where'] = null;

            while ( $field->fetch( ) ) {
                if ( ( $field->is_view && $action == CRM_Core_Action::VIEW ) || ! $field->is_view ) {
                    $name = $field->field_name;
                    $fields[$name] =
                        array('name'             => $name,
                              'groupTitle'       => $group->title,
                              'groupHelpPre'     => $group->help_pre,
                              'groupHelpPost'    => $group->help_post,
                              'title'            => $importableFields[$field->field_name]['title'],
                              'where'            => $importableFields[$field->field_name]['where'],
                              'attributes'       => CRM_Core_DAO::makeAttribute( $importableFields[$field->field_name] ),
                              'is_required'      => $field->is_required,
                              'is_view'          => $field->is_view,
                              'is_match'         => $field->is_match,
                              'weight'           => $field->weight,
                              'help_post'        => $field->help_post,
                              'visibility'       => $field->visibility,
                              'in_selector'      => $field->in_selector,
                              'default'          => $field->default_value,
                              'rule'             => CRM_Utils_Array::value( 'rule', $importableFields[$field->field_name] ),
                              'options_per_line' => $importableFields[$field->field_name]['options_per_line']
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
        
        if ( ! self::$_matchClause ) {
            $ufGroups =& CRM_Core_PseudoConstant::ufGroup( );

            self::$_matchClause = array( );
            foreach ( $ufGroups as $id => $title ) {
                $subset = self::getFields( $id, false, CRM_Core_Action::VIEW, true );
                self::$_matchClause = array_merge( self::$_matchClause, $subset );
            }
        }

        return CRM_Contact_BAO_Query::getWhereClause( $params, self::$_matchClause, $tables, true );
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
        $clause = self::getMatchClause( $params, $tables, $flatten );
        if ( ! $clause ) {
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
        $returnProperties = array( );
        foreach ( $fields as $name => $dontCare ) {
            $returnProperties[$name] = 1;
        }
        $options = array( );
        $contact = CRM_Contact_BAO_Contact::contactDetails( $id, $options, $returnProperties );
        if ( ! $contact ) {
            return;
        }

        $params = array( );
        foreach ( $fields as $name => $field ) {
            $objName = $field['name'];

            $index = $field['title'];
            if ( $objName == 'state_province' ) {
                $values[$index] = null;
                if ( $contact->state_province ) {
                    $values[$index] = $contact->state_province;
                    $params[$index] = $contact->state_province_id;
                }
            } else if ( $objName == 'country' ) {
                $values[$index] = null;
                if ( $contact->country ) {
                    $values[$index] = $contact->country;
                    $params[$index] = $contact->country_id;
                }
            } else if ( $objName == 'group' ) {
                $groups = CRM_Contact_BAO_GroupContact::getContactGroup( $id, 'Added' );
                $title = array( );
                foreach ( $groups as $g ) {
                    if ( $g['visibility'] != 'User and User Admin Only' ) {
                        $title[] = $g['title'];
                    }
                }
                $values[$index] = implode( ', ', $title );
                $params[$index] = '';
            } else if ( $objName == 'tag' ) {
                $entityTags =& CRM_Core_BAO_EntityTag::getTag('civicrm_contact', $id );
                $allTags    =& CRM_Core_PseudoConstant::tag();
                $title = array( );
                foreach ( $entityTags as $tagId ) {
                    $title[] = $allTags[$tagId];
                }
                $values[$index] = implode( ', ', $title );
                $params[$index] = '';
            } else if ( $cfID = CRM_Core_BAO_CustomField::getKeyID($objName)) {
                $params[$index] = $contact->$objName;
                $values[$index] = CRM_Core_BAO_CustomField::getDisplayValue( $contact->$objName, $cfID, $options );
            } else {
                $values[$index] = $contact->$objName;
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
                                              'reset=1&' . 
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

  public static function del($id) 
    { 
        //check wheter this group contains  any profile fields
        $profileField = & new CRM_Core_DAO_UFField();
        $profileField->uf_group_id = $id;
        $profileField->find();
        while($profileField->fetch()) {
            return false;
            
        }
        //delete profile group
        $group = & new CRM_Core_DAO_UFGroup();
        $group->id = $id; 
        $group->delete();
        return true;
    }



}

?>
