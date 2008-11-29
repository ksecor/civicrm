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

/**
 * This class holds all the Pseudo constants that are specific for CiviCase.
 *
 */
class CRM_Case_PseudoConstant extends CRM_Core_PseudoConstant 
{

    /**
     * case statues
     * @var array
     * @static
     */
    static $caseStatus;

    /**
     * case type
     * @var array
     * @static
     */
    static $caseType;

    /**
     * activity type
     * @var array
     * @static
     */
    static $activityType = array( );

    /**
     * case type
     * @var array
     * @static
     */
    static $caseTypePair = array( );

    /**
     * Get all the case statues
     *
     * @access public
     * @return array - array reference of all case statues
     * @static
     */

    public static function caseStatus( )
    {
        if ( ! self::$caseStatus ) {
            self::$caseStatus = array( );
        
            require_once 'CRM/Core/OptionGroup.php';
            self::$caseStatus = CRM_Core_OptionGroup::values('case_status');
        }
        return self::$caseStatus;
    }

    /**
     * Get all the case type
     *
     * @access public
     * @return array - array reference of all case type
     * @static
     */

    public static function caseType( )
    {
        if ( ! self::$caseType ) {
            self::$caseType = array( );
        
            require_once 'CRM/Core/OptionGroup.php';
            self::$caseType = CRM_Core_OptionGroup::values('case_type');
        }
        return self::$caseType;
    }

    /**
     * Get all Activty types for the CiviCase component
     *
     * The static array activityType is returned
     * @param boolean $indexName - true return activity name in array
     * key else activity id as array key.
     *
     * @access public
     * @static
     *
     * @return array - array reference of all activty types.
     */
    public static function activityType( $indexName = true )
    {
        $indexName = (int) $indexName;

        if ( ! array_key_exists($indexName, self::$activityType) ) {
            self::$activityType[$indexName] = array( );
            require_once 'CRM/Core/OptionGroup.php';
            $componentId = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Component',
                                                        'CiviCase',
                                                        'id', 'name' );
            
            $query = "
              SELECT  v.label as label ,v.value as value, v.name as name, v.description as description
              FROM   civicrm_option_value v,
                     civicrm_option_group g
              WHERE  v.option_group_id = g.id
                     AND  g.name         = 'activity_type'
                     AND  v.is_active    = 1 
                     AND  g.is_active    = 1 
                     AND  v.component_id = {$componentId} ";
            
            $query .= "  ORDER BY v.weight";
            
            $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            $$activityTypes = array();
            
            while( $dao->fetch() ) {
                if ( $indexName ) {
                    $index = $dao->name;
                } else {
                    $index = $dao->value;
                }
                $activityTypes[$index]['id']          = $dao->value; 
                $activityTypes[$index]['label']       = $dao->label; 
                $activityTypes[$index]['name']        = $dao->name;
                $activityTypes[$index]['description'] = $dao->description;
            }
            self::$activityType[$indexName] = $activityTypes;
        }
        return self::$activityType[$indexName];
    }

    /**
     * Get the associated case type name/id, given a case Id
     *
     * @access public
     * @return array - array reference of all case type name/id
     * @static
     */
    public static function caseTypeName( $caseId )
    {
        if ( !$caseId ) {
            return false;
        }
        
        if ( ! array_key_exists($caseId, self::$caseTypePair) ) {
            $caseTypes    = self::caseType();
            $caseTypeIds  = CRM_Core_DAO::getFieldValue( 'CRM_Case_DAO_Case',
                                                         $caseId,
                                                         'case_type_id' );
            $caseTypeId   = explode( CRM_Case_BAO_Case::VALUE_SEPERATOR, 
                                     trim($caseTypeIds, 
                                          CRM_Case_BAO_Case::VALUE_SEPERATOR) );
            $caseTypeId   = $caseTypeId[0];
            
            self::$caseTypePair[$caseId] = array( 'id'   => $caseTypeId,
                                                  'name' => $caseTypes[$caseTypeId] );
        }

        return self::$caseTypePair[$caseId];
    }

}