<?php

/**
 *  File for the TestActivityType class
 *
 *  (PHP 5)
 *  
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

require_once 'CiviTest/CiviUnitTestCase.php';
require_once 'CRM/Member/BAO/MembershipLog.php';
require_once 'CiviTest/Contact.php';
require_once 'CiviTest/Membership.php';
require_once 'CRM/Member/BAO/Membership.php';
require_once 'CRM/Member/BAO/MembershipType.php';
require_once 'api/v2/MembershipType.php';
require_once 'api/v2/MembershipStatus.php';

/**
 *  Test CRM/Member/BAO Membership Log add , delete functions
 *
 *  @package   CiviCRM
 */


class CRM_Member_BAO_MembershipLogTest extends CiviUnitTestCase 
{
    
    function get_info( ) 
    {
        return array(
                     'name'        => 'MembershipLog Test',
                     'description' => 'Test all Membership Log methods.',
                     'group'       => 'CiviCRM BAO Tests',
                     );
    }
    
    function setUp( ) 
    {
        parent::setUp();
        
        $this->_relationshipTypeId  = $this->relationshipTypeCreate( $params ); 
        $this->_orgContactID        = $this->organizationCreate( ) ;
        $this->_contributionTypeId  = $this->contributionTypeCreate();
        
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
        
        $membershipType = CRM_Member_BAO_MembershipType::add( $params, $ids );
        $this->_membershipTypeID    = $membershipType->id;
        $this->_mebershipStatusID  = $this->membershipStatusCreate( 'test status' );           
    }
    
    /**
     *  Test add()
     */
    function testadd()
    {  
        $contactId = Contact::createIndividual( );
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'          => '2007-01-21',
                        'start_date'         => '2007-01-21',
                        'end_date'           => '2007-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_mebershipStatusID
                        );
        $ids = array();
        $membership = CRM_Member_BAO_Membership::create( $params, $ids );
        
        $membership_id = $this->assertDBNotNull( 'CRM_Member_BAO_MembershipLog',$membership->id ,
                                                 'membership_id', 'id',
                                                 'Database checked on membershiplog record.' );
        
        
        
    }
    
    
    /**
     *  Test del()
     */
    function testdel()
    {  
        $contactId = Contact::createIndividual( );
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'          => '2008-01-21',
                        'start_date'         => '2008-01-21',
                        'end_date'           => '2008-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_mebershipStatusID
                        );
        $ids = array();
        $membership = CRM_Member_BAO_Membership::create( $params, $ids );
        
        $membershipDelete =  CRM_Member_BAO_Membership::deleteMembership( $membership->id );

        $membershipdelete = $this->assertDBNull( 'CRM_Member_BAO_MembershipLog',$membership->id, 'membership_id', 
                                                 'id', 'Database check for deleted membership log.' );
        
        
        
    }
    
    
    /**
     *  Test resetmodified()
     */
    function testresetmodifiedId()
    {  
        $contactId = Contact::createIndividual( );
        
        $params = array(
                        'contact_id'         => $contactId,  
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'          => '2009-01-21',
                        'start_date'         => '2009-01-21',
                        'end_date'           => '2009-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_mebershipStatusID
                        );
        $ids = array();
        $membership = CRM_Member_BAO_Membership::create( $params, $ids );
        
        
        $contactId2 = Contact::createIndividual( );
        
        $this->assertDBNull( 'CRM_Member_BAO_MembershipLog',$contactId2, 'modified_id', 
                             'modified_id', 'Database check for NULL modified id.' );      
    }
}