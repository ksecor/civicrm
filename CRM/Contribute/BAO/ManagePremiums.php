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

require_once 'CRM/Contribute/DAO/Product.php';

class CRM_Contribute_BAO_ManagePremiums extends CRM_Contribute_DAO_Product 
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
     * @return object CRM_Contribute_BAO_ManagePremium object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $premium =& new CRM_Contribute_DAO_Product( );
        $premium->copyValues( $params );
        if ( $premium->find( true ) ) {
            CRM_Core_DAO::storeValues( $premium, $defaults );
            return $premium;
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
        return CRM_Core_DAO::setFieldValue( 'CRM_Contribute_DAO_Product', $id, 'is_active', $is_active );
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
        $premium               =& new CRM_Contribute_DAO_Product( );
        $premium->copyValues( $params );;
        
        $premium->id = CRM_Utils_Array::value( 'premium', $ids );
        $premium->save( );
        return $premium;
    }
    
    /**
     * Function to delete contribution Types 
     * 
     * @param int $contributionTypeId
     * @static
     */
    
    static function del($premiumID) 
    {
        //check dependencies
        
        //delete from contribution Type table
        require_once 'CRM/Contribute/DAO/Product.php';
        $premium =& new CRM_Contribute_DAO_Product( );
        $premium->id = $premiumID;
        $premium->delete();
    }

}

?>
