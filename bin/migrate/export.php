<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

class bin_migrate_export {

    protected $_xml;

    function __construct( ) {
        $this->_xml =
            array( 'customGroup'  => array( 'data'     => null           ,
                                            'name'     => 'CustomGroup'  ,
                                            'scope'    => 'CustomGroups',
                                            'required' => true,
                                            'map'      => array( ) ),
                   'customField'  => array( 'data'     => null           ,
                                            'name'     => 'CustomField'  ,
                                            'scope'    => 'CustomFields',
                                            'required' => true,
                                            'map'      => array( ) ),
                   'optionGroup'  => array( 'data'     => null           ,
                                            'name'     => 'OptionGroup'  ,
                                            'scope'    => 'OptionGroups',
                                            'required' => false,
                                            'map'      => array( ) ),
                   'optionValue'  => array( 'data'     => null           ,
                                            'name'     => 'OptionValue'  ,
                                            'scope'    => 'OptionValues',
                                            'required' => false,
                                            'map'      => array( ) ),
                   'profileGroup' => array( 'data'     => null           ,
                                            'name'     => 'ProfileGroup'  ,
                                            'scope'    => 'ProfileGroups',
                                            'required' => false,
                                            'map'      => array( ) ),
                   'profileField' => array( 'data'     => null           ,
                                            'name'     => 'ProfileField'  ,
                                            'scope'    => 'ProfileFields',
                                            'required' => false,
                                            'map'      => array( ) ),
                   );
    }

    function run( ) {
        $sql = "
SELECT distinct(g.id), g.*
FROM   civicrm_option_group g,
       civicrm_custom_field f
WHERE  f.option_group_id = g.id
";
        $this->fetch( 'optionGroup',
                      'CRM_Core_DAO_OptionGroup',
                      $sql,
                      array( 'id', 'label' ) );

        $sql = "
SELECT v.*
FROM   civicrm_option_value v,
       civicrm_option_group g,
       civicrm_custom_field f
WHERE  v.option_group_id = g.id
  AND  f.option_group_id = g.id
";
        $this->fetch( 'optionValue',
                      'CRM_Core_DAO_OptionValue',
                      $sql,
                      null,
                      array( array( 'optionGroup', 'option_group_id', 'option_group_label' ) ) );

        $this->fetch( 'customGroup',
                      'CRM_Core_DAO_CustomGroup',
                      null,
                      array( 'id', 'title' ) );

        $this->fetch( 'customField',
                      'CRM_Core_DAO_CustomField',
                      null,
                      null,
                      array( array( 'optionGroup', 'option_group_id', 'option_group_label' ),
                             array( 'customGroup', 'custom_group_id', 'custom_group_title' ) ) );

        $this->fetch( 'profileGroup',
                      'CRM_Core_DAO_UFGroup',
                      null,
                      array( 'id', 'title' ),
                      null );

        $this->fetch( 'profileField',
                      'CRM_Core_DAO_UFField',
                      null,
                      null,
                      array( array( 'profileGroup', 'uf_group_id', 'profile_group_title' ) ) );

        $buffer  = '<?xml version="1.0" encoding="iso-8859-1" ?>';
        $buffer .= "\n\n<CustomData>\n";
        foreach ( array_keys( $this->_xml ) as $key ) {
            if ( ! empty( $this->_xml[$key]['data'] ) ) {
                $buffer .= "  <{$this->_xml[$key]['scope']}>\n{$this->_xml[$key]['data']}  </{$this->_xml[$key]['scope']}>\n";
            } else if ( $this->_xml[$key]['required'] ) {
                CRM_Core_Error::fatal( "No records in DB for $key" );
            }
        }
        $buffer .= "</CustomData>\n";

        CRM_Utils_System::download( 'CustomGroupData.xml', 'text/plain', $buffer );
    }

    function fetch( $groupName, $daoName, $sql = null, $map = null, $add = null ) {
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $daoName ) . '.php' );

        eval( "\$dao = new $daoName( );" );
        if ( $sql ) {
            $dao->query( $sql );
        } else {
            $dao->find( );
        }

        while ( $dao->fetch( ) ) {
            $additional = null;
            if ( $add ) {
                foreach ( $add as $filter ) {
                    if ( isset( $dao->{$filter[1]} ) ) {
                        $label = $this->_xml[$filter[0]]['map'][$dao->{$filter[1]}];
                        $additional .= "\n      <{$filter[2]}>{$label}</{$filter[2]}>";
                    }
                }
            }
            $this->_xml[$groupName]['data'] .= $this->exportDAO( $dao,
                                                                 $this->_xml[$groupName]['name'],
                                                                 $additional );
            if ( $map ) {
                $this->_xml[$groupName]['map'][$dao->{$map[0]}] = $dao->{$map[1]};
            }
        }
        
    }

    function exportDAO( $object, $objectName, $additional = null ) {
        $dbFields =& $object->fields( );

        $xml = "    <$objectName>";
        foreach ( $dbFields as $name => $dontCare ) {
            // ignore all ids
            if ( $name == 'id' ||
                 substr( $name, -3, 3 ) == '_id' ) {
                continue;
            }
            if ( isset( $object->$name ) &&
                 $object->$name !== null ) {
                $value = str_replace( CRM_Core_DAO::VALUE_SEPARATOR,
                                      ":;:;:;",
                                      $object->$name );
                $xml .= "\n      <$name>$value</$name>";
            }
        }
        if ( $additional ) {
            $xml .= $additional;
        }
        $xml .= "\n    </$objectName>\n";
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

    $export = new bin_migrate_export( );

    $export->run( );
}

run( );
