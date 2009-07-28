<?php  // vim: set si ai expandtab tabstop=4 shiftwidth=4 softtabstop=4:

/**
 *  File for the TestActivity class
 *
 *  (PHP 5)
 *  
 *   @author Walt Haas <walt@dharmatech.org> (801) 534-1262
 *   @copyright Copyright CiviCRM LLC (C) 2009
 *   @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html
 *              GNU Affero General Public License version 3
 *   @version   $Id$
 *   @package   CiviCRM
 *
 *   This file is part of CiviCRM
 *
 *   CiviCRM is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Affero General Public License
 *   as published by the Free Software Foundation; either version 3 of
 *   the License, or (at your option) any later version.
 *
 *   CiviCRM is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Affero General Public License for more details.
 *
 *   You should have received a copy of the GNU Affero General Public
 *   License along with this program.  If not, see
 *   <http://www.gnu.org/licenses/>.
 */

/**
 *  Include class definitions
 */
require_once 'PHPUnit/Extensions/Database/TestCase.php';
require_once 'PHPUnit/Extensions/Database/DataSet/FlatXmlDataSet.php';
require_once 'PHPUnit/Extensions/Database/DataSet/XmlDataSet.php';
require_once 'PHPUnit/Extensions/Database/DataSet/QueryDataSet.php';
require_once 'AllTests.php';
require_once 'api/v2/Activity.php';

/**
 *  Test APIv2 civicrm_activity_* functions
 *
 *  @package   CiviCRM
 */
class api_v2_TestActivity extends PHPUnit_Extensions_Database_TestCase
{
    /**
     *  Database connection
     *
     *  @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    private $dbconn;

    /**
     *  Create database connection for this instance
     *
     *  @return PHPUnit_Extensions_Database_DB_IDatabaseConnection connection
     */
    protected function getConnection()
    {
        AllTests::installDB();
        return $this->createDefaultDBConnection(AllTests::$utils->pdo,
                                             'civicrm_tests_dev');
    }

    /**
     *  Required implementation of abstract method
     */
    protected function getDataSet() { }

    /**
     *  Test setup for every test
     *
     *  Connect to the database, truncate the tables that will be used
     *  and redirect stdin to a temporary file
     */
    public function setUp()
    {
        $this->dbconn = $this->getConnection();
        //  Use a temporary file for STDIN
        $GLOBALS['stdin'] = tmpfile( );
        if ( $GLOBALS['stdin'] === false ) {
            echo "Couldn't open temporary file\n";
            exit(1);
        }

        //  Truncate the tables
        $op = new PHPUnit_Extensions_Database_Operation_Truncate( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__) . '/truncate.xml') );
 
        //  Insert a row in civicrm_contact creating contact 17
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/contact_17.xml') );
 
        //  Insert a row in civicrm_option_group creating option group
        //  activity_type 
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/option_group_activity_type.xml') );
 
        //  Insert a row in civicrm_option_value creating
        //  activity_type 5
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/option_value_activity_5.xml') );
    }

    /**
     *  If tearDown() isn't defined, getConnection() and getDataSet()
     *  will be called automatically during teardown
     */
    public function tearDown() { }
    
    /**
     * check with empty array
     */
    function testActivityCreateEmpty( )
    {
        $params = array( );
        $result = & civicrm_activity_create($params);
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * check if required fields are not passed
     */
    function testActivityCreateWithoutRequired( )
    {
        $params = array(
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );
        
        $result = & civicrm_activity_create($params);
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * check with incorrect required fields
     */
    function testActivityCreateWithIncorrectData( )
    {
        $params = array(
                        'activity_name'       => 'Breaking Activity',
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );

        $result = & civicrm_activity_create($params);
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * check with incorrect required fields
     */
    function testActivityCreateWithIncorrectContactId( )
    {
        $params = array(
                        'activity_name'       => 'Meeting',
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );

        $result = & civicrm_activity_create($params);
        
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * this should create activity
     */
    function testActivityCreate( )
    {
        $params = array(
                        'source_contact_id'   => 17,
                        'subject'             => 'Discussion on Apis for v2',
                        'activity_date_time'  => date('Ymd'),
                        'duration_hours'      => 30,
                        'duration_minutes'    => 20,
                        'location'            => 'Pensulvania',
                        'details'             => 'a test activity',
                        'status_id'           => 1,
                        'activity_name'       => 'Test activity type'
                        );
        
        $result = & civicrm_activity_create( $params );
        $this->assertEquals( $result['is_error'], 0,
                             "Error message: " . $result['error_message'] );
        $this->assertEquals( $result['source_contact_id'], 17 );
        $this->assertEquals( $result['subject'], 'Discussion on Apis for v2' );
        $this->assertEquals( $result['activity_date_time'], date('Ymd') );
        $this->assertEquals( $result['location'], 'Pensulvania' );
        $this->assertEquals( $result['details'], 'a test activity' );
        $this->assertEquals( $result['status_id'], 1 );
    }

    /**
     * check activity deletion with empty params
     */
    function testDeleteActivityForEmptyParams( )
    {
        $params = array( );
        $result =& civicrm_activity_delete($params);
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * check activity deletion without activity id
     */
    function testDeleteActivityWithoutId( )
    {
        $params = array('activity_name' => 'Meeting');
        $result =& civicrm_activity_delete($params);
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * check activity deletion without activity type
     */
    function testDeleteActivityWithoutActivityType( )
    {
        $params = array( 'id' => $this->_activityId );
        $result =& civicrm_activity_delete( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }
    
    /**
     * check activity deletion with incorrect data
     */
    function testDeleteActivityWithIncorrectActivityType( )
    {
        $params = array( 'id'            => $this->_activityId,
                         'activity_name' => 'Test Activity'
                         );

        $result =& civicrm_activity_delete( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * check activity deletion with correct data
     */
    function testDeleteActivity( )
    {
        //  Insert rows in civicrm_activity creating activities 4 and
        //  13 
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/activity_4_13.xml') );
        $params = array( 'id' => 13,
                         'activity_type_id' => 5 );
        
        $result =& civicrm_activity_delete($params);
        $this->assertEquals( $result['is_error'], 0,
                             "Error message: " . $result['error_message'] );
    }
    
    /**
     * check with empty array
     */
    function testActivityUpdateEmpty( )
    {
        $params = array( );
        $result =& civicrm_activity_update($params);
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * check if required fields are not passed
     */
    function testActivityUpdateWithoutRequired( )
    {
        $params = array(
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );
        
        $result =& civicrm_activity_update($params);
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * check with incorrect required fields
     */
    function testActivityUpdateWithIncorrectData( )
    {
        $params = array(
                        'activity_name'       => 'Meeting',
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );

        $result =& civicrm_activity_update($params);
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * check with incorrect required fields
     */
    function testActivityUpdateWithIncorrectId( )
    {
        $params = array( 'id'                  => 'lets break it',
                         'activity_name'       => 'Meeting',
                         'subject'             => 'this case should fail',
                         'scheduled_date_time' => date('Ymd')
                         );

        $result =& civicrm_activity_update($params);
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * check with incorrect required fields
     */
    function testActivityUpdateWithIncorrectContactActivityType( )
    {
        $params = array(
                        'id'                  => 4,
                        'activity_name'       => 'Test Activity',
                        'subject'             => 'this case should fail',
                        'scheduled_date_time' => date('Ymd')
                        );

        $result =& civicrm_activity_update($params);
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Invalid Activity Name' );
    }
    
    /**
     * this should create activity
     */
    function testActivityUpdate( )
    {
        //  Insert a row in civicrm_activity creating activity 4
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/activity_type_5.xml') );
        $params = array( 'id' => 13,
                         'activity_type_id' => 5 );
        $today = date('Ymd');
        $params = array(
                        'id'                  => 4,
                        'subject'             => 'Update Discussion on Apis for v2',
                        'activity_date_time'  => $today,
                        'duration_hours'      => 15,
                        'duration_minutes'    => 20,
                        'location'            => '21, Park Avenue',
                        'details'             => 'Lets update Meeting',
                        'status_id'           => 1,
                        'activity_name'       => 'Test activity type',
                        );

        $result =& civicrm_activity_update( $params );
        $this->assertNull( $result['is_error'],
                             "Error message: " . $result['error_message'] );
        $this->assertEquals( 4, $result['id'],
                             "In line " . __LINE__ );
        $this->assertEquals( $today, $result['activity_date_time'],
                             "In line " . __LINE__ );
        $this->assertEquals( 'Update Discussion on Apis for v2',
                             $result['subject'],
                             "In line " . __LINE__ );
        $this->assertEquals( $result['location'], '21, Park Avenue',
                             "In line " . __LINE__); 
        $this->assertEquals( $result['details'], 'Lets update Meeting',
                             "In line " . __LINE__);
        $this->assertEquals( $result['status_id'], 1,
                             "In line " . __LINE__ );
        
    }
    
    /**
     * check activity update with status
     */
    function testActivityUpdateWithStatus( )
    {
        //  Insert a row in civicrm_activity creating activity 4
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/activity_type_5.xml') );
        $params = array(
                        'id'                  => 4,
                        'source_contact_id'   => 17,
                        'subject'             => 'Hurry update works', 
                        'status_id'           => 2,
                        'activity_name'       => 'Test activity type',
                        );

        $result =& civicrm_activity_update( $params );
        $this->assertNull( $result['is_error'],
                             "Error message: " . $result['error_message'] );
        $this->assertEquals( $result['id'] , 4,
                             "In line " . __LINE__ );
        $this->assertEquals( $result['source_contact_id'] , 17,
                             "In line " . __LINE__ );
        $this->assertEquals( $result['subject'], 'Hurry update works',
                             "In line " . __LINE__ );
        $this->assertEquals( $result['status_id'], 2,
                             "In line " . __LINE__ );
    }

} // class api_v2_TestActivity

// -- set Emacs parameters --
// Local variables:
// mode: php;
// tab-width: 4
// c-basic-offset: 4
// c-hanging-comment-ender-p: nil
// indent-tabs-mode: nil
// End: