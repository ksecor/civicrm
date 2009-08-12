<?php  // vim: set si ai expandtab tabstop=4 shiftwidth=4 softtabstop=4:

/**
 *  File for the CRM_Contact_Form_Search_Custom_GroupTest class
 *
 *  (PHP 5)
 *  
 *   @author Walt Haas <walt@dharmatech.org> (801) 534-1262
 *   @copyright Copyright CiviCRM LLC (C) 2009
 *   @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html
 *              GNU Affero General Public License version 3
 *   @version   $Id$
 *   @package CiviCRM
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
 *  Include parent class definition
 */
require_once 'CiviTest/CiviUnitTestCase.php';

/**
 *  Include class under test
 */
require_once 'CRM/Contact/Form/Search/Custom/Group.php';

/**
 *  Include form definitions
 */
require_once 'CRM/Core/Form.php';

/**
 *  Include DAO to do queries
 */
require_once 'CRM/Core/DAO.php';

/**
 *  Test contact custom search functions
 *
 *  @package CiviCRM
 */
class CRM_Contact_Form_Search_Custom_GroupTest extends CiviUnitTestCase
{
    /**
     *  Constructor
     *
     *  Initialize configuration
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     *  Test setup for every test
     *
     *  Connect to the database, truncate the tables that will be used
     *  and redirect stdin to a temporary file
     */
    public function setUp()
    {
        //  Connect to the database
        parent::setUp();
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  If it's OK to include only contacts that are members of some group
     *  the the right answer is '13', '14', '15', '16'
     */
    public function testAllExcGroup3()
    {
        //echo "\ntestAllExcGroup3()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts not in group 3
        $formValues = array( 'excludeGroups' => array( '3' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 9-17 once each
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '9', '10', '11', '12', '13', '14', '15', '16', '17' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with only group inclusion
     */
    public function testAllIncGroup3()
    {
        //echo "\ntestAllIncGroup3()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );
                     
        //  Find contacts in group 3
        $formValues = array( 'includeGroups' => array( '3' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 17-24 once each
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '17', '18', '19', '20', '21', '22', '23', '24' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with only group inclusion
     */
    public function testAllIncGroup5()
    {
        //echo "\ntestAllIncGroup5()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts in group 5
        $formValues = array( 'includeGroups' => array( '5' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 13-16, 21-24 once each
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '13', '14', '15', '16', '21', '22', '23', '24' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with only group inclusion
     */
    public function testAllIncGroup3or5()
    {
        //echo "\ntestAllIncGroup3or5()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts in group 5
        $formValues = array( 'includeGroups' => array( '3', '5' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 13-24 once each
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '13', '14', '15', '16', '17', '18',
                                    '19', '20', '21', '22', '23', '24' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with group inclusion and exclusion
     */
    public function testAllIncGroup3ExcGroup5()
    {
        //echo "\ntestAllIncGroup3ExcGroup5()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts in group 5 but not in group 3
        $formValues = array( 'includeGroups' => array( '3' ),
                             'excludeGroups' => array( '5' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 17-20
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '17', '18', '19', '20' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  If it's OK to include only contacts that have some tag then
     *  the right answer is '10', '14', '18', '22'
     */
    public function testAllExcTag7()
    {
        //echo "\ntestAllExcTag7()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts without tag 7
        $formValues = array( 'excludeTags' => array( '7' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 9, 10, 14, 18, 22 once each
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '9', '10', '14',
                                    '18', '22' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with only tag inclusion
     */
    public function testAllIncTag7()
    {
        //echo "\ntestAllIncTag7()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts with tag 7
        $formValues = array( 'includeTags' => array( '7' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 11, 12, 15, 16, 19, 20, 23, 24 once each
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '11', '12', '15', '16',
                                    '19', '20', '23', '24' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with only tag inclusion
     */
    public function testAllIncTag9()
    {
        //echo "\ntestAllIncTag9()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts with tag 7
        $formValues = array( 'includeTags' => array( '9' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 10, 12, 14, 16, 18, 20, 22, 24 once each
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '10', '12', '14', '16',
                                    '18', '20', '22', '24' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with only tag inclusion
     */
    public function testAllIncTag7or9()
    {
        //echo "\ntestAllIncTag7or9()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts with tag 7 or 9
        $formValues = array( 'includeTags' => array( '7', '9' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 10-24 once each
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '10', '11', '12', '14', '15', '16',
                                    '18', '19', '20', '22', '23', '24' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with tag inclusion and exclusion
     */
    public function testAllIncExcTags()
    {
        //echo "\ntestAllIncExcTags()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts with tags 7 and 9
        $formValues = array( 'includeTags' => array( '7' ),
                             'excludeTags' => array( '9' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 11, 15, 19, 23
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '11', '15', '19', '23' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with tag and group inclusion
     */
    public function testAllIncGroup3IncTag7()
    {
        //echo "\ntestAllIncGroup3IncTag7()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts in group 3 or with tag 7
        $formValues = array( 'includeGroups' => array( '3' ),
                             'includeTags'   => array( '7' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 11, 12, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '11', '12', '15', '16', '17', '18', '19',
                                    '20', '21', '22', '23', '24' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with group inclusion and tag exclusion
     */
    public function testAllIncGroup3ExcTag7()
    {
        //echo "\ntestAllIncGroup3ExcTag7()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts in group 3 but not with tag 7
        $formValues = array( 'includeGroups' => array( '3' ),
                             'excludeTags'   => array( '7' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 17, 18,  21, 22
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array(  '17', '18',  '21', '22' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with tag inclusion and group exclusion
     */
    public function testAllIncTag9ExcGroup5()
    {
        //echo "\ntestAllIncTag9ExcGroup5()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts with tag 9 but not in group 5
        $formValues = array( 'includeTags' => array( '9' ),
                             'excludeGroups' => array( '5' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 10, 12, 18, 20
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array(  '10', '12',  '18', '20' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::all()
     *  Test with tag and group exclusion
     *  If it's OK to include only contacts that have some tag then
     *  the right answer is '11', '17', '19'
     */
    public function testAllExcTag9ExcGroup5()
    {
        //echo "\ntestAllExcTag9ExcGroup5()\n";
        //  Insert database contents
        $op = new PHPUnit_Extensions_Database_Operation_Insert( );
        $op->execute( $this->_dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__)
                             . '/dataset.xml') );

        //  Find contacts with tag 9 but not in group 5
        $formValues = array( 'excludeTags' => array( '9' ),
                             'excludeGroups' => array( '5' ) );
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $sql = $obj->all( );
        $this->assertTrue( is_string( $sql ), 'In line ' . __LINE__ );
        $dao =& CRM_Core_DAO::executeQuery( $sql );
        $contacts = array( );

        //  We should fetch contacts 9, 11, 17, 19
        while ( $dao->fetch( ) ) {
            $contacts[] = $dao->contact_id;
        }
        asort( $contacts );
        $this->assertEquals( array( '9', '11', '17', '19' ),
                             $contacts, 'In line ' . __LINE__ );
    }

    /**
     *  Test something
     *  @todo write this test
     */
    public function testBuildForms()
    {
        throw new PHPUnit_Framework_IncompleteTestError("test not implemented");
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::columns()
     *  It returns an array of translated name => keys
     */
    public function testColumns()
    {
        $formValues = array();
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $columns = $obj->columns( );
        $this->assertTrue( is_array( $columns ), 'In line ' . __LINE__ );
        foreach( $columns as $key => $value ) {
            $this->assertTrue( is_string( $key ), 'In line ' . __LINE__ );
            $this->assertTrue( is_string( $value ), 'In line ' . __LINE__ );
        }
    }

    /**
     *  Test something
     *  @todo write this test
     */
    public function testContactIDs()
    {
        throw new PHPUnit_Framework_IncompleteTestError("test not implemented");
    }

    /**
     *  Test something
     *  @todo write this test
     */
    public function testCount()
    {
        throw new PHPUnit_Framework_IncompleteTestError("test not implemented");
    }

    /**
     *  Test something
     *  @todo write this test
     */
    public function testFrom()
    {
        throw new PHPUnit_Framework_IncompleteTestError("test not implemented");
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::summary()
     *  It returns NULL
     */
    public function testSummary()
    {
        $formValues = array();
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $this->assertNull( $obj->summary( ), 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::templateFile()
     *  Returns the path to the file as a string
     */
    public function testTemplateFile()
    {
        $formValues = array();
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $fileName = $obj->templateFile( );
        $this->assertTrue( is_string( $fileName ), 'In line ' . __LINE__ );
        //FIXME: we would need to search the include path to do the following
        //$this->assertTrue( file_exists( $fileName ), 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::where( )
     *  With no arguments it returns '(1)'
     */
    public function testWhereNoArgs()
    {
        $formValues = array( CRM_Core_Form::CB_PREFIX . '17' => true,
                             CRM_Core_Form::CB_PREFIX . '23' => true);
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $this->assertEquals( ' (1) ', $obj->where( ), 'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::where( )
     *  With false argument it returns '(1)'
     */
    public function testWhereFalse()
    {
        $formValues = array( CRM_Core_Form::CB_PREFIX . '17' => true,
                             CRM_Core_Form::CB_PREFIX . '23' => true);
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $this->assertEquals( ' (1) ', $obj->where( false ),
                             'In line ' . __LINE__ );
    }

    /**
     *  Test CRM_Contact_Form_Search_Custom_Group::where( )
     *  With true argument it returns list of contact IDs
     */
    public function testWhereTrue()
    {
        $formValues = array( CRM_Core_Form::CB_PREFIX . '17' => true,
                             CRM_Core_Form::CB_PREFIX . '23' => true);
        $obj = new CRM_Contact_Form_Search_Custom_Group( $formValues );
        $this->assertEquals( 'contact_a.id IN ( 17, 23 )', $obj->where( true ),
                             'In line ' . __LINE__ );
    }

} // class CRM_Contact_Form_Search_Custom_GroupTest

// -- set Emacs parameters --
// Local variables:
// mode: php;
// tab-width: 4
// c-basic-offset: 4
// c-hanging-comment-ender-p: nil
// indent-tabs-mode: nil
// End: