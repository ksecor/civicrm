<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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

require_once 'CRM/Core/DAO/Domain.php';

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
     * @param NULL
     * 
     * @return  object  CRM_Core_BAO_Domain object
     * 
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
     * 
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
     * @param NULL
     * 
     * @return array        Location::getValues
     * @access public
     */
    function &getLocationValues() {
        if ($this->_location == null) {
            $params = array(
                'domain_id' => $this->id,
                'entity_id' => $this->id, 
                'entity_table' => self::getTableName()
            );
            $values = array();
            $ids = array();
            
            CRM_Core_BAO_Location::getValues($params, $values, $ids, 1);
            if ( ! CRM_Utils_Array::value( 'location', $values ) ||
                 ! CRM_Utils_Array::value( '1', $values['location'] ) ) {
                $this->_location = null;
                return $this->_location;
            }
            $loc =& $values['location'];
            
            /* Translate the state/province and country ids to names */
            if ( CRM_Utils_Array::value( 'address', $loc[1] ) ) {
                if ( ! array_key_exists('state_province', $loc[1]['address'])) {
                    $loc[1]['state_province'] = CRM_Core_PseudoConstant::stateProvince($loc[1]['address']['state_province_id']);
                    if (! $loc[1]['address']['state_province']) {
                        $loc[1]['address']['state_province'] =
                            CRM_Core_PseudoConstant::stateProvinceAbbreviation($loc[1]['address']['state_province_id']);
                    }
                }
                
                if (! array_key_exists('country', $loc[1]['address'])) {
                    $loc[1]['address']['country'] = CRM_Core_PseudoConstant::country($loc[1]['address']['country_id']);
                    if (! $loc[1]['address']['country']) {
                        $loc[1]['address']['country'] =
                            CRM_Core_PseudoConstant::countryIsoCode($loc[1]['address']['country_id']);
                    }
                }
                
            }
            $this->_location = $loc[1];
        }
        return $this->_location;
    }

    /**
     * Save the values of a domain
     *
     * @return domain array        
     * @access public
     */
    static function edit(&$params, &$id) {
        $domain     =& new CRM_Core_DAO_Domain( );
        $domain->id = $id;
        $domain->copyValues( $params );
        $domain->save( );
        return $domain;
    }

    static function multipleDomains( ) {
        $session =& CRM_Core_Session::singleton( );
        
        $numberDomains = $session->get( 'numberDomains' );
        if ( ! $numberDomains ) {
            $query = "SELECT count(*) from civicrm_domain";
            $numberDomains = CRM_Core_DAO::singleValueQuery( $query, CRM_Core_DAO::$_nullArray );
            $session->set( 'numberDomains', $numberDomains );
        }
        return $numberDomains > 1 ? true : false;
    }

}

?>
