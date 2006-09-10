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
 * Files required
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Session.php';

class CRM_Contact_Form_Search_Simple extends CRM_Core_Form {

    public function preProcess( ) {
        $this->assign( 'rows', $this->get( 'rows' ) );
    }

    public function buildQuickForm( ) { 
        $config   =& CRM_Core_Config::singleton( );
        $domainID =  CRM_Core_Config::domainID( );
        
        $this->assign( 'dojoIncludes',
                       "dojo.require('dojo.widget.Select');dojo.require('dojo.widget.ComboBox');dojo.require('dojo.widget.Tooltip');" );
        $attributes = array( 'dojoType'     => 'ComboBox',
                             'mode'         => 'remote',
                             'dataUrl'      => $config->userFrameworkResourceURL . "extern/ajax.php?q=civicrm/search&d={$domainID}&s=%{searchString}",
                             );
        $attributes += CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Contact', 'sort_name' );
        $this->add( 'text', 'sort_name', ts('Name'), $attributes );

        $attributes = array( 'dojoType'     => 'Select',
                             'style'        => 'width: 300px;',
                             'autocomplete' => 'true' );

        // select for state province
        $stateProvince = array('' => ts('- any state/province -')) + CRM_Core_PseudoConstant::stateProvince( );
        $this->addElement('select', 'state_province', ts('State/Province'), $stateProvince, $attributes);

        // select for country
        $country = array('' => ts('- any country -')) + CRM_Core_PseudoConstant::country( );
        $this->addElement('select', 'country', ts('Country'), $country, $attributes );

        $this->addButtons( array(
                                 array ( 'type'      => 'refresh',
                                         'name'      => ts('Search'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );

    }

    public function postProcess( ) {
        $values = $this->exportValues( );

        if ( $values['sort_name'] ) {
            $string = CRM_Utils_Type::escape( $values['sort_name'], 'String' );
            $query = "
SELECT civicrm_contact.id as contact_id, civicrm_contact.sort_name as sort_name
FROM   civicrm_contact
WHERE  civicrm_contact.sort_name LIKE '%{$string}%'";
            $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );
            $rows = array( );
            while ( $dao->fetch( ) ) {
                $rows[$dao->contact_id] = $dao->sort_name;
            }

            $this->assign_by_ref( 'rows', $rows );
            $this->set( 'rows', $rows );
        }
    }

}

?>