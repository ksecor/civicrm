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
        $this->add('text', 'sort_name', ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Contact', 'sort_name' ) );

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
            $query = "
SELECT civicrm_contact.id as contact_id, civicrm_contact.sort_name as sort_name
FROM   civicrm_contact
WHERE  civicrm_contact.sort_name LIKE '%" . CRM_Utils_Type::escape( $values['sort_name'], 'String' ) . "%'";

            $dao =& CRM_Core_DAO::executeQuery( $query );
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