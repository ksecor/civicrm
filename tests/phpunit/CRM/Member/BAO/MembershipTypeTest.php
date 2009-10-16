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
require_once 'CRM/Member/BAO/MembershipType.php';

class CRM_Member_BAO_MembershipTypeTest extends CiviUnitTestCase
{
    function get_info( ) 
    {
        return array(
                     'name'        => 'MembershipType BAOs',
                     'description' => 'Test all Member_BAO_MembershipType methods.',
                     'group'       => 'CiviCRM BAO Tests',
                     );
    }
    
    function setUp( ) 
    { 
        parent::setUp();
        
        //create relationship
        $params = array(
                           'name_a_b'       => 'Relation 1',
                           'name_b_a'       => 'Relation 2',
                           'contact_type_a' => 'Individual',
                           'contact_type_b' => 'Organization',
                           'is_reserved'    => 1,
                           'is_active'      => 1
                               );
        $this->_relationshipTypeId  = $this->relationshipTypeCreate( $params ); 
        $this->_orgContactID        = $this->organizationCreate( ) ;
        $this->_indiviContactID     = $this->individualCreate( ) ;
        $this->_contributionTypeId  = $this->contributionTypeCreate();

    }

    /* check function add()
     *
     */
    function testAdd( ) 
    {
        $ids    = array( 'memberOfContact' => $this->_orgContactID );
        $params = array( 'name' => 'test type',
                         'description' => null,
                         'minimum_fee' => 10,
                         'duration_unit' => 'year',
                         'period_type' => 'fixed',
                         'duration_interval' => 1,
                         'contribution_type_id' => $this->_contributionTypeId,
                         'relationship_type_id' => $this->_relationshipTypeId,
                         'visibility' => 'Public'
                         );
        
        CRM_Member_BAO_MembershipType::add( $params, $ids );

        $membership = $this->assertDBNotNull( 'CRM_Member_BAO_MembershipType', $this->_orgContactID,
                                                    'name', 'member_of_contact_id',
                                                    'Database check on updated membership record.' );
       
        $this->assertEquals( $membership, 'test type', 'Verify membership type name.');
    }

    /* check function retrive()
     *
     */
    function testRetrieve( ) 
    {
        $ids    = array( 'memberOfContact' => $this->_orgContactID );
        $params = array( 'name' => 'General',
                         'description' => null,
                         'minimum_fee' => 100,
                         'duration_unit' => 'year',
                         'period_type' => 'fixed',
                         'duration_interval' => 1,
                         'contribution_type_id' => $this->_contributionTypeId,
                         'relationship_type_id' => $this->_relationshipTypeId,
                         'visibility' => 'Public'
                         );
        CRM_Member_BAO_MembershipType::add( $params, $ids );

        $params  = array('name' => 'General');
        $default = array( );  
        $result = CRM_Member_BAO_MembershipType::retrieve( $params ,$default);
        $this->assertEquals( $result->name , 'General', 'Verify membership type name.');
    }

    /* check function isActive()
     *
     */
    function testSetIsActive( ) 
    {        
        $ids    = array( 'memberOfContact' => $this->_orgContactID );
        $params = array( 'name' => 'General',
                         'description' => null,
                         'minimum_fee' => 100,
                         'duration_unit' => 'year',
                         'period_type' => 'fixed',
                         'duration_interval' => 1,
                         'contribution_type_id' => $this->_contributionTypeId,
                         'relationship_type_id' => $this->_relationshipTypeId,
                         'visibility' => 'Public',
                         'is_active'  => 1
                         );
        $membership = CRM_Member_BAO_MembershipType::add( $params, $ids );
        
        CRM_Member_BAO_MembershipType::setIsActive( $membership->id , 0 ) ;

        $isActive = $this->assertDBNotNull( 'CRM_Member_BAO_MembershipType', $membership->id,
                                                    'is_active', 'id',
                                                    'Database check on membership type status.' );

        $this->assertEquals( $isActive, 0, 'Verify membership type status.');
    }

    /* check function del()
     *
     */
     function testdel( ) 
     {        
        $ids    = array( 'memberOfContact' => $this->_orgContactID );
        $params = array( 'name' => 'General',
                         'description' => null,
                         'minimum_fee' => 100,
                         'duration_unit' => 'year',
                         'period_type' => 'fixed',
                         'duration_interval' => 1,
                         'contribution_type_id' => $this->_contributionTypeId,
                         'relationship_type_id' => $this->_relationshipTypeId,
                         'visibility' => 'Public',
                         'is_active'  => 1
                         );
        $membership = CRM_Member_BAO_MembershipType::add( $params, $ids );
 
        $result = CRM_Member_BAO_MembershipType::del($membership->id) ;
        
        $this->assertEquals( $result, true , 'Verify membership deleted.');

    }


}
