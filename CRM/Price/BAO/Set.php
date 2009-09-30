<?php

/**
 *
 * @package CRM
 *
 */

require_once 'CRM/Price/DAO/Set.php';

/**
 * Business object for managing price sets
 *
 */
class CRM_Price_BAO_Set extends CRM_Price_DAO_Set
{

    /**
     * class constructor
     */
    function __construct( )
    {
        parent::__construct( );
    }

    /**
     * takes an associative array and creates a price set object
     *
     * @param array $params (reference) an assoc array of name/value pairs
     *
     * @return object CRM_Price_DAO_Set object 
     * @access public
     * @static
     */
    static function create(&$params)
    {
        $priceSetBAO =& new CRM_Price_BAO_Set();
        $priceSetBAO->copyValues($params);
        return $priceSetBAO->save();
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
     * @return object CRM_Core_DAO_CustomGroup object
     * @access public
     * @static
     */
    static function retrieve(&$params, &$defaults)
    {
        return CRM_Core_DAO::commonRetrieve( 'CRM_Price_DAO_Set', $params, $defaults );
    }

    /**
     * update the is_active flag in the db
     *
     * @param  int      $id         id of the database record
     * @param  boolean  $is_active  value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     * @access public
     */
    static function setIsActive($id, $is_active) 
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Price_DAO_Set', $id, 'is_active', $is_active );
    }

    /**
     * Get the price set title.
     *
     * @param int $id id of price set
     * @return string title 
     *
     * @access public
     * @static
     *
     */
    public static function getTitle( $id )
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Price_DAO_Set', $id, 'title' );
    }
   
    /**
     * Check if the price set is in use anywhere.  Returns true in the
     * case that the group is used by an active form, or by a form which
     * has any registrations.
     *
     * @param int $id id of group
     * @return bool
     *
     * @access public
     * @static
     *
     */
    //
    // NOTE: This currently works differently from getUsedBy().
    //
    public static function isUsed( $id )
    {
        // this could be optimized later.  For now, it's a set of
        // separate queries.

        // default not used
        $is_used = false;

        // get events which use this price set
        $queryString = "SELECT entity_table, entity_id FROM civicrm_price_set_entity ";
        $queryString .= "WHERE price_set_id = %1";
        $params = array( 1 => array( $id, 'Integer') );
        $crmEventDAO = CRM_Core_DAO::executeQuery( $queryString, $params );

        $events = array();
        while ( $crmEventDAO->fetch( ) ) {
            $events[ $crmEventDAO->entity_table ][] = $crmEventDAO->entity_id;
        }

        // check if events are active or have participants
        foreach ( $events as $table => $entities ) {
            $entity_list = implode( ',', $entities );
            // a single match is sufficient.  limit 1 for performance
            $queryString = "SELECT COUNT(*) AS is_used FROM $table";
            if ( $table == 'civicrm_event' ) {
                $queryString .= ", civicrm_event";
            }
            $queryString .= " WHERE $table.id IN ( $entity_list )";
            if ( $table == 'civicrm_event' ) {
                $now = date( 'Y-m-d H:i:s' );
                $queryString .= " AND civicrm_event.start_date < '$now' AND civicrm_event.end_date > '$now'";
            }
            $queryString .= " LIMIT 1";
            $params = array();
            $is_used = (bool)CRM_Core_DAO::singleValueQuery( $queryString, $params );
            if ( ! $is_used && $table == 'civicrm_event' ) {
                // check participant table
                $queryString = "SELECT COUNT(*) AS is_used FROM civicrm_participant ";
                $queryString .= "WHERE event_id IN ( $entity_list ) LIMIT 1";
                $is_used = (bool)CRM_Core_DAO::singleValueQuery( $queryString, $params );
            }
        }

        return $is_used;
    }

    /**
     * Return a list of all forms which use this price set.
     *
     * @param int  $id id of price set
     * @param bool $checkPast search for events in the past as well as
     * present or future
     * @param bool $getInactive return only active forms if false, or only
     * inactive if true
     *
     * @return array
     */
    public static function &getUsedBy( $id, $checkPast = false, $getInactive = false ) 
    {
        $usedBy = array( );
        $today = date('Y-m-d');
        $queryString = "SELECT entity_table, entity_id FROM civicrm_price_set_entity ";
        $queryString .= "WHERE price_set_id = %1";
        $params = array( 1 => array( $id, 'Integer') );
        $crmFormDAO = CRM_Core_DAO::executeQuery( $queryString, $params );

        $forms = array( );
        while ( $crmFormDAO->fetch( ) ) {
            $forms[ $crmFormDAO->entity_table ][] = $crmFormDAO->entity_id;
        }

        if ( empty( $forms ) ) {
            return $usedBy;
        }

        require_once 'CRM/Event/DAO/Event.php';
        require_once 'CRM/Core/OptionGroup.php';
        require_once 'CRM/Utils/Array.php';

        $eventTypes  = CRM_Core_OptionGroup::values("event_type" );

        foreach ( $forms as $table => $entities ) {
            switch ($table) {
            case 'civicrm_event':
                //FIXME : clean up the code
                $eventIdList = implode( ',', $entities );
                $queryString = "SELECT id event_id FROM civicrm_event WHERE";
                $queryString .= " id IN ($eventIdList)";
                $crmDAO = CRM_Core_DAO::executeQuery( $queryString );

                while ( $crmDAO->fetch() ) {
                    $is_past = false;
                    $eventInfo = array();
                    $eventDAO =& new CRM_Event_DAO_Event( );
                    $eventDAO->id = $crmDAO->event_id;
                    if ( $eventDAO->find() ) {
                        $eventDAO->fetch();

                        // we only care about the end date, not time.
                        // note that in less than 8000 years, dates will be
                        // longer than 10 characters.
                        $endDate = substr( $eventDAO->end_date, 0, 10 );
                        if ( $checkpast && $endDate < $today ) {
                            $is_past = true;
                        }

                        // past events count as active
                        $is_inactive = ! $eventDAO->is_active || $is_past;

                        // ignore active events if searching for inactive
                        // and ignore inactive events if searching for active
                        if ( $getInactive xor $is_inactive) {
                            continue;
                        }

                        $eventInfo['title'] = $eventDAO->title;
                        $eventInfo['eventType'] = CRM_Utils_Array::value( $eventDAO->event_type_id, $eventTypes );
                        $eventInfo['isPublic'] = $eventDAO->is_public;
                        $eventInfo['startDate'] = substr( $eventDAO->start_date, 0, 10 );
                        $eventInfo['endDate'] = $endDate;
                        $usedBy[$table][$eventDAO->id] = $eventInfo;
                    }
                }
                break;

            case 'civicrm_contribution_page':                
                $contributionIds = implode( ',', $entities );
                $queryString = "SELECT cp.id as id, cp.title as title, cp.start_date as startDate, cp.end_date as endDate,ct.name as type
                                FROM civicrm_contribution_page cp, civicrm_contribution_type ct
                                WHERE ct.id = cp.contribution_type_id
                                AND cp.id IN ($contributionIds);";
                $crmDAO = CRM_Core_DAO::executeQuery( $queryString );
                while ( $crmDAO->fetch() ) {
                    $usedBy[$table][$crmDAO->id]['title']     = $crmDAO->title;
                    $usedBy[$table][$crmDAO->id]['type']      = $crmDAO->type;
                    $usedBy[$table][$crmDAO->id]['startDate'] = $crmDAO->startDate;
                    $usedBy[$table][$crmDAO->id]['endDate']   = $crmDAO->endDate;
                }
                break;
                
            default:
                CRM_Core_Error::fatal( "$table is not supported in PriceSet::usedBy()" );
                break;

            }
        }
        return $usedBy;
    }

    /**
     * Determine if a price set has fields
     *
     * @param int id Price Set id
     *
     * @return boolean
     *
     * @access public
     * @static
     */
    public static function hasFields($id)
    {
        require_once 'CRM/Price/DAO/Field.php';
        $priceField =& new CRM_Price_DAO_Field();
        $priceField->price_set_id = $id;
        $numFields = $priceField->count();

        return (bool)$numFields;
    }

    /**
     * Delete the price set
     *
     * @param int $id Price Set id
     *
     * @return boolean false if fields exist for this set, true if the
     * set could be deleted
     *
     * @access public
     * @static
     */
    public static function deleteSet( $id )
    {
        // remove from all inactive forms
        $usedBy =& self::getUsedBy( $id, true, true );
        if ( isset( $usedBy['civicrm_event'] ) ) {
            require_once 'CRM/Event/DAO/Event.php';
            foreach ( $usedBy['civicrm_event'] as $eventId => $unused ) {
                $eventDAO =& new CRM_Event_DAO_Event( );
                $eventDAO->id = $eventId;
                $eventDAO->find( );
                while ( $eventDAO->fetch( ) ) {
                    self::removeFrom( 'civicrm_event', $eventDAO->id );
                }
            }
        }
        
        // delete price fields
        require_once 'CRM/Price/DAO/Field.php';
        $priceField =& new CRM_Price_DAO_Field( );
        $priceField->price_set_id = $id;
        $priceField->find( );
        while ( $priceField->fetch( ) ) {
            // delete options first
            CRM_Price_BAO_Field::deleteField( $priceField->id );
        }
        
        $set     =& new CRM_Price_DAO_Set( );
        $set->id = $id;
        return $set->delete( );
    }
    
    /**
     * Link the price set with the specified table and id
     *
     * @param string $entityTable
     * @param integer $entityId
     * @param integer $priceSetId
     * @return bool
     */
    public static function addTo( $entityTable, $entityId, $priceSetId ) 
    {
        // verify that the price set exists
        $dao =& new CRM_Price_DAO_Set( );
        $dao->id = $priceSetId;
        if ( !$dao->find( ) ) {
            return false;
        }
        unset( $dao );

        require_once 'CRM/Price/DAO/SetEntity.php';
        $dao =& new CRM_Price_DAO_SetEntity( );
        // find if this already exists
        $dao->entity_table = $entityTable;
        $dao->entity_id = $entityId;
        $dao->find( );
        // add or update price_set_id
        $dao->price_set_id = $priceSetId;
        return $dao->save( );
    }

    /**
     * Delete price set for the given entity and id
     *
     * @param string $entityTable
     * @param integer $entityId
     */
    public static function removeFrom( $entityTable, $entityId ) 
    {
        require_once 'CRM/Price/DAO/SetEntity.php';
        $dao =& new CRM_Price_DAO_SetEntity( );
        $dao->entity_table = $entityTable;
        $dao->entity_id = $entityId;
        return $dao->delete();
    }

    /**
     * Find a price_set_id associatied with the given table and id
     *
     * @param string $entityTable
     * @param integer $entityId
     * @return integer|false price_set_id, or false if none found
     */
    public static function getFor( $entityTable, $entityId ) 
    {
        require_once 'CRM/Price/DAO/SetEntity.php';
        $dao =& new CRM_Price_DAO_SetEntity( );
        $dao->entity_table = $entityTable;
        $dao->entity_id = $entityId;
        if ( $dao->find( true ) ) {
            return $dao->price_set_id;
        } else {
            return false;
        }
    }

     /**
      * Find a price_set_id associatied with the given option value or  field ID 
      * @param array $params (reference) an assoc array of name/value pairs
      *                      array may contain either option id or
      *                      price field id 
      *
      * @return price set id on success, null  otherwise 
      * @static
      * @access public
      */
    public static function getSetId( &$params ) 
    {
        $fid = null;
        
        require_once 'CRM/Utils/Array.php';
        if ( $oid = CRM_Utils_Array::value( 'oid', $params ) ) {
            require_once 'CRM/Core/DAO/OptionGroup.php';
            $optionGroup     = new CRM_Core_DAO_OptionGroup( );
            $optionGroup->id = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', 
                                                            $oid, 'option_group_id' );
            if ( $optionGroup->find( true ) ) {
                $groupName   = explode( ".", $optionGroup->name );
                $fid         = $groupName[2];
            }
            
        } else {
            $fid = CRM_Utils_Array::value( 'fid', $params ) ;
        }
        
        if ( isset ( $fid ) ) {
            return CRM_Core_DAO::getFieldValue( 'CRM_Price_DAO_Field', 
                                                $fid, 'price_set_id' );
            
        }
        return null;

    }

    /**
     * Return an associative array of all price sets
     *
     * @param bool $withInactive whether or not to include inactive entries
     *
     * @return array associative array of id => name
     */
    public static function getAssoc( $withInactive = false, $extends = false ) 
    {
        $query = "
    SELECT 
       DISTINCT ( price_set_id ) as id, title 
    FROM 
       civicrm_price_field, 
       civicrm_price_set 
    WHERE 
       civicrm_price_set.id = civicrm_price_field.price_set_id ";

        if ( !$withInactive ) {
           $query .= " AND civicrm_price_set.is_active = 1 ";
        }

        if ( $extends ) {
            $query .= " AND civicrm_price_set.extends LIKE '%{$extends}%' ";
        }
        
        $dao =& CRM_Core_DAO::executeQuery( $query );
        $priceSets = array();
        while ( $dao->fetch() ) {
            $priceSets[$dao->id] = $dao->title;
        }       
        return $priceSets;
    }

    /**
     * Get price set details
     *
     * An array containing price set details (including price fields) is returned
     *
     * @param int $setId - price set id whose details are needed
     * @return array $setTree - array consisting of field details
     */
    public static function getSetDetail($setID, $required = true) 
    {
        // create a new tree
        $setTree = array();
        $select = $from = $where = $orderBy = '';

        $priceFields = array(
            'id',
            'name',
            'label',
            'html_type',
            'is_enter_qty',
            'help_post',
            'is_display_amounts',
            'options_per_line',
            'is_active'
        );
        if ( $required == true ) {
            $priceFields[] = 'is_required';   
        }
        // create select
        $select = 'SELECT ' . implode( ',', $priceFields );
        $from = ' FROM civicrm_price_field';
        
        $params = array( );
        $params[1] = array( $setID, 'Integer' );
        $where = ' WHERE price_set_id = %1';
        $where .= ' AND is_active = 1';

        $orderBy = ' ORDER BY weight';

        $sql = $select . $from . $where . $orderBy;

        $dao =& CRM_Core_DAO::executeQuery( $sql, $params );

        require_once 'CRM/Price/BAO/Field.php';
        while ( $dao->fetch() ) {
            $fieldID = $dao->id;

            $setTree[$setID]['fields'][$fieldID] = array();
            $setTree[$setID]['fields'][$fieldID]['id'] = $fieldID;

            foreach ( $priceFields as $field ) {
                if ( $field == 'id' || is_null( $dao->$field) ) {
                    continue;
                }
                $setTree[$setID]['fields'][$fieldID][$field] = $dao->$field;
                $setTree[$setID]['fields'][$fieldID]['options'] = CRM_Price_BAO_Field::getOptions( $fieldID, false );
            }
        }

        // also get the pre and post help from this price set
        $sql = "
SELECT help_pre, help_post
FROM   civicrm_price_set
WHERE  id = %1";
        $dao =& CRM_Core_DAO::executeQuery( $sql, $params );
        if ( $dao->fetch( ) ) {
            $setTree[$setID]['help_pre'] = $dao->help_pre;
            $setTree[$setID]['help_post'] = $dao->help_post;
        }

        return $setTree;
    }

    static function initSet( &$form, $id, $entityTable = 'civicrm_event' ) 
    {
        // get price info
        if ( $priceSetId = self::getFor( $entityTable, $id ) ) {
            if ( $form->_action & CRM_Core_Action::UPDATE ){
                require_once 'CRM/Event/BAO/Participant.php';
                $form->_values['line_items'] = CRM_Event_BAO_Participant::getLineItems( $form->_participantId );
                $required = false;
            } else {
                $required = true;
            }
            $form->_priceSetId = $priceSetId;
            $priceSet = self::getSetDetail($priceSetId, $required);
            $form->_priceSet = CRM_Utils_Array::value($priceSetId,$priceSet);
            $form->_values['fee'] = CRM_Utils_Array::value($priceSetId,$priceSet);
            $form->set('priceSetId', $form->_priceSetId);
            $form->set('priceSet', $form->_priceSet);
            return $priceSetId;
        } else {
            return false;
        }
    }    
    
    static function processAmount( &$fields, &$params, &$lineItem ) 
    {
        // using price set
        $totalPrice    = 0;
        $radioLevel    = $checkboxLevel = $selectLevel = $textLevel = array( );
        
        foreach ( $fields as $id => $field ) {
            if ( empty( $params["price_{$id}"]) && $params["price_{$id}"] == null ) {
                // skip if nothing was submitted for this field
                continue;
            }
            
            switch ( $field['html_type'] ) {
            case 'Text':
                $params["price_{$id}"] = array( key( $field['options'] ) => $params["price_{$id}"] );
                self::getLineItem( $id, $params, $field, $lineItem );
                $totalPrice += $lineItem[key( $field['options'] )]['line_total'];
                break;
                
            case 'Radio':
                $params["price_{$id}"] = array( $params["price_{$id}"] => 1 );
                $optionValueId = CRM_Utils_Array::key( 1, $params["price_{$id}"] );
                $optionLabel   = $field['options'][$optionValueId]['label'];
                $params['amount_priceset_level_radio']                = array( );
                $params['amount_priceset_level_radio'][$optionValueId]= $optionLabel;
                if( isset( $radioLevel ) ) {
                    $radioLevel   = array_merge( $radioLevel,
                                                 array_keys( $params['amount_priceset_level_radio'] ) );   
                } else {
                    $radioLevel   = array_keys($params['amount_priceset_level_radio']);
                }
                self::getLineItem( $id, $params, $field, $lineItem );
                $totalPrice += $lineItem[$optionValueId]['line_total'];
                break;

            case 'Select': 
                $params["price_{$id}"] = array( $params["price_{$id}"] => 1 );
                $optionValueId    = CRM_Utils_Array::key( 1, $params["price_{$id}"] );
                $optionLabel      = $field['options'][$optionValueId]['label'];
                $params['amount_priceset_level_select']                 = array();
                $params['amount_priceset_level_select']
                    [CRM_Utils_Array::key( 1, $params["price_{$id}"] )] = $optionLabel;
                if( isset( $selectLevel ) ) {
                    $selectLevel   = array_merge($selectLevel,array_keys($params['amount_priceset_level_select']));   
                } else {
                    $selectLevel   = array_keys($params['amount_priceset_level_select']);
                }
                self::getLineItem( $id, $params, $field, $lineItem );
                $totalPrice += $lineItem[$optionValueId]['line_total'];
                break;
                
            case 'CheckBox':
                $params['amount_priceset_level_checkbox'] = $optionIds = array( );
                foreach ( $params["price_{$id}"] as $optionId => $option ) {
                    $optionIds[] = $optionId;
                    $optionLabel = $field['options'][$optionId]['label'];
                    $params['amount_priceset_level_checkbox']["{$field['options'][$optionId]['id']}"]= $optionLabel;
                    if( isset($checkboxLevel) ){
                        $checkboxLevel=array_unique( 
                                                    array_merge(
                                                                $checkboxLevel, 
                                                                array_keys($params['amount_priceset_level_checkbox'])
                                                                )
                                                     );
                    } else {
                        $checkboxLevel=array_keys($params['amount_priceset_level_checkbox']);
                    }
                }
                self::getLineItem( $id, $params, $field, $lineItem );
                foreach( $optionIds as $optionId ) {
                    $totalPrice += $lineItem[$optionId]['line_total'];
                }
                break;
            }
        }
        
        $amount_level = array( );
        if ( is_array( $lineItem ) ) {
            foreach( $lineItem as $values ) {
                if ( $values['html_type'] == 'Text' ) {
                    $amount_level[] = $values['label'] . ' - ' . $values['qty'];
                    continue;
                }
                $amount_level[] = $values['label'];
            }
        }
        
        require_once 'CRM/Core/BAO/CustomOption.php';
        $params['amount_level'] =
            CRM_Core_BAO_CustomOption::VALUE_SEPERATOR .
            implode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $amount_level ) .
            CRM_Core_BAO_CustomOption::VALUE_SEPERATOR; 
        $params['amount']       = $totalPrice;
    }
    
    /**
     * This method will create the lineItem array required for
     * processAmount method
     *
     * @param  int   $fid       price set field id
     * @param  array $params    referance to form values
     * @param  array $fields    referance to array of fields belonging
     *                          to the price set used for particular event
     * @param  array $values    referance to the values array(this is
     *                          lineItem array)
     *
     * @return void
     * @access static
     */
    static function getLineItem( $fid, &$params, &$fields, &$values )
    {
        if ( empty( $params["price_{$fid}"] ) ) {
            return;
        }

        $optionIDs = implode( ',', array_keys( $params["price_{$fid}"] ) );
        $sql = "
SELECT id, option_group_id, label, description
FROM   civicrm_option_value
WHERE  id IN ($optionIDs)
";
        $dao = CRM_Core_DAO::executeQuery( $sql,
                                           CRM_Core_DAO::$_nullArray );
        $optionValues = array( );
        while ( $dao->fetch( ) ) {
            $optionValues[$dao->id] = array('gid'         => $dao->option_group_id,
                                            'label'       => $dao->label,
                                            'description' => $dao->description );
        }
                            
        foreach( $params["price_{$fid}"] as $oid => $qty ) {
            $price        = $fields['options'][$oid]['name'];
            $values[$oid] = array(
                                  'price_field_id'   => $fid,
                                  'option_value_id'  => $oid,
                                  'option_group_id'  => $optionValues[$oid]['gid'],
                                  'label'            => $optionValues[$oid]['label'],
                                  'description'      => $optionValues[$oid]['description'],
                                  'qty'              => $qty,
                                  'unit_price'       => $price,
                                  'line_total'       => $qty * $fields['options'][$oid]['name'],
                                  'html_type'        => $fields['html_type']
                                  );
        }
    }
}


