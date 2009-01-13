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

require_once 'CiviTestCase.php';
require_once 'Custom.php';

class BAO_Core_CustomValue extends CiviTestCase 
{

    function get_info( ) 
    {
        return array(
                     'name'        => 'CustomValue BAOs',
                     'description' => 'Test all Core_BAO_CustomValue methods.',
                     'group'       => 'CiviCRM BAO Tests',
                     );
    }

    function testTypeCheckWithValidInput( )
    {

        $values = array( );
        $values = array( 'Memo'             => 'Test1',
                         'String'           => 'Test',
                         'Int'              =>  1,
                         'Float'            =>  10.00,
                         'Date'             => '2008-06-24',
                         'Boolean'          =>  True,
                         'StateProvince'    => 'California',
                         'Country'          =>  'US',
                         'Link'             => 'http://civicrm.org'
                         );
        require_once 'CRM/Core/BAO/CustomValue.php';
        foreach ( $values as $type => $value ) {
            $valid =  CRM_Core_BAO_CustomValue::typecheck( $type, $value );
            if ( $type == 'Date' ) {
                $this->assertEqual( $valid, '2008-06-24','Checking type '.$type.' for returned CustomField Type.' ); 
            } else {
                $this->assertEqual( $valid, 'true','Checking type '.$type.' for returned CustomField Type.' ); 
            }
        } 
    }     
    
    function testTypeCheckWithInvalidInput( )
    {
        $values = array( );
        $values = array( 'check1'  => 'chk' );
        foreach ( $values as $type => $value ) {
            $valid =  CRM_Core_BAO_CustomValue::typecheck( $type, $value );  
            $this->assertEqual( $valid, null , 'Checking invalid type for returned CustomField Type.' ); 
        }
    }
    
    function testTypeCheckWithWrongInput( )
    {
        $values = array( );
        $values = array ( 'String'   => 1 ,
                          'Boolean'  => 'US'
                          );
        require_once 'CRM/Core/BAO/CustomValue.php';
        foreach ( $values as $type => $value ) {
            $valid =  CRM_Core_BAO_CustomValue::typecheck( $type, $value );
            $this->assertEqual( $valid, null, 'Checking type '.$type.' for returned CustomField Type.' ); 
        }

    }

    function testTypeToFieldWithValidInput ( )
    {
        $values = array( );
        $values = array( 'String'        => 'char_data',
                         'File'          => 'char_data',
                         'Boolean'       => 'int_data',
                         'Int'           => 'int_data',
                         'StateProvince' => 'int_data',
                         'Country'       => 'int_data',  
                         'Float'         => 'float_data',
                         'Memo'          => 'memo_data',
                         'Money'         => 'decimal_data',
                         'Date'          => 'date_data',
                         'Link'          => 'char_data'
                        );

        require_once 'CRM/Core/BAO/CustomValue.php';
        foreach ( $values as $type => $value ) {
            $valid =  CRM_Core_BAO_CustomValue::typeToField( $type );
            $this->assertEqual( $valid, $value, 'Checking type '.$type.' for returned CustomField Type.'); 
        }
    }

    function testTypeToFieldWithWrongInput ( )
    {
      $values = array( );
      $values = array( 'String'        => 'memo_data',
                       'File'          => 'date_data',
                       'Boolean'       => 'char_data'
                       );
      require_once 'CRM/Core/BAO/CustomValue.php';
      foreach ( $values as $type => $value ) {
          $valid =  CRM_Core_BAO_CustomValue::typeToField( $type );
          $this->assertNotEqual( $valid, $value, 'Checking type '.$type.' for returned CustomField Type.'); 
      }
      
    }
    
    function testFixFieldValueOfTypeMemo ( )
    {
        $customGroup = Custom::createGroup( array(), 'Individual' );
     
        $fields      = array (
                              'groupId'  => $customGroup->id,
                              'dataType' => 'Memo',
                              'htmlType' => 'TextArea'
                              );
        
        $customField = Custom::createField( array(), $fields );
        
        $custom = 'custom_'.$customField->id;
        $params = array ( );
        $params = array (   'email'  => 'abc@webaccess.co.in',
                            $custom  => 'note'
                            );
        
        require_once 'CRM/Core/BAO/CustomValue.php';
        CRM_Core_BAO_CustomValue::fixFieldValueOfTypeMemo( $params );
        $this->assertEqual( $params[$custom], '%note%', 'Checking the returned value of type Memo.');        
        
        Custom::deleteField( $customField );        
        Custom::deleteGroup( $customGroup );    
    }

    function testFixFieldValueOfTypeMemoWithEmptyParams ( )
    {
        $params = array ( );
        require_once 'CRM/Core/BAO/CustomValue.php';
        CRM_Core_BAO_CustomValue::fixFieldValueOfTypeMemo( $params );
        $this->assertEqual( $params, null, 'Checking the returned value of type Memo.');  
    }

}
