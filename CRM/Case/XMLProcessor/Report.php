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

class CRM_Case_XMLProcessor_Report extends CRM_Case_XMLProcessor {

    function run( $clientID,
                  $caseID,
                  $activitySetName,
                  $params ) {
        require_once 'CRM/Core/OptionGroup.php';
        require_once 'CRM/Contact/BAO/Contact.php';

        $template =& CRM_Core_Smarty::singleton( );

        // first get all case information
        $case = $this->caseInfo( $clientID, $caseID );
        $template->assign_by_ref( 'case', $case );

        if ( CRM_Utils_Array::value( 'is_redact', $params ) ) {
            $template->assign( 'isRedact', 'true' );
        } else {
            $template->assign( 'isRedact', 'false' );
        }

        if ( $params['include_activities'] == 1 ) {
            $template->assign( 'includeActivities', 'All' );
        } else {
            $template->assign( 'includeActivities', 'Missing activities only' );
        }

        $xml = $this->retrieve( $case['caseType'] );

        $activityTypes = $this->getActivityTypes( $xml, $activitySetName );
        if ( ! $activityTypes ) {
            return false;
        }


        // next get activity set Informtion
        $activitySet = array( 'label'             => $this->getActivitySetLabel( $xml, $activitySetName ),
                              'includeActivities' => 'All',
                              'redact'            => 'false' );
        $template->assign_by_ref( 'activitySet', $activitySet );

        //now collect all the information about activities
        $activities = array( );
        $this->getActivities( $clientID, $caseID, $activityTypes, $activities );

        $template->assign_by_ref( 'activities', $activities );

        // now run the template
        $contents = $template->fetch( 'CRM/Case/XMLProcessor/Report.tpl' );

        require_once 'CRM/Case/Audit/Audit.php';
        Audit::run( $contents );

        require_once 'CRM/Utils/System.php';
        CRM_Utils_System::download( "{$case['clientName']} {$case['caseType']}",
                                    'text/xml',
                                    $contents,
                                    'xml', true );
    }

    function &caseInfo( $clientID,
                       $caseID ) {
        $case = array( );

        $case['clientName'] = CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                           $clientID,
                                                           'display_name' );
        
        require_once 'CRM/Case/DAO/Case.php';
        $dao = new CRM_Case_DAO_Case( );
        $dao->id = $caseID;
        if ( $dao->find( true ) ) {
            $case['subject']    = $dao->subject;
            $case['start_date'] = $dao->start_date;
            $case['end_date']   = $dao->end_date;
            // FIXME: when we resolve if case_type_is single or multi-select
            if ( strpos( $dao->case_type_id, CRM_Core_DAO::VALUE_SEPARATOR ) !== false ) {
                $caseTypeID = substr( $dao->case_type_id, 1, -1 );
            } else {
                $caseTypeID = $dao->case_type_id;
            }
            $caseTypeIDs = explode( CRM_Core_DAO::VALUE_SEPARATOR,
                                    $dao->case_type_id );
            $case['caseType']   = CRM_Core_OptionGroup::getLabel( 'case_type',
                                                                  $caseTypeID );
            $case['status']     = CRM_Core_OptionGroup::getLabel( 'case_status',
                                                                  $dao->status_id );
        }

        return $case;
    }

    function getActivityTypes( $xml, $activitySetName ) {
        foreach ( $xml->ActivitySets as $activitySetsXML ) {
            foreach ( $activitySetsXML->ActivitySet as $activitySetXML ) {
                if ( (string ) $activitySetXML->name == $activitySetName ) {
                    $activityTypes    =  array( );
                    $allActivityTypes =& $this->allActivityTypes( );
                    foreach ( $activitySetXML->ActivityTypes as $activityTypesXML ) {
                        foreach ( $activityTypesXML as $activityTypeXML ) {
                            $activityTypeName =  (string ) $activityTypeXML->name;
                            $categoryName     =  (string ) $activityTypeXML->category;
                            $activityTypeInfo = CRM_Utils_Array::value( $activityTypeName,
                                                                        CRM_Utils_Array::value( $categoryName,
                                                                                                $allActivityTypes ) );
                            if ( $activityTypeInfo ) {
                                $activityTypes[$activityTypeInfo['id']] = $activityTypeInfo;
                            }
                        }
                    }
                    return  empty( $activityTypes ) ? false : $activityTypes;
                }
            }
        }
        return false;
    }    

    function getActivitySetLabel( $xml, $activitySetName ) {
        foreach ( $xml->ActivitySets as $activitySetsXML ) {
            foreach ( $activitySetsXML->ActivitySet as $activitySetXML ) {
                if ( (string ) $activitySetXML->name == $activitySetName ) {
                    return (string ) $activitySetXML->label;
                }
            }
        }
        return null;
    }

    function getActivities( $clientID,
                            $caseID,
                            $activityTypes,
                            &$activities ) {
        // get all activities for this case that in this activityTypes set

        foreach ( $activityTypes as $aType ) {
            $map[$aType['id']] = $aType;
        }

        $activityTypeIDs = implode( ',', array_keys( $map ) );
        $query = "
SELECT a.*
FROM   civicrm_activity a,
       civicrm_case     c,
       civicrm_case_activity ac
WHERE  a.is_current_revision = 1
AND    a.activity_type_id IN ( $activityTypeIDs )
AND    c.id = ac.case_id
AND    a.id = ac.activity_id
AND    ac.case_id = %1
";

        $params = array( 1 => array( $caseID, 'Integer' ) );
        $dao = CRM_Core_DAO::executeQuery( $query, $params );
        while ( $dao->fetch( ) ) {
            $activityTypeInfo = $map[$dao->activity_type_id];
            $activities[] = $this->getActivity( $clientID,
                                                $dao,
                                                $map[$dao->activity_type_id] );
        }
    }

    function &getActivity( $clientID,
                           $activityDAO,
                           &$activityTypeInfo ) {
        $activity = array( );

        $activity['editURL'] = CRM_Utils_System::url( 'civicrm/activity',
                                                      "action=update&reset=1&cid={$clientID}&id={$activityDAO->id}" );
        $activity['fields'] = array( );

        // Activity Type info is a special field
        $activity['fields'][] = array( 'label'    => 'Activity Type',
                                       'value'    => $activityTypeInfo['label'],
                                       'category' => $activityTypeInfo['parentLabel'],
                                       'type'     => 'String' );
        
        $activity['fields'][] = array( 'label' => 'Created By',
                                       'value' => $this->getCreatedBy( $activityDAO->id ),
                                       'type'  => 'String' );
        
        $activity['fields'][] = array( 'label' => 'Reported By',
                                       'value' => CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                                               $activityDAO->source_contact_id,
                                                                               'display_name' ),
                                       'type'  => 'String' );
        
        $activity['fields'][] = array( 'label' => 'Due Date',
                                       'value' => $activityDAO->due_date_time,
                                       'type'  => 'Date' );
        
        $activity['fields'][] = array( 'label' => 'Actual Date',
                                       'value' => $activityDAO->activity_date_time,
                                       'type'  => 'Date' );

        $activity['fields'][] = array( 'label' => 'Medium',
                                       'value' => CRM_Core_OptionGroup::getLabel( 'Encounter Medium',
                                                                                  $activityDAO->medium ),
                                       'type'  => 'String' );

        $activity['fields'][] = array( 'label' => 'Status',
                                       'value' => CRM_Core_OptionGroup::getLabel( 'case_status',
                                                                                  $activityDAO->status_id ),
                                       'type'  => 'String' );
        
        $activity['fields'][] = array( 'label' => 'Duration',
                                       'value' => $activityDAO->duration,
                                       'type'  => 'Int' );
        
        $activity['fields'][] = array( 'label' => 'Subject',
                                       'value' => $activityDAO->subject,
                                       'type'  => 'Memo' );

        $activity['fields'][] = array( 'label' => 'Details',
                                       'value' => $activityDAO->details,
                                       'type'  => 'Memo' );
        
        // for now empty custom groups
        $activity['customGroups'] = null;
        return $activity;
    }

    function getCreatedBy( $activityID ) {
        $query = "
SELECT c.display_name
FROM   civicrm_contact c,
       civicrm_log     l
WHERE  l.entity_table = 'civicrm_activity'
AND    l.entity_id    = %1
AND    l.modified_id  = c.id
LIMIT  1
";
        $params = array( 1 => array( $activityID, 'Integer' ) );
        return CRM_Core_DAO::singleValueQuery( $query, $params );
    }

}


