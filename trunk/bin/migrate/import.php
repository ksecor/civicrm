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

class bin_migrate_import {

    function __construct( ) {
    }

    function run( $file ) {
        require_once 'CRM/Core/DAO/CustomGroup.php';
        require_once 'CRM/Core/DAO/CustomField.php';
        require_once 'CRM/Core/DAO/OptionValue.php';

        // read xml file
        $dom = DomDocument::load( $file );
        $dom->xinclude( );
        $xml = simplexml_import_dom( $dom );

        $idMap = array( 'custom_group' => array( ),
                        'option_group' => array( ) );

        // first create option groups and values if any
        $this->optionGroups( $xml, $idMap );
        $this->optionValues( $xml, $idMap );

        // now create custom groups
        $this->customGroups( $xml, $idMap );
        $this->customFields( $xml, $idMap );

        // now create profile groups
        $this->profileGroups( $xml, $idMap );
        $this->profileFields( $xml, $idMap );
    }

    function copyData( &$dao, &$xml, $save = false ) {
        $fields =& $dao->fields( );
        foreach ( $fields as $name => $dontCare ) {
            if ( isset( $xml->$name ) ) {
                $value = (string ) $xml->$name;
                $value = str_replace( ":;:;:;",
                                      CRM_Core_DAO::VALUE_SEPARATOR,
                                      $value );
                $dao->$name = $value;
            }
        }
        if ( $save ) {
            $dao->save( );
        }
    }

    function optionGroups( &$xml, &$idMap ) {
        require_once 'CRM/Core/DAO/OptionGroup.php';
        foreach ( $xml->OptionGroups as $optionGroupsXML ) {
            foreach ( $optionGroupsXML->OptionGroup as $optionGroupXML ) {
                $optionGroup = new CRM_Core_DAO_OptionGroup( );
                $this->copyData( $optionGroup, $optionGroupXML, true );
                $idMap['option_group'][$optionGroup->label] = $optionGroup->id;
            }
        }
    }

    function optionValues( &$xml, &$idMap ) {
        require_once 'CRM/Core/DAO/OptionValue.php';
        foreach ( $xml->OptionValues as $optionValuesXML ) {
            foreach ( $optionValuesXML->OptionValue as $optionValueXML ) {
                $optionValue = new CRM_Core_DAO_OptionValue( );
                $this->copyData( $optionValue, $optionValueXML, false );
                $optionValue->option_group_id =
                    $idMap['option_group'][(string ) $optionValueXML->option_group_label];
                $optionValue->save( );
            }
        }
    }

    function customGroups( &$xml, &$idMap ) {
        require_once 'CRM/Core/BAO/CustomGroup.php';
        require_once 'CRM/Utils/String.php';
        foreach ( $xml->CustomGroups as $customGroupsXML ) {
            foreach ( $customGroupsXML->CustomGroup as $customGroupXML ) {
                $customGroup = new CRM_Core_DAO_CustomGroup( );
                $this->copyData( $customGroup, $customGroupXML, true );

                // fix table name
                $customGroup->table_name = 
                    "civicrm_value_" .
                    strtolower( CRM_Utils_String::munge( $customGroup->title, '_', 32 ) ) .
                    "_{$customGroup->id}";
                $customGroup->save( );

                CRM_Core_BAO_CustomGroup::createTable( $customGroup );
                $idMap['custom_group'][$customGroup->title] = $customGroup->id;
            }
        }
    }

    function customFields( &$xml, &$idMap ) {
        require_once 'CRM/Core/BAO/CustomField.php';
        foreach ( $xml->CustomFields as $customFieldsXML ) {
            foreach ( $customFieldsXML->CustomField as $customFieldXML ) {
                $customField = new CRM_Core_DAO_CustomField( );
                $this->copyData( $customField, $customFieldXML, false );
                $customField->custom_group_id =
                    $idMap['custom_group'][(string ) $customFieldXML->custom_group_title];
                if ( isset( $customFieldXML->option_group_label ) ) {
                    $customField->option_group_id =
                        $idMap['option_group'][(string ) $customFieldXML->option_group_label];
                }
                $customField->save( );

                // fix column name
                $customField->table_name = 
                    strtolower( CRM_Utils_String::munge( $customField->label, '_', 32 ) ) .
                    "_{$customField->id}";
                $customField->save( );

                CRM_Core_BAO_CustomField::createField( $customField, 'add' );
            }
        }
    }

    function profileGroups( &$xml, &$idMap ) {
        require_once 'CRM/Core/DAO/UFGroup.php';
        foreach ( $xml->ProfileGroups as $profileGroupsXML ) {
            foreach ( $profileGroupsXML->ProfileGroup as $profileGroupXML ) {
                $profileGroup = new CRM_Core_DAO_UFGroup( );
                $this->copyData( $profileGroup, $profileGroupXML, true );
                $idMap['profile_group'][$profileGroup->title] = $profileGroup->id;
            }
        }
    }

    function profileFields( &$xml, &$idMap ) {
        require_once 'CRM/Core/DAO/UFField.php';
        foreach ( $xml->ProfileFields as $profileFieldsXML ) {
            foreach ( $profileFieldsXML->ProfileField as $profileFieldXML ) {
                $profileField = new CRM_Core_DAO_UFField( );
                $this->copyData( $profileField, $profileFieldXML, false );
                $profileField->uf_group_id = $idMap['profile_group'][(string ) $profileFieldXML->profile_group_title];
                $profileField->save( );
            }
        }
    }

}
    

function run( ) {
    session_start( );

    require_once '../../civicrm.config.php';
    require_once 'CRM/Core/Config.php'; 
    $config =& CRM_Core_Config::singleton( );

    // this does not return on failure
    CRM_Utils_System::authenticateScript( true );

    $import = new bin_migrate_import( );

    $import->run( $_GET['file'] );
}

run( );
