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

        $config =& CRM_Core_Config::singleton();
        $dirList = preg_grep('/^[a-z][a-z]_[A-Z][A-Z]$/', scandir($config->gettextResourceDir));
        
        foreach ($dirList as $key) {
            $locales[$key] = $key;
        }
        
        $this->addElement('select','lcMessages', ts('LC Messages'), $locales);
        $this->addElement('text','moneyformat', ts('Money Format')); 
        $this->addElement('text','lcMonetary', ts('Monetory')); 

        $country = CRM_Core_PseudoConstant::country();
        $includeCountry =& $this->addElement('advmultiselect', 'countryLimit', 
                                      ts('Country Limit') . ' ', $country,
                                      array('size' => 5, 'style' => 'width:150px'));

        $includeCountry->setButtonAttributes('add', array('value' => ts('Add >>')));
        $includeCountry->setButtonAttributes('remove', array('value' => ts('<< Remove')));

        $stateProvince = CRM_Core_PseudoConstant::stateProvince();
        $includeState =& $this->addElement('advmultiselect', 'provinceLimit', 
                                      ts('Province Limit') . ' ', $stateProvince,
                                      array('size' => 5, 'style' => 'width:150px'));

        $includeState->setButtonAttributes('add', array('value' => ts('Add >>')));
        $includeState->setButtonAttributes('remove', array('value' => ts('<< Remove')));
    
        $this->addElement('select','defaultContactCountry', ts('Default Contact Country'), $country);
        $this->addElement('text','defaultCurrency', ts('CiviContribute Default Currency')); 
        $this->addElement('text','legacyEncoding', ts('Legacy Encoding'));  
       
        parent::buildQuickForm();
    }
}

?>