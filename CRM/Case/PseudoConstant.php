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
     * Get all the category
     *
     * @params $onlyParent boolean true if parent needs to retrieved, false if you want children
     * @access public
     * @return array - array reference of all categories if any
     * @static
     */
    public static function category( $onlyParent  = true,
                                     $returnValue = 'label',
                                     $componentID = null )
    {
        if ( $onlyParent ) {
            $condition = '(parent_id IS NULL)';
            $index     = '1_';
        } else {
            $condition = '(parent_id IS NOT NULL)';
            $index     = '0_';
        }

        if ( $componentID ) {
            $condition .= " AND (component_id = $componentID)";
            $index     .= "_$componentID";
        } else {
            $componentID = 'not set';
            $index     .= "_0";
        }
        $index .= "_$label";

        if ( ! self::$category ) {
            self::$category = array( );
        }

        if ( ! CRM_Utils_Array::value( $index, self::$category ) ) {
            self::$category[$index] = null;
            CRM_Core_PseudoConstant::populate( self::$category[$index],
                                               'CRM_Core_DAO_Category', true, $returnValue, 'is_active', $condition );
        }
        return self::$category[$index];
    }

}


