<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

require_once 'CRM/Core/Page.php';

class CRM_Admin_Page_DedupeAssoc extends CRM_Core_Page {

    /**
     * offset and limit
     *
     * @var array
     * @access protected
     */
    protected $_offset;

    protected $_limit;


    function preProcess() 
    {
        $this->_offset = CRM_Utils_Request::retrieve('offset', 'Positive', $this, false, 0 );
        $this->_limit  = CRM_Utils_Request::retrieve('limit',  'Positive', $this, false, 50);

        CRM_Utils_System::setTitle( ts('Duplicate Contacts') );
    }

    /** 
     * run this page (figure out the action needed and perform it). 
     * 
     * @return void 
     */ 
    function run( ) {
        $this->preProcess(); 

        $query = "
SELECT c1.id as c1, 
       c1.display_name as c1_name, 
       c2.id as c2, 
       c2.display_name as c2_name, 
       e1.email as c1_email
FROM   civicrm_email e1,    civicrm_email e2, 
       civicrm_location l1, civicrm_location l2,
       civicrm_contact c1,  civicrm_contact c2
WHERE  e1.email = e2.email
AND    l1.entity_table = 'civicrm_contact'
AND    e1.location_id  = l1.id
AND    l2.entity_table = 'civicrm_contact'
AND    e2.location_id  = l2.id
AND    c1.id = l1.entity_id
AND    c2.id = l2.entity_id
AND    c1.id < c2.id
GROUP BY c1.id, c2.id
ORDER BY c1.id, c2.id
LIMIT " . (int) $this->_offset . "," . (int) $this->_limit;

        $nullArray = array( );
        $dao =& CRM_Core_DAO::executeQuery( $query, $nullArray );
        
        $result = array( );
        while ( $dao->fetch( ) ) {
            $result[] =  array( 'c1'       => $dao->c1,
                                'c2'       => $dao->c2,
                                'c1_name'  => $dao->c1_name,
                                'c2_name'  => $dao->c2_name,
                                'c1_email' => $dao->c1_email,
                                );
        }

        $this->assign( 'rows', $result );

        parent::run();
    }
  
}

?>
