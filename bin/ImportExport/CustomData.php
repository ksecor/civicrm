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

class bin_ImportExport_CustomData {

    function __construct( ) {
    }

    function run( ) {
        require_once 'CRM/Core/DAO/CustomGroup.php';
        require_once 'CRM/Core/DAO/CustomField.php';
        require_once 'CRM/Core/DAO/OptionGroup.php';
        require_once 'CRM/Core/DAO/OptionValue.php';

        $xml = array( 'group'       => array( 'data'     => null           ,
                                              'name'     => 'CustomGroup'  ,
                                              'scope'    => 'CustomGroups',
                                              'required' => true,
                                              'map'      => array( ) ),
                      'optionGroup' => array( 'data'     => null           ,
                                              'name'     => 'OptionGroup'  ,
                                              'scope'    => 'OptionGroups',
                                              'required' => false,
                                              'map'      => array( ) ),
                      'optionValue' => array( 'data'     => null           ,
                                              'name'     => 'OptionValue'  ,
                                              'scope'    => 'OptionValues',
                                              'required' => false,
                                              'map'      => array( ) ),
                      'field'       => array( 'data'     => null           ,
                                              'name'     => 'CustomField'  ,
                                              'scope'    => 'CustomFields',
                                              'required' => true,
                                              'map'      => array( ) ),
                      );

        $group    = new CRM_Core_DAO_CustomGroup( );
        $group->find( );
        while ( $group->fetch( ) ) {
            $xml['group']['data'] .= $this->export( $group,
                                                    $xml['group']['name'] );
            $xml['group']['map'][$group->id] = $group->title;
        }

        $sql = "
SELECT g.*
FROM   civicrm_option_group g,
       civicrm_custom_field f
WHERE  f.option_group_id = g.id
";
        
        $optionGroup = new CRM_Core_DAO_OptionGroup( );
        $optionGroup->query( $sql );
        while ( $optionGroup->fetch( ) ) {
            $xml['optionGroup']['data'] .= $this->export( $optionGroup,
                                                          $xml['optionGroup']['name'] );
            $xml['optionGroup']['map'][$optionGroup->id] = $optionGroup->label;
        }

        $sql = "
SELECT v.*
FROM   civicrm_option_value v,
       civicrm_option_group g,
       civicrm_custom_field f
WHERE  v.option_group_id = g.id
  AND  f.option_group_id = g.id
";
        
        $optionValue = new CRM_Core_DAO_OptionValue( );
        $optionValue->query( $sql );
        while ( $optionValue->fetch( ) ) {
            $optionGroupLabel = $xml['optionGroup']['map'][$optionValue->option_group_id];
            $additional = "\n    <option_group_label>$optionGroupLabel<option_group_label>";
            $xml['optionValue']['data'] .= $this->export( $optionValue,
                                                          $xml['optionValue']['name'],
                                                          $additional );
        }

        $field    = new CRM_Core_DAO_CustomField( );
        $field->find( );
        while ( $field->fetch( ) ) {
            $customGroupTitle = $xml['group']['map'][$field->custom_group_id];
            $additional = "\n    <custom_group_title>$customGroupTitle</custom_group_title>";
            if ( $field->option_group_id ) {
                $optionGroupLabel = $xml['optionGroup']['map'][$field->option_group_id];
                $additional .= "\n    <option_group_label>$optionGroupLabel<option_group_label>";
            }
            $xml['field']['data'] .= $this->export( $field, 'CustomField', $additional );
        }

        $buffer = null;
        foreach ( array_keys( $xml ) as $key ) {
            if ( ! empty( $xml[$key]['data'] ) ) {
                $buffer .= "<{$xml[$key]['scope']}>\n{$xml[$key]['data']}</{$xml[$key]['scope']}>\n";
            } else if ( $xml[$key]['required'] ) {
                CRM_Core_Error::fatal( 'No records in DB for $key' );
            }
        }

        CRM_Core_Error::debug( $buffer );
        exit( );
        CRM_Utils_System::download( 'CustomGroupData.xml', 'text/plain', $buffer );
    }

    function export( $object, $objectName, $additional = null ) {
        $dbFields =& $object->fields( );

        $xml = "  <$objectName>";
        foreach ( $dbFields as $name => $value ) {
            // ignore all ids
            if ( $name == 'id' ||
                 substr( $name, -3, 3 ) == '_id' ) {
                continue;
            }
            if ( isset( $object->$name ) &&
                 $object->$name !== null ) {
                $xml .= "\n    <$name>{$object->$name}</$name>";
            } else {
                $xml   .= "\n    <$name></$name>";
            }
        }
        if ( $additional ) {
            $xml .= $additional;
        }
        $xml .= "\n  </$objectName>\n";
        return $xml;
    }

}
    

function run( ) {
    session_start( );

    require_once '../../civicrm.config.php';
    require_once 'CRM/Core/Config.php'; 
    $config =& CRM_Core_Config::singleton( );

    // this does not return on failure
    CRM_Utils_System::authenticateScript( true );

    $object = new bin_ImportExport_CustomData( );

    $object->run( );
}

run( );
