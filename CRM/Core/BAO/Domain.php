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
     * Get the domain BAO 
     *
     * @return null|object CRM_Core_BAO_Domain
     * @access public
     * @static
     */
    static function &getDomain( ) {
        static $domain = null;
        if ( ! $domain ) {
            $domain =& new CRM_Core_BAO_Domain();
            if ( ! $domain->find(true) ) {
                CRM_Core_Error::fatal( );
            }
        }
        return $domain;
    }

    static function version( ) {
        return CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Domain',
                                            1,
                                            'version' );
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
                            'entity_id' => $this->id, 
                            'entity_table' => self::getTableName()
                            );
            $values = array();
            $ids = array();
            CRM_Core_BAO_Location::getValues($params, $values, true);
            if ( ! CRM_Utils_Array::value( 'location', $values ) ||
                 ! CRM_Utils_Array::value( '1', $values['location'] ) ) {
                $this->_location = null;
                return $this->_location;
            }
            
            $loc =& $values['location'];
            
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
            $numberDomains = CRM_Core_DAO::singleValueQuery( $query );
            $session->set( 'numberDomains', $numberDomains );
        }
        return $numberDomains > 1 ? true : false;
    }

    static function getNameAndEmail( ) 
    {
        require_once 'CRM/Core/OptionGroup.php';
        $formEmailAddress = CRM_Core_OptionGroup::values( 'from_email_address', null, null, null, ' AND is_default = 1' );
        if ( !empty( $formEmailAddress ) ) {
            require_once 'CRM/Utils/Mail.php';
            foreach ( $formEmailAddress as $key => $value ) {
                $email    = CRM_Utils_Mail::pluckEmailFromHeader( $value );
                $fromName = CRM_Utils_Array::value( 1, explode('"', $value ) );
                break;
            }
            return array( $fromName, $email );
        }
        
        $url = CRM_Utils_System::url( 'civicrm/contact/domain', 
                                      'action=update&reset=1' );
        $status = ts( "There is no valid default from email address configured for the domain. You can configure here <a href='%1'>Configure From Email Address.</a>", array( 1 => $url ) );
        
        CRM_Core_Error::fatal( $status );
    }
    
}


