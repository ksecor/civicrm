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
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/DAO/UFField.php';
/**
 * This class contains function for UFField
 *
 */
class CRM_Core_BAO_UFField extends CRM_Core_DAO_UFField 
{

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
     * @return object CRM_Core_BAO_UFField object
     * @access public
     * @static
     */
    static function retrieve(&$params, &$defaults)
    {
        return CRM_Core_DAO::commonRetrieve( 'CRM_Core_DAO_UFField', $params, $defaults );
    }
    
    /**
     * Get the form title.
     *
     * @param int $id id of uf_form
     * @return string title
     *
     * @access public
     * @static
     *
     */
    public static function getTitle( $id )
    {
        return CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFField', $groupId, 'title' );
    }
    /**
     * update the is_active flag in the db
     *
     * @param int      $id         id of the database record
     * @param boolean  $is_active  value we want to set the is_active field
     *
     * @return Object              DAO object on sucess, null otherwise
     * @access public
     * @static
     */
    static function setIsActive($id, $is_active) 
    {
        //check if custom data profile field is disabled
        if ($is_active) {
            if (CRM_Core_BAO_UFField::checkUFStatus($id)) {
                CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_UFField', $id, 'is_active', $is_active );
            } else {
                CRM_Core_Session::setStatus(ts('Cannot enable this UF field since the used custom field is disabled.'));
            }
        } else {
            return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_UFField', $id, 'is_active', $is_active );
        }
    }
    
    /**
     * Delete the profile Field.
     *
     * @param int  $id    Field Id 
     * 
     * @return boolean
     *
     * @access public
     * @static
     *
     */
    public static function del($id) 
    { 
        //delete  field field
        $field = & new CRM_Core_DAO_UFField();
        $field->id = $id; 
        $field->delete();
        return true;
    }
    
    /**
     * Function to check duplicate for duplicate field in a group
     * 
     * @param array $params an associative array with field and values
     * @ids   array $ids    array that containd ids 
     *
     *@access public
     *@static
     */
    public static function duplicateField($params, $ids)
    {
        $ufField                   =& new CRM_Core_DAO_UFField();
        $ufField->field_name       = $params['field_name'][0];
        $ufField->location_type_id = CRM_Utils_Array::value( 1, $params['field_name'] );
        $ufField->phone_type       = CRM_Utils_Array::value( 2, $params['field_name'] );
        $ufField->uf_group_id      = CRM_Utils_Array::value( 'uf_group', $ids );
        if (CRM_Utils_Array::value( 'uf_field', $ids )) {
            $ufField->whereAdd("id <> ".CRM_Utils_Array::value( 'uf_field', $ids ));
        }

        return $ufField->find(true);
    }

    
    /**
     * function to add the UF Field
     *
     * @param array $params (reference) array containing the values submitted by the form
     * @param array $ids    (reference) array containing the id
     * 
     * @return object CRM_Core_BAO_UFField object
     * 
     * @access public
     * @static 
     * 
     */
    static function add( &$params, &$ids) 
    {
      // set values for uf field properties and save
        $ufField                   =& new CRM_Core_DAO_UFField();
        $ufField->field_type       = $params['field_name'][0];
        $ufField->field_name       = $params['field_name'][1];
	$uffield->location_type_id = CRM_Utils_Array::value( 2, $params['field_name'], null );
	$ufField->phone_type       = CRM_Utils_Array::value( 3, $params['field_name'], 'NULL' );

        $ufField->listings_title = CRM_Utils_Array::value( 'listings_title', $params );
        $ufField->visibility     = $params['visibility'];
        $ufField->help_post      = $params['help_post'];
        $ufField->label          = $params['label'];

        $ufField->is_required     = CRM_Utils_Array::value( 'is_required'    , $params, false );
        $ufField->is_active       = CRM_Utils_Array::value( 'is_active'      , $params, false );
        $ufField->in_selector     = CRM_Utils_Array::value( 'in_selector'    , $params, false );
        $ufField->is_view         = CRM_Utils_Array::value( 'is_view'        , $params, false );
        $ufField->is_registration = CRM_Utils_Array::value( 'is_registration', $params, false );
        $ufField->is_match        = CRM_Utils_Array::value( 'is_match'       , $params, false );
        $ufField->is_searchable   = CRM_Utils_Array::value( 'is_searchable'  , $params, false );

        // fix for CRM-316
        if ( isset( $ids['uf_field'] ) ) {
            $uf =& new CRM_Core_DAO_UFField();
            $uf->id = $ids['uf_field'];
            $uf->find();

            if ( $uf->fetch() && $uf->weight != CRM_Utils_Array::value( 'weight', $params, false ) ) {
                $searchWeight =& new CRM_Core_DAO_UFField();
                $searchWeight->uf_group_id = $ids['uf_group'];
                $searchWeight->weight = CRM_Utils_Array::value( 'weight', $params, false );
                
                if ( $searchWeight->find() ) {                   
                    $query = "SELECT id FROM civicrm_uf_field WHERE weight >= %1 AND uf_group_id = %2";
                    $p = array( 1 => array( $searchWeight->weight, 'Integer' ),
                                2 => array( $ids['uf_group']     , 'Integer' ) );
                    $tempDAO =& CRM_Core_DAO::executeQuery($query, $p);
		    $fieldIds = array();
                    while($tempDAO->fetch()) {
                        $fieldIds[] = $tempDAO->id; 
                    }
		    if ( !empty($fieldIds) ) {
                        $sql = "UPDATE civicrm_uf_field SET weight = weight + 1 WHERE id IN ( ".implode(",", $fieldIds)." ) ";
                        CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
                    }
                }
            }                
            $ufField->weight = CRM_Utils_Array::value( 'weight', $params, false );
        } else {
            $uf =& new CRM_Core_DAO_UFField();
            $uf->uf_group_id = $ids['uf_group'];
            $uf->weight = CRM_Utils_Array::value( 'weight', $params, false );
            
            if ( $uf->find() ) {
                $query = "SELECT id FROM civicrm_uf_field WHERE weight >= %1 AND uf_group_id = %2";
                $p = array( 1 => array( $params['weight'], 'Integer' ),
                            2 => array( $ids['uf_group'] , 'Integer' ) );
                $tempDAO =& CRM_Core_DAO::executeQuery($query, $p);

                $fieldIds = array();                
                while($tempDAO->fetch()) {
                    $fieldIds[] = $tempDAO->id;                
                }                

                if ( !empty($fieldIds) ) {
                    $sql = "UPDATE civicrm_uf_field SET weight = weight + 1 WHERE id IN ( ".implode(",", $fieldIds)." ) ";
                    CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray ); 
                }
            }

            $ufField->weight = CRM_Utils_Array::value( 'weight', $params, false );
        }

        // need the FKEY - uf group id
        $ufField->uf_group_id = CRM_Utils_Array::value('uf_group', $ids , false );
        $ufField->id          = CRM_Utils_Array::value('uf_field', $ids , false ); 
        return $ufField->save();
    }

    /**
     * Function to enable/disable profile field given a custom field id
     *
     * @param int      $customFieldId     custom field id
     * @param boolean  $is_active         set the is_active field
     *
     * @return void
     * @static
     * @access public
     */
    static function setUFField($customFieldId, $is_active) 
    {
        //find the profile id given custom field
        $ufField =& new CRM_Core_DAO_UFField();
        $ufField->field_name = "custom_".$customFieldId;
        
        $ufField->find();
        while ($ufField->fetch()) {
            //enable/ disable profile
            CRM_Core_BAO_UFField::setIsActive($ufField->id, $is_active);
        }
    }

  /**
     * Function to copy exisiting profile fields to 
     * new profile from the already built profile
     *
     * @param int      $old_id  from which we need to copy     
     * @param boolean  $new_id  in which to copy  
     *
     * @return void
     * @static
     * @access public
     */
    static function copy( $old_id, $new_id ) 
    {
        $ufField =& new CRM_Core_DAO_UFField( );
        $ufField->uf_group_id = $old_id;
        $ufField->find( );
        while( $ufField->fetch( ) ) {
            //copy the field records as it is on new ufgroup id
            $ufField->uf_group_id = $new_id;
            $ufField->id          = null;
            $ufField->save();
        }
    }

    /**
     * Function to delete profile field given a custom field
     *
     * @param int   $customFieldId      ID of the custom field to be deleted
     *
     * @return void
     * 
     * @static
     * @access public
     */
    function delUFField($customFieldId)
    {
        //find the profile id given custom field id
        $ufField =& new CRM_Core_DAO_UFField();
        $ufField->field_name = "custom_".$customFieldId;
        
        $ufField->find();
        while ($ufField->fetch()) {
            //enable/ disable profile
            CRM_Core_BAO_UFField::del($ufField->id);
        }
    }

    /**
     * Function to enable/disable profile field given a custom group id
     *
     * @param int      $customGroupId custom group id
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return void
     * @static
     * @access public
     */
    function setUFFieldStatus ($customGroupId, $is_active) 
    {
        //find the profile id given custom group id
        $queryString = "SELECT civicrm_custom_field.id as custom_field_id
                        FROM   civicrm_custom_field, civicrm_custom_group
                        WHERE  civicrm_custom_field.custom_group_id = civicrm_custom_group.id
                          AND  civicrm_custom_group.id = %1";
        $p = array( 1 => array( $customGroupId, 'Integer' ) );
        $dao =& CRM_Core_DAO::executeQuery($queryString, $p);
        
        while ($dao->fetch()) {
            //enable/ disable profile
            CRM_Core_BAO_UFField::setUFField($dao->custom_field_id, $is_active);
        }
    }
    
    /**
     * Function to check the status of custom field used in uf fields
     *
     * @params  int $UFFieldId     uf field id 
     *
     * @return boolean   false if custom field are disabled else true
     * @static
     * @access public
     */
    static function checkUFStatus($UFFieldId) 
    {
        $fieldName = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_UFField', $UFFieldId, 'field_name' );
        
        $customFieldId = explode('_', $fieldName);
        
        $customField =& new CRM_Core_DAO_CustomField();
        $customField->id = $customFieldId[1];

        if ($customField->find(true) ) { // if uf field is custom field
            if ( !$customField->is_active) {
                return false;
            } else {
                return true;
            }
        } else { // this is if field is not a custom field
            return true;
        }
    }


    /**
     * function to check for mix profile fields (eg: individual + other contact types)
     *
     * @params int     $ufGroupId  uf group id 
     * @params boolean $check      this is to check mix profile (if true it will check if profile is
     *                             pure ie. it contains only one contact type)
     *
     * @return  true for mix profile else false
     * @acess public
     * @static
     */
    static function checkProfileType($ufGroupId, $check = false) 
    {
        $ufField =& new CRM_Core_DAO_UFField();
        $ufField->uf_group_id = $ufGroupId;
        $ufField->is_active = 1;
        $ufField->find();
        $fields = array( );
        
        while ( $ufField->fetch() ) {
            if ($ufField->field_type == 'Individual') {
                $fields['Individual'] += 1;
            } else if ($ufField->field_type == 'Contribution') {
                $fields['Contribution'] += 1;
            } else {
                $fields['Other'] +=1;
            }
        }
        
        if ( $check &&
             count($fields) > 1 ) {
            return true;
        }
        
        if ( ($fields['Individual'] && $fields['Other']) || $fields['Contribution'] && $fields['Other'] ) {
            return true;
        }
        return false;
    }

    /**
     * function to get the profile type (eg: individual/organization/household)
     *
     * @params int $ufGroupId  uf group id 
     *
     * @return  contact_type
     * @acess public
     * @static
     */
    static function getProfileType($ufGroupId) 
    {
        require_once "CRM/Core/SelectValues.php";
        $profileTypes = CRM_Core_SelectValues::contactType();
        $otherTypes   = array ("Student" => "Students", "Contribution" => "Contributions", "Participant" => "Participants");
        $profileTypes = array_merge($profileTypes, $otherTypes);
        
        $ufField =& new CRM_Core_DAO_UFField();
        $ufField->uf_group_id = $ufGroupId;
        
        $ufField->find();

        $fieldType = null;
        while ( $ufField->fetch() ) {
            if ( array_key_exists( $ufField->field_type, $profileTypes ) ) {
                if ( $fieldType &&
                     $fieldType != $ufField->field_type) {
                    return 'Mixed';
                }
                $fieldType = $ufField->field_type;
            }
        }
        return $fieldType;
    }

    /**
     * function to check for mix profiles groups (eg: individual + other contact types)
     *
     * @return  true for mix profile group else false
     * @acess public
     * @static
     */
    static function checkProfileGroupType( $ctype ) 
    {
        $ufGroup =& new CRM_Core_DAO_UFGroup();

        $query = "
SELECT ufg.id as id
  FROM civicrm_uf_group as ufg, civicrm_uf_join as ufj
 WHERE ufg.id = ufj.uf_group_id
   AND ufj.module = 'User Registration'
   AND ufg.is_active = 1 
   AND ufg.domain_id = " . CRM_Core_Config::domainID( );

        $ufGroup =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
        
        $fields = array( );
        $validProfiles = array( 'Individual', 'Organization', 'Household', 'Contribution' );
        while ( $ufGroup->fetch() ) {
            $profileType = self::getProfileType($ufGroup->id);
            if ( in_array( $profileType, $validProfiles ) ) {
                continue;
            } else if ( $profileType ) {
                return false;
            }
        }

        return true;
    }

}
?>
