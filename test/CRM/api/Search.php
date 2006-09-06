<?php

require_once 'api/crm.php';

class TestOfSearch extends UnitTestCase 
{
    function setUp( ) 
    {
    }

    function tearDown( ) 
    {
    }
    
    
    function testSearchCountNull( )
    {
        $params = array( );
        $count = crm_contact_search_count( $params );
        echo "<br/><b><i>Total No of Contacts : </i></b>$count \n<br/>";
    }
    
    function testSearchCountIndividual( )
    {
        $params = array( 'contact_type' => 'Individual' );
        $count = crm_contact_search_count( $params );
        echo "<br/><b><i>Individual Contact Count : </i></b>$count\n<br/>";
    }

    function testSearchCountHousehold( )
    {
        $params = array( 'contact_type' => 'Household' );
        $count = crm_contact_search_count( $params );
        echo "<br/><b><i>Household Contact Count : </i></b>$count\n<br/>";
    }

    function testSearchCountOrganization( )
    {
        $params = array( 'contact_type' => 'Organization' );
        $count = crm_contact_search_count( $params );
        echo "<br/><b><i>Organization Contact Count : </i></b>$count\n<br/>";
        
        $returnProperties = array( 'organization_name' => 1 );
        $sort = array('organization_id' => 'ASC');
        $contacts = crm_contact_search( $params , $returnProperties, $sort, 0, 100 );
        echo "<br/><b><i>Available Organization type of Contact(s) : </i></b>\n <br />";
        
        foreach ($contacts[0] as $key => $contactArray) {
            echo "( <i>Organization ID :</i> ". $contactArray['organization_id'] . " ) " . $contactArray['organization_name'] ."\n <br />";
        }
    }
    
    function testSearchForGender( )
    {
        $params = array( 'gender' => 'Female' );
        $count =& crm_contact_search_count( $params );
        echo "<br/><b><i>No of Individual Contact(s) Found : </i></b>$count\n<br/>";
        
        $returnProperties = array(
                                  'contact_type' => 1,
                                  'display_name' => 1,
                                  'email'=> 1
                                  );
        $sort = array('individual_id' => 'ASC');
        $contacts =& crm_contact_search( $params, $returnProperties, null, 0, 100 );
        
        foreach ($contacts[0] as $key => $contactArray) {
            echo "( <i>Contact ID :</i> ". $contactArray['contact_id'] . " ) [<i>Display Name:</i> ] " . $contactArray['display_name'] . " &nbsp;&nbsp;&nbsp;&nbsp;[<i>Email :</i> ]" . $contactArray['email']  . "\n <br />";
        }
    }
    
    function testSearchReturnCustomData()
    {
        $params = array( 'first_name' => 'Sam' );
        $count =& crm_contact_search_count( $params );        
        echo "<br/><b><i>No of Individual Contact(s) Found : </i></b>$count\n<br/>";
        
        $returnProperties = array(
                                  'display_name' => 1,
                                  'custom_2'   => 1,
                                  'custom_7'   => 1);
        $search =& crm_contact_search( $params, $returnProperties, null, 0, 100 );        
        
        foreach ($search[0] as $key => $contactArray) {
            echo "( <i>Individual ID :</i> ". $contactArray['individual_id'] . " ) " . $contactArray['display_name'] . "\n <br />";
        }
    }
    
    function testSearchForCustomData()
    {
        $params = array('custom_2' => 'BJP');   
        $count =& crm_contact_search_count( $params );        
        echo "<br/><b><i>No of Contact(s) Found : </i></b>$count\n<br/>";
        
        $search =& crm_contact_search( $params, null, null, 0, 100 );
        
        foreach ($search[0] as $key => $contactArray) {
            echo "( <i>Contact ID :</i> ". $contactArray['contact_id'] . " ) " . $contactArray['display_name'] . " [ <i>Contact Type :</i> " . $contactArray['contact_type'] . " ] "  . "\n <br />";            
        }
    }
    
    function testSearchByState()
    {
        // This example searches for all contacts who have a Home (civicrm_location_type.id = 1) address in
        // California AND whose email address ends in yahoo.co.in . Contacts will be returned regardless of 
        // whether their Home address is their Primary address using this array format for location_type.
        $location_type = array('1' => 1);
        $params = array( 'location_type' => $location_type, 'state_province' => 'California', 'email' => '%yahoo.co.in' );
        
        // You can also search only for contacts where Home address is primary and is in California.
        // Note that you can use the civicrm_state_province.id value instead of state name if desired.
        $sort   = array( 'sort_name' => 'ASC' );
        $return_properties = array( "sort_name"=>1, "email"=>1 );
        
        $searchResultCount = crm_contact_search_count($params, $return_properties, $sort );
        
        echo("<h4>Search by State Test - Total Result Count = " . $searchResultCount . "</h4>");
        $limit = 50;
        
        $page = 0;
        for ($offset = 0; $offset < $searchResultCount; $offset += $limit ) {
            $page++;
            echo("<h4>Search by State Results - Page " . $page . "</h4>");
            // Iterate over results in pages of $row_count records.
            $searchResults =& crm_contact_search($params, $return_properties, $sort, $offset, $limit);           
            
            foreach ($searchResults[0] as $id => $values) {
                print_r( "Contact ID : " . $values['contact_id'] . "<br/>Sort Name : " . $values['sort_name'] . "<br/>Email : " . $values['email'] . "<br/>");
            }
        }
        
    }

    

    function testSearchByContactID( ) {
        $params = array( 'contact_id' => 11 );
        $result = crm_contact_search( $params );
        
    }

     function testSearchSmartGroupMembership( ) {
        $group = array( '1' => 1);
        $params = array( 'contact_id' => 77, 'group' => $group );
        $returnProperties = array('contact_id' => 1, 'sort_name' => 1, 'email' => 1 );
        $result = crm_contact_search_count( $params, $returnProperties );
     }
    
     function testSearchGroup( ) {
        
        $group = array( '1' => 1);
        $params = array( 'group' => $group );    
        $result = crm_contact_search( $params );
        
    }
}

?>