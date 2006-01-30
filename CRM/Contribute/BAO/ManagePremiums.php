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

require_once 'CRM/Contribute/DAO/ContributionType.php';

class CRM_Contribute_BAO_ContributionType extends CRM_Contribute_DAO_ContributionType 
{

    /**
     * static holder for the default LT
     */
    static $_defaultContributionType = null;
    

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
     * @return object CRM_Contribute_BAO_ContributionType object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $contributionType =& new CRM_Contribute_DAO_ContributionType( );
        $contributionType->copyValues( $params );
        if ( $contributionType->find( true ) ) {
            CRM_Core_DAO::storeValues( $contributionType, $defaults );
            return $contributionType;
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
        return CRM_Core_DAO::setFieldValue( 'CRM_Contribute_DAO_ContributionType', $id, 'is_active', $is_active );
    }

    /**
     * function to add the contribution types
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids) 
    {
        
        $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );
        $params['is_deductible'] =  CRM_Utils_Array::value( 'is_deductible', $params, false );
        
        // action is taken depending upon the mode
        $contributionType               =& new CRM_Contribute_DAO_ContributionType( );
        $contributionType->domain_id    = CRM_Core_Config::domainID( );
        
        $contributionType->copyValues( $params );;
        
        $contributionType->id = CRM_Utils_Array::value( 'contributionType', $ids );
        $contributionType->save( );
        return $contributionType;
    }
    
    /**
     * Function to delete contribution Types 
     * 
     * @param int $contributionTypeId
     * @static
     */
    
    static function del($contributionTypeId) 
    {
        //check dependencies
        
        //delete from contribution Type table
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $contributionType =& new CRM_Contribute_DAO_ContributionType( );
        $contributionType->id = $contributionTypeId;
        $contributionType->delete();
    }

}

?>
