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
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

class CRM_Case_XMLProcessor {

    static protected $_xml;

    function retrieve( $caseType ) {
        require_once 'CRM/Utils/String.php';
        require_once 'CRM/Utils/Array.php';

        // trim all spaces from $caseType
        $caseType = CRM_Utils_String::munge( $caseType, '', 0 );

        if ( ! CRM_Utils_Array::value( $caseType, self::$_xml ) ) {
            if ( ! self::$_xml ) {
                self::$_xml = array( );
            }

            // ensure that the file exists
            $fileName = implode( DIRECTORY_SEPARATOR,
                                 array( dirname( __FILE__ ),
                                        'xml',
                                        'configuration',
                                        "$caseType.xml" ) );
            if ( ! file_exists( $fileName ) ) {
                CRM_Core_Error::statusBounce( ts("Unable to locate configuration file for the referenced case type. Expecting: '%1'", 
                                                 array( 1 => $fileName ) ) );
                return false;
            }

            // read xml file
            $dom = DomDocument::load( $fileName );
            $dom->xinclude( );
            self::$_xml[$caseType] = simplexml_import_dom( $dom );
        }
        return self::$_xml[$caseType];
    }

    function &allActivityTypes( ) {
        static $activityTypes = null;
        require_once 'CRM/Core/Component.php';
        if ( ! $activityTypes ) {
            require_once 'CRM/Case/PseudoConstant.php';
            $activityTypes = CRM_Case_PseudoConstant::categoryTree( CRM_Core_Component::getComponentID( 'CiviCase' ) );
        }
        return $activityTypes; 
    }

    function &allRelationshipTypes( ) {
        static $relationshipTypes = null;

        if ( ! $relationshipTypes ) {
            require_once 'CRM/Core/PseudoConstant.php';
            $relationshipInfo  = CRM_Core_PseudoConstant::relationshipType( );

            $relationshipTypes = array( );
            foreach ( $relationshipInfo as $id => $info ) {
                $relationshipTypes[$id] = $info['name_b_a'];
            }
        }

        return $relationshipTypes;
    }

}