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
 * @copyright CiviCRM LLC (c) 2004-2008
 * $Id$
 *
 */

require_once 'PHPgettext/streams.php';
require_once 'PHPgettext/gettext.php';
require_once 'CRM/Core/Config.php';

class CRM_Core_I18n
{
    /**
     * A PHP-gettext instance for string translation; should stay null if the strings are not to be translated (en_US).
     */
    private $_phpgettext = null;

    /**
     * A locale-based constructor that shouldn't be called from outside of this class (use singleton() instead).
     *
     * @param  $locale string  the base of this certain object's existence
     * @return         void
     */
    private function __construct($locale)
    {
        if ($locale != '' and $locale != 'en_US') {
            $config =& CRM_Core_Config::singleton();
            $streamer = new FileReader(implode(DIRECTORY_SEPARATOR, array($config->gettextResourceDir, $locale, 'LC_MESSAGES', 'civicrm.mo')));
            $this->_phpgettext = new gettext_reader($streamer);
        }
    }

    /**
     * Return languages available in this instance of CiviCRM.
     *
     * @param $justEnabled boolean  whether to return all languages or just the enabled ones
     * @return             array    of code/language name mappings
     */
    static function languages($justEnabled = false)
    {
        static $all     = null;
        static $enabled = null;

        if (!$all) {
            $all = array('en_US' => 'English (USA)',
                         'af_ZA' => 'Afrikaans',
                         'ar_EG' => 'العربية',
                         'bg_BG' => 'български',
                         'ca_ES' => 'Català',
                         'cs_CZ' => 'Česky',
                         'da_DK' => 'dansk',
                         'de_DE' => 'Deutsch',
                         'el_GR' => 'Ελληνικά',
                         'en_AU' => 'English (Australia)',
                         'en_GB' => 'English (United Kingdom)',
                         'es_ES' => 'español',
                         'fr_FR' => 'français',
                         'fr_CA' => 'français (Canada)',
                         'id_ID' => 'Bahasa Indonesia',
                         'hi_IN' => 'हिन्दी',
                         'it_IT' => 'Italiano',
                         'he_IL' => 'עברית',
                         'lt_LT' => 'Lietuvių',
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
                         'sk_SK' => 'slovenčina',
                         'sl_SI' => 'slovenščina',
                         'fi_FI' => 'suomi',
                         'sv_SE' => 'Svenska',
                         'th_TH' => 'ไทย',
                         'vi_VN' => 'Tiếng Việt',
                         'tr_TR' => 'Türkçe',
                         'uk_UA' => 'Українська',
                         'zh_CN' => '中文 (简体)',
                         'zh_TW' => '中文 (繁體)');

            // check which ones are available; add them to $all if not there already
            $config =& CRM_Core_Config::singleton();
            $codes = array();
            if (is_dir($config->gettextResourceDir)) {
                $dir = opendir($config->gettextResourceDir);
                while ($filename = readdir($dir)) {
                    if (preg_match('/^[a-z][a-z]_[A-Z][A-Z]$/', $filename)) {
                        $codes[] = $filename;
                        if (!isset($all[$filename])) $all[$filename] = $filename;
                    }
                }
                closedir($dir);
            }

            // drop the unavailable languages (except en_US)
            foreach (array_keys($all) as $code) {
                if ($code == 'en_US') continue;
                if (!in_array($code, $codes)) unset($all[$code]);
            }
        }

        if ($enabled === null) {
            $config =& CRM_Core_Config::singleton();
            $enabled = array();
            if (isset($config->languageLimit) and $config->languageLimit) {
                foreach ($all as $code => $name) {
                    if (in_array($code, array_keys($config->languageLimit))) $enabled[$code] = $name;
                }
            }
        }

        return $justEnabled ? $enabled : $all;
    }

    /**
     * Replace arguments in a string with their values. Arguments are represented by % followed by their number.
     *
     * @param  $str string  source string
     * @param       mixed   arguments, can be passed in an array or through single variables
     * @return      string  modified string
     */
    function strarg($str)
    {
        $tr = array();
        $p = 0;
        for ($i = 1; $i < func_num_args(); $i++) {
            $arg = func_get_arg($i);
            if (is_array($arg)) {
                foreach ($arg as $aarg) {
                    $tr['%'.++$p] = $aarg;
                }
            } else {
                $tr['%'.++$p] = $arg;
            }
        }
        return strtr($str, $tr);
    }

    /**
     * Smarty block function, provides gettext support for smarty.
     *
     * The block content is the text that should be translated.
     *
     * Any parameter that is sent to the function will be represented as %n in the translation text,
     * where n is 1 for the first parameter. The following parameters are reserved:
     *   - escape - sets escape mode:
     *       - 'html' for HTML escaping, this is the default.
     *       - 'js' for javascript escaping.
     *       - 'no'/'off'/0 - turns off escaping
     *   - plural - The plural version of the text (2nd parameter of ngettext())
     *   - count - The item count for plural mode (3rd parameter of ngettext())
     *
     * @param $text   string  the original string
     * @param $params array   the params of the translation (if any)
     * @return        string  the translated string
     */
    function crm_translate($text, $params)
    {
        if (isset($params['escape'])) {
            $escape = $params['escape'];
            unset($params['escape']);
        }

        if (isset($params['plural'])) {
            $plural = $params['plural'];
            unset($params['plural']);
            if (isset($params['count'])) {
                $count = $params['count'];
            }
        }

        // use plural if required parameters are set
        if (isset($count) && isset($plural)) {

            if ($this->_phpgettext) {
                $text = $this->_phpgettext->ngettext($text, $plural, $count);
            } else {
                // if the locale's not set, we do ngettext work by hand
                // if $count == 1 then $text = $text, else $text = $plural
                if ($count != 1) $text = $plural;
            }

            // expand %count in translated string to $count
            $text = strtr($text, array('%count' => $count));

        // if not plural, but the locale's set, translate
        } elseif ($this->_phpgettext) {
            $text = $this->_phpgettext->translate($text);
        }

        // replace the numbered %1, %2, etc. params if present
        if (count($params)) {
            $text = $this->strarg($text, $params);
        }

        // escape SQL if we were asked for it
        if (isset($escape) and ($escape == 'sql')) $text = mysql_escape_string($text);

        return $text;
    }

    /**
     * Translate a string to the current locale.
     *
     * @param  $string string  this string should be translated
     * @return         string  the translated string
     */
    function translate($string)
    {
        return ($this->_phpgettext) ? $this->_phpgettext->translate($string) : $string;
    }

    /**
     * Localize (destructively) array values.
     *
     * @param  $array array  the array for localization (in place)
     * @return        void
     */
    function localizeArray(&$array)
    {
        if ($this->_phpgettext) {
            foreach ($array as $key => $value) {
                $array[$key] = $this->_phpgettext->translate($value);
            }
        }
    }

    /**
     * Localize (destructively) array elements with keys of 'title'.
     *
     * @param  $array array  the array for localization (in place)
     * @return        void
     */
    function localizeTitles(&$array)
    {
        if ($this->_phpgettext) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $this->localizeTitles($value);
                    $array[$key] = $value;
                } elseif ((string ) $key == 'title') {
                    $array[$key] = $this->_phpgettext->translate($value);
                }
            }
        }
    }

    /**
     * Static instance provider - return the instance for the current locale.
     */
    static function &singleton()
    {
        static $singleton = array();

        $config =& CRM_Core_Config::singleton();
        if (!isset($singleton[$config->lcMessages])) {
            $lcMessages = $config->lcMessages;
            $singleton[$config->lcMessages] =& new CRM_Core_I18n($config->lcMessages);
        }

        return $singleton[$config->lcMessages];
    }

    /**
     * Set the LC_TIME locale if it's not set already (for a given language choice).
     *
     * @return string  the final LC_TIME that got set
     */
    static function setLcTime()
    {
        static $locales = array();

        $config =& CRM_Core_Config::singleton();
        if (!isset($locales[$config->lcMessages])) {
            // with the config being set to pl_PL: try pl_PL.UTF-8,
            // then pl_PL, if neither present fall back to C
            $locales[$config->lcMessages] = setlocale(LC_TIME, $config->lcMessages . '.UTF-8', $config->lcMessages, 'C');
        }

        return $locales[$config->lcMessages];
    }

}

/**
 * Short-named function for string translation, defined in global scope so it's available everywhere.
 * @param  $text   string  string for trnaslating
 * @param  $params array   an array of additional parameters
 * @return         string  the translated string
 */
function ts($text, $params = array())
{
    static $config   = null;
    static $locale   = null;
    static $i18n     = null;
    static $function = null;

    if ($text == '') {
        return '';
    }

    if (!$config) {
        $config =& CRM_Core_Config::singleton();
    }

    if (!$i18n or $locale != $config->lcMessages) {
        $i18n =& CRM_Core_I18n::singleton();
        $locale = $config->lcMessages;
        if (isset($config->customTranslateFunction) and function_exists($config->customTranslateFunction)) {
            $function = $config->customTranslateFunction;
        }
    }

    if ($function) {
        return $function($text, $params);
    } else {
        return $i18n->crm_translate($text, $params);
    }
}
