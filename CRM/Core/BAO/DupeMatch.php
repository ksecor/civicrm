<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

/** 
 *  this file contains functions for dupematch
 */

class CRM_Core_BAO_DupeMatch extends CRM_Core_DAO_DupeMatch {

    /**
     * static holder for the default LT
     */
    static $_defaultDupeMatch = null;


    /**
     * class constructor
     */
    function __construct( ) {
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
     * @return object CRM_Core_BAO_IndividualSuffix object if dupmatch found else NULL
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) {
        $dupematch =& new CRM_Core_DAO_DupeMatch( );
        $dupematch->copyValues( $params );
        if ( $dupematch->find( true ) ) {
            //CRM_Core_DAO::storeValues( $dupematch, $defaults );
            $defaults['match_on'] = $dupematch->rule;
            $rule = explode(' AND ', $dupematch->rule );
            $count = 1;
            foreach($rule as $field) {
                $defaults['match_on_'.$count] = trim($field);
                $count++;    
            }
            return $dupematch;
        }
        return null;
    }

    


    /**
     * retrieve the list of suffix
     *
     * @param NULL
     * 
     * @return object           The default activity type object on success,
     *                          null otherwise
     * @static
     * @access public
     */
    static function &getDefault() {
        if (self::$_defaultIndividualSuffix == null) {
            $defaults = array();
            self::$_defaultIndividualSuffix = self::retrieve($params, $defaults);
        }
        return self::$_defaultIndividualSuffix;
    }

    /**
     * function to add the dupematch
     * 
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @return object   Object of CRM_Core_DAO_DupeMatch
     * 
     * @access public
     * @static 
     */
    static function add($rule) {
        // action is taken depending upon the mode
        $dupematch               =& new CRM_Core_DAO_DupeMatch( );
        $dupematch->domain_id    = CRM_Core_Config::domainID( );
        $dupematch-> find(true);
        $id = $dupematch->id;

        $dupematch               =& new CRM_Core_DAO_DupeMatch( );
        $dupematch->domain_id    = CRM_Core_Config::domainID( );
        $dupematch->id           = $id;  
        $dupematch->entity_table = 'civicrm_individual';
        $dupematch->rule         = $rule;
       
        $dupematch->save( );
        return $dupematch;
    }
    
    /**
     * Function to delete DupeMatch 
     * 
     * @param   int  $dupematchId      ID of the dupematch to be deleted.
     * 
     * @return boolean
     * 
     * @static
     * @access public
     */
    static function del($dupematchId) 
    {
        //check dependencies
        $dupematch = & new CRM_Core_DAO_DupeMatch();
        $dupematch->id = $dupematchId;
        $dupematch->delete();
        return true;
    }

}

?>