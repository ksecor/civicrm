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
        CRM_Utils_System::setTitle(ts('Settings - Localisation'));

        $locales = array();
        if (is_dir($config->gettextResourceDir)) {
            $dirList = preg_grep('/^[a-z][a-z]_[A-Z][A-Z]$/', scandir($config->gettextResourceDir));
            foreach ($dirList as $key) {
                $locales[$key] = $key;
            }
        }
        
        $this->addElement('select','lcMessages', ts('User Language'), array('en_US' => 'en_US') + $locales);
        $this->addElement('select','lcMonetary', ts('Monetary Locale'), array('en_US' => 'en_US') + $locales);
        $this->addElement('text','moneyformat', ts('Monetary Display Format')); 

        $country = array( ) ;
        CRM_Core_PseudoConstant::populate( $country, 'CRM_Core_DAO_Country', true, 'name', 'is_active' );
        
        $includeCountry =& $this->addElement('advmultiselect', 'countryLimit', 
                                      ts('Available Countries') . ' ', $country,
                                      array('size' => 5, 'style' => 'width:150px'));

        $includeCountry->setButtonAttributes('add', array('value' => ts('Add >>')));
        $includeCountry->setButtonAttributes('remove', array('value' => ts('<< Remove')));

        $includeState =& $this->addElement('advmultiselect', 'provinceLimit', 
                                      ts('Available States and Provinces') . ' ', $country,
                                      array('size' => 5, 'style' => 'width:150px'));

        $includeState->setButtonAttributes('add', array('value' => ts('Add >>')));
        $includeState->setButtonAttributes('remove', array('value' => ts('<< Remove')));
    
        $this->addElement('select','defaultContactCountry', ts('Default Country'), $country);
        $symbol = $config->currencySymbols;
        foreach($symbol as $key=>$value) {
            $currencySymbols[$key] = "$key ($value)";
        } 
        $this->addElement('select','defaultCurrency', ts('Default Currency'), $currencySymbols);
        $this->addElement('text','legacyEncoding', ts('Legacy Encoding'));  
       
        parent::buildQuickForm();
    }
}

?>
