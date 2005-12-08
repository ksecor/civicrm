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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form.php';

/**
 * This class generates form components for Credit Card
 * 
 */
class CRM_Contribute_Form_AcceptCreditCard extends CRM_Contribute_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );
        
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }

        $this->applyFilter('__ALL__', 'trim');
        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_AcceptCreditCard', 'name' ) );
        $this->addRule( 'name', ts('Please enter a valid Credit Card name.'), 'required' );
        $this->addRule( 'name', ts('That name already exists in Database.'), 'objectExists', array( 'CRM_Contribute_DAO_AcceptCreditCard', $this->_id ) );
        
        $this->add('text', 'title', ts('Title'), CRM_Core_DAO::getAttribute( 'CRM_Contribute_DAO_AcceptCreditCard', 'title' ) );

        $this->add('checkbox', 'is_active', ts('Enabled?'));

        if ($this->_action == CRM_Core_Action::UPDATE && CRM_Core_DAO::getFieldValue( 'CRM_Contribute_DAO_AcceptCreditCard', $this->_id, 'is_reserved' )) { 
            $this->freeze(array('name', 'title', 'is_active' ));
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
        require_once 'CRM/Contribute/BAO/AcceptCreditCard.php';
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Contribute_BAO_AcceptCreditCard::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected Credit Card has been deleted.') );
        } else { 

            $params = $ids = array( );
            // store the submitted values in an array
            $params = $this->exportValues();
            
            if ($this->_action & CRM_Core_Action::UPDATE ) {
                $ids['acceptCreditCard'] = $this->_id;
            }
            
            $acceptCreditCard = CRM_Contribute_BAO_AcceptCreditCard::add($params, $ids);
            CRM_Core_Session::setStatus( ts('The Credit Card "%1" has been saved.', array( 1 => $acceptCreditCard->name )) );
        }
    }
}

?>
