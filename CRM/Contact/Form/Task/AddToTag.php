<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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

require_once 'CRM/Contact/Form/Task.php';
require_once 'CRM/Core/BAO/EntityTag.php';

/**
 * This class provides the functionality to delete a group of
 * contacts. This class provides functionality for the actual
 * addition of contacts to groups.
 */
class CRM_Contact_Form_Task_AddToTag extends CRM_Contact_Form_Task {

    /**
     * name of the tag
     *
     * @var string
     */
    protected $_name;

    /**
     * all the tags in the system
     *
     * @var array
     */
    protected $_tags;

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
        // add select for tag
        $this->_tags = array( '' => ' - select tag - ') + CRM_Core_PseudoConstant::tag( );
        $this->add('select', 'tag_id', ts('Select Tag'), $this->_tags, true);

        $this->addDefaultButtons( ts('Tag Contacts') );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $tagId    = $this->controller->exportValue( 'AddToTag', 'tag_id'  );
        $this->_name   = $this->_tags[$tagId];

        list( $total, $added, $notAdded ) = CRM_Core_BAO_EntityTag::addContactsToTag( $this->_contactIds, $tagId );
        $status = array(
                        'Contact(s) tagged as: '       . $this->_name,
                        'Total Selected Contact(s): '  . $total
                        );
        if ( $added ) {
            $status[] = 'Total Contact(s) tagged: ' . $added;
        }
        if ( $notAdded ) {
            $status[] = 'Total Contact(s) already tagged: ' . $notAdded;
        }
        CRM_Core_Session::setStatus( $status );
    }//end of function


}

?>
