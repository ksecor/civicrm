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
 *
 */

/**
 * This class contains all the function that are called using AJAX (dojo)
 */
class CRM_Core_Page_AJAX_Mapper
{
    static function hack( &$config ) {

        $items = array( 'first' => 1,
                        'two'   => 2,
                        'three' => 3,
                        );
        
        $name = trim( CRM_Utils_Type::escape( $_GET['name'],
                                              'String' ) );      
        $name = str_replace('*', '', $name);        
        $pattern = '/^' . $name .'/i';
        
        $elements = array( );
        if ( is_array($items) ) {
            foreach ( $items as $key => $val ) {
                if ( preg_match($pattern, $key) ) {
                    $elements[]= array( 'name'  => $key, 
                                        'value' => $val );
                }
            }
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'value' );
    }

    static function select( &$config ) {
        $index = CRM_Utils_Request::retrieve( 'index', 'Integer',
                                              CRM_Core_DAO::$_nullObject, false, 1 );
        
        switch ( $index ) {
        case 1:
            return self::selectOne( $config );
        case 2:
            return self::selectTwo( $config );
        case 3:
            return self::selectThree( $config );
        case 4:
            return self::selectFour( $config );
        }

        return;
    }

    static function &allItems( ) {
        $components = array( 'CiviContribute' => array( 'Contribution', ts( 'Contribution' ) ),
                             'CiviEvent'      => array( 'Participant' , ts( 'Participant'  ) ),
                             'CiviMember'     => array( 'Membership'  , ts( 'Membership'   ) ) );
        
        $allItems = CRM_Core_SelectValues::contactType( );
        unset( $allItems[''] );

        foreach ( $components as $key => $value ) {
            if ( CRM_Core_Permission::access( $key ) ) {
                $allItems[$value[0]] = $value[1];
            }
        }

        return $allItems;
    }

    static function output( &$items, $filter = true ) {
        if ( $filter ) {
            $name = CRM_Utils_Request::retrieve( 'name', 'String',
                                                 CRM_Core_DAO::$_nullObject, false, '' );
            $name = trim( $name );
            $name = str_replace( '*', '', $name );
            if ( empty( $name ) ) {
                $returnItems = $items;
            } else {
                $pattern = '/^' . $name .'/i';
                $returnItems = array( );
                foreach ( $items as $key => $value ) {
                    if ( preg_match( $pattern, $value ) ) {
                        $returnItems[$key] = $value;
                    }
                }
            }
        } else {
            $returnItems = $items;
        }

        $elements = array( );
        foreach ( $returnItems as $key => $value ) {
            $elements[]= array( 'name'  => $key, 
                                'value' => $value );
        }

        require_once "CRM/Utils/JSON.php";
        echo CRM_Utils_JSON::encode( $elements, 'value' );
    }

    static function &fields( ) {
        $node1 = trim( CRM_Utils_Type::escape( $_GET['node1'],
                                               'String' ) );
        
        $allItems =& self::allItems( );
        $fields = array( );
        if ( ! array_key_exists( $node1, $allItems ) ) {
            return $fields;
        }
        
        switch ( $node1 ) {
        case 'Individual':
        case 'Household':
        case 'Organization':
            require_once 'CRM/Contact/BAO/Contact.php';
            $fields =& CRM_Contact_BAO_Contact::exportableFields( $node1, false, true );
            break;

        case 'Contribution':
            require_once 'CRM/Contribute/BAO/Contribution.php';
            $fields =& CRM_Contribute_BAO_Contribution::exportableFields();
            break;

        case 'Event':
            require_once 'CRM/Event/BAO/Query.php';
            $fields =& CRM_Event_BAO_Query::getParticipantFields( true );
            break;

        case 'Member':
            require_once 'CRM/Member/BAO/Membership.php';
            $fields =& CRM_Member_BAO_Membership::getMembershipFields( );
            break;

        default:
            break;
        }

        return $fields;
    }

    static function selectOne( &$config ) {
        $allItems =& self::allItems( );

        self::output( $allItems, true );
    }

    function selectTwo( &$config ) {
        $fields =& self::fields( );

        $items = array( );
        foreach ( $fields as $key => $value ) {
            $items[$key] = $value['title'];
        }

        self::output( $items, true );
    }

    static function selectThree( &$config ) {
        $fields =& self::fields( );

        $items = array( );
        
        $node2 = trim( CRM_Utils_Type::escape( $_GET['node2'],
                                               'String' ) );

        $locationTypes       =& CRM_Core_PseudoConstant::locationType();

        require_once 'CRM/Core/BAO/LocationType.php';
        $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();
            
        /* FIXME: dirty hack to make the default option show up first.  This
         * avoids a mozilla browser bug with defaults on dynamically constructed
         * selector widgets. */
        
        if ( $defaultLocationType ) {
            $defaultLocation = $locationTypes[$defaultLocationType->id];
            unset( $locationTypes[$defaultLocationType->id] );
            $locationTypes = 
                array( $defaultLocationType->id => $defaultLocation ) + 
                $locationTypes;
        }
        
        $locationTypes = array (' ' => ts('Primary')) + $locationTypes;

        if ( empty( $node2 ) ||
             ! array_key_exists( $node2, $fields ) ||
             ! isset( $fields[$node2]['hasLocationType'] ) ) {
            $items = array( );
        } else {
            $items = $locationTypes;
        }
        
        self::output( $items );
    }

    static function selectFour( &$config ) {
        $node2 = trim( CRM_Utils_Type::escape( $_GET['node2'],
                                               'String' ) );

        if ( $node2 != 'phone' ) {
            return;
        }

        $phoneTypes = CRM_Core_SelectValues::phoneType( );
        unset( $phoneTypes[''] );

        asort( $phoneTypes );

        self::output( $phoneTypes );
        
    }

}
