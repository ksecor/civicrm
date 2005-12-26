<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

/**
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
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive($id, $is_active) {
        return CRM_Core_DAO::setFieldValue( 'CRM_Core_DAO_UFField', $id, 'is_active', $is_active );
    }

     /**
     * Delete the profile Field.
     *
     * @param int    id  Field Id 
     * 
     * @return void
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
        $ufField->location_type_id = $params['field_name'][1];
        $ufField->phone_type       = $params['field_name'][2];
        $ufField->uf_group_id      = CRM_Utils_Array::value( 'uf_group', $ids );
        if (CRM_Utils_Array::value( 'uf_field', $ids )) {
            $ufField->whereAdd("id <> ".CRM_Utils_Array::value( 'uf_field', $ids ));
        }

        return $ufField->find(true);
        
    }

    /**
     * function to add the UF Field
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add( &$params, &$ids) {
        
        // set values for uf field properties and save
        $ufField                   =& new CRM_Core_DAO_UFField();
        $ufField->field_name       = $params['field_name'][0];
        $ufField->location_type_id = $params['field_name'][1];
        $ufField->phone_type       = $params['field_name'][2];
        

        $ufField->listings_title = $params['listings_title'];
        $ufField->visibility     = $params['visibility'];
        $ufField->help_post      = $params['help_post'];

        $ufField->is_required     = CRM_Utils_Array::value( 'is_required'    , $params, false );
        $ufField->is_active       = CRM_Utils_Array::value( 'is_active'      , $params, false );
        $ufField->in_selector     = CRM_Utils_Array::value( 'in_selector'    , $params, false );
        $ufField->is_view         = CRM_Utils_Array::value( 'is_view'        , $params, false );
        $ufField->is_registration = CRM_Utils_Array::value( 'is_registration', $params, false );
        $ufField->is_match        = CRM_Utils_Array::value( 'is_match'       , $params, false );
        $ufField->is_searchable   = CRM_Utils_Array::value( 'is_searchable'  , $params, false );
        
        // fix for CRM-316
        if ( $ids['uf_field'] ) {

            $uf =& new CRM_Core_DAO_UFField();
            $uf->id = $ids['uf_field'];
            $uf->find();

            
            if ( $uf->fetch() && $uf->weight != CRM_Utils_Array::value( 'weight', $params, false ) ) {
                    
                $searchWeight =& new CRM_Core_DAO_UFField();
                $searchWeight->uf_group_id = $ids['uf_group'];
                $searchWeight->weight = CRM_Utils_Array::value( 'weight', $params, false );
                
                if ( $searchWeight->find() ) {                   
                    
                    $tempDAO =& new CRM_Core_DAO();
                    $query = "SELECT id FROM civicrm_uf_field WHERE weight >= ". $searchWeight->weight ." AND uf_group_id = ".$ids['uf_group'];
                    $tempDAO->query($query);

                    $fieldIds = array();
                    while($tempDAO->fetch()) {
                        $fieldIds[] = $tempDAO->id; 
                    }
                    
                    if ( !empty($fieldIds) ) {
                        $ufDAO =& new CRM_Core_DAO();
                        $updateSql = "UPDATE civicrm_uf_field SET weight = weight + 1 WHERE id IN ( ".implode(",", $fieldIds)." ) ";
                        $ufDAO->query($updateSql);                    
                    }
                }
            }                
             
            $ufField->weight = CRM_Utils_Array::value( 'weight', $params, false );
            
        } else {
            $uf =& new CRM_Core_DAO_UFField();
            $uf->uf_group_id = $ids['uf_group'];
            $uf->weight = CRM_Utils_Array::value( 'weight', $params, false );
            
            if ( $uf->find() ) {
                $tempDAO =& new CRM_Core_DAO();
                $query = "SELECT id FROM civicrm_uf_field WHERE weight >= ". CRM_Utils_Array::value( 'weight', $params, false ) ." AND uf_group_id = ".$ids['uf_group'];
                $tempDAO->query($query);

                $fieldIds = array();                
                while($tempDAO->fetch()) {
                    $fieldIds[] = $tempDAO->id;                
                }                

                if ( !empty($fieldIds) ) {
                    $ufDAO =& new CRM_Core_DAO();
                    $updateSql = "UPDATE civicrm_uf_field SET weight = weight + 1 WHERE id IN ( ".implode(",", $fieldIds)." ) ";
                    $ufDAO->query($updateSql);
                }
            }

            $ufField->weight = CRM_Utils_Array::value( 'weight', $params, false );
        }


        // need the FKEY - uf group id
        $ufField->uf_group_id = CRM_Utils_Array::value('uf_group', $ids , false );
        $ufField->id          = CRM_Utils_Array::value('uf_field', $ids , false ); 
        return $ufField->save();

        
        
    }

    
    
    
}

?>
