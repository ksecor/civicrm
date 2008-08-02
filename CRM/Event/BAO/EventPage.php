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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/DAO/EventPage.php';

class CRM_Event_BAO_EventPage extends CRM_Event_DAO_EventPage 
{

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }
    
    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Event_BAO_ManageEvent object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $eventPage  = new CRM_Event_DAO_EventPage( );
        $eventPage->copyValues( $params );
        if ( $eventPage->find( true ) ) {            
            CRM_Core_DAO::storeValues( $eventPage, $defaults );
            return $eventPage;
        }
        return null;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active ) 
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Event_DAO_EventPage', $id, 'is_active', $is_active );
    }
    
    /**
     * function to add the eventship types
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add( &$params ) 
    {
        $eventPage            =& new CRM_Event_DAO_EventPage( );
        $eventPage->event_id  =  CRM_Utils_Array::value( 'event_id', $params );
        $eventPage->copyValues( $params );

        // we get the eventPage id from the event_id association, hence this find
        if ( $eventPage->event_id ) {
            $eventPage->id = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage',
                                                          $eventPage->event_id,
                                                          'id',
                                                          'event_id' );
        }

        $eventPage->save( );
        return $eventPage;
    }

    /**
     * Process that send e-mails
     *
     * @return void
     * @access public
     */
    static function sendMail( $contactID, &$values, $participantId, $isTest = false, $returnMessageText = false ) 
    {
        require_once 'CRM/Core/BAO/UFGroup.php';
        //this condition is added, since same contact can have
        //multiple event registrations..       
        $params = array( array( 'participant_id', '=', $participantId, 0, 0 ) );
        $gIds = array(
                      'custom_pre_id' => $values['custom_pre_id'],
                      'custom_post_id'=> $values['custom_post_id']
                      );
        
        if ( ! $returnMessageText ) {
            //send notification email if field values are set (CRM-1941)
            foreach ( $gIds as $gId ) {
                if ( $gId ) {
                    $email = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFGroup', $gId, 'notify' );
                    if ( $email ) {
                        $val = CRM_Core_BAO_UFGroup::checkFieldsEmptyValues( $gId, $contactID, $params );         
                        CRM_Core_BAO_UFGroup::commonSendMail( $contactID, $val );
                    }
                }
            }
        }
        
        if ( $values['event_page']['is_email_confirm'] ) {
            $template =& CRM_Core_Smarty::singleton( );
            require_once 'CRM/Contact/BAO/Contact/Location.php';

            // get the billing location type
            $locationTypes =& CRM_Core_PseudoConstant::locationType( );
            $bltID = array_search( 'Billing',  $locationTypes );

            list( $displayName, 
                  $email ) = CRM_Contact_BAO_Contact_Location::getEmailDetails( $contactID, $bltID, false );
            self::buildCustomDisplay( $values['custom_pre_id'] , 'customPre' , $contactID, $template, $participantId, $isTest );
            self::buildCustomDisplay( $values['custom_post_id'], 'customPost', $contactID, $template, $participantId, $isTest );

            // set confirm_text and contact email address for display in the template here
            $template->assign( 'email', $email );
            $template->assign( 'confirm_email_text', $values['event_page']['confirm_email_text'] );
           
            $isShowLocation = CRM_Utils_Array::value('is_show_location',$values['event']);
            $template->assign( 'isShowLocation', $isShowLocation );

            $subject = trim( $template->fetch( 'CRM/Event/Form/Registration/ReceiptSubject.tpl' ) );
            $message = $template->fetch( 'CRM/Event/Form/Registration/ReceiptMessage.tpl' );
            $receiptFrom = '"' . $values['event_page']['confirm_from_name'] . '" <' . $values['event_page']['confirm_from_email'] . '>';

            if ( $returnMessageText ) {
                return array( 'subject' => $subject,
                              'body'    => $message,
                              'to'      => $displayName );
            }
            require_once 'CRM/Utils/Mail.php';
            CRM_Utils_Mail::send( $receiptFrom,
                                  $displayName,
                                  $email,
                                  $subject,
                                  $message,
                                  CRM_Utils_Array::value( 'cc_confirm', $values['event_page'] ),
                                  CRM_Utils_Array::value( 'bcc_confirm', $values['event_page'] )
                                  );
        }
    }

    /**  
     * Function to add the custom fields OR array of participant's
     * profile info
     *  
     * @return None  
     * @access public  
     */ 
    function buildCustomDisplay( $gid, $name, $cid, &$template, $participantId, $isTest, $isCustomProfile = false ) 
    {  
        if ( $gid ) {
            require_once 'CRM/Core/BAO/UFGroup.php';
            if ( CRM_Core_BAO_UFGroup::filterUFGroups($gid, $cid) ){
                $values = array( );
                $fields = CRM_Core_BAO_UFGroup::getFields( $gid, false, CRM_Core_Action::VIEW );

                //this condition is added, since same contact can have multiple event registrations..
                $params = array( array( 'participant_id', '=', $participantId, 0, 0 ) );
                
                //add participant id
                $fields['participant_id'] = array ( 'name' => 'participant_id',
                                                    'title'=> 'Participant Id');
                //check whether its a text drive
                if ( $isTest ) {
                    $params[] = array( 'participant_test', '=', 1, 0, 0 );
                }
                
                $groupTitle = null;
                foreach( $fields as $k => $v  ) {
                    if ( ! $groupTitle ) {
                        $groupTitle = $v["groupTitle"];
                    }
                    // suppress all file fields from display
                    if ( CRM_Utils_Array::value( 'data_type', $v, '' ) == 'File' ) {
                        unset( $fields[$k] );
                    }
                }

                if ( $groupTitle ) {
                    $template->assign( $name."_grouptitle", $groupTitle );
                }


                CRM_Core_BAO_UFGroup::getValues( $cid, $fields, $values , false, $params );

                if ( isset($values[$fields['participant_status_id']['title']]) &&
                     is_numeric( $values[$fields['participant_status_id']['title']] ) ) {
                    $status = array( );
                    $status = CRM_Event_PseudoConstant::participantStatus( );
                    $values[$fields['participant_status_id']['title']] = $status[$values[$fields['participant_status_id']['title']]];
                }
                
                if ( isset( $values[$fields['participant_role_id']['title']] ) &&
                     is_numeric( $values[$fields['participant_role_id']['title']] ) ) {
                    $roles = array( );
                    $roles = CRM_Event_PseudoConstant::participantRole( );
                    $values[$fields['participant_role_id']['title']] = $roles[$values[$fields['participant_role_id']['title']]];
                }

                if ( isset($values[$fields['participant_register_date']['title']]) ) {
                    $values[$fields['participant_register_date']['title']] = 
                        CRM_Utils_Date::customFormat($values[$fields['participant_register_date']['title']]);
                }
                
                //handle fee_level for price set
                if ( isset( $values[$fields['participant_fee_level']['title']] ) ) {
                    $feeLevel = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, 
                                         $values[$fields['participant_fee_level']['title']] );
                    foreach ( $feeLevel as $key => $val ) {
                        if ( ! $val ) {
                            unset( $feeLevel[$key] );
                        }
                    }
                    $values[$fields['participant_fee_level']['title']] = implode( ",", $feeLevel );
                }
                
                unset( $values[$fields['participant_id']['title']] );

                //return if we only require array of participant's info.
                if ( $isCustomProfile ) {
                    if ( count($values) ) {
                        return $values;
                    } else {
                        return null;
                    }
                } 

                if ( count( $values ) ) {
                    $template->assign( $name, $values );
                }
            }
        }
    }
    
    /**  
     * Function to build the array for display the profile fields
     *  
     * @param array $params key value. 
     * @param int $gid profile Id
     * @param array $groupTitle Profile Group Title.
     * @param array $values formatted array of key value
     *
     * @return None  
     * @access public  
     */ 
    function displayProfile( &$params, $gid, &$groupTitle, &$values ) 
    {   
        if ( $gid ) {
            require_once 'CRM/Core/BAO/UFGroup.php';
            require_once 'CRM/Profile/Form.php';
            $session =& CRM_Core_Session::singleton( );
            $contactID = $session->get( 'userID' );
            if ( $contactID ) {
                if ( CRM_Core_BAO_UFGroup::filterUFGroups($gid, $contactID ) ) {
                    $fields = CRM_Core_BAO_UFGroup::getFields( $gid, false, CRM_Core_Action::VIEW );
                }
            } else {
                $fields = CRM_Core_BAO_UFGroup::getFields( $gid, false, CRM_Core_Action::ADD ); 
            }
            
            foreach ( $fields as $v  ) {
                if ( CRM_Utils_Array::value( 'groupTitle', $v ) ) {
                    $groupTitle['groupTitle'] = $v["groupTitle"];
                    break;
                }
            }
            
            $config =& CRM_Core_Config::singleton( );
            require_once 'CRM/Core/PseudoConstant.php'; 
            $locationTypes = $imProviders = array( );
            $locationTypes = CRM_Core_PseudoConstant::locationType( );
            $imProviders   = CRM_Core_PseudoConstant::IMProvider( );
            //start of code to set the default values
            foreach ($fields as $name => $field ) { 
                $index   = $field['title'];
                $customFieldName = null;
                if ( $name === 'organization_name' ) {
                    $values[$index] = $params[$name];
                }
                if ( !empty( $params[$name] ) || $name == 'group' || $name == 'tag') { 
                    if ( 'state_province' == substr( $name, 0, 14 ) ) {
                        $values[$index] = CRM_Core_PseudoConstant::stateProvince( $params[$name] );
                    } else if ( 'country' == substr( $name, 0, 7 ) ) {
                        $values[$index] = CRM_Core_PseudoConstant::country( $params[$name] );
                    } else if ( 'county' == substr( $name, 0, 6 ) ) {
                        $values[$index] = $params[$name];
                    } else if ( 'gender' == substr( $name, 0, 6 ) ) {
                        $gender =  CRM_Core_PseudoConstant::gender( );
                        $values[$index] = $gender[$params[$name]];
                    } else if ( 'individual_prefix' == substr( $name, 0, 17 ) ) {
                        $prefix =  CRM_Core_PseudoConstant::individualPrefix( );
                        $values[$index] = $prefix[$params[$name]];
                    } else if ( 'individual_suffix' == substr( $name, 0, 17 ) ) {
                        $suffix = CRM_Core_PseudoConstant::individualSuffix( );
                        $values[$index] = $suffix[$params[$name]];
                    } else if ( $name === 'preferred_communication_method' ) {
                        $communicationFields = CRM_Core_PseudoConstant::pcm();
                        $pref = array();
                        $compref = array();
                        $pref = $params[$name];
                        foreach($pref as $k => $v ) {
                            if ( $v ) {
                                $compref[] = $communicationFields[$k];
                            }
                        }
                        $values[$index] = implode( ",", $compref);
                    } else if ( $name == 'group' ) {
                        require_once 'CRM/Contact/BAO/GroupContact.php';
                        $groups = CRM_Contact_BAO_GroupContact::getGroupList( );
                        $title = array( );
                        foreach ( $params[$name] as $gId => $dontCare ) {
                            $title[] = $groups[$gId];
                        }
                        $values[$index] = implode( ', ', $title );
                    } else if ( $name == 'tag' ) {
                        require_once 'CRM/Core/BAO/EntityTag.php';
                        $entityTags = $params[$name];
                        $allTags    =& CRM_Core_PseudoConstant::tag();
                        $title = array( );
                        foreach ( $entityTags as $tagId => $dontCare ) { 
                            $title[] = $allTags[$tagId];
                        }
                        $values[$index] = implode( ', ', $title );
                    } else {
                        if ( substr($name, 0, 7) === 'do_not_' or substr($name, 0, 3) === 'is_' ) {  
                            if ($params[$name] ) {
                                $values[$index] = '[ x ]';
                            }
                        } else {
                            require_once 'CRM/Core/BAO/CustomField.php';
                            if ( $cfID = CRM_Core_BAO_CustomField::getKeyID($name)) {
                                $query  = "
SELECT html_type, data_type
FROM   civicrm_custom_field
WHERE  id = $cfID
";
                                $dao = CRM_Core_DAO::executeQuery( $query,
                                                                   CRM_Core_DAO::$_nullArray );
                                $dao->fetch( );
                                $htmlType  = $dao->html_type;
                                $dataType  = $dao->data_type;
                                
                                if ( $htmlType == 'File') {
                                    //$fileURL = CRM_Core_BAO_CustomField::getFileURL( $contactID, $cfID );
                                    //$params[$index] = $values[$index] = $fileURL['file_url'];
                                    $values[$index] = $params[$index];
                                } else {
                                    if ( $dao->data_type == 'Int' ||
                                         $dao->data_type == 'Boolean' ) {
                                        $customVal = (int ) ($params[$name]);
                                    } else if ( $dao->data_type == 'Float' ) {
                                        $customVal = (float ) ($params[$name]);
                                    } else if ( $dao->data_type == 'Date' ) {
                                        $date = CRM_Utils_Date::format( $params[$name], null, 'invalidDate' );
                                        if ( $date != 'invalidDate' ) {
                                            $customVal = $date;
                                        }
                                    } else {
                                        $customVal = $params[$name];
                                    }
                                    //take the custom field options
                                    $returnProperties = array( $name => 1 );
                                    require_once 'CRM/Contact/BAO/Query.php';
                                    $query   =& new CRM_Contact_BAO_Query( $params, $returnProperties, $fields );
                                    $options =& $query->_options;
                                    $values[$index] = CRM_Core_BAO_CustomField::getDisplayValue( $customVal, $cfID, $options );
                                    if ( CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_CustomField', 
                                                                      $cfID, 'is_search_range' ) ) {
                                        $customFieldName = "{$name}_from";
                                    }
                                }
                            } else if ( $name == 'home_URL' &&
                                        ! empty( $params[$name] ) ) {
                                $url = CRM_Utils_System::fixURL( $params[$name] );
                                $values[$index] = "<a href=\"$url\">{$params[$name]}</a>";
                            } else if ( in_array( $name, array('birth_date', 'deceased_date','participant_register_date') ) ) {
                                require_once 'CRM/Utils/Date.php';
                                $values[$index] = CRM_Utils_Date::customFormat( CRM_Utils_Date::format( $params[$name] ) );
                            } else {
                                $values[$index] = $params[$name];
                            }
                        }
                    }
                } else if ( strpos( $name, '-' ) !== false ) {
                    list( $fieldName, $id, $type ) = CRM_Utils_System::explode( '-', $name, 3 );
                    if ($id == 'Primary') {
                        // not sure why we'd every use Primary location type id
                        // we need to fix the source if we are using it
                        // $locationTypeName = CRM_Contact_BAO_Contact::getPrimaryLocationType( $cid ); 
                        $locationTypeName = 1;
                    } else {
                        $locationTypeName = CRM_Utils_Array::value( $id, $locationTypes );
                    }
                    if ( ! $locationTypeName ) {
                        continue;
                    }
                    $detailName = "{$locationTypeName}-{$fieldName}";
                    $detailName = str_replace( ' ', '_', $detailName );
                    
                    if ( in_array( $fieldName, array( 'phone', 'im', 'email' ) ) ) {
                        if ( $type ) {
                            $detailName .= "-{$type}";
                        } else {
                            $detailName .= '-1';
                        }
                    }
                    
                    if ( in_array( $fieldName, array( 'state_province', 'country', 'county' ) ) ) {
                        $values[$index] = $params[$detailName];
                        $idx = $detailName . '_id';
                        $values[$index] = $params[$idx];
                    } else if ( $fieldName == 'im'){
                        $providerId     = $detailName . '-provider_id';
                        $providerName   = $imProviders[$params[$providerId]];
                        if ( $providerName ) {
                            $values[$index] = $params[$detailName] . " (" . $providerName .")";
                        } else {
                            $values[$index] = $params[$detailName];
                        }
                        $values[$index] = $params[$detailName];        
                    } else {
                        $values[$index] = $params[$detailName];
                    }
                }
            }
        }
    }

    /**  
     * Function to build the array for Additional participant's information  array of priamry and additional Ids 
     *  
     *@param int $participantId id of Primary participant
     *@param array $values key/value eventpage info
     *@param int $contactId contact id of Primary participant 
     *@param boolean $isTest whether test or live transaction 
     *@param boolean $isIdsArray to return an array of Ids
     *
     *@return array $customProfile array of Additional participant's info OR array of Ids.   
     *@access public  
     */ 
    function buildCustomProfile( $participantId, $values, $contactId = null, $isTest = false, $isIdsArray = false ) 
    {
        $additionalIDs = array();
        $customProfile = array();

        //set Ids of Primary Participant also.
        if ( $isIdsArray && $contactId ) {
            $additionalIDs[$participantId] = $contactId; 
        }
        require_once 'CRM/Event/DAO/Participant.php';
        $participant   =  & new CRM_Event_DAO_Participant( );
        $participant->registered_by_id = $participantId;
        $participant->find();
        
        while ( $participant->fetch() ) {
            $additionalIDs[$participant->id] = $participant->contact_id;
        } 
        $participant->free( );
        
        //return if only array is required.
        if ( $isIdsArray && $contactId ) {
            return $additionalIDs;
        }
       
        //else build array of Additional participant's information. 
        if ( count($additionalIDs) ) { 
            if ( $values['custom_pre_id'] || $values['custom_post_id'] ) {
                $template =& CRM_Core_Smarty::singleton( );
                $isCustomProfile = true;
                $i = 1;
                foreach ( $additionalIDs as $pId => $cId ) {
                    $profilePre =  self::buildCustomDisplay( $values['custom_pre_id'], 'customPre',
                                                             $cId, $template, $pId, $isTest, $isCustomProfile );
                    if ( $profilePre ) {
                        $customProfile[$i]['customPre'] =  $profilePre;
                    }

                    $profilePost =  self::buildCustomDisplay( $values['custom_post_id'], 'customPost',
                                                              $cId, $template, $pId, $isTest, $isCustomProfile );
                    if ( $profilePost ) {
                        $customProfile[$i]['customPost'] =  $profilePost;
                    }
                    $i++;
                }
            }
        }

        return $customProfile;
    }
}

