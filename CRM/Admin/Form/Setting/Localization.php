<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
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

require_once 'CRM/Admin/Form/Setting.php';

/**
 * This class generates form components for Localization
 * 
 */
class CRM_Admin_Form_Setting_Localization extends  CRM_Admin_Form_Setting
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {

        $config =& CRM_Core_Config::singleton();
        $i18n   =& CRM_Core_I18n::singleton();
        CRM_Utils_System::setTitle(ts('Settings - Localization'));

        $locales = array();
        if (is_dir($config->gettextResourceDir)) {
            $dir = opendir($config->gettextResourceDir);
            while ($filename = readdir($dir)) {
                if (preg_match('/^[a-z][a-z]_[A-Z][A-Z]$/', $filename)) {
                    $locales[$filename] = $filename;
                }
            }
            closedir($dir);
        }
        asort($locales);
        
        $this->addElement('select','lcMessages', ts('User Language'), array('en_US' => 'en_US') + $locales);
        $this->addElement('select','lcMonetary', ts('Monetary Locale'), array('en_US' => 'en_US') + $locales);
        $this->addElement('text','moneyformat', ts('Monetary Display')); 

        $country = array( ) ;
        CRM_Core_PseudoConstant::populate( $country, 'CRM_Core_DAO_Country', true, 'name', 'is_active' );
        $i18n->localizeArray($country);
        asort($country);
        
        $includeCountry =& $this->addElement('advmultiselect', 'countryLimit', 
                                             ts('Available Countries') . ' ', $country,
                                             array('size' => 5,
                                                   'style' => 'width:150px',
                                                   'class' => 'advmultiselect')
                                             );

        $includeCountry->setButtonAttributes('add', array('value' => ts('Add >>')));
        $includeCountry->setButtonAttributes('remove', array('value' => ts('<< Remove')));

        $includeState =& $this->addElement('advmultiselect', 'provinceLimit', 
                                           ts('Available States and Provinces') . ' ', $country,
                                           array('size' => 5,
                                                 'style' => 'width:150px',
                                                 'class' => 'advmultiselect')
                                          );

        $includeState->setButtonAttributes('add', array('value' => ts('Add >>')));
        $includeState->setButtonAttributes('remove', array('value' => ts('<< Remove')));
    
        $this->addElement('select','defaultContactCountry', ts('Default Country'), array('' => ts('- select -')) + $country);

        // we do this only to initialize currencySymbols, kinda hackish but works!
        $config->defaultCurrencySymbol( );
        $symbol = $config->currencySymbols;
        foreach($symbol as $key=>$value) {
            $currencySymbols[$key] = "$key ($value)";
        } 
        $this->addElement('select','defaultCurrency', ts('Default Currency'), $currencySymbols);
        $this->addElement('text','legacyEncoding', ts('Legacy Encoding'));  
       
        parent::buildQuickForm();
    }
}


