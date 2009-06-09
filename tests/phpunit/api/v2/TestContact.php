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
                                             'test_civicrm');
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
    
    function testCreateEmptyContact() 
    {
        $params = array();
        $contact =& civicrm_contact_create($params);
        $this->assertEquals( $contact['is_error'], 1 );
    }
    
    function testCreateBadTypeContact()
    {
        $params = array( 
                        'email'        => 'man1@yahoo.com',
                        'contact_type' => 'Does not Exist' 
                        );
        $contact =& civicrm_contact_create($params);
        $this->assertEquals( $contact['is_error'], 1 );
    }
    
    function testCreateBadRequiredFieldsIndividual() 
    {
        $params = array(
                        'middle_name'  => 'This field is not required',
                        'contact_type' => 'Individual' 
                        );

        $contact =& civicrm_contact_create($params);
        $this->assertEquals( $contact['is_error'], 1 );
    }
    
    function testCreateBadRequiredFieldsHousehold() 
    {
        $params = array(
                        'middle_name'  => 'This field is not required',
                        'contact_type' => 'Household' 
                        );
        
        $contact =& civicrm_contact_create($params);
        $this->assertEquals( $contact['is_error'], 1 );
    }
    
    function testCreateBadRequiredFieldsOrganization()
    {
        $params = array(
                        'middle_name'  => 'This field is not required',
                        'contact_type' => 'Organization' 
                        );
        
        $contact =& civicrm_contact_create($params);
        $this->assertEquals( $contact['is_error'], 1 );
    }
    
    function testCreateEmailIndividual() 
    {
        $params = array(
                        'email'            => 'man2@yahoo.com',
                        'contact_type'     => 'Individual',
                        'location_type_id' => 1,
                        );

        $contact =& civicrm_contact_create($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }

    function testCreateNameIndividual() 
    {
        $params = array(
                        'first_name'   => 'abc1',
                        'contact_type' => 'Individual',
                        'last_name'    => 'xyz1'
                        );

        $contact =& civicrm_contact_create($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateNameHousehold() 
    {
        $params = array(
                        'household_name' => 'The abc Household',
                        'contact_type'   => 'Household',
                        );

        $contact =& civicrm_contact_create($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateNameOrganization() 
    {
        $params = array(
                        'organization_name' => 'The abc Organization',
                        'contact_type' => 'Organization',
                        );
        $contact =& civicrm_contact_create($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateIndividualwithEmail() 
    {
        $params = array(
                        'first_name'   => 'abc3',
                        'last_name'    => 'xyz3',
                        'contact_type' => 'Individual',
                        'email'        => 'man3@yahoo.com'
                        );
        
        $contact =& civicrm_contact_create($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateIndividualwithEmailLocationType() 
        {
        $params = array(
                        'first_name'       => 'abc4',
                        'last_name'        => 'xyz4',
                        'email'            => 'man4@yahoo.com',
                        'contact_type'     => 'Individual',
                        'location_type_id' => 1
                        );
        $contact =& civicrm_contact_create($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }

    
    function testCreateIndividualwithPhone() 
    {
        $params = array(
                        'first_name'    => 'abc5',
                        'last_name'     => 'xyz5',
                        'contact_type'  => 'Individual'
                        );
        
        $contact =& civicrm_contact_create($params);
        
        $paramsPhone = array(
                             'contact_id'    => $contact['contact_id'],
                             'location_type' => 'Work',
                             'is_primary'    => 1,
                             'phone'         => '11111',
                             'phone_type'    => 'Phone'
                             );

        $location = & civicrm_location_add( $paramsPhone );                

        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateIndividualwithAll() 
    {
        $params = array(
                        'first_name'    => 'abc7',
                        'last_name'     => 'xyz7', 
                        'contact_type'  => 'Individual',
                        'do_not_trade'  => 1,
                        'preferred_communication_method' => array(
                                                                  '2' => 1,
                                                                  '3' => 1,
                                                                  '4' => 1,
                                                                  )
                        );

        $contact =& civicrm_contact_create($params);
        
        $paramsAll = array(
                           'contact_id'    => $contact['contact_id'],
                           'location_type' => 'Work',
                           'phone'         => '999999',
                           'phone_type'    => 'Phone',
                           'email'         => 'man7@yahoo.com'
                           );

        $location = & civicrm_location_add( $paramsAll );

        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testCreateHouseholdDetails() 
    {
        $params = array(
                        'household_name' => 'abc8\'s House',
                        'nick_name'      => 'x House',
                        'email'          => 'man8@yahoo.com',
                        'contact_type'   => 'Household',
                        );

        $contact =& civicrm_contact_create($params);
        $this->assertNotNull( $contact['contact_id'] );
        $this->_contacts[] = $contact['contact_id'];
    }
    
    function testUpdateIndividualwithAll()
    {
        $params = array(
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
                        'do_not_trade'          => '1',
                        'is_opt_out'            => '1'
                        );
        
        $contact =& civicrm_contact_create($params);
        $this->assertNotNull( $contact['contact_id'] );
        $retrievedId = array( 'contact_id' => $contact['contact_id'] );
        $retrieved = &civicrm_contact_get( $retrievedId );
        
        $params1 = array('contact_id'            => $contact['contact_id'],
                         'first_name'            => 'efgh',
                         'last_name'             => 'stuv', 
                         'contact_type'          => 'Individual',
                         'nick_name'             => 'This is nickname second',
                         'do_not_email'          => '0',
                         'do_not_phone'          => '0',
                         'do_not_mail'           => '0',
                         'do_not_trade'          => '0',
                         'contact_sub_type'      => 'CertainSubType',
                         'legal_identifier'      => 'DEF23853XX2235',
                         'external_identifier'   => '123456789',
                         'home_URL'              => 'http://some1.url.com',
                         'image_URL'             => 'http://some1.url.com/image1.jpg',
                         'preferred_mail_format' => 'Both',
                         'is_opt_out'            => '0'
                         );
        
        $contact1 =& civicrm_contact_update($params1);
        $this->assertNotNull( $contact1['contact_id'] );
        $retrievedId1 = array( 'contact_id' => $contact1['contact_id'] );
        $target = &civicrm_contact_get( $retrievedId1 );
        
        //get the target contact values.
        $targetContactValues = array_pop( $target );
        
        $this->assertEquals( $contact1['contact_id'], $targetContactValues['contact_id'] );
        $this->_assertAttributesEqual( $params1, $targetContactValues );
        $this->_contacts[] = $targetContactValues['contact_id'];
    }        
    
    function testUpdateOrganizationwithAll()
    {
        $params = array(
                        'organization_name' => 'WebAccess India Pvt Ltd',
                        'legal_name'        => 'WebAccess',
                        'sic_code'          => 'ABC12DEF',
                        'contact_type'      => 'Organization'
                        );
        
        $contact =& civicrm_contact_create($params);
        $this->assertNotNull( $contact['contact_id'] );
        $retrievedId = array( 'contact_id' => $contact['contact_id'] );
        $retrieved = &civicrm_contact_get( $retrievedId );
        
        $params1 = array(
                         'contact_id'        => $contact['contact_id'],
                         'organization_name' => 'WebAccess Inc Pvt Ltd',
                         'legal_name'        => 'WebAccess Global',
                         'sic_code'          => 'GHI34JKL',
                         'contact_type'      => 'Organization'
                         );
        
        $contact1 =& civicrm_contact_update($params1);
        $this->assertNotNull( $contact1['contact_id'] );
        $retrievedId1 = array( 'contact_id' => $contact1['contact_id'] );
        $target = &civicrm_contact_get( $retrievedId1 );
        
        //get the target contact values.
        $targetContactValues = array_pop( $target );
        
        $this->assertEquals( $contact1['contact_id'], $targetContactValues['contact_id'] );
        $this->_assertAttributesEqual( $params1, $targetContactValues );
        $this->_contacts[] = $targetContactValues['contact_id'];
    }
    
    function testUpdateHouseholdwithAll()
    {
        $params = array(
                        'household_name' => 'ABC household',
                        'nick_name'      => 'ABC House',
                        'contact_type'   => 'Household',
                        );
        
        $contact =& civicrm_contact_create($params);
        $this->assertNotNull( $contact['contact_id'] );
        $retrievedId = array( 'contact_id' => $contact['contact_id'] );
        $retrieved = &civicrm_contact_get( $retrievedId );
        
        $params1 = array(
                         'contact_id'     => $contact['contact_id'],
                         'household_name' => 'XYZ household',
                         'nick_name'      => 'XYZ House',
                         'contact_type'   => 'Household',
                         );

        $contact1 =& civicrm_contact_update($params1);
        $this->assertNotNull( $contact1['contact_id'] );
        $retrievedId1 = array( 'contact_id' => $contact1['contact_id'] );
        $target = &civicrm_contact_get( $retrievedId1 );
        
        //get the target contact values.
        $targetContactValues = array_pop( $target );
        
        $this->assertEquals( $contact1['contact_id'], $targetContactValues['contact_id'] );
        $this->_assertAttributesEqual( $params1, $targetContactValues );
        $this->_contacts[] = $targetContactValues['contact_id'];
    }
    
    private function _assertAttributesEqual( $params, $target ) {
        if( empty( $params['custom'] ) ){
            unset( $params['custom'] );
        }
        
        foreach( $params as $paramName => $paramValue ) {
            if( isset( $target[$paramName] ) ) {
                $this->assertEquals( $paramValue, $target[$paramName] );
            } else {
                $this->fail( "Attribute $paramName not available in results, but present in API call parameters."  );
            }
        }        
    }
    
    function testDeleteContacts() 
    {
        foreach ($this->_contacts as $id) {
            $params = array( 'contact_id' => $id );
            $result = civicrm_contact_delete( $params );
            $this->assertEquals( $result['is_error'], 0 );
        }
        
        // delete an unknown id
        $params = array( 'contact_id' => 1000567 );
        $result = civicrm_contact_delete( $params );
        $this->assertEquals( $result['is_error'], 1 );
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