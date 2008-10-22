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

class CRM_Case_XMLProcessor_Process {

    static protected $_xml;

    function run( $caseType,
                  &$params ) {
        $xml = $this->retrieve( $caseType );

        if ( $xml === false ) {
            return false;
        }

        $this->process( $xml, $params );
    }

    function get( $caseType,
                  $fieldSet ) {
        $xml = $this->retrieve( $caseType );

        if ( $xml === false ) {
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

    function retrieve( $caseType ) {
        require_once 'CRM/Utils/String.php';
        require_once 'CRM/Utils/Array.php';

        // trim all spaces from $caseType
        $caseType = CRM_Utils_String::munge( $caseType, '', 0 );

        if ( ! CRM_Utils_Array::value( $caseType, self::$_xml ) ) {

            if ( ! self::$_xml ) {
                self::$_xml = array( );
            }

            // ensure that the file exists
            $fileName = implode( DIRECTORY_SEPARATOR,
                                 array( dirname( __FILE__ ),
                                        'xml',
                                        'configuration',
                                        "$caseType.xml" ) );
            if ( ! file_exists( $fileName ) ) {
                return false;
            }

            // read xml file
            $dom = DomDocument::load( $fileName );
            $dom->xinclude( );
            self::$_xml[$caseType] = simplexml_import_dom( $dom );
        }
        return self::$_xml[$caseType];
    }

    function process( $xml,
                      &$params ) {
        $standardTimeline = CRM_Utils_Array::value( 'standardTimeline', $params );
        $activitySetName  = CRM_Utils_Array::value( 'activitySetName' , $params );

        require_once 'CRM/Core/Error.php';
        foreach ( $xml->ActivitySets as $activitySetsXML ) {
            foreach ( $activitySetsXML->ActivitySet as $activitySetXML ) {
                if ( $standardTimeline ) {
                    $timeline = (boolean ) $activitySetXML->timeline;
                    if ( $timeline ) {
                        return $this->processStandardTimeline( $activitySetXML,
                                                               $xml->CaseRoles,
                                                               $params );
                    }
                } else if ( $activitySetName ) {
                    $name = (string ) $activitySetXML->keyname;
                    if ( $name == $activitySetName ) {
                        return $this->processActivitySetReport( $activitySetXML,
                                                                $params ); 
                   }
                }
            }
        }
    }

    function processStandardTimeline( $activitySetXML,
                                      $caseRolesXML,
                                      &$params ) {
        foreach ( $activitySetXML->ActivityTypes as $activityTypesXML ) {
            foreach ( $activityTypesXML as $activityTypeXML ) {
                $this->createActivity( $activityTypeXML, $params );
            }
        }

        foreach ( $caseRolesXML as $caseRoleXML ) {
            foreach ( $caseRoleXML->relationship_type as $relationshipTypeXML ) {
                if (! $this->createRelationships( (string ) $relationshipTypeXML,
                                                  $params ) ) {
                    return false;
                }
            }
        }
    }

    function &getRelationshipTypes( ) {
        static $relationshipTypes = null;

        if ( ! $relationshipTypes ) {
            require_once 'CRM/Core/PseudoConstant.php';
            $relationshipInfo  = CRM_Core_PseudoConstant::relationshipType( );

            $relationshipTypes = array( );
            foreach ( $relationshipInfo as $id => $info ) {
                $relationshipTypes[$id] = $info['name_b_a'];
            }
        }

        return $relationshipTypes;
    }

    function &caseRoles( $caseRolesXML ) {
        $relationshipTypes =& self::getRelationshipTypes( );

        $result = array( );
        foreach ( $caseRolesXML as $caseRoleXML ) {
            foreach ( $caseRoleXML->relationship_type as $relationshipTypeXML ) {
                $relationshipTypeName = (string ) $relationshipTypeXML;
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
        $relationshipTypes =& self::getRelationshipTypes( );

        // get the relationship id
        $relationshipTypeID = array_search( $relationshipTypeName,
                                            $relationshipTypes );
        if ( $relationshipTypeID === false ) {
            return false;
        }

        $relationshipParams = array( 'relationship_type_id' => $relationshipTypeID,
                                     'contact_id_a'         => $params['clientID'],
                                     'is_active'            => 1,
                                     'case_id'              => $params['caseID'] );
        if ( $relationshipTypeName == 'Case Coordinator' ) {
            $relationshipParams['contact_id_b'] = $params['creatorID'];
        }
        $relationshipParams['contact_id_b'] = $params['creatorID'];

        if ( ! $this->createRelationship( $relationshipParams ) ) {
            return false;
        }
        return true;
    }

    function createRelationship( $params ) {
        require_once 'CRM/Contact/DAO/Relationship.php';

        $dao =& new CRM_Contact_DAO_Relationship( );
        $dao->copyValues( $params );
        $dao->save( );
        return true;
    }

    function &getActivityTypes( ) {
        static $activityTypes = null;
        require_once 'CRM/Core/Component.php';
        if ( ! $activityTypes ) {
            $condition = "(component_id = " . CRM_Core_Component::getComponentID( 'CiviCase' ) . ")";
            require_once 'CRM/Case/PseudoConstant.php';
            $activityTypes = CRM_Case_PseudoConstant::category( false,
                                                                'label',
                                                                $condition );
        }
        return $activityTypes; 
    }

    function activityTypes( $activityTypesXML ) {
        $activityTypes =& $this->getActivityTypes( );
        $result = array( );
        foreach ( $activityTypesXML as $activityTypeXML ) {
            foreach ( $activityTypeXML as $recordXML ) {
                $activityTypeName = (string ) $recordXML->keyname;
                $activityTypeID   = array_search( $activityTypeName,
                                                  $activityTypes );
                if ( $activityTypeID ) {
                    $result[$activityTypeID] = $activityTypeName;
                }
            }
        }
        return $result;
    }

    function createActivity( $activityTypeXML,
                             &$params ) {

        $activityTypeName = (string ) $activityTypeXML->keyname;
        $activityTypes =& $this->getActivityTypes( );

        $activityTypeID = array_search( $activityTypeName,
                                        $activityTypes );
        if ( ! $activityTypeID ) {
            return false;
        }

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

        if ( (int ) $activityTypeXML->reference_offset ) {
            $dueDateTime = $params['dueDateTime'] + 
                (int ) $activityTypeXML->reference_offset * 24 * 3600; // this might cause a DST issue
        } else {
            $dueDateTime = $params['dueDateTime'];
        }
        $activityParams['due_date_time'] = date( 'Ymd', $dueDateTime );

        require_once 'CRM/Activity/BAO/Activity.php';
        $activity = CRM_Activity_BAO_Activity::create( $activityParams );
        if ( ! $activity ) {
            return false;
        }

        // create case activity record
        $caseParams = array( 'activity_id' => $activity->id,
                             'case_id'     => $params['caseID'] );
        require_once 'CRM/Case/BAO/Case.php';
        CRM_Case_BAO_Case::processCaseActivity( $caseParams );
    }

    function activitySets( $activitySetsXML ) {
        $result = array( );
        foreach ( $activitySetsXML as $activitySetXML ) {
            foreach ( $activitySetXML as $recordXML ) {
                $activitySetName  = (string ) $recordXML->keyname;
                $activitySetLabel = (string ) $recordXML->label;
                $result[$activitySetName] = $activitySetLabel;
            }
        }
        return $result;
    }

}
