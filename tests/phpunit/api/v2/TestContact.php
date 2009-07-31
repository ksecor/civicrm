<?php  // vim: set si ai expandtab tabstop=4 shiftwidth=4 softtabstop=4:

/**
 *  File for the TestContact class
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
require_once 'api/v2/Contact.php';

/**
 *  Test APIv2 civicrm_activity_* functions
 *
 *  @package   CiviCRM
 */
class api_v2_TestContact extends PHPUnit_Extensions_Database_TestCase
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
    }

    /**
     *  If tearDown() isn't defined, getConnection() and getDataSet()
     *  will be called automatically during teardown
     */
    public function tearDown() { }
    
    /**
     *  Verify that attempt to create contact with empty params fails
     */
    function testCreateEmptyContact() 
    {
        $params = array();
        $contact =& civicrm_contact_create($params);
        $this->assertEquals( $contact['is_error'], 1,
                             "In line " . __LINE__ );
    }
    
    /**
     *  Verify that attempt to create contact with bad contact type fails
     */
    function testCreateBadTypeContact()
    {
        $params = array( 
                        'email'        => 'man1@yahoo.com',
                        'contact_type' => 'Does not Exist' 
                        );
        $contact =& civicrm_contact_create($params);
        $this->assertEquals( $contact['is_error'], 1, "In line " . __LINE__ );
    }
    
    /**
     *  Verify that attempt to create individual contact with required
     *  fields missing fails
     */
    function testCreateBadRequiredFieldsIndividual() 
    {
        $params = array(
                        'middle_name'  => 'This field is not required',
                        'contact_type' => 'Individual' 
                        );

        $contact =& civicrm_contact_create($params);
        $this->assertEquals( $contact['is_error'], 1,
                             "In line " . __LINE__ );
    }
    
    /**
     *  Verify that attempt to create household contact with required
     *  fields missing fails
     */
    function testCreateBadRequiredFieldsHousehold() 
    {
        $params = array(
                        'middle_name'  => 'This field is not required',
                        'contact_type' => 'Household' 
                        );
        
        $contact =& civicrm_contact_create($params);
        $this->assertEquals( $contact['is_error'], 1,
                             "In line " . __LINE__ );
    }
    
    /**
     *  Verify that attempt to create organization contact with
     *  required fields missing fails
     */
    function testCreateBadRequiredFieldsOrganization()
    {
        $params = array(
                        'middle_name'  => 'This field is not required',
                        'contact_type' => 'Organization' 
                        );
        
        $contact =& civicrm_contact_create($params);
        $this->assertEquals( $contact['is_error'], 1,
                             "In line " . __LINE__ );
    }
    
    /**
     *  Verify that attempt to create individual contact with only an
     *  email succeeds
     */
    function testCreateEmailIndividual() 
    {
        $params = array(
                        'email'            => 'man2@yahoo.com',
                        'contact_type'     => 'Individual',
                        'location_type_id' => 1
                        );

        $contact =& civicrm_contact_create($params);
        $this->assertEquals( 0, $contact['is_error'], "In line " . __LINE__
                           . " error message: " . $result['error_message'] );
        $this->assertEquals( 1, $contact['contact_id'], "In line " . __LINE__ );
    }

    /**
     *  Verify that attempt to create individual contact with only
     *  first and last names succeeds
     */
    function testCreateNameIndividual() 
    {
        $params = array(
                        'first_name'   => 'abc1',
                        'contact_type' => 'Individual',
                        'last_name'    => 'xyz1'
                        );

        $contact =& civicrm_contact_create($params);
        $this->assertEquals( 0, $contact['is_error'], "In line " . __LINE__
                           . " error message: " . $result['error_message'] );
        $this->assertEquals( 1, $contact['contact_id'], "In line " . __LINE__ );
    }
    
    /**
     *  Verify that attempt to create household contact with only
     *  household name succeeds
     */
    function testCreateNameHousehold() 
    {
        $params = array(
                        'household_name' => 'The abc Household',
                        'contact_type'   => 'Household',
                        );

        $contact =& civicrm_contact_create($params);
        $this->assertEquals( 0, $contact['is_error'], "In line " . __LINE__
                           . " error message: " . $result['error_message'] );
        $this->assertEquals( 1, $contact['contact_id'], "In line " . __LINE__ );
    }
    
    /**
     *  Verify that attempt to create organization contact with only
     *  organization name succeeds
     */
    function testCreateNameOrganization() 
    {
        $params = array(
                        'organization_name' => 'The abc Organization',
                        'contact_type' => 'Organization',
                        );
        $contact =& civicrm_contact_create($params);
        $this->assertEquals( 0, $contact['is_error'], "In line " . __LINE__
                           . " error message: " . $result['error_message'] );
        $this->assertEquals( 1, $contact['contact_id'], "In line " . __LINE__ );
    }
    
    /**
     *  Verify that attempt to create individual contact with first
     *  and last names and email succeeds
     */
    function testCreateIndividualWithNameEmail() 
    {
        $params = array(
                        'first_name'   => 'abc3',
                        'last_name'    => 'xyz3',
                        'contact_type' => 'Individual',
                        'email'        => 'man3@yahoo.com'
                        );
        
        $contact =& civicrm_contact_create($params);
        $this->assertEquals( 0, $contact['is_error'], "In line " . __LINE__
                           . " error message: " . $result['error_message'] );
        $this->assertEquals( 1, $contact['contact_id'], "In line " . __LINE__ );
    }
    
    /**
     *  Verify that attempt to create individual contact with first
     *  and last names, email and location type succeeds
     */
    function testCreateIndividualWithNameEmailLocationType() 
        {
        $params = array(
                        'first_name'       => 'abc4',
                        'last_name'        => 'xyz4',
                        'email'            => 'man4@yahoo.com',
                        'contact_type'     => 'Individual',
                        'location_type_id' => 1
                        );
        $contact =& civicrm_contact_create($params);
        $this->assertEquals( 0, $contact['is_error'], "In line " . __LINE__
                           . " error message: " . $result['error_message'] );
        $this->assertEquals( 1, $contact['contact_id'], "In line " . __LINE__ );
    }
    
    /**
     *  Verify that attempt to create household contact with details
     *  succeeds
     */
    function testCreateHouseholdDetails() 
    {
        $params = array(
                        'household_name' => 'abc8\'s House',
                        'nick_name'      => 'x House',
                        'email'          => 'man8@yahoo.com',
                        'contact_type'   => 'Household',
                        );

        $contact =& civicrm_contact_create($params);
        $this->assertEquals( 0, $contact['is_error'], "In line " . __LINE__
                           . " error message: " . $result['error_message'] );
        $this->assertEquals( 1, $contact['contact_id'], "In line " . __LINE__ );
    }
    
    /**
     *  Verify successful update of individual contact
     */
    function testUpdateIndividualWithAll()
    {
        //  Insert a row in civicrm_contact creating individual contact
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/contact_ind.xml') );

        $params = array(
                        'contact_id'            => 23,
                        'first_name'            => 'abcd',
                        'last_name'             => 'wxyz', 
                        'contact_type'          => 'Individual',
                        'nick_name'             => 'This is nickname first',
                        'do_not_email'          => '1',
                        'do_not_phone'          => '1',
                        'do_not_mail'           => '1',
                        'do_not_trade'          => '1',
                        'contact_sub_type'      => 'CertainSubType',
                        'legal_identifier'      => 'ABC23853ZZ2235',
                        'external_identifier'   => '1928837465',
                        'home_URL'              => 'http://some.url.com',
                        'image_URL'             => 'http://some.url.com/image.jpg',
                        'preferred_mail_format' => 'HTML',
                        );
        
        $expected = array( 'is_error'   => 0,
                           'contact_id' => 23 );
        $result =& civicrm_contact_update($params);

        //  Result should indicate successful update
        $this->assertEquals( 0, $result['is_error'], "In line " . __LINE__
                           . " error message: " . $result['error_message'] );
        $this->assertEquals( $expected, $result, "In line " . __LINE__ );

        //  Check updated civicrm_contact against expected
        $expected = new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                 dirname( __FILE__ ) . '/contact_ind_upd.xml' );
        $actual = new PHPUnit_Extensions_Database_DataSet_QueryDataset(
                                       $this->dbconn );
        $actual->addTable( 'civicrm_contact' );
        $expected->assertEquals( $actual );
    }        
    
    /**
     *  Verify successful update of organization contact
     */
    function testUpdateOrganizationWithAll()
    {
        //  Insert a row in civicrm_contact creating organization contact
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/contact_org.xml') );

        $params = array(
                        'contact_id'        => 24,
                        'organization_name' => 'WebAccess India Pvt Ltd',
                        'legal_name'        => 'WebAccess',
                        'sic_code'          => 'ABC12DEF',
                        'contact_type'      => 'Organization'
                        );
        
        $result =& civicrm_contact_update( $params );
        
        $expected = array( 'is_error'   => 0,
                           'contact_id' => 24 );

        //  Result should indicate successful update
        $this->assertEquals( 0, $result['is_error'], "In line " . __LINE__
                           . " error message: " . $result['error_message'] );
        $this->assertEquals( $expected, $result, "In line " . __LINE__ );

        //  Check updated civicrm_contact against expected
        $expected = new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                 dirname( __FILE__ ) . '/contact_org_upd.xml' );
        $actual = new PHPUnit_Extensions_Database_DataSet_QueryDataset(
                                       $this->dbconn );
        $actual->addTable( 'civicrm_contact' );
        $expected->assertEquals( $actual );
    }
    
    /**
     *  Verify successful update of household contact
     */
    function testUpdateHouseholdwithAll()
    {
        //  Insert a row in civicrm_contact creating household contact
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/contact_hld.xml') );

        $params = array(
                        'contact_id'     => 25,
                        'household_name' => 'ABC household',
                        'nick_name'      => 'ABC House',
                        'contact_type'   => 'Household',
                        );
        
        $result =& civicrm_contact_update( $params );
        
        $expected = array( 'is_error'   => 0,
                           'contact_id' => 25 );

        //  Result should indicate successful update
        $this->assertEquals( 0, $result['is_error'], "In line " . __LINE__
                           . " error message: " . $result['error_message'] );
        $this->assertEquals( $expected, $result, "In line " . __LINE__ );

        //  Check updated civicrm_contact against expected
        $expected = new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                 dirname( __FILE__ ) . '/contact_hld_upd.xml' );
        $actual = new PHPUnit_Extensions_Database_DataSet_QueryDataset(
                                       $this->dbconn );
        $actual->addTable( 'civicrm_contact' );
        $expected->assertEquals( $actual );
    }
    
    /**
     *  Verify deletion of a contact
     */
    function testDeleteContacts() 
    {
        //  Insert a row in civicrm_contact creating contact 17
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/contact_17.xml') );
        $params = array( 'contact_id' => 17 );
        $result = civicrm_contact_delete( $params );
        $this->assertEquals( 0, $result['is_error'], "In line " . __LINE__
                           . " error message: " . $result['error_message'] );
    }

} // class api_v2_TestContact

// -- set Emacs parameters --
// Local variables:
// mode: php;
// tab-width: 4
// c-basic-offset: 4
// c-hanging-comment-ender-p: nil
// indent-tabs-mode: nil
// End: