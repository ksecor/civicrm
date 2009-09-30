<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
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
require_once 'api/v2/Domain.php';

/**
 * Test class for Domain API - civicrm_domain_*
 *
 *  @package   CiviCRM
 */
class api_v2_DomainTest extends CiviUnitTestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        parent::setUp();

        //  Insert a row in civicrm_option_group creating option group
        //  from_email_address group
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset/option_group_from_email_address.xml') );
 
        //  Insert a row in civicrm_option_value creating
        //  from email address
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_XMLDataSet(
                             dirname(__FILE__)
                             . '/dataset/option_value_from_email_address.xml') );

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

///////////////// civicrm_domain_get methods

    /**
     * Test civicrm_domain_get. Takes no params.
     * Testing mainly for format.
     */
    public function testGet()
    {
        $domain = civicrm_domain_get();

        $this->assertType( 'array', $domain );
        $this->assertEquals( $domain['from_email'], 'test@email.label.net' );
        $this->assertEquals( $domain['from_name'],  'Test Label - Domain');

        // checking other important parts of domain information
        // test will fail if backward incompatible changes happen
        $this->assertArrayHasKey( 'id', $domain );
        $this->assertArrayHasKey( 'domain_name', $domain );
        $this->assertArrayHasKey( 'domain_email', $domain );
        $this->assertArrayHasKey( 'domain_phone', $domain );
        $this->assertArrayHasKey( 'domain_address', $domain ); 
    }
        
///////////////// civicrm_domain_create methods

    /**
     * @todo Implement testCreate().
     */
    public function testCreate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete('This test has not been implemented yet.');
    }    

    /**
     * Test civicrm_domain_create with empty params.
     */
    public function testCreateWithEmptyParams()
    {
        $params = array( );
        $result =& civicrm_domain_create($params);
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }
    
    /**
     * @todo Implement testCreateWithEmptyParams().
     */
    public function testCreateWithWrongParams()
    {
        $params = 'a string';
        $result =& civicrm_domain_create($params);
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }    
    
}
?>
