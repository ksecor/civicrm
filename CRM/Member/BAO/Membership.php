<?php
  /*
   +--------------------------------------------------------------------+
   | CiviCRM version 1.4                                                |
   +--------------------------------------------------------------------+
   | Copyright (c) 2005 Donald A. Lobo                                  |
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
   | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
   | questions about the Affero General Public License or the licensing |
   | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
   | at http://www.openngo.org/faqs/licensing.html                      |
   +--------------------------------------------------------------------+
  */

  /**
   *
   *
   * @package CRM
   * @author Donald A. Lobo <lobo@yahoo.com>
   * @copyright Donald A. Lobo (c) 2005
   * $Id$
   *
   */

require_once 'CRM/Member/DAO/Membership.php';

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

        $duplicates = array( );
        if ( self::checkDuplicate( $params, $duplicates ) ) {
            $error =& CRM_Core_Error::singleton( ); 
            $d = implode( ', ', $duplicates );
            $error->push( CRM_Core_Error::DUPLICATE_MEMBERSHIP, 'Fatal', array( $d ), "Found matching membership(s): $d" );
            return $error;
        }

        if ( CRM_Utils_Array::value( 'membership', $ids ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Membership', $ids['membership'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Membership', null, $params ); 
        }

        $membership =& new CRM_Member_BAO_Membership();
        
        $membership->copyValues($params);
        
        $membership->id        = CRM_Utils_Array::value( 'membership', $ids );

        $result = $membership->save();

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

        if ( $membership->find(true) ) {
            $ids['membership'] = $membership->id;

            CRM_Core_DAO::storeValues( $membership, $values );

            return $membership;
        }
        return null;
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
        //require_once 'CRM/Utils/Money.php';
        require_once 'CRM/Utils/Date.php';

        // FIXME: a cludgy hack to fix the dates to MySQL format
        $dateFields = array('receive_date', 'cancel_date', 'receipt_date', 'thankyou_date');
        foreach ($dateFields as $df) {
            if (isset($params[$df])) {
                $params[$df] = CRM_Utils_Date::isoToMysql($params[$df]);
            }
        }
        
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
     * Get the values for pseudoconstants for name->value and reverse.
     *
     * @param array   $defaults (reference) the default values, some of which need to be resolved.
     * @param boolean $reverse  true if we want to resolve the values in the reverse direction (value -> name)
     *
     * @return void
     * @access public
     * @static
     */
    static function resolveDefaults(&$defaults, $reverse = false)
    {
        require_once 'CRM/Member/PseudoConstant.php';

        self::lookupValue($defaults, 'membership_type', CRM_Member_PseudoConstant::membershipType(), $reverse);
        self::lookupValue($defaults, 'payment_instrument', CRM_Member_PseudoConstant::paymentInstrument(), $reverse);
    }

    /**
     * This function is used to convert associative array names to values
     * and vice-versa.
     *
     * This function is used by both the web form layer and the api. Note that
     * the api needs the name => value conversion, also the view layer typically
     * requires value => name conversion
     */
    static function lookupValue(&$defaults, $property, &$lookup, $reverse)
    {
        $id = $property . '_id';

        $src = $reverse ? $property : $id;
        $dst = $reverse ? $id       : $property;

        if (!array_key_exists($src, $defaults)) {
            return false;
        }

        $look = $reverse ? array_flip($lookup) : $lookup;
        
        if(is_array($look)) {
            if (!array_key_exists($defaults[$src], $look)) {
                return false;
            }
        }
        $defaults[$dst] = $look[$defaults[$src]];
        return true;
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
        if ( $membership->find( true ) ) {
            CRM_Core_DAO::storeValues( $membership, $defaults );
            return $membership;
        }
        return null;
    }

    /**                                                           
     * Delete the object records that are associated with this contact 
     *                    
     * @param  int  $contactId id of the contact to delete                                                                           
     * 
     * @return boolean  true if deleted, false otherwise
     * @access public 
     * @static 
     */ 
    static function deleteContact( $contactId ) {
        $membership =& new CRM_Member_DAO_Membership( );
        $membership->contact_id = $contactId;
        $membership->find( );

        require_once 'CRM/Member/DAO/FinancialTrxn.php';
        while ( $membership->fetch( ) ) {
            self::deleteMembership($membership->id);
            //self::deleteMembershipSubobjects($membership->id);
            $membership->delete( );
        }
    }

    static function deleteMembership( $id ) {

        require_once 'CRM/Member/DAO/MembershipProduct.php';
        $dao = & new CRM_Member_DAO_MembershipProduct();
        $dao->membership_id = $id;
        $dao->delete();;

        $membership =& new CRM_Member_DAO_Membership( ); 
        $membership->id = $id;
        if ( $membership->find( true ) ) {
            self::deleteMembershipSubobjects($id);
            $membership->delete( ); 
        }
         
        return true;
    }

    static function deleteMembershipSubobjects($contribId) {
        require_once 'CRM/Member/DAO/FinancialTrxn.php';
        $trxn =& new CRM_Member_DAO_FinancialTrxn();
        $trxn->entity_table = 'civicrm_membership';
        $trxn->entity_id    = $membership->id;
        if ($trxn->find(true)) {
            $trxn->delete();
        }

        require_once 'CRM/Core/DAO/ActivityHistory.php';
        $activityHistory =& new CRM_Core_DAO_ActivityHistory();
        $activityHistory->module      = 'CiviMember';
        $activityHistory->activity_id = $membership->id;
        if ($activityHistory->find(true)) {
            $activityHistory->delete();
        }
    }

    /**
     * Check if there is a membership with the same trxn_id or invoice_id
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array  $duplicates (reference ) store ids of duplicate contribs
     *
     * @return boolean true if duplicate, false otherwise
     * @access public
     * static
     */
    static function checkDuplicate( $params, &$duplicates ) {
        $id         = CRM_Utils_Array::value( 'id'        , $params );
        $trxn_id    = CRM_Utils_Array::value( 'trxn_id'   , $params );
        $invoice_id = CRM_Utils_Array::value( 'invoice_id', $params );

        $clause = array( );
        $params = array( );

        if ( $trxn_id ) {
            $clause[]  = "trxn_id = %1";
            $params[1] = array( $trxn_id, 'String' );
        }

        if ( $invoice_id ) {
            $clause[]  = "invoice_id = %2";
            $params[2] = array( $invoice_id, 'String' );
        }

        if ( empty( $clause ) ) {
            return false;
        }

        $clause = implode( ' OR ', $clause );
        if ( $id ) {
            $clause = "( $clause ) AND id != %3";
            $params[3] = array( $id, 'Integer' );
        }

        $query = "SELECT id FROM civicrm_membership WHERE $clause";
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        $result = false;
        while ( $dao->fetch( ) ) {
            $duplicates[] = $dao->id;
            $result = true;
        }
        return $result;
    }
    
    /**
     * Function to get list of membership fields for profile
     * For now we only allow custom membership fields to be in
     * profile
     *
     * @return return the list of membership fields
     * @static
     * @access public
     */
    static function getMembershipFields( ) 
    {
        $membershipFields =& CRM_Member_DAO_Membership::export( );
        foreach ($membershipFields as $key => $var) {
            if ($key == 'contact_id') {
                continue;
            }
            $fields[$key] = $var;
        }

        // $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport('Membership'));
        $fields = CRM_Core_BAO_CustomField::getFieldsForImport('Membership');
        return $fields;
    }

   /**
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
    function buildMembershipBlock( $form , $pageID , $formItems = false) {
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
            CRM_Core_DAO::storeValues($dao, $membershipBlock );
            if( $dao->membership_types ) {
                $membershipTypeIds = explode( ',' , $dao->membership_types);
            }
            if(! empty( $membershipTypeIds ) ) {
                foreach ( $membershipTypeIds as $value ) {
                    $memType = & new CRM_Member_DAO_MembershipType(); 
                    $memType->id = $value;
                    if ( $memType->find(true) ) {
                        $mem = array();
                        CRM_Core_DAO::storeValues($memType,$mem);
                        $radio[$memType->id] = $form->createElement('radio',null, null, null, $memType->id , null);
                        $membership = &new CRM_Member_DAO_Membership();
                        $membership->contact_id         = $cid;
                        $membership->membership_type_id = $memType->id;
                        if ( $membership->find(true) ) {
                            $mem['current_membership'] =  $membership->end_date;
                        }
                        $membershipTypes[] = $mem;
                    }
                }
            }
            
            $form->assign( 'showRadio',$formItems );
            if ( $formItems ) {
                if ( ! $dao->is_required ) {
                    $radio[''] = $form->createElement('radio',null,null,ts('No thank you'),'no_thanks', null);
                }
                $form->addGroup($radio,'selectMembership',null);
                $form->addRule('selectMembership',ts("Plese select one of the Memebership "),'required');
            }
            
            $form->assign( 'membershipBlock' , $membershipBlock );
            $form->assign( 'membershipTypes' ,$membershipTypes );
        
        }
    }
    

}

?>
