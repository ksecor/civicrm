<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components generic to all the contact types.
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Profile_Form_Search extends CRM_Core_Form
{

    /** 
     * the fields needed to build this form 
     * 
     * @var array 
     */ 
    protected $_fields; 

    function preProcess( ) 
    {
        $fields = CRM_Core_BAO_UFGroup::getListingFields( $this->_action,
                                                          CRM_Core_BAO_UFGroup::LISTINGS_VISIBILITY );
        foreach ($fields as $name => $field ) {
            if ( $field['visibility'] == 'Public User Pages and Listings' ) {
                $this->_fields[$name] = $field;
            }
        }
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function &setDefaultValues( ) 
    {
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->assign( 'action', $this->_action ); 
        $this->assign( 'fields', $this->_fields ); 

        // add the form elements 
        foreach ($this->_fields as $name => $field ) { 
            if ( $field['name'] === 'state_province' ) {
                $this->add('select', $field['name'], $field['title'],
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince(),
                           false );
            } else if ( $field['name'] === 'country' ) {                            
                $this->add('select', $field['name'], $field['title'],  
                           array('' => ts('- select -')) + CRM_Core_PseudoConstant::country(), false );
            } else if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($field['name'])) { 
                CRM_Core_BAO_CustomField::addQuickFormElement($this, $field['name'], $customFieldID, $inactiveNeeded, false); 
            } else { 
                $this->add('text', $field['name'], $field['title'], $field['attributes'], false );
            } 
                                           
        } 
                                       
        $this->addButtons(array( 
                                array ('type'      => 'refresh', 
                                       'name'      => ts('Search'), 
                                       'isDefault' => true ), 
                                ) ); 
     }

       
    /**
     * Form submission of new/edit contact is processed.
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
    }
}

?>
