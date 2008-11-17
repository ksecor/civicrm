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
     * category
     * @var array
     * @static
     */
    static $category;

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
     * Get the case type name/id given case Id
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
        
        static $caseTypePair;

        if ( ! $caseTypePair[$caseId] ) {
            $caseTypes    = self::caseType();
            $caseTypeIds  = CRM_Core_DAO::getFieldValue( 'CRM_Case_DAO_Case',
                                                         $caseId,
                                                         'case_type_id' );
            $caseTypeId   = explode( CRM_Case_BAO_Case::VALUE_SEPERATOR, 
                                     trim($caseTypeIds, 
                                          CRM_Case_BAO_Case::VALUE_SEPERATOR) );
            $caseTypeId   = $caseTypeId[0];
            
            $caseTypePair = array();
            $caseTypePair[$caseId] = array( 'id'   => $caseTypeId,
                                            'name' => $caseTypes[$caseTypeId] );
        }
        return $caseTypePair[$caseId];
    }

    /**
     * Get all the category
     *
     * @params $onlyParent boolean true if parent needs to retrieved, false if you want children
     * @access public
     * @return array - array reference of all categories if any
     * @static
     */
    public static function category( $onlyParent  = true,
                                     $returnValue = 'label',
                                     $inputCond   = null ) {
        if ( $onlyParent ) {
            $cond = '(parent_id IS NULL)';
            $index     = '1';
        } else {
            $cond = '(parent_id IS NOT NULL)';
            $index     = '0';
        }

        if ( $inputCond ) {
            $cond .= " AND ($inputCond)";
        }

        $index .= "_$returnValue";

        if ( ! self::$category ) {
            self::$category = array( );
        }

        if ( ! CRM_Utils_Array::value( $index, self::$category ) ) {
            self::$category[$index] = null;
            CRM_Core_PseudoConstant::populate( self::$category[$index],
                                               'CRM_Core_DAO_Category', true, $returnValue, 'is_active', $cond );
        }
        return self::$category[$index];
    }

    static function categoryTree( $componentID ) {
        $query = "
SELECT     c.id as id, c.label as label, c.name as name,
           p.id as p_id 
FROM       civicrm_category c
LEFT JOIN  civicrm_category p ON c.parent_id = p.id 
WHERE      c.component_id = %1
ORDER BY   p.id
";
        $params = array( 1 => array( $componentID, 'Integer' ) );
        $dao = CRM_Core_DAO::executeQuery( $query, $params );

        $tree  = $names = $labels = array( );
        while ( $dao->fetch( ) ) {
            $names [$dao->id] = $dao->name;
            $labels[$dao->id] = $dao->label;
            $parentName  = $dao->p_id  ? $names[$dao->p_id]  : 'Root Category';
            $parentLabel  = $dao->p_id ? $labels[$dao->p_id] : 'Root Category';
            if ( ! array_key_exists( $parentName, $tree ) ) {
                $tree[$parentName] = array( );
            }
            $tree[$parentName][$dao->name] = array( 'id'          => $dao->id,
                                                    'label'       => $dao->label,
                                                    'name'        => $dao->name ,
                                                    'parent'      => $dao->p_id ,
                                                    'parentLabel' => $parentLabel );
        }
	$names['id'] = null;
        $tree['Root Category']['All Names'] = $names;
        return $tree;
    }

}