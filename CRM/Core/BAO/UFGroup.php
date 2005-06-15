<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 *
 */
class CRM_Core_BAO_UFGroup extends CRM_Core_DAO_UFGroup {

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
    static function getUFRegistrationFields( $action ) {
        $ufGroups =& CRM_Core_PseudoConstant::ufGroup( );

        $fields = array( );
        foreach ( $ufGroups as $id => $title ) {
            $subset = self::getUFFields( $id, true, $action );
            $fields = array_merge( $fields, $subset );
        }
        return $fields;
    }

    /**
     * get all the fields that belong to the group with the named title
     *
     * @param int $id       the id of the UF group
     * @param int $register are we only interested in registration fields
     * @param int $action   what action are we doing
     *
     * @return array the fields that belong to this title
     * @static
     * @access public
     */
    static function getUFFields( $id, $register = false, $action = null ) {
        $group = new CRM_Core_DAO_UFGroup( );

        $group->id = $id;
        if ( $group->find( true ) ) {
            $field = new CRM_Core_DAO_UFField( );
            $field->uf_group_id = $group->id;
            $field->is_active   = 1;
            if ( $register ) {
                $field->is_registration = 1;
            }
            $field->orderBy('weight', 'field_name');
            $field->find( );
            $fields = array( );
            $importableFields =& CRM_Contact_BAO_Contact::importableFields( );

            while ( $field->fetch( ) ) {
                if ( ( $field->is_view && $action == CRM_Core_Action::VIEW ) || ! $field->is_view ) {
                    $field->title      = $importableFields[$field->field_name]['title'];
                    $field->attributes = CRM_Core_DAO::makeAttribute( $importableFields[$field->field_name] );
                    if ( $field->field_name == 'StateProvince.name' ) {
                        $name = 'state_province_id';
                    } else if ( $field->field_name == 'Country.name' ) {
                        $name = 'country_id';
                    } else {
                        $name = $field->field_name;
                    }
                    $fields['edit[' . $name . ']'] =
                        array('name'        => $name,
                              'title'       => $importableFields[$field->field_name]['title'],
                              'attributes'  => CRM_Core_DAO::makeAttribute( $importableFields[$field->field_name] ),
                              'is_required' => $field->is_required,
                              'is_view'     => $field->is_view,
                              'is_match'    => $field->is_match,
                              'weight'      => $field->weight,
                              'help_post'   => $field->help_post,
                              'rule'        => CRM_Utils_Array::value( 'rule', $importableFields[$field->field_name] ),
                              );
                }
            }
            return $fields;
        }
        return null;
    }

    static function isValid( $title, $register = false, $action = null ) {
        $session =& CRM_Core_Session::singleton( );

        if ( $register ) {
            $controller =& new CRM_Core_Controller_Simple( 'CRM_UF_Form_Dynamic', 'Dynamic Form Creator', $action );
            $controller->set( 'gid'  , $group->id );
            $controller->set( 'id'   , $session->get( 'userID' ) );
            $controller->process( );
            return $controller->validate( 'Dynamic' );
        } else {
            // make sure we have a valid group
            $group = new CRM_Core_DAO_UFGroup( );
            
            $group->title     = $title;
            $group->domain_id = CRM_Core_Config::domainID( );
            
            if ( $group->find( true ) && $session->get( 'userID' ) ) {
                $controller =& new CRM_Core_Controller_Simple( 'CRM_UF_Form_Dynamic', 'Dynamic Form Creator', $action );
                $controller->set( 'gid'  , $group->id );
                $controller->set( 'id'   , $session->get( 'userID' ) );
                $controller->process( );
                return $controller->validate( );
            }
            return true;
        }
    }

    /**
     * get the html for the form that represents this particular group
     *
     * @param string $title the title of the group we are interested in
     * @param int    $action  the action of the form
     *
     * @return string       the html for the form
     * @static
     * @access public
     */
    static function getEditHTML( $title, $action = null, $register = false ) {
        $session =& CRM_Core_Session::singleton( );

        if ( $register ) {
            $controller =& new CRM_Core_Controller_Simple( 'CRM_UF_Form_Dynamic', 'Dynamic Form Creator', $action );
            $controller->set( 'id'      , $session->get( 'userID' ) );
            $controller->set( 'register', 1 );
            $controller->process( );
            $controller->setEmbedded( true );
            $controller->run( );

            $template =& CRM_Core_Smarty::singleton( );
            return trim( $template->fetch( 'CRM/UF/Form/Dynamic.tpl' ) );
        } else {
            // make sure we have a valid group
            $group = new CRM_Core_DAO_UFGroup( );
            
            $group->title     = $title;
            $group->domain_id = CRM_Core_Config::domainID( );
            
            if ( $group->find( true ) && $session->get( 'userID' ) ) {
                $controller =& new CRM_Core_Controller_Simple( 'CRM_UF_Form_Dynamic', 'Dynamic Form Creator', $action );
                $controller->set( 'gid'     , $group->id );
                $controller->set( 'id'      , $session->get( 'userID' ) ); 
                $controller->set( 'register', 0 );
                $controller->process( );
                $controller->setEmbedded( true );
                $controller->run( );
                
                $template =& CRM_Core_Smarty::singleton( );
                return trim( $template->fetch( 'CRM/UF/Form/Dynamic.tpl' ) );
            }
        }
        return '';
    }

    /**
     * get the html for the form that represents this particular group
     *
     * @param int    $action  the action of the form
     *
     * @return string       the html for the form
     * @static
     * @access public
     */
    static function getRegisterHTML( $action = null ) {
        $session =& CRM_Core_Session::singleton( );

        $controller =& new CRM_Core_Controller_Simple( 'CRM_UF_Form_Register', 'Registration Form Creator', $action );
        $controller->set( 'id'      , $session->get( 'userID' ) );
        $controller->process( );
        $controller->setEmbedded( true );
        $controller->run( );
            
        $template =& CRM_Core_Smarty::singleton( );
        return $template->fetch( 'CRM/UF/Form/Dynamic.tpl' );
    }

}

?>
