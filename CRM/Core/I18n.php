<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 * This is CiviCRM's internationalisation mechanism based on smarty_gettext
 *
 * @package CRM
 * @author Piotr Szotkowski <shot@caltha.pl>
 * @author Michal Mach <mover@artnet.org>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */
class CRM_Core_I18n
{

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     *
     * @var object
     * @static
     */
    static private $_singleton = null;

    /**
     * class constructor
     *
     * @access private
     */
    function __construct()
    {
        if (function_exists('gettext')) {
            $config = CRM_Core_Config::singleton();
            setlocale(LC_MESSAGES, $config->lcMessages);
            bindtextdomain($config->gettextDomain, $config->gettextResourceDir);
            bind_textdomain_codeset($config->gettextDomain, $config->gettextCodeset);
            textdomain($config->gettextDomain);
        }
    }

    /**
     * Replace arguments in a string with their values. Arguments are represented by % followed by their number.
     *
     * @param   string Source string
     * @param   mixed  Arguments, can be passed in an array or through single variables.
     * @returns string Modified string
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
     */
    function crm_translate($text, $params)
    {
        // $text = stripslashes($text);

        // set escape mode
        if (isset($params['escape'])) {
            $escape = $params['escape'];
            unset($params['escape']);
        }

        // set plural version
        if (isset($params['plural'])) {
            $plural = $params['plural'];
            unset($params['plural']);

            // set count
            if (isset($params['count'])) {
                $count = $params['count'];
            }
        }

        // use plural if required parameters are set
        if (isset($count) && isset($plural)) {

            // if there's gettext support, use it
            if (function_exists('ngettext')) {
                $text = ngettext($text, $plural, $count);

            // if there's no gettext support, we have to do ngettext work by hand
            // if $count == 1 then $text = $text, else $text = $plural
            } else {
                if ($count != 1) {
                    $text = $plural;
                }
            }

            // expand %count in translated string to $count
            $text = strtr($text, array('%count' => $count));

        // use normal gettext() if present, otherwise $text = $text
        } else {

            if (function_exists('gettext')) {
                $text = gettext($text);
            }
        
        }

        // run strarg if there are parameters
        if (count($params)) {
            $text = $this->strarg($text, $params);
        }

        // FIXME escaped until escaping issue is sorted out
        // if (!isset($escape) || $escape == 'html') { // html escape, default
        //     $text = nl2br(htmlspecialchars($text));
        // } elseif (isset($escape) && ($escape == 'javascript' || $escape == 'js')) { // javascript escape
        //     $text = str_replace('\'','\\\'',stripslashes($text));
        // }

        return $text;
    }

    /**
     * Static instance provider.
     *
     * Method providing static instance of SmartTemplate, as
     * in Singleton pattern.
     */
    static function singleton()
    {
        if (!isset(self::$_singleton)) {
            self::$_singleton = new CRM_Core_I18n();
        }
        return self::$_singleton;
    }

}

// function defined in global scope so it will be available everywhere
function ts($text, $params = array())
{
    $i18n = CRM_Core_I18n::singleton();
    return $i18n->crm_translate($text, $params);
}

?>
