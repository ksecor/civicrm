<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/BAO/PriceSet.php';
/**
 * This class is to build the form for Deleting Set
 */
class CRM_Price_Form_DeleteSet extends CRM_Core_Form {

    /**
     * the set id
     *
     * @var int
     */
    protected $_id;

    /**
     * The title of the set being deleted
     *
     * @var string
     */
    protected $_title;

    /**
     * set up variables to build the form
     *
     * @return void
     * @acess protected
     */
    function preProcess( ) {
        $this->_id    = $this->get( 'id' );
        
       
        $defaults = array( );
        $params   = array( 'id' => $this->_id );
        CRM_Core_BAO_PriceSet::retrieve( $params, $defaults );
        $this->_title = $defaults['title'];
        $this->assign( 'name' , $this->_title );
        
        CRM_Utils_System::setTitle( ts('Confirm Price Set Delete') );
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Delete Price Set'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    /**
     * Process the form when submitted
     *
     * @return void
     * @access public
     */
    public function postProcess( ) {
        $set = & new CRM_Core_DAO_PriceSet();
        $set->id = $this->_id;
        $set->find();
        $set->fetch();
        
        if (CRM_Core_BAO_PriceSet::deleteSet( $this->_id)) {
            CRM_Core_Session::setStatus( ts('The Set "%1" has been deleted.', array(1 => $set->title)) );        
        } else {
            CRM_Core_Session::setStatus( ts('The Set "%1" has not been deleted! You must Delete all price fields in this set prior to deleting the set', array(1 => $set->title)) );        
        }
            
    }
}

?>
