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
 *
 */

require_once 'CRM/Utils/Type.php';

/**
 * This class contains all contact related functions that are called using AJAX (jQuery)
 */
class CRM_Contact_Page_AJAX
{
    static function getContactList( &$config ) 
    {
        $name = CRM_Utils_Type::escape( $_GET['s'], 'String' );
        
        $query = "
SELECT sort_name, id
FROM civicrm_contact
WHERE sort_name LIKE '$name%'
ORDER BY sort_name ";            

        $dao = CRM_Core_DAO::executeQuery( $query );
        $contactList = null;
        while ( $dao->fetch( ) ) {
            echo $contactList = "$dao->sort_name|$dao->id\n";
        }
        exit();
    } 
    
    /**
     * Function to fetch the values 
     */
    function autocomplete( &$config ) 
    {
        $fieldID       = CRM_Utils_Type::escape( $_GET['cfid'], 'Integer' );
        $optionGroupID = CRM_Utils_Type::escape( $_GET['ogid'], 'Integer' );
        $label         = CRM_Utils_Type::escape( $_GET['s'], 'String' );
        
        require_once 'CRM/Core/BAO/CustomOption.php';
        $selectOption =& CRM_Core_BAO_CustomOption::valuesByID( $fieldID, $optionGroupID );

        $completeList = null;
        foreach ( $selectOption as $id => $value ) {
            if ( strtolower( $label ) == strtolower( substr( $value, 0, strlen( $label ) ) ) ) {
                echo $completeList = "$value|$id\n";
            }
        }
        exit();
    }
    
    static function relationship( &$config ) 
    {
        // CRM_Core_Error::debug_var( 'GET' , $_GET , true, true );
        // CRM_Core_Error::debug_var( 'POST', $_POST, true, true );
        
        $relType         = CRM_Utils_Array::value( 'rel_type', $_POST );
        $relContactID    = CRM_Utils_Array::value( 'rel_contact', $_POST );
        $sourceContactID = CRM_Utils_Array::value( 'contact_id', $_POST );
        $relationshipID  = CRM_Utils_Array::value( 'rel_id', $_POST );
        $caseID          = CRM_Utils_Array::value( 'case_id', $_POST );


        $relationParams = array('relationship_type_id' => $relType .'_a_b', 
                                'contact_check'        => array( $relContactID => 1),
                                'is_active'            => 1,
                                'case_id'              => $caseID,
                                'start_date'           => date("Ymd")
                                );
        
        if ( $relationshipID == 'null' ) {
            $relationIds = array( 'contact'      => $sourceContactID);
        } else {
            $relationIds = array( 'contact'      => $sourceContactID, 
                                  'relationship' => $relationshipID,
                                  'contactTarget'=>  $relContactID );
        }

        require_once "CRM/Contact/BAO/Relationship.php";
        $return = CRM_Contact_BAO_Relationship::create( $relationParams, $relationIds );

		$relationshipID = $return[4][0];

		// we should return phone and email
		require_once "CRM/Case/BAO/Case.php";
        $caseRelationship = CRM_Case_BAO_Case::getCaseRoles( $sourceContactID, $caseID, $relationshipID );

        //create an activity for case role assignment.CRM-4480
        CRM_Case_BAO_Case::createCaseRoleActivity( $caseID, $relationshipID, $relContactID );

		$relation           = $caseRelationship[$relationshipID];
		$relation['rel_id'] = $relationshipID;
		echo json_encode( $relation );
		exit();
    }
    
    
    /**
     * Function to fetch the custom field help 
     */
    function customField( &$config ) 
    {
        $fieldId = CRM_Utils_Type::escape( $_POST['id'], 'Integer' );

        $helpPost = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField',
                                                 $fieldId,
                                                 'help_post' );
        echo $helpPost;
        exit();
    }

    
    /**
     * Function to obtain list of permissioned employer for the given contact-id.
     */
    function getPermissionedEmployer( &$config ) 
    {
        $cid       = CRM_Utils_Type::escape( $_GET['cid'], 'Integer' );
        $name      = trim(CRM_Utils_Type::escape( $_GET['name'], 'String')); 
        $name      = str_replace( '*', '%', $name );

        require_once 'CRM/Contact/BAO/Relationship.php';
        $elements  = CRM_Contact_BAO_Relationship::getPermissionedEmployer( $cid, $name );

        if( ! empty( $elements ) ) {
            foreach( $elements as $cid => $name ) {
                echo $element = $name['name']."|$cid\n";
            }
        }
        exit();
    }


    function groupTree( $config ) 
    {
        $gids  = CRM_Utils_Type::escape( $_GET['gids'], 'String' ); 
        require_once 'CRM/Contact/BAO/GroupNestingCache.php';
        echo CRM_Contact_BAO_GroupNestingCache::json( $gids );
        exit();
    }    

    /**
     * Function for building contact combo box
     */
    function search( &$config ) 
    {
        $json = true;
        $name = CRM_Utils_Array::value( 'name', $_GET, '' );
        if ( ! array_key_exists( 'name', $_GET ) ) {
            $name = CRM_Utils_Array::value( 's',$_GET ) .'%';
            $json = false;
        }
        $name      = CRM_Utils_Type::escape( $name, 'String' ); 
        $whereIdClause = '';
        if ( CRM_Utils_Array::value( 'id', $_GET ) ) {
            $json = true;
            if ( is_numeric( $_GET['id'] ) ) {
                $id  = CRM_Utils_Type::escape( $_GET['id'], 'Integer' ) ; 
                $whereIdClause = " AND civicrm_contact.id = {$id}";
            } else {
                $name = $_GET['id'];
            }
        }

        $elements = array( );
        if ( $name || isset( $id ) ) {
            $name  = $name . '%';
            
            //contact's based of relationhip type
            $relType = null; 
            if ( isset($_GET['rel']) ) {
                $relation = explode( '_', $_GET['rel'] );
                $relType  = CRM_Utils_Type::escape( $relation[0], 'Integer');
                $rel      = CRM_Utils_Type::escape( $relation[2], 'String');
            }

            //shared household info
            $shared = null;
            if ( isset($_GET['sh']) ) {
                $shared = CRM_Utils_Type::escape( $_GET['sh'], 'Integer');
                 if ( $shared == 1 ) {
                     $contactType = 'Household';
                     $cName = 'household_name';
                 } else {
                     $contactType = 'Organization';
                     $cName = 'organization_name';
                 }
            }

            // contacts of type household
            $hh = $addStreet = $addCity = null;
            if ( isset($_GET['hh']) ) {
                $hh = CRM_Utils_Type::escape( $_GET['hh'], 'Integer');
            }
            
            //organization info
            $organization = $street = $city = null;
            if ( isset($_GET['org']) ) {
                $organization = CRM_Utils_Type::escape( $_GET['org'], 'Integer');
            }
            
            if ( isset($_GET['org']) || isset($_GET['hh']) ) {
                $json = false;
                if ( $splitName = explode( ' :: ', $name ) ) {
                    $contactName = trim( CRM_Utils_Array::value( '0', $splitName ) );
                    $street      = trim( CRM_Utils_Array::value( '1', $splitName ) );
                    $city        = trim( CRM_Utils_Array::value( '2', $splitName ) );
                } else {
                    $contactName = $name;
                }
                
                if ( $street ) {
                    $addStreet = "AND civicrm_address.street_address LIKE '$street%'";
                }
                if ( $city ) {
                    $addCity = "AND civicrm_address.city LIKE '$city%'";
                }
            }
            
            if ( $organization ) {
                
                $query = "
SELECT CONCAT_WS(' :: ',sort_name,LEFT(street_address,25),city) 'sort_name', 
civicrm_contact.id 'id'
FROM civicrm_contact
LEFT JOIN civicrm_address ON ( civicrm_contact.id = civicrm_address.contact_id
                                AND civicrm_address.is_primary=1
                             )
WHERE civicrm_contact.contact_type='Organization' AND organization_name LIKE '%$contactName%'
{$addStreet} {$addCity} {$whereIdClause}
ORDER BY organization_name ";
            } else if ( $shared ) {
                $query = "
SELECT CONCAT_WS(':::' , sort_name, supplemental_address_1, sp.abbreviation, postal_code, cc.name )'sort_name' , civicrm_contact.id 'id' , civicrm_contact.display_name 'disp' FROM civicrm_contact LEFT JOIN civicrm_address ON (civicrm_contact.id =civicrm_address.contact_id AND civicrm_address.is_primary =1 )LEFT JOIN civicrm_state_province sp ON (civicrm_address.state_province_id =sp.id )LEFT JOIN civicrm_country cc ON (civicrm_address.country_id =cc.id )WHERE civicrm_contact.contact_type ='{$contactType}' AND {$cName} LIKE '%$name%' {$whereIdClause} ORDER BY {$cName} ";

            } else if ( $hh ) {
                $query = "
SELECT CONCAT_WS(' :: ' , sort_name, LEFT(street_address,25),city) 'sort_name' , civicrm_contact.id 'id' FROM civicrm_contact LEFT JOIN civicrm_address ON (civicrm_contact.id =civicrm_address.contact_id AND civicrm_address.is_primary =1 )
WHERE civicrm_contact.contact_type ='Household' AND household_name LIKE '%$contactName%' {$addStreet} {$addCity} {$whereIdClause} ORDER BY household_name ";
            } else if ( $relType ) {
                if ( CRM_Utils_Array::value( 'case', $_GET ) ) {
                    $query = "
SELECT distinct(c.id), c.sort_name
FROM civicrm_contact c 
LEFT JOIN civicrm_relationship ON civicrm_relationship.contact_id_{$rel} = c.id
WHERE c.sort_name LIKE '%$name%'
AND civicrm_relationship.relationship_type_id = $relType
GROUP BY sort_name 
";
                } else {
                    $query = "
SELECT c.sort_name, c.id
FROM civicrm_contact c, civicrm_relationship_type r
WHERE c.sort_name LIKE '%$name'
AND r.id = $relType
AND ( c.contact_type = r.contact_type_{$rel} OR r.contact_type_{$rel} IS NULL )
    {$whereIdClause} 
ORDER BY sort_name" ;
                }
            } else {
                
                $query = "
SELECT sort_name, id
FROM civicrm_contact
WHERE sort_name LIKE '%$name'
{$whereIdClause}
ORDER BY sort_name ";            
        }

            $start = 0;
            $end   = 10;
            
            if ( isset( $_GET['start'] ) ) {
                $start = CRM_Utils_Type::escape( $_GET['start'], 'Integer' );
            }
            
            if ( isset( $_GET['count'] ) ) {
                $end   = CRM_Utils_Type::escape( $_GET['count'], 'Integer' );
            }
            
            $query .= " LIMIT {$start},{$end}";

            $dao = CRM_Core_DAO::executeQuery( $query );
            
            if ( $shared ) {
                while ( $dao->fetch( ) ) {
                    echo $dao->sort_name;
                    exit();
                }
            } else {  
                while ( $dao->fetch( ) ) {
                    if( $json ) {
                    $elements[] = array( 'name' => addslashes( $dao->sort_name ),
                                         'id'   => $dao->id );
                    } else {
                     echo $elements = "$dao->sort_name|$dao->id\n";
                    }
                }
                //for adding new household address / organization
                if( empty( $elements ) && !$json && ( $hh || $organization )){
                    echo CRM_Utils_Array::value( 's', $_GET )."|$";
                }
            }
        }

        if ( isset($_GET['sh']) ) {
            echo "";
            exit();
        }

        if ( empty( $elements ) ) {
            $name = str_replace( '%', '', $name );
            $elements[] = array( 'name' => $name,
                                 'id'   => $name );
        }

        if( $json ) {
          require_once "CRM/Utils/JSON.php";
          echo json_encode( $elements );
        } 
        exit();
    }

    /*                                                                                                                                                                                            
     * Function to check how many contact exits in db for given criteria, 
     * if one then return contact id else null                                                                                  
     */
    function contact( &$config )
    {
        $name = CRM_Utils_Type::escape( $_GET['name'], 'String' );

        $query = "                                                                                                                                                                                 
SELECT id                                                                                                                                                                                          
FROM civicrm_contact                                                                                                                                                                               
WHERE sort_name LIKE '%$name%'";

        $dao = CRM_Core_DAO::executeQuery( $query );
        $dao->fetch( );

        if ( $dao->N == 1) {
            echo $dao->id;
        }
        exit();
    }

    /**
     * Function to delete custom value
     *
     */
    function deleteCustomValue( &$config ) {
        $customValueID  = CRM_Utils_Type::escape( $_POST['valueID'], 'Positive' );
        $customGroupID  = CRM_Utils_Type::escape( $_POST['groupID'], 'Positive' );
        
        require_once "CRM/Core/BAO/CustomValue.php";
        CRM_Core_BAO_CustomValue::deleteCustomValue( $customValueID, $customGroupID );
		if( $contactId = CRM_Utils_Array::value( 'contactId', $_POST ) ) {
			require_once 'CRM/Contact/BAO/Contact.php';
			echo CRM_Contact_BAO_Contact::getCountComponent( 'custom_'.$_POST['groupID'], $contactId  );		
		}
    }

    /**
     * Function to perform enable / disable actions on record.
     *
     */
    function enableDisable( &$config ) {
        $op        = CRM_Utils_Type::escape( $_POST['op'       ],  'String'   );
        $recordID  = CRM_Utils_Type::escape( $_POST['recordID' ],  'Positive' );
        $recordBAO = CRM_Utils_Type::escape( $_POST['recordBAO'],  'String'   );

        $isActive = null;
        if ( $op == 'disable-enable' ) {
           $isActive = true;
        } else if ( $op == 'enable-disable' ) {
           $isActive = false;
        }
        $status = array( 'status' => 'record-updated-fail' );
        if ( isset( $isActive ) ) { 
             require_once(str_replace('_', DIRECTORY_SEPARATOR, $recordBAO) . ".php");
             $method  = 'setIsActive'; 
             $result  = array($recordBAO,$method);
             $updated = call_user_func_array(($result), array($recordID,$isActive));
               if ( $updated ) {
              $status = array( 'status' => 'record-updated-success' );
           }
        }
        echo json_encode( $status );
        exit( );
     }
 
    /*
     *Function to check the CMS username
     *
    */
    static public function checkUserName() 
    {
        $config   =& CRM_Core_Config::singleton();
        $username = trim(htmlentities($_POST['cms_name']));
             
        $isDrupal = ucfirst($config->userFramework) == 'Drupal' ? TRUE : FALSE;
        $isJoomla = ucfirst($config->userFramework) == 'Joomla' ? TRUE : FALSE;
        $params   = array( 'name' => $username );

        $errors = array();
        require_once 'CRM/Core/BAO/CMSUser.php';
        CRM_Core_BAO_CMSUser::checkUserNameEmailExists( $params, $errors );
	
        if ( $isDrupal ) {
            //unset the drupal errors, related to email field is required.
            unset($errors['email']);
            unset($errors['mail']);
        }
        if ( !empty($errors)) {
            //user name is not availble
            $user =  array('name' => 'no');
            echo json_encode( $user );
        } else {
            //user name is available
            $user =  array('name' => 'yes');
            echo json_encode( $user );
        }
        exit();
    }
   
   /**
    *  Function to get email address of a contact
    */
    static function getContactEmail( ) {
        if( CRM_Utils_Array::value( 'contact_id', $_POST ) ) {
            $contactID = CRM_Utils_Type::escape( $_POST['contact_id'], 'Positive' );
            require_once 'CRM/Contact/BAO/Contact/Location.php';
            list( $displayName, 
                  $userEmail ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $contactID );
            if ( $userEmail ) {
                echo $userEmail;
            }
        } else {
            $name  = CRM_Utils_Type::escape( $_GET['name'], 'String' );
			$queryString = "cc.sort_name LIKE '%$name%'";
			if ( !$name ) {
				$cid = CRM_Utils_Array::value( 'cid', $_GET );
				$queryString = "cc.id IN ( $cid )";
			}
            $query="
SELECT sort_name name, ce.email, cc.id
FROM civicrm_email ce LEFT JOIN civicrm_contact cc ON cc.id = ce.contact_id
WHERE ce.is_primary = 1 AND ce.on_hold = 0 AND cc.is_deceased = 0 AND cc.do_not_email = 0 AND {$queryString};";
            
            $dao = CRM_Core_DAO::executeQuery( $query );
            
            while( $dao->fetch( ) ) {
                $result[]= array( 'name' => '"'.$dao->name.'" < '.$dao->email.' >',
                                  'id'   => (CRM_Utils_Array::value( 'id', $_GET ) ) ? $dao->id :'"'.$dao->name.'" < '.$dao->email.' >');
            }
            if( $result ) {
                echo json_encode( $result );
            }
        }
        exit();    
    } 
   
}
