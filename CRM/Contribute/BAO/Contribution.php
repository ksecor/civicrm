<?php
  /*
   +--------------------------------------------------------------------+
   | CiviCRM version 1.7                                                |
   +--------------------------------------------------------------------+
   | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
   * @copyright CiviCRM LLC (c) 2004-2007
   * $Id$
   *
   */

require_once 'CRM/Contribute/DAO/Contribution.php';

require_once 'CRM/Core/BAO/CustomField.php';
require_once 'CRM/Core/BAO/CustomValue.php';

class CRM_Contribute_BAO_Contribution extends CRM_Contribute_DAO_Contribution
{
    /**
     * static field for all the contribution information that we can potentially import
     *
     * @var array
     * @static
     */
    static $_importableFields = null;

    /**
     * static field for all the contribution information that we can potentially export
     *
     * @var array
     * @static
     */
    static $_exportableFields = null;


    function __construct()
    {
        parent::__construct();
    }
    

    /**
     * takes an associative array and creates a contribution object
     *
     * the function extract all the params it needs to initialize the create a
     * contribution object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Contribute_BAO_Contribution object
     * @access public
     * @static
     */
    static function add(&$params, &$ids) {
        require_once 'CRM/Utils/Hook.php';

        $duplicates = array( );
        if ( self::checkDuplicate( $params, $duplicates ) ) {
            $error =& CRM_Core_Error::singleton( ); 
            $d = implode( ', ', $duplicates );
            $error->push( CRM_Core_Error::DUPLICATE_CONTRIBUTION, 'Fatal', array( $d ), "Found matching contribution(s): $d" );
            return $error;
        }

        if ( CRM_Utils_Array::value( 'contribution', $ids ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Contribution', $ids['contribution'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Contribution', null, $params ); 
        }

        $contribution =& new CRM_Contribute_BAO_Contribution();
        
        $contribution->copyValues($params);

        $contribution->domain_id = CRM_Utils_Array::value( 'domain' , $ids, CRM_Core_Config::domainID( ) );
        
        $contribution->id        = CRM_Utils_Array::value( 'contribution', $ids );

        require_once 'CRM/Utils/Rule.php';
        if (!CRM_Utils_Rule::currencyCode($contribution->currency)) {
            require_once 'CRM/Core/Config.php';
            $config =& CRM_Core_Config::singleton();
            $contribution->currency = $config->defaultCurrency;
        }

        $result = $contribution->save();

        if ( CRM_Utils_Array::value( 'contribution', $ids ) ) {
            CRM_Utils_Hook::post( 'edit', 'Contribution', $contribution->id, $contribution );
        } else {
            CRM_Utils_Hook::post( 'create', 'Contribution', $contribution->id, $contribution );
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
     * @return CRM_Contribute_BAO_Contribution|null the found object or null
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids ) {

        $contribution =& new CRM_Contribute_BAO_Contribution( );

        $contribution->copyValues( $params );

        if ( $contribution->find(true) ) {
            $ids['contribution'] = $contribution->id;
            $ids['domain' ] = $contribution->domain_id;

            CRM_Core_DAO::storeValues( $contribution, $values );

            return $contribution;
        }
        return null;
    }

    /**
     * takes an associative array and creates a contribution object
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Contribute_BAO_Contribution object 
     * @access public
     * @static
     */
    static function &create(&$params, &$ids) {
        require_once 'CRM/Utils/Money.php';
        require_once 'CRM/Utils/Date.php';

        // FIXME: a cludgy hack to fix the dates to MySQL format
        $dateFields = array('receive_date', 'cancel_date', 'receipt_date', 'thankyou_date');
        foreach ($dateFields as $df) {
            if (isset($params[$df])) {
                $params[$df] = CRM_Utils_Date::isoToMysql($params[$df]);
            }
        }

        CRM_Core_DAO::transaction('BEGIN');

        $contribution = self::add($params, $ids);

        if ( is_a( $contribution, 'CRM_Core_Error') ) {
            CRM_Core_DAO::transaction( 'ROLLBACK' );
            return $contribution;
        }

        $params['contribution_id'] = $contribution->id;

        // add custom field values
        if (CRM_Utils_Array::value('custom', $params)) {
            foreach ($params['custom'] as $customValue) {
                $cvParams = array(
                                  'entity_table'    => 'civicrm_contribution',
                                  'entity_id'       => $contribution->id,
                                  'value'           => $customValue['value'],
                                  'type'            => $customValue['type'],
                                  'custom_field_id' => $customValue['custom_field_id'],
                                  );
                
                if ($customValue['id']) {
                    $cvParams['id'] = $customValue['id'];
                }
                CRM_Core_BAO_CustomValue::create($cvParams);
            }
        }

        // let's create an (or update the relevant) Acitivity History record
        require_once 'CRM/Contribute/PseudoConstant.php';
        $contributionType = CRM_Contribute_PseudoConstant::contributionType($contribution->contribution_type_id);
        if (!$contributionType) {
            $contributionType = ts('Contribution');
        }

        static $insertDate = null;
        if ( ! $insertDate ) {
            $insertDate = CRM_Utils_Date::customFormat(date('Y-m-d H:i'));
        }

        $activitySummary = ts(
            '%1 - %2 (offline)',
            array(
                1 => CRM_Utils_Money::format($contribution->total_amount, $contribution->currency),
                2 => $contribution->source
            )
        );

        $historyParams = array(
            'entity_table'     => 'civicrm_contact',
            'entity_id'        => $contribution->contact_id,
            'activity_type'    => $contributionType,
            'module'           => 'CiviContribute',
            'callback'         => 'CRM_Contribute_Page_Contribution::details',
            'activity_id'      => $contribution->id,
            'activity_summary' => $activitySummary,
            'activity_date'    => $contribution->receive_date
        );

        if ( CRM_Utils_Array::value( 'contribution', $ids ) ) {
            // this contribution should have an Activity History record already
            $getHistoryParams = array('module' => 'CiviContribute', 'activity_id' => $contribution->id);
            require_once "CRM/Core/BAO/History.php";
            $getHistoryValues =& CRM_Core_BAO_History::getHistory($getHistoryParams, 0, 1, null, 'Activity');
            if ( ! empty( $getHistoryValues ) ) {
                $tmp = array_keys( $getHistoryValues  );
                $ids['activity_history'] = $tmp[0];
            }
        }
        
        require_once 'CRM/Core/BAO/History.php';
        $historyDAO =& CRM_Core_BAO_History::create($historyParams, $ids, 'Activity');
        if (is_a($historyDAO, 'CRM_Core_Error')) {
            CRM_Core_Error::fatal("Failed creating Activity History for contribution of id {$contribution->id}");
        }

        CRM_Core_DAO::transaction('COMMIT');

        return $contribution;
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
        require_once 'CRM/Contribute/PseudoConstant.php';

        self::lookupValue($defaults, 'contribution_type', CRM_Contribute_PseudoConstant::contributionType(), $reverse);
        self::lookupValue($defaults, 'payment_instrument', CRM_Contribute_PseudoConstant::paymentInstrument(), $reverse);
        self::lookupValue($defaults, 'contribution_status', CRM_Contribute_PseudoConstant::contributionStatus(), $reverse);
       
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
     * @return object CRM_Contribute_BAO_Contribution object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults, &$ids ) {
        $contribution = CRM_Contribute_BAO_Contribution::getValues( $params, $defaults, $ids );
        return $contribution;
    }

    /**
     * combine all the importable fields from the lower levels object
     *
     * The ordering is important, since currently we do not have a weight
     * scheme. Adding weight is super important and should be done in the
     * next week or so, before this can be called complete.
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
                $fields = array( '' => array( 'title' => ts('- Contribution Fields -') ) );
            }

            $tmpFields     = CRM_Contribute_DAO_Contribution::import( );
            unset($tmpFields['option_value']);
            require_once 'CRM/Core/OptionValue.php';
            $optionFields = CRM_Core_OptionValue::getFields($mode ='contribute' );
            //$contactFields = CRM_Contact_BAO_Contact::importableFields('Individual', null );
            $contactFields = CRM_Contact_BAO_Contact::importableFields( $contacType, null );
            if ($contacType == 'Individual') {
                require_once 'CRM/Core/DAO/DupeMatch.php';
                $dao = & new CRM_Core_DAO_DupeMatch();;
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
            $fields = array_merge($fields, $optionFields);
            $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport('Contribution'));
            self::$_importableFields = $fields;
        }
        return self::$_importableFields;
    }

    function &exportableFields( ) {
        if ( ! self::$_exportableFields ) {
            if ( ! self::$_exportableFields ) {
                self::$_exportableFields = array();
            }
            require_once 'CRM/Core/OptionValue.php';
            require_once 'CRM/Contribute/DAO/Product.php';
            require_once 'CRM/Contribute/DAO/ContributionProduct.php';
            require_once 'CRM/Contribute/DAO/ContributionType.php';
            $impFields = CRM_Contribute_DAO_Contribution::import( );
            $expFieldProduct = CRM_Contribute_DAO_Product::export( );
            $expFieldsContrib = CRM_Contribute_DAO_ContributionProduct::export( );
            $typeField = CRM_Contribute_DAO_ContributionType::export( );
            $optionField = CRM_Core_OptionValue::getFields($mode ='contribute' );
            $fields = array_merge($impFields, $typeField);
            $fields = array_merge($fields, $expFieldProduct );
            $fields = array_merge($fields, $expFieldsContrib );
            $fields = array_merge($fields, $optionField );
            $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport('Contribution'));
            
            self::$_exportableFields = $fields;
        }
        return self::$_exportableFields;
    }

    function getTotalAmountAndCount( $status = null, $startDate = null, $endDate = null ) {
        
        $where = array( );
        switch ( $status ) {
        case 'Valid':
            $where[] = 'cancel_date is null';
            break;

        case 'Cancelled':
            $where[] = 'cancel_date is not null';
            break;
        }

        if ( $startDate ) {
            $where[] = "receive_date >= '" . CRM_Utils_Type::escape( $startDate, 'Timestamp' ) . "'";
        }
        if ( $endDate ) {
            $where[] = "receive_date <= '" . CRM_Utils_Type::escape( $endDate, 'Timestamp' ) . "'";
        }

        $whereCond = implode( ' AND ', $where );
        $domainID  = CRM_Core_Config::domainID( );

        $query = "
SELECT sum( total_amount ) as total_amount, count( id ) as total_count
FROM   civicrm_contribution
WHERE  domain_id = $domainID AND $whereCond AND is_test=0
";

        $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        if ( $dao->fetch( ) ) {
            return array( 'amount' => $dao->total_amount,
                          'count'  => $dao->total_count );
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
        $contribution =& new CRM_Contribute_DAO_Contribution( );
        $contribution->contact_id = $contactId;
        $contribution->find( );

        require_once 'CRM/Contribute/DAO/FinancialTrxn.php';
        while ( $contribution->fetch( ) ) {
            self::deleteContribution($contribution->id);
            //self::deleteContributionSubobjects($contribution->id);
            $contribution->delete( );
        }
    }
    /**                                                           
     * Delete the record that are associated with this contribution 
     * record are deleted from contribution product, note and contribution                   
     * @param  int  $id id of the contribution to delete                                                                           
     * 
     * @return boolean  true if deleted, false otherwise
     * @access public 
     * @static 
     */ 
    static function deleteContribution( $id ) 
    {    
        require_once 'CRM/Contribute/DAO/ContributionProduct.php';
        $dao = & new CRM_Contribute_DAO_ContributionProduct();
        $dao->contribution_id = $id;
        $dao->delete();
        
        //Delete Contribution Note
        require_once 'CRM/Core/BAO/Note.php';
        $noteBAO = & new CRM_Core_BAO_Note();
        $noteBAO->entity_table = 'civicrm_contribution';
        $noteBAO->entity_id = $id;
        $noteBAO->find(true);
        if ($noteBAO) {
            $noteBAO->delete( );
        }

        $contribution =& new CRM_Contribute_DAO_Contribution( ); 
        $contribution->id = $id;
        if ( $contribution->find( true ) ) {
            self::deleteContributionSubobjects($id);
            $contribution->delete( ); 
        }

        return true;
    }

    static function deleteContributionSubobjects($contribId) {
        require_once 'CRM/Contribute/DAO/FinancialTrxn.php';
        $trxn =& new CRM_Contribute_DAO_FinancialTrxn();
        $trxn->entity_table = 'civicrm_contribution';
        $trxn->entity_id    = $contribution->id;
        if ($trxn->find(true)) {
            $trxn->delete();
        }

        require_once 'CRM/Core/DAO/ActivityHistory.php';
        $activityHistory =& new CRM_Core_DAO_ActivityHistory();
        $activityHistory->module      = 'CiviContribute';
        $activityHistory->activity_id = $contribution->id;
        if ($activityHistory->find(true)) {
            $activityHistory->delete();
        }
    }

    /**
     * Check if there is a contribution with the same trxn_id or invoice_id
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

        $query = "SELECT id FROM civicrm_contribution WHERE $clause";
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        $result = false;
        while ( $dao->fetch( ) ) {
            $duplicates[] = $dao->id;
            $result = true;
        }
        return $result;
    }
    
    /**
     * takes an associative array and creates a contribution_product object
     *
     * the function extract all the params it needs to initialize the create a
     * contribution_product object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
    
     * @return object CRM_Contribute_BAO_ContributionProduct object
     * @access public
     * @static
     */
    static function addPremium ( &$params ) {

        require_once 'CRM/Contribute/DAO/ContributionProduct.php';
        $contributionProduct = new CRM_Contribute_DAO_ContributionProduct();
        $contributionProduct->copyValues($params);
        return $contributionProduct->save();
    }

    /**
     * Function to get list of contribution fields for profile
     * For now we only allow custom contribution fields to be in
     * profile
     *
     * @return return the list of contribution fields
     * @static
     * @access public
     */
    static function getContributionFields( ) 
    {
        $contributionFields =& CRM_Contribute_DAO_Contribution::export( );

        foreach ($contributionFields as $key => $var) {
            if ($key == 'contact_id') {
                continue;
            }
            $fields[$key] = $var;
        }

        $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport('Contribution'));
        // $fields = CRM_Core_BAO_CustomField::getFieldsForImport('Contribution');
        return $fields;
    }

    static function getCurrentandGoalAmount( $pageID ) {
        $query = "
SELECT p.goal_amount as goal, sum( c.total_amount ) as total
  FROM civicrm_contribution_page p,
       civicrm_contribution      c
 WHERE p.id = c.contribution_page_id
   AND p.id = %1
   AND c.cancel_date is null
   AND p.domain_id = %2
GROUP BY p.id
";

        $config =& CRM_Core_Config::singleton( );
        $params = array( 1 => array( $pageID, 'Integer' ),
                         2 => array( $config->domainID( ), 'Integer' ) );
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        
        if ( $dao->fetch( ) ) {
            return array( $dao->goal, $dao->total );
        } else {
            return array( null, null );
        }
    }

    function createHonorContact( $params , $honorId = null ) {
        $honorParams = array();
        $honorParams["prefix_id"] = $params["honor_prefix"];
        $honorParams["first_name"]   = $params["honor_firstname"];
        $honorParams["last_name"]    = $params["honor_lastname"];
        $honorParams["email"]        = $params["honor_email"];
        $honorParams["contact_type"] = "Individual";
        
        //update if contact  already exists
        if ( $honorId ) {
            $ids = array( );
            $idParams = array( 'id' => $honorId, 'contact_id' => $honorId );
            CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
            $contact =& CRM_Contact_BAO_Contact::createFlat( $honorParams, $ids );
            return $contact->id;    
             
        } else {
            require_once "CRM/Core/BAO/UFGroup.php";
            $ids = CRM_Core_BAO_UFGroup::findContact( $honorParams );
            $contactsIDs = explode( ',', $ids );
            if ( $contactsIDs[0] == "" || count ( $contactsIDs ) > 1) {
                $contact =& CRM_Contact_BAO_Contact::createFlat( $honorParams, $ids );
                return $contact->id;
            } else {
                $contact_id =  $contactsIDs[0];
                $ids = array( );
                $idParams = array( 'id' => $contact_id, 'contact_id' => $contact_id );
                $defaults = array( );
                CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
                $contact =& CRM_Contact_BAO_Contact::createFlat( $honorParams, $ids );
                return $contact->id;    
            }
         }
    }

 /**
     * function to get the sort name of a contact for a particular contribution
     *
     * @param  int    $id      id of the contribution
     *
     * @return null|string     sort name of the contact if found
     * @static
     * @access public
     */
    static function sortName( $id ) {
        $query = "
SELECT civicrm_contact.sort_name
FROM   civicrm_contribution, civicrm_contact
WHERE  civicrm_contribution.contact_id = civicrm_contact.id
  AND  civicrm_contribution.id = {$id}
";
        return CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );
    }
}

?>
