<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Admin/Form.php';

/**
 * This class generates form components for Mapping
 * 
 */
class CRM_Admin_Form_Mapping extends CRM_Admin_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function preProcess() {
        parent::preProcess();
        $mapping =& new CRM_Core_DAO_Mapping(); 
        $mapping->id = $this->_id;
        $mapping->find(true);
        $this->assign('mappingType', $mapping->mapping_type);
    }
    
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );
        if ($this->_action == CRM_Core_Action::DELETE) {
            
            return;
        } else {
            $this->applyFilter('__ALL__', 'trim');
            

            $this->add('text', 'name' , ts('Name')       ,
                              CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Mapping', 'name' ),true );
            $this->addRule( 'name', ts('Name already exists in Database.'), 'objectExists', array( 'CRM_Core_DAO_Mapping', $this->_id ) );
            
            $this->addElement('text', 'description', ts('Description'), 
                              CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Mapping', 'description' ) );
            
            $mappingType = $this->addElement('text', 'mapping_type', ts('Mapping Type'));
            
            if ( $this->_action == CRM_Core_Action::UPDATE ) {
                $mappingType->freeze();
            }
        }
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params = $ids = array();
        
        // store the submitted values in an array
        $params = $this->exportValues();
        
        $ids['mapping'] = $this->_id;
        
        if ($this->_action == CRM_Core_Action::DELETE) {
            if ($this->_id  > 0 ) {
                CRM_Core_BAO_Mapping::del( $this->_id );
            }
        } else {
            CRM_Core_BAO_Mapping::add($params, $ids);
        }        
    }//end of function
}

?>