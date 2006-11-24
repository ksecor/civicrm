<?php
  /*
   +--------------------------------------------------------------------+
   | CiviCRM version 1.6                                                |
   +--------------------------------------------------------------------+
   | Copyright CiviCRM LLC (c) 2004-2006                                  |
   +--------------------------------------------------------------------+
   | This file is a part of CiviCRM.                                    |
   |                                                                    |
   | CiviCRM is free software; you can copy, modify, and distribute it  |
   | under the terms of the Affero General Public License Version 1,    |
   | March 2002.                                                        |
   |                                                                    |
   | CiviCRM is distributed in the hope that it will be useful, but     |
   | WITHOUT ANY WARRANTY; without even the implied warranty of         |
   | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
   | See the Affero General Public License for more details.            |
   |                                                                    |
   | You should have received a copy of the Affero General Public       |
   | License along with this program; if not, contact the Social Source |
   | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
   | about the Affero General Public License or the licensing  of       |
   | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
   | http://www.civicrm.org/licensing/                                 |
   +--------------------------------------------------------------------+
  */

  /**
   *
   *
   * @package CRM
   * @author Donald A. Lobo <lobo@civicrm.org>
   * @copyright CiviCRM LLC (c) 2004-2006
   * $Id$
   *
   */

require_once 'CRM/Member/DAO/Membership.php';
require_once 'CRM/Member/DAO/MembershipType.php';

require_once 'CRM/Core/BAO/CustomField.php';
require_once 'CRM/Core/BAO/CustomValue.php';

class CRM_Member_BAO_Membership extends CRM_Member_DAO_Membership
{
    /**
     * static field for all the membership information that we can potentially import
     *
     * @var array
     * @static
     */
    static $_importableFields = null;

    function __construct()
    {
        parent::__construct();
    }
    

    /**
     * takes an associative array and creates a membership object
     *
     * the function extract all the params it needs to initialize the create a
     * membership object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Member_BAO_Membership object
     * @access public
     * @static
     */
    static function add(&$params, &$ids) {
        require_once 'CRM/Utils/Hook.php';
        
        if ( CRM_Utils_Array::value( 'membership', $ids ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Membership', $ids['membership'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Membership', null, $params ); 
        }

        // converting dates to mysql format
        $params['join_date']  = CRM_Utils_Date::isoToMysql($params['join_date']);
        $params['start_date'] = CRM_Utils_Date::isoToMysql($params['start_date']);
        $params['end_date']   = CRM_Utils_Date::isoToMysql($params['end_date']);

        $membership =& new CRM_Member_BAO_Membership();
        $membership->copyValues($params);
        $membership->id = CRM_Utils_Array::value( 'membership', $ids );
        
        $result = $membership->save();
        
        $session = & CRM_Core_Session::singleton();
        
        $membershipLog = array('membership_id' => $result->id,
                               'status_id'     => $result->status_id,
                               'start_date'    => $result->start_date,
                               'end_date'      => $result->end_date,
                               'modified_id'   => $session->get('userID'),
                               'modified_date' => date('Ymd')
                               );
        require_once 'CRM/Member/BAO/MembershipLog.php';
        $temp = array();
        CRM_Member_BAO_MembershipLog::add($membershipLog, $temp);
        
        if ( CRM_Utils_Array::value( 'membership', $ids ) ) {
            CRM_Utils_Hook::post( 'edit', 'Membership', $membership->id, $membership );
        } else {
            CRM_Utils_Hook::post( 'create', 'Membership', $membership->id, $membership );
        }
        
        return $result;
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Member_BAO_Membership|null the found object or null
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids ) {
        
        $membership =& new CRM_Member_BAO_Membership( );
        
        $membership->copyValues( $params );
        $membership->find();
        $memberships = array();
        while ( $membership->fetch() ) {
            $ids['membership'] = $membership->id;
            
            CRM_Core_DAO::storeValues( $membership, $values[$membership->id] );
            
            $memberships[$membership->id] = $membership;
        }
        
        return $memberships;
    }

    /**
     * takes an associative array and creates a membership object
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Member_BAO_Membership object 
     * @access public
     * @static
     */
    static function &create(&$params, &$ids) {
        require_once 'CRM/Utils/Date.php';

        CRM_Core_DAO::transaction('BEGIN');
        
        $membership = self::add($params, $ids);

        if ( is_a( $membership, 'CRM_Core_Error') ) {
            CRM_Core_DAO::transaction( 'ROLLBACK' );
            return $membership;
        }

        $params['membership_id'] = $membership->id;
        
        CRM_Core_DAO::transaction('COMMIT');
        
        return $membership;
    }


    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. We'll tweak this function to be more
     * full featured over a period of time. This is the inverse function of
     * create.  It also stores all the retrieved values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the name / value pairs
     *                        in a hierarchical manner
     * @param array $ids      (reference) the array that holds all the db ids
     *
     * @return object CRM_Member_BAO_Membership object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) {
        $membership =& new CRM_Member_DAO_Membership( );
        $membership->copyValues( $params );
        $idList = array('membership_type' => 'MembershipType',
                        'status'          => 'MembershipStatus',
                        );
        if ( $membership->find( true ) ) {
            CRM_Core_DAO::storeValues( $membership, $defaults );
            foreach ( $idList as $name => $file ) {
                if ( $defaults[$name .'_id'] ) {
                    $defaults[$name] = CRM_Core_DAO::getFieldValue( 'CRM_Member_DAO_' . $file, 
                                                                    $defaults[$name .'_id'] );
                }
            }
            if ( $membership->status_id ) {
                $active = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipStatus', $membership->status_id, 'is_current_member');
                if ( $active ) {
                    $defaults['active'] = $active;
                }
            }
            
            return $membership;
        }
        return null;
    }

    /** Function to delete membership.
     * 
     * @static
     * @access public
     */
    static function deleteMembership( $membershipId ) {
        
        require_once 'CRM/Member/BAO/MembershipLog.php';
        CRM_Member_BAO_MembershipLog::del($membershipId);

        require_once 'CRM/Member/DAO/MembershipPayment.php';
        $membershipPayment = & new CRM_Member_DAO_MembershipPayment( );
        $membershipPayment->membership_id = $membershipId;
        $membershipPayment->delete();

        require_once 'CRM/Member/DAO/Membership.php';
        $membership = & new CRM_Member_DAO_Membership( );
        $membership->id = $membershipId;
        $count = $membership->delete( );
        
        // Changing the "return true" to "return count".
        // Reason for change is "return true" can not be used to check 
        // whether membership deleted or not. 
        // ( The returned value is always true even in case no membership deleted. )
        return $count;
        //return true;
    }
    

    /**                                                           
     * Delete the object records that are associated with this contact 
     *                    
     * @param  int  $contactId id of the contact to delete 
     *
     * @userId int  $userID of the Logged-in id of the Civicrm.                                                                          
     * 
     * @return boolean  true if deleted, false otherwise
     * @access public 
     * @static 
     */ 
    static function deleteContact( $contactID, $userID = null ) {
        $membership =& new CRM_Member_DAO_Membership( );
        $membership->contact_id = $contactID;
        $membership->find( );
        
        while ( $membership->fetch( ) ) {
            self::deleteMembership( $membership->id );
        }
        
        // also we need to fix any membership types which point to this contact
        // for now lets just make this point to the current userID

        if ( !$userID ) {
            $session =& CRM_Core_Session::singleton( );
            $userID  = $session->get( 'userID' );
        }
        
        $query = "
UPDATE civicrm_membership_type
  SET  member_of_contact_id = %1
 WHERE member_of_contact_id = %2
";
        $params = array( 1 => array( $userID, 'Integer' ), 2 => array( $contactID, 'Integer' ) );
        CRM_Core_DAO::executeQuery( $query, $params );
        
    }



    /** Function to obtain active/inactive memberships from the list of memberships passed to it.
     * 
     * @static
     * @access public
     */
    static function activeMembers( $contactId, $memberships, $status = 'active' ) {
        $actives = array();
        if ( $status == 'active' ) {
            foreach ($memberships as $f => $v) {
                if ($v['active']) {
                    $actives[$f] = $v;
                }
            }
            return $actives;
        } elseif ( $status == 'inactive' ) {
            foreach ($memberships as $f => $v) {
                if ( !$v['active'] ) {
                    $actives[$f] = $v;
                }
            }
            return $actives;
        }
        return null;
    }


    /**
     * Function to build Membership  Block im Contribution Pages 
     * 
     * @param int $pageId 
     * @static
     */
    function buildMembershipBlock( &$form , $pageID , $formItems = false, $selectedMembershipID = null ,$thankPage = false ) {
        require_once 'CRM/Member/DAO/MembershipBlock.php';
        require_once 'CRM/Member/DAO/MembershipType.php';
        require_once 'CRM/Member/DAO/Membership.php';

        $session = & CRM_Core_Session::singleton();
        $cid = $session->get('userID');
        

        $membershipBlock   = array(); 
        $membershipTypeIds = array();
        $membershipTypes   = array(); 
        $radio             = array(); 

        $dao = & new CRM_Member_DAO_MembershipBlock();
        $dao->entity_table = 'civicrm_contribution_page';
        $dao->entity_id = $pageID; 
        $dao->is_active = 1;
        if ( $dao->find(true) ) {
            $this->assign( "is_separate_payment", $dao->is_separate_payment );
            CRM_Core_DAO::storeValues($dao, $membershipBlock );
            if( $dao->membership_types ) {
                $membershipTypeIds = explode( ',' , $dao->membership_types);
            }
            if(! empty( $membershipTypeIds ) ) {
                foreach ( $membershipTypeIds as $value ) {
                    $memType = & new CRM_Member_DAO_MembershipType(); 
                    $memType->id = $value;
                    if ( $memType->find(true) ) {
                        if ($selectedMembershipID  != null ) {
                            if ( $memType->id == $selectedMembershipID ) {
                                CRM_Core_DAO::storeValues($memType,$mem);
                                $this->assign( 'minimum_fee', $mem['minimum_fee'] );
                                $this->assign( 'membership_name', $mem['name'] );
                                if ( !$thankPage && $cid ) {
                                    $membership = &new CRM_Member_DAO_Membership();
                                    $membership->contact_id         = $cid;
                                    $membership->membership_type_id = $memType->id;
                                    if ( $membership->find(true) ) {
                                        $this->assign("renewal_mode", true );
                                        $mem['current_membership'] =  $membership->end_date;
                                    }
                                }
                                $membershipTypes[] = $mem;
                            }
                        } else {
                            $mem = array();
                            CRM_Core_DAO::storeValues($memType,$mem);
                            $radio[$memType->id] = $form->createElement('radio',null, null, null, $memType->id , null);
                            if ( $cid ) {
                                $membership = &new CRM_Member_DAO_Membership();
                                $membership->contact_id         = $cid;
                                $membership->membership_type_id = $memType->id;
                                if ( $membership->find(true) ) {
                                    $this->assign("renewal_mode", true );
                                    $mem['current_membership'] =  $membership->end_date;
                                }
                            }
                            $membershipTypes[] = $mem;
                        }
                    }
                }
            }
            
            $form->assign( 'showRadio',$formItems );
            if ( $formItems ) {
                if ( ! $dao->is_required ) {
                    $form->assign( 'showRadioNoThanks', true );
                    $radio[''] = $form->createElement('radio',null,null,null,'no_thanks', null);
                    $form->addGroup($radio,'selectMembership',null);
                } else if( $dao->is_required  && count( $radio ) == 1 ) {
                    $temp = array_keys( $radio ) ;
                    $form->addElement('hidden', "selectMembership", $temp[0]  );
                    $form->assign('singleMembership' , true );
                    $form->assign( 'showRadio', false );
                } else {
                    $form->addGroup($radio,'selectMembership',null);
                }
                $form->addRule('selectMembership',ts("Please select one of the memeberships"),'required');
            }
            
            $form->assign( 'membershipBlock' , $membershipBlock );
            $form->assign( 'membershipTypes' ,$membershipTypes );
        
        }
    }
    
    /**
     * Function to return Membership  Block info in Contribution Pages 
     * 
     * @param int $pageId 
     * @static
     */
    static function getMembershipBlock( $pageID ) {
        $membershipBlock = array();
        require_once 'CRM/Member/DAO/MembershipBlock.php';
        $dao = & new CRM_Member_DAO_MembershipBlock();
        $dao->entity_table = 'civicrm_contribution_page';
        
        $dao->entity_id = $pageID; 
        $dao->is_active = 1;
        if ( $dao->find(true) ) {
            CRM_Core_DAO::storeValues($dao, $membershipBlock );
        } else {
            return null;
        } 
        
        return $membershipBlock;
    }

    /**
     * Function to return current membership of given contacts 
     * 
     * @param int $contactID  
     * @static
     */
    static function getContactMembership( $contactID , $memType ) {
        require_once 'CRM/Member/DAO/MembershipStatus.php';
        $membership = array();
        $dao = &new CRM_Member_DAO_Membership();
        $dao->contact_id         = $contactID;
        $dao->membership_type_id = $memType;
        if ( $dao->find(true) ) {
            CRM_Core_DAO::storeValues($dao, $membership );
            $statusID = $membership['status_id'];
            $dao = &new CRM_Member_DAO_MembershipStatus();
            $dao->id = $statusID;
            $dao->find(true);
            $status = array();
            CRM_Core_DAO::storeValues($dao, $status );
            $membership['is_current_member'] = $status['is_current_member'];
            return $membership;
        }
        return false;
    }

    /**
     * combine all the importable fields from the lower levels object
     *
     * @return array array of importable Fields
     * @access public
     */
    function &importableFields( $contacType = 'Individual' ) {
        if ( ! self::$_importableFields ) {
            if ( ! self::$_importableFields ) {
                self::$_importableFields = array();
            }
            if (!$status) {
                $fields = array( '' => array( 'title' => ts('- do not import -') ) );
            } else {
                $fields = array( '' => array( 'title' => ts('- Membership Fields -') ) );
            }
            
            $tmpFields     = CRM_Member_DAO_Membership::import( );
            //$tmpFields     = array_merge($tmpFields, CRM_Member_DAO_MembershipType::import( ));
            //print_r($tmpFields);
            //unset($tmpFields['option_value']);
            //require_once 'CRM/Core/OptionValue.php';
            //$optionFields = CRM_Core_OptionValue::getFields($mode ='member' );
            //$contactFields = CRM_Contact_BAO_Contact::importableFields('Individual', null );
            $contactFields = CRM_Contact_BAO_Contact::importableFields( $contacType, null );
            if ($contacType == 'Individual') {
                require_once 'CRM/Core/DAO/DupeMatch.php';
                $dao = & new CRM_Core_DAO_DupeMatch();
                $dao->find(true);
                $fieldsArray = explode('AND',$dao->rule);
            } elseif ($contacType == 'Household') {
                $fieldsArray = array('household_name', 'email');
            } elseif ($contacType == 'Organization') {
                $fieldsArray = array('organization_name', 'email');
            }
            $tmpConatctField = array();
            if( is_array($fieldsArray) ) {
                foreach ( $fieldsArray as $value) {
                    $tmpConatctField[trim($value)] = $contactFields[trim($value)];
                    $tmpConatctField[trim($value)]['title'] = $tmpConatctField[trim($value)]['title']." (match to contact)" ;
                }
            }
            $fields = array_merge($fields, $tmpConatctField);
            $fields = array_merge($fields, $tmpFields);
            //$fields = array_merge($fields, $optionFields);
            $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport('Membership'));
            self::$_importableFields = $fields;
        }
        return self::$_importableFields;
    }

    function &exportableFields( ) { 
//         require_once 'CRM/Member/DAO/Membership.php';
//         require_once 'CRM/Member/DAO/MembershipType.php';
        //$impFields = self::importableFields( );
        $expFieldMembership = CRM_Member_DAO_Membership::export( );
        $expFieldsMemType   = CRM_Member_DAO_MembershipType::export( );
        $fields = array_merge($expFieldMembership, $expFieldsMemType);
        $fields = array_merge($fields, $expFieldMembership );
        return $fields;
    }
    
    function getMembershipSummary( $membershipTypeId ,$membershipTypeName = null) {
        $membershipSummary = array();
        $queryString =  "SELECT  count( id ) as total_count
FROM   civicrm_membership
WHERE ";
        
        //calculate member count for current month 
        $currentMonth    = date("Y-m-01");
        $currentMonthEnd = date("Y-m-31");
        $whereCond =  "membership_type_id = $membershipTypeId AND start_date > '".$currentMonth ."' AND start_date < ' ".$currentMonthEnd."'" ;

        $query = $queryString . $whereCond;
        
        $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        if ( $dao->fetch( ) ) {
            $membershipSummary['month'] = array( "count" => $dao->total_count ,"name" => $membershipTypeName);
        }

        //calculate member count for current year 
        $currentYear    = date("Y-01-01");
        $currentYearEnd = date("Y-12-31");
        $whereCond =  "membership_type_id = $membershipTypeId AND start_date > '".$currentYear ."' AND start_date < '".$currentYearEnd."'";

        $query = $queryString . $whereCond;
        
        $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        if ( $dao->fetch( ) ) {
            $membershipSummary['year'] = array ( "count" => $dao->total_count ,"name" => $membershipTypeName) ;
        }

        // calculate total count for current membership
        $query = "SELECT  count(civicrm_membership.id ) as total_count
FROM   civicrm_membership left join civicrm_membership_status on ( civicrm_membership.status_id = civicrm_membership_status.id  ) WHERE civicrm_membership.membership_type_id = $membershipTypeId AND 
civicrm_membership_status.is_current_member =1";

        $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        if ( $dao->fetch( ) ) {
            $membershipSummary['current'] = array( "count" => $dao->total_count ,"name" => $membershipTypeName) ;
        }

        return $membershipSummary;
    }
    
    function statusAvilability($contactId) 
    {
        require_once 'CRM/Member/DAO/MembershipStatus.php';
        $membership =& new CRM_Member_DAO_MembershipStatus( );
        $membership->whereAdd('1');
        $count = $membership->count();
        
        if(!$count){
            $session =& CRM_Core_Session::singleton( );
            CRM_Core_Session::setStatus(ts('There are no status present, You can not add membership.'));
            return CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contact/view/membership', "reset=1&force=1&cid={$contactId}"));
        }
    }

    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcessMembership( $membershipParams, $contactID ,&$form )
    {
        $tempParams = $membershipParams;
        $paymemtDone = false;

        $form->assign('membership_assign' , true );
        $form->set('membershipID' , $membershipParams['selectMembership']);
        
        require_once 'CRM/Member/BAO/MembershipType.php';
        require_once 'CRM/Member/BAO/Membership.php';
        $membershipID = $membershipParams['selectMembership'];
        $membershipDetails = CRM_Member_BAO_MembershipType::getMembershipTypeDetails( $membershipID );
        $form->assign('membership_name',$membershipDetails['name']);
        
        $minimumFee = $membershipDetails['minimum_fee'];
        $memBlockDetails    = CRM_Member_BAO_Membership::getMemberShipBlock( $form->id );
        $contributionType =& new CRM_Contribute_DAO_ContributionType( );
        if ( $form->_values['amount_block_is_active']) {
            $contributionType->id = $form->_values['contribution_type_id'];
        } else {
            $paymemtDone  = true ;
            $membershipParams['amount'] = $minimumFee;
            $contributionType->id = $membershipDetails['contribution_type_id']; 
        }
        if ( ! $contributionType->find( true ) ) {
            CRM_Core_Error::fatal( "Could not find a system table" );
        }
        $membershipParams['contributionType_name'] = $contributionType->name;
        $membershipParams['contributionType_accounting_code'] = $contributionType->accounting_code;
        $membershipParams['contributionForm_id']              = $form->_values['id'];

        if ($form->_values['is_monetary']) {
            require_once 'CRM/Contribute/Payment.php';
            $payment =& CRM_Contribute_Payment::singleton( $form->_mode );
            
            if ( $form->_contributeMode == 'express' ) {
                $result =& $payment->doExpressCheckout( $membershipParams);
            } else {
                $result =& $payment->doDirectPayment( $membershipParams );
            }
        }
        $errors = array();
        if ( is_a( $result, 'CRM_Core_Error' ) ) {
            $errors[1] = $result;
        } else {
            $now = date( 'YmdHis' );
            if ( $result ) {
                $membershipParams = array_merge($membershipParams, $result );
            }
            $membershipParams['receive_date'] = $now;
            $form->set( 'params', $membershipParams );
            $form->assign( 'trxn_id', $result['trxn_id'] );
            $form->assign( 'receive_date',
                           CRM_Utils_Date::mysqlToIso( $membershipParams['receive_date']) );
            
            $config =& CRM_Core_Config::singleton( );
            if ( $contributionType->is_deductible ) {
                $form->assign('is_deductible' , true );
                $form->set('is_deductible' , true);
            }
            $contribution[1] =  CRM_Contribute_Form_Contribution_Confirm::processContribution( $membershipParams ,$result ,$contactID ,$contributionType ,true );
            CRM_Contribute_Form_Contribution_Confirm::postProcessPremium( $premiumParams ,$contribution[1] );
            
        }
        
        if ( $memBlockDetails['is_separate_payment']  && ! $paymemtDone ) {
            $contributionType =& new CRM_Contribute_DAO_ContributionType( );
            $contributionType->id = $membershipDetails['contribution_type_id']; 
            if ( ! $contributionType->find( true ) ) {
                CRM_Core_Error::fatal( "Could not find a system table" );
            }
            $tempParams['amount'] = $minimumFee;
            $invoiceID = md5(uniqid(rand(), true));
            $tempParams['invoiceID'] = $invoiceID;
            if ($form->_values['is_monetary']) {
                if ( $form->_contributeMode == 'express' ) {
                    $result =& $payment->doExpressCheckout( $tempParams );
                } else {
                    $result =& $payment->doDirectPayment( $tempParams );
                }
            }
            if ( is_a( $result, 'CRM_Core_Error' ) ) {
                $errors[2] = $result;
            } else {
                $form->set('membership_trx_id' , $result['trxn_id']);
                $form->set('membership_amount'  , $minimumFee);
                
                $form->assign('membership_trx_id' , $result['trxn_id']);
                $form->assign('membership_amount'  , $minimumFee);
                $contribution[2] =  CRM_Contribute_Form_Contribution_Confirm::processContribution( $tempParams, $result, $contactID, $contributionType, false );
            }
        }
        
        $index = $memBlockDetails['is_separate_payment'] ? 2 : 1;

        if ( ! $errors[$index] ){
            if ( $currentMembership = CRM_Member_BAO_Membership::getContactMembership($contactID,  $membershipID) ) {
                $form->set("renewal_mode", true );
                if ( ! $currentMembership['is_current_member'] ) {
                    require_once 'CRM/Member/BAO/MembershipStatus.php';
                    $dao = &new CRM_Member_DAO_Membership();
                    $dates = CRM_Member_BAO_MembershipType::getRenewalDatesForMembershipType( $currentMembership['id']);
                    $currentMembership['start_date'] = CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d');
                    $currentMembership['end_date']   = CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d');
                    $currentMembership['reminder_date'] = CRM_Utils_Date::customFormat($dates['reminder_date'],'%Y%m%d'); 
                    $currentMembership['source']     = ts( 'Online Contribution:' ) . ' ' . $form->_values['title'];
                    $dao->copyValues($currentMembership);
                    $membership = $dao->save();
                    
                    //insert log here 
                    require_once 'CRM/Member/DAO/MembershipLog.php';
                    $dao = new CRM_Member_DAO_MembershipLog();
                    $dao->membership_id = $membership->id;
                    $dao->status_id     = $membership->status_id;
                    $dao->start_date    = CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d');
                    $dao->end_date      = CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'); 
                    $dao->renewal_reminder_date = CRM_Utils_Date::customFormat($dates['reminder_date'],'%Y%m%d'); 
                    $dao->modified_id   = $contactID;
                    $dao->modified_date = date('Ymd');
                    $dao->save();
                    
                    $form->assign('mem_start_date',  CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d'));
                    $form->assign('mem_end_date', CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'));
                    
                } else {
                    require_once 'CRM/Member/BAO/MembershipStatus.php';
                    $dao = &new CRM_Member_DAO_Membership();
                    $dao->id = $currentMembership['id'];
                    $dao->find(true); 
                    $membership = $dao ;
                    
                    //insert log here 
                    require_once 'CRM/Member/DAO/MembershipLog.php';
                    $dates = CRM_Member_BAO_MembershipType::getRenewalDatesForMembershipType( $membership->id);
                    $dao = new CRM_Member_DAO_MembershipLog();
                    $dao->membership_id = $membership->id;
                    $dao->status_id     = $membership->status_id;
                    $dao->start_date    = CRM_Utils_Date::customFormat($dates['log_start_date'],'%Y%m%d');
                    $dao->end_date      = CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d');
                    $dao->renewal_reminder_date = CRM_Utils_Date::customFormat($dates['reminder_date'],'%Y%m%d');
                    $dao->modified_id   = $contactID;
                    $dao->modified_date = date('Ymd');
                    $dao->save();
                    $form->assign('mem_start_date',  CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d'));
                    $form->assign('mem_end_date', CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'));
                    
                }
                
            } else {
                require_once 'CRM/Member/BAO/MembershipStatus.php';
                $memParams = array();
                $memParams['contact_id']             = $contactID;
                $memParams['membership_type_id']     = $membershipID;
                $dates = CRM_Member_BAO_MembershipType::getDatesForMembershipType($membershipID);
                
                $memParams['join_date']  = CRM_Utils_Date::customFormat($dates['join_date'],'%Y%m%d');
                $memParams['start_date'] = CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d');
                $memParams['end_date']   = CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d');
                $memParams['reminder_date'] = CRM_Utils_Date::customFormat($dates['reminder_date'],'%Y%m%d'); 
                $memParams['source'  ]   = ts( 'Online Contribution:' ) . ' ' . $form->_values['title'];
                $status = CRM_Member_BAO_MembershipStatus::getMembershipStatusByDate( CRM_Utils_Date::customFormat($dates['start_date'],'%Y-%m-%d'),CRM_Utils_Date::customFormat($dates['end_date'],'%Y-%m-%d'),CRM_Utils_Date::customFormat($dates['join_date'],'%Y-%m-%d')) ;
                
                $memParams['status_id']   = $status['id'];
                $memParams['is_override'] = false;
                $dao = &new CRM_Member_DAO_Membership();
                $dao->copyValues($memParams);
                $membership = $dao->save();
                $form->assign('mem_start_date',  CRM_Utils_Date::customFormat($dates['start_date'],'%Y%m%d'));
                $form->assign('mem_end_date', CRM_Utils_Date::customFormat($dates['end_date'],'%Y%m%d'));
            }
            
            //insert payment record
            require_once 'CRM/Member/DAO/MembershipPayment.php';
            $dao =& new CRM_Member_DAO_MembershipPayment();    
            $dao->membership_id         =  $membership->id;
            $dao->payment_entity_table  = 'civicrm_contribute';
            $dao->payment_entity_id     = $contribution[$index]->id;
            $dao->save();
        }
        
        foreach($errors as $error ) {
            CRM_Core_Error::displaySessionError( $error );
            CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/contribute/transact', '_qf_Main_display=true' ) );
        }
        
        //finally send an email receipt
        if ( !$errors[1]  &&  !$errors[2] ) {
            require_once "CRM/Contribute/BAO/ContributionPage.php";
            CRM_Contribute_BAO_ContributionPage::sendMail( $contactID,$form->_values );
        }
    }
    
}
?>
