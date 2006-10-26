<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Admin/Form/Setting.php';

/**
 * This class generates form components for Localisation
 * 
 */
class CRM_Admin_Form_Setting_Localisation extends  CRM_Admin_Form_Setting
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {

        $this->addElement('text','message', ts('LC Message')); 
        $this->addElement('text','money', ts('Money Format')); 
        $this->addElement('text','monetory', ts('Monetory')); 

        $country = CRM_Core_PseudoConstant::country();
        $include =& $this->addElement('advmultiselect', 'country_limit', 
                                      ts('Country Limit') . ' ', $country,
                                      array('size' => 5, 'style' => 'width:150px'));

        $include->setButtonAttributes('add', array('value' => ts('Add >>')));;
        $include->setButtonAttributes('remove', array('value' => ts('<< Remove')));

        $this->addElement('text','province_limit', ts('Province Limit'));
        $this->addElement('select','contact_country', ts('Default Contact Country'), $country);
        $this->addElement('text','currency', ts('CiviContribute Default Currency')); 
        $this->addElement('text','legacy_encoding', ts('Legacy Encoding'));  
       
        parent::buildQuickForm();
    }
}

?>