<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Task/SaveSearch.php';

/**
 * This class provides the functionality to update a saved search
 *
 */
class CRM_Contact_Form_Task_SaveSearch_Update extends CRM_Contact_Form_Task_SaveSearch {

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess()
    {
        parent::preProcess( );
        $this->_id = $this->get( 'ssID' );
    }

    /**
     * This function sets the default values for the form.
     * the default values are retrieved from the database
     *
     * @access public
     * @return None
     */
    function setDefaultValues( ) {

        $defaults = array( );
        $params   = array( );
        
        $params = array( 'saved_search_id' => $this->_id );
        CRM_Contact_BAO_Group::retrieve( $params, $defaults );

        return $defaults;
    }

}

?>
