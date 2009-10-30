<?php  
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
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

require_once 'CiviTest/CiviUnitTestCase.php';
require_once 'api/v2/ActivityContact.php';
require_once 'CRM/Core/BAO/CustomGroup.php';

class api_v2_ActivityContactTest extends CiviUnitTestCase
{

    public function setUp()
    {
        //  Connect to the database
        parent::setUp();

        //  Truncate the tables
        $op = new PHPUnit_Extensions_Database_Operation_Truncate( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__) . '/../../CiviTest/truncate-option.xml') );
 
        //  Insert a row in civicrm_contact creating contact 17
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/dataset/contact_17.xml') );
 
        //  Insert a row in civicrm_option_group creating option group
        //  activity_type 
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset/option_group_activity_type.xml') );
 
        //  Insert a row in civicrm_option_value creating
        //  activity_type 5
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/dataset/option_value_activity_5.xml') );

        //  Insert rows in civicrm_activity creating activities 4 and
        //  13
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/dataset/activity_4_13.xml') );
    }

    /**
     *  Test civicrm_activities_contact_get()
     */
    function testActivitiesContactGet()
    {

        //  Get activities associated with contact 17
        $params = array( 'contact_id' => 17 );
        $result = civicrm_activity_contact_get( $params );
        $this->assertEquals( 0, $result['is_error'],
                             "Error message: " . $result['error_message'] );
        $this->assertEquals( 2, count( $result['result'] ),
                             'In line ' . __LINE__ );
        $this->assertEquals( 5, $result['result'][4]['activity_type_id'] ,
                             'In line ' . __LINE__ );
        $this->assertEquals( 'Test activity type',
                             $result['result'][4]['activity_name'],
                             'In line ' . __LINE__ );
        $this->assertEquals( 'Test activity type',
                             $result['result'][13]['activity_name'],
                             'In line ' . __LINE__ );
    }

    /**
     * check civicrm_activities_contact_get() with empty array
     */
    function testActivityContactGetEmpty( )
    {
        $params = array( );
        $result = civicrm_activity_contact_get( $params );
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }
   
    /**
     *  Test  civicrm_activity_contact_get() with missing source_contact_id
     */
    function testActivitiesContactGetWithInvalidParameter( )
    {
        $params = null;
        $result = civicrm_activity_contact_get( $params );
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }

    /**
     *  Test civicrm_activity_contact_get() with invalid Contact Id
     */
    function testActivitiesContactGetWithInvalidContactId( )
    {
        $params = array( 'contact_id' => null );
        $result = civicrm_activity_contact_get( $params );
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );

        $params = array( 'contact_id' => 'contact' );
        $result = civicrm_activity_contact_get( $params );
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
        
        $params = array( 'contact_id' => 2.4 );
        $result = civicrm_activity_contact_get( $params );
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }

} 