<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 *
 */
class CRM_Core_BAO_Domain extends CRM_Core_DAO_Domain {
    /**
     * Cache for the current domain object
     */
    static $_domain = null;
    
    /**
     * Cache for a domain's location array
     */
    private $_location = null;
    
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
     * @return object CRM_Core_DAO_Domain object
     * @access public
     * @static
     */
    static function retrieve(&$params, &$defaults)
    {
        return CRM_Core_DAO::commonRetrieve( 'CRM_Core_DAO_Domain', $params, $defaults );
    }
    
    /**
     * Return the domain BAO for the current domain.
     *
     * @param void
     * @return object CRM_Core_BAO_Domain object
     * @access public
     * @static
     */
    static function &getCurrentDomain() {
        if (self::$_domain == null) {
            self::$_domain =& self::getDomainByID(CRM_Core_Config::domainID());
        }
        return self::$_domain;
    }

    /**
     * Get the domain BAO with the given ID
     * 
     * @param int $id       the domain id to find
     * @return null|object CRM_Core_BAO_Domain
     * @access public
     * @static
     */
    static function &getDomainByID($id) {
        $domain =& new CRM_Core_BAO_Domain();
        $domain->id = $id;
        if ($domain->find(true)) {
            return $domain;
        }
        return null;
    }

    /**
     * Get the location values of a domain
     *
     * @param void
     * @return array        Location::getValues
     * @access public
     */
    function &getLocationValues() {
        if ($this->_location == null) {
            $params = array(
                'entity_id' => $this->id, 
                'entity_table' => self::getTableName()
            );
            $loc = array();
            $ids = array();
            CRM_Core_BAO_Location::getValues($params, $loc, $ids, 1);
            
            /* Translate the state/province and country ids to names */
            if (! array_key_exists($loc[1]['address'], 'state_province')) 
            {
                $loc[1]['state_province'] = CRM_Core_PseudoConstant::stateProvince($loc[1]['address']['state_province_id']);
                if (! $loc[1]['address']['state_province']) {
                    $loc[1]['address']['state_province'] =
                    CRM_Core_PseudoConstant::stateProvinceAbbreviation($loc[1]['address']['state_province_id']);
                }
            }

            if (! array_key_exists($loc[1]['address'], 'country')) {
                $loc[1]['address']['country'] = CRM_Core_PseudoConstant::country($loc[1]['address']['country_id']);
                if (! $loc[1]['address']['country']) {
                    $loc[1]['address']['country'] =
                    CRM_Core_PseudoConstant::countryIsoCode($loc[1]['address']['country_id']);
                }
            }
            
            $this->_location = $loc[1];
        }
        return $this->_location;
    }
}

?>
