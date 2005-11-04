<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contribute/DAO/ContributionMode.php';

class CRM_Contribute_BAO_ContributionMode extends CRM_Contribute_DAO_ContributionMode 
{

    /**
     * static holder for the default LT
     */
    static $_defaultContributionMode = null;
    

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
     * @return object CRM_Contribute_BAO_ContributionMode object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $contributionMode =& new CRM_Contribute_DAO_ContributionMode( );
        $contributionMode->copyValues( $params );
        if ( $contributionMode->find( true ) ) {
            CRM_Core_DAO::storeValues( $contributionMode, $defaults );
            return $contributionMode;
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
        return CRM_Core_DAO::setFieldValue( 'CRM_Contribute_DAO_ContributionMode', $id, 'is_active', $is_active );
    }


    /**
     * retrieve the default contribution_mode
     *
     * @return object           The default contribution Mode object on success,
     *                          null otherwise
     * @static
     * @access public
     */
    static function &getDefault() 
    {
        if (self::$_defaultContributionMode == null) {
            $params = array('is_default' => 1);
            $defaults = array();
            self::$_defaultContributionMode = self::retrieve($params, $defaults);
        }
        return self::$_defaultContributionMode;
    }

    /**
     * function to add the contribution Mode
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
        $params['is_default'] =  CRM_Utils_Array::value( 'is_default', $params, false );
        
        // action is taken depending upon the mode
        $contributionMode               =& new CRM_Contribute_DAO_ContributionMode( );
        $contributionMode->domain_id    = CRM_Core_Config::domainID( );
        
        $contributionMode->copyValues( $params );;
        
        if ($params['is_default']) {
            $unsetDefault =& new CRM_Contribute_DAO();
            $query = 'UPDATE civicrm_contribution_mode SET is_default = 0';
            $unsetDefault->query($query);
        }
        
        $contributionMode->id = CRM_Utils_Array::value( 'contributionMode', $ids );
        $contributionMode->save( );
        return $contributionMode;
    }
    
    /**
     * Function to delete contribution Mode 
     * 
     * @param int $contributionModeId
     * @static
     */
    
    static function del($contributionModeId) 
    {
        //check the dependencies
        
        //delete from contribution Mode table
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $contributionMode =& new CRM_Contribute_DAO_ContributionMode( );
        $contributionMode->id = $contributionModeId;
        $contributionMode->delete();

    }

}

?>
