<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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

        // create the code-keyed list of locales
        $locales = array('en_US' => 'English (USA)',
                         'af_ZA' => 'Afrikaans',
                         'bg_BG' => 'български',
                         'ca_ES' => 'Català',
                         'da_DK' => 'dansk',
                         'de_DE' => 'Deutsch',
                         'el_GR' => 'Ελληνικά',
                         'en_AU' => 'English (Australia)',
                         'en_GB' => 'English (United Kingdom)',
                         'es_ES' => 'español',
                         'fr_FR' => 'français',
                         'fr_CA' => 'français (Canada)',
                         'hi_IN' => 'हिन्दी',
                         'it_IT' => 'Italiano',
                         'he_IL' => 'עברית',
                         'hu_HU' => 'Magyar',
                         'nl_NL' => 'Nederlands',
                         'ja_JP' => '日本語',
                         'no_NO' => 'Norsk',
                         'km_KH' => 'ភាសាខ្មែរ',
                         'pl_PL' => 'polski',
                         'pt_PT' => 'Português',
                         'pt_BR' => 'Português (Brasil)',
                         'ro_RO' => 'română',
                         'ru_RU' => 'русский',
                         'sl_SI' => 'slovenščina',
                         'fi_FI' => 'suomi',
                         'sv_SE' => 'Svenska',
                         'th_TH' => 'ไทย',
                         'tr_TR' => 'Türkçe',
                         'uk_UA' => 'Українська',
                         'zh_CN' => '中文 (简体字)',
                         'zh_TW' => '中文 (繁體字)');
       
        // check which ones are available; add them to $locales if not there already
        $available = array();
        if (is_dir($config->gettextResourceDir)) {
            $dir = opendir($config->gettextResourceDir);
            while ($filename = readdir($dir)) {
                if (preg_match('/^[a-z][a-z]_[A-Z][A-Z]$/', $filename)) {
                    $available[] = $filename;
                    if (!isset($locales[$filename])) $locales[$filename] = $filename;
                }
            }
            closedir($dir);
        }

        // drop the unavailable locales (except en_US)
        foreach (array_keys($locales) as $code) {
            if ($code == 'en_US') continue;
            if (!in_array($code, $available)) unset($locales[$code]);
        }

        $this->addElement('select', 'lcMessages', ts('Default Language'), $locales);
        $this->addElement('select', 'lcMonetary', ts('Monetary Locale'),  $locales);
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
            $currencySymbols[$key] = "$key";
            if ($value) $currencySymbols[$key] .= " ($value)";
        } 
        $this->addElement('select','defaultCurrency', ts('Default Currency'), $currencySymbols);
        $this->addElement('text','legacyEncoding', ts('Legacy Encoding'));  
        $this->addElement('text','customTranslateFunction', ts('Custom Translate Function'));  
        $this->addElement('text','fieldSeparator', ts('Import / Export Field Separator'), array('size' => 2)); 

        $this->addFormRule( array( 'CRM_Admin_Form_Setting_Localization', 'formRule' ) );

        parent::buildQuickForm();
    }

    static function formRule( &$fields ) {
        $errors = array( );
        if ( trim( $fields['customTranslateFunction'] ) &&
             ! function_exists( trim( $fields['customTranslateFunction'] ) ) ) {
            $errors['customTranslateFunction'] = ts( 'Please define the custom translation function first' );
        }
        return empty( $errors ) ? true : $errors;
    }

}


