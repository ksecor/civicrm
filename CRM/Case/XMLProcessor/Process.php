<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

require_once 'CRM/Case/XMLProcessor.php';

class CRM_Case_XMLProcessor_Process extends CRM_Case_XMLProcessor {

    function run( $caseType,
                  &$params ) {
        $xml = $this->retrieve( $caseType );

        if ( $xml === false ) {
            CRM_Core_Error::fatal( );
            return false;
        }

        $this->process( $xml, $params );
    }

    function get( $caseType,
                  $fieldSet ) {
        $xml = $this->retrieve( $caseType );

        if ( $xml === false ) {
            CRM_Core_Error::fatal( );
            return false;
        }

        switch ( $fieldSet ) {
        case 'CaseRoles':
            return $this->caseRoles( $xml->CaseRoles );
        case 'ActivitySets':
            return $this->activitySets( $xml->ActivitySets );
        case 'ActivityTypes':
            return $this->activityTypes( $xml->ActivityTypes );
        }
    }

    function process( $xml,
                      &$params ) {
        $standardTimeline = CRM_Utils_Array::value( 'standardTimeline', $params );
        $activitySetName  = CRM_Utils_Array::value( 'activitySetName' , $params );
        $activityTypeName = CRM_Utils_Array::value( 'activityTypeName', $params );
        
        if ( 'Open Case' ==
             CRM_Utils_Array::value( 'activityTypeName', $params ) ) {
            // create relationships for the ones that are required
            foreach ( $xml->CaseRoles as $caseRoleXML ) {
                foreach ( $caseRoleXML->RelationshipType as $relationshipTypeXML ) {
                    if ( (int ) $relationshipTypeXML->creator == 1 ) {
                        if (! $this->createRelationships( (string ) $relationshipTypeXML->name,
                                                          $params ) ) {
                            CRM_Core_Error::fatal( );
                            return false;
                        }
                    }
                }
            }
        }

        foreach ( $xml->ActivitySets as $activitySetsXML ) {
            foreach ( $activitySetsXML->ActivitySet as $activitySetXML ) {
                if ( $standardTimeline ) {
                    if ( (boolean ) $activitySetXML->timeline ) {
                        return $this->processStandardTimeline( $activitySetXML,
                                                               $params );
                    }
                } else if ( $activitySetName ) {
                    $name = (string ) $activitySetXML->name;
                    if ( $name == $activitySetName ) {
                        return $this->processActivitySetReport( $activitySetXML,
                                                                $params ); 
                    }
                }
            }
        }

    }

    function processStandardTimeline( $activitySetXML,
                                      &$params ) {
        if ( 'Change Case Type' ==
             CRM_Utils_Array::value( 'activityTypeName', $params ) ) {
            // delete all existing activities which are non-empty
            $this->deleteEmptyActivity( $params );
        }

        foreach ( $activitySetXML->ActivityTypes as $activityTypesXML ) {
            foreach ( $activityTypesXML as $activityTypeXML ) {
                $this->createActivity( $activityTypeXML, $params );
            }
        }
    }

    function &caseRoles( $caseRolesXML ) {
        $relationshipTypes =& $this->allRelationshipTypes( );

        $result = array( );
        foreach ( $caseRolesXML as $caseRoleXML ) {
            foreach ( $caseRoleXML->RelationshipType as $relationshipTypeXML ) {
                $relationshipTypeName = (string ) $relationshipTypeXML->name;
                $relationshipTypeID   = array_search( $relationshipTypeName,
                                                      $relationshipTypes );
                if ( $relationshipTypeID === false ) {
                    continue;
                }
                $result[$relationshipTypeID] = $relationshipTypeName;
            }
        }
        return $result;
    }

    function createRelationships( $relationshipTypeName,
                                  &$params ) {
        $relationshipTypes =& $this->allRelationshipTypes( );

        // get the relationship id
        $relationshipTypeID = array_search( $relationshipTypeName,
                                            $relationshipTypes );
        if ( $relationshipTypeID === false ) {
            CRM_Core_Error::fatal( );
            return false;
        }

        $relationshipParams = array( 'relationship_type_id' => $relationshipTypeID,
                                     'contact_id_a'         => $params['clientID'],
                                     'contact_id_b'         => $params['creatorID'],
                                     'is_active'            => 1,
                                     'case_id'              => $params['caseID'] );

        if ( ! $this->createRelationship( $relationshipParams ) ) {
            CRM_Core_Error::fatal( );
            return false;
        }
        return true;
    }

    function createRelationship( &$params ) {
        require_once 'CRM/Contact/DAO/Relationship.php';

        $dao =& new CRM_Contact_DAO_Relationship( );
        $dao->copyValues( $params );
        // only create a relationship if it does not exist
        if ( ! $dao->find( true ) ) {
            $dao->save( );
        }
        return true;
    }

    function activityTypes( $activityTypesXML ) {
        $activityTypes =& $this->allActivityTypes( );
        $result = array( );
        foreach ( $activityTypesXML as $activityTypeXML ) {
            foreach ( $activityTypeXML as $recordXML ) {
                $activityTypeName = (string ) $recordXML->name;
                $categoryName     = (string ) $recordXML->category;
                $activityTypeInfo = CRM_Utils_Array::value( $activityTypeName,
                                                            CRM_Utils_Array::value( $categoryName,
                                                                                    $activityTypes ) );
                if ( $activityTypeInfo ) {
                    $result[$activityTypeInfo['id']] = $activityTypeName;
                }
            }
        }
        return $result;
    }

    function deleteEmptyActivity( &$params ) {
        $query = "
DELETE a
FROM   civicrm_activity a
INNER JOIN civicrm_activity_target t ON t.activity_id = a.id
WHERE  t.target_contact_id = %1
AND    a.is_auto = 1
";
        $sqlParams = array( 1 => array( $params['clientID'], 'Integer' ) );
        CRM_Core_DAO::executeQuery( $query, $sqlParams );
    }

    function isActivityPresent( &$params ) {
        $query = "
SELECT a.id
FROM   civicrm_activity a
INNER JOIN civicrm_activity_target t ON t.activity_id = a.id
WHERE  t.target_contact_id = %1
AND    a.activity_type_id  = %2
";
        $sqlParams = array( 1 => array( $params['clientID']      , 'Integer' ),
                            2 => array( $params['activityTypeID'], 'Integer' ) );
        return CRM_Core_DAO::singleValueQuery( $query, $sqlParams ) > 0 ? true : false;
    }

    function createActivity( $activityTypeXML,
                             &$params ) {

        $activityTypeName =  (string ) $activityTypeXML->name;
        $categoryName     =  (string ) $activityTypeXML->category;
        $activityTypes    =& $this->allActivityTypes( );
        $activityTypeInfo = CRM_Utils_Array::value( $activityTypeName,
                                                    CRM_Utils_Array::value( $categoryName,
                                                                            $activityTypes ) );
        if ( ! $activityTypeInfo ) {
            CRM_Core_Error::fatal( );
            return false;
        }
        $activityTypeID = $activityTypeInfo['id'];

        require_once 'CRM/Core/OptionGroup.php';
        $activityParams = array( 'activity_type_id'    => $activityTypeID,
                                 'source_contact_id'   => $params['creatorID'],
                                 'is_auto'             => true,
                                 'is_current_revision' => 1,
                                 'subject'             => $activityTypeName,
                                 'status_id'           => CRM_Core_OptionGroup::getValue( 'case_status',
                                                                                          'Scheduled',
                                                                                          'name' ),
                                 'target_contact_id'   => $params['clientID'] );
        
        if ( array_key_exists('custom', $params) && is_array($params['custom']) ) {
            $activityParams['custom'] = $params['custom'];
        }

        // if same activity is already there, skip and dont touch
        $params['activityTypeID'] = $activityTypeID;
        if ( $this->isActivityPresent( $params ) ) {
            return true;
        }

        if ( (int ) $activityTypeXML->reference_offset ) {
            $dueDateTime = $params['dueDateTime'] + 
                (int ) $activityTypeXML->reference_offset * 24 * 3600; // this might cause a DST issue
        } else {
            $dueDateTime = $params['dueDateTime'];
        }
        $activityParams['due_date_time'] = date( 'Ymdhis', $dueDateTime );

        require_once 'CRM/Activity/BAO/Activity.php';
        $activity = CRM_Activity_BAO_Activity::create( $activityParams );
        if ( ! $activity ) {
            CRM_Core_Error::fatal( );
            return false;
        }

        // create case activity record
        $caseParams = array( 'activity_id' => $activity->id,
                             'case_id'     => $params['caseID'] );
        require_once 'CRM/Case/BAO/Case.php';
        CRM_Case_BAO_Case::processCaseActivity( $caseParams );
             return true;
    }

    function activitySets( $activitySetsXML ) {
        $result = array( );
        foreach ( $activitySetsXML as $activitySetXML ) {
            foreach ( $activitySetXML as $recordXML ) {
                $activitySetName  = (string ) $recordXML->name;
                $activitySetLabel = (string ) $recordXML->label;
                $result[$activitySetName] = $activitySetLabel;
            }
        }
        return $result;
    }

}
