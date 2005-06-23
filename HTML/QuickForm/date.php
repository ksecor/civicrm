<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Alexey Borzov <avb@php.net>                                 |
// |          Adam Daniel <adaniel1@eesus.jnj.com>                        |
// |          Bertrand Mansion <bmansion@mamasam.com>                     |
// +----------------------------------------------------------------------+
//
// $Id: date.php,v 1.47 2004/10/14 19:55:41 avb Exp $

require_once 'HTML/QuickForm/group.php';
require_once 'HTML/QuickForm/select.php';

/**
 * Class for a group of elements used to input dates (and times).
 * 
 * Inspired by original 'date' element but reimplemented as a subclass
 * of HTML_QuickForm_group
 * 
 * @author Alexey Borzov <avb@php.net>
 * @access public
 */
class HTML_QuickForm_date extends HTML_QuickForm_group
{
    // {{{ properties

   /**
    * Various options to control the element's display.
    * 
    * Currently known options are
    * 'format': Format of the date, based on PHP's date() function.
    *     The following characters are recognised in format string:
    *       D => Short names of days
    *       l => Long names of days
    *       d => Day numbers
    *       M => Short names of months
    *       F => Long names of months
    *       m => Month numbers
    *       Y => Four digit year
    *       y => Two digit year
    *       h => 12 hour format
    *       H => 23 hour format
    *       i => Minutes
    *       s => Seconds
    *       a => am/pm
    *       A => AM/PM
    * 'minYear': Minimum year in year select
    * 'maxYear': Maximum year in year select
    * 'addEmptyOption': Should an empty option be added to the top of
    *     each select box?
    * 'emptyOptionValue': The value passed by the empty option.
    * 'emptyOptionText': The text displayed for the empty option.
    * 'optionIncrement': Step to increase the option values by (works for 'i' and 's')
    * 
    * @access   private
    * @var      array
    */
    var $_options = array(
        'format'           => 'dMY',
        'minYear'          => 2001,
        'maxYear'          => 2010,
        'addEmptyOption'   => false,
        'emptyOptionValue' => '',
        'emptyOptionText'  => '&nbsp;',
        'optionIncrement'  => array('i' => 1, 's' => 1)
    );

   /**
    * These complement separators, they are appended to the resultant HTML
    * @access   private
    * @var      array
    */
    var $_wrap = array('', '');

   /**
    * Locale array build from CRM_Utils_Date-provided names
    * 
    * @access   private
    * @var      array
    */
    var $_locale = array();

    // }}}
    // {{{ constructor

   /**
    * Class constructor
    * 
    * @access   public
    * @param    string  Element's name
    * @param    mixed   Label(s) for an element
    * @param    array   Options to control the element's display
    * @param    mixed   Either a typical HTML attribute string or an associative array
    */
    function HTML_QuickForm_date($elementName = null, $elementLabel = null, $options = array(), $attributes = null)
    {
        $this->_locale = array(
            'weekdays_short'=> CRM_Utils_Date::getAbbrWeekdayNames(),
            'weekdays_long' => CRM_Utils_Date::getFullWeekdayNames(),
            'months_short'  => CRM_Utils_Date::getAbbrMonthNames(),
            'months_long'   => CRM_Utils_Date::getFullMonthNames()
        );
        $this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'date';
        // set the options, do not bother setting bogus ones
        if (is_array($options)) {
            foreach ($options as $name => $value) {
                if (isset($this->_options[$name])) {
                    if (is_array($value)) {
                        $this->_options[$name] = @array_merge($this->_options[$name], $value);
                    } else {
                        $this->_options[$name] = $value;
                    }
                }
            }
        }
    }

    // }}}
    // {{{ _createElements()

    function _createElements()
    {
        $this->_separator = $this->_elements = array();
        $separator =  '';
        $locale    =& $this->_locale;
        $backslash =  false;
        for ($i = 0, $length = strlen($this->_options['format']); $i < $length; $i++) {
            $sign = $this->_options['format']{$i};
            if ($backslash) {
                $backslash  = false;
                $separator .= $sign;
            } else {
                $loadSelect = true;
                switch ($sign) {
                    case 'D':
                        // Sunday is 0 like with 'w' in date()
                        $options   = $locale['weekdays_short'];
                        $emptyText = 'Day of Week';
                        break;
                    case 'l':
                        $options = $locale['weekdays_long'];
                        $emptyText = 'Day of Week';
                        break;
                    case 'd':
                        $options = $this->_createOptionList(1, 31);
                        $emptyText = 'Day';
                        break;
                    case 'M':
                        $options = $locale['months_short'];
                        array_unshift($options , '');
                        unset($options[0]);
                        $emptyText = 'Month';
                        break;
                    case 'm':
                        $options = $this->_createOptionList(1, 12);
                        $emptyText = 'Month';
                        break;
                    case 'F':
                        $options = $locale['months_long'];
                        array_unshift($options , '');
                        unset($options[0]);
                        $emptyText = 'Month';
                        break;
                    case 'Y':
                        $options = $this->_createOptionList(
                            $this->_options['minYear'],
                            $this->_options['maxYear'], 
                            $this->_options['minYear'] > $this->_options['maxYear']? -1: 1
                        );
                        $emptyText = 'Year';
                        break;
                    case 'y':
                        $options = $this->_createOptionList(
                            $this->_options['minYear'],
                            $this->_options['maxYear'],
                            $this->_options['minYear'] > $this->_options['maxYear']? -1: 1
                        );
                        array_walk($options, create_function('&$v,$k','$v = substr($v,-2);')); 
                        $emptyText = 'Year';
                        break;
                    case 'h':
                        $options = $this->_createOptionList(1, 12);
                        $emptyText = 'Hour';
                        break;
                    case 'H':
                        $options = $this->_createOptionList(0, 23);
                        $emptyText = 'Hour';
                        break;
                    case 'i':
                        $options = $this->_createOptionList(0, 59, $this->_options['optionIncrement']['i']);
                        $emptyText = 'Minutes';
                        break;
                    case 's':
                        $options = $this->_createOptionList(0, 59, $this->_options['optionIncrement']['s']);
                        $emptyText = 'Seconds';
                        break;
                    case 'a':
                        $options = array('am' => 'am', 'pm' => 'pm');
                        $emptyText = 'AM / PM';
                        break;
                    case 'A':
                        $options = array('AM' => 'AM', 'PM' => 'PM');
                        $emptyText = 'AM / PM';
                        break;
                    case '\\':
                        $backslash  = true;
                        $loadSelect = false;
                        break;
                    default:
                        $separator .= (' ' == $sign? '&nbsp;': $sign);
                        $loadSelect = false;
                }
    
                if ($loadSelect) {
                    if (0 < count($this->_elements)) {
                        $this->_separator[] = $separator;
                    } else {
                        $this->_wrap[0] = $separator;
                    }
                    $separator = '';
                    // Should we add an empty option to the top of the select?
                    if ($this->_options['addEmptyOption']) {
                        // Preserve the keys
                        $text = $emptyText ? $emptyText : $this->_options['emptyOptionText'];
                        $options = array($this->_options['emptyOptionValue'] => $text) + $options;
                    }
                    $this->_elements[] =& new HTML_QuickForm_select($sign, null, $options, $this->getAttributes());
                }
            }
        }
        $this->_wrap[1] = $separator . ($backslash? '\\': '');
    }

    // }}}
    // {{{ _createOptionList()

   /**
    * Creates an option list containing the numbers from the start number to the end, inclusive
    *
    * @param    int     The start number
    * @param    int     The end number
    * @param    int     Increment by this value
    * @access   private
    * @return   array   An array of numeric options.
    */
    function _createOptionList($start, $end, $step = 1)
    {
        for ($i = $start, $options = array(); $start > $end? $i >= $end: $i <= $end; $i += $step) {
            $options[$i] = sprintf('%02d', $i);
        }
        return $options;
    }

    // }}}
    // {{{ setValue()

    function setValue($value)
    {
        if (empty($value)) {
            $value = array();
        } elseif (is_scalar($value)) {
            if (!is_numeric($value)) {
                $value = strtotime($value);
            }
            // might be a unix epoch, then we fill all possible values
            $arr = explode('-', date('w-d-n-Y-h-H-i-s-a-A', (int)$value));
            $value = array(
                'D' => $arr[0],
                'l' => $arr[0],
                'd' => $arr[1],
                'M' => $arr[2],
                'm' => $arr[2],
                'F' => $arr[2],
                'Y' => $arr[3],
                'y' => $arr[3],
                'h' => $arr[4],
                'H' => $arr[5],
                'i' => $arr[6],
                's' => $arr[7],
                'a' => $arr[8],
                'A' => $arr[9]
            );
        }
        parent::setValue($value);
    }

    // }}}
    // {{{ toHtml()

    function toHtml()
    {
        include_once('HTML/QuickForm/Renderer/Default.php');
        $renderer =& new HTML_QuickForm_Renderer_Default();
        $renderer->setElementTemplate($this->_wrap[0] . '{element}' . $this->_wrap[1]);
        parent::accept($renderer);
        return $renderer->toHtml();
    }

    // }}}
    // {{{ accept()

    function accept(&$renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }

    // }}}
    // {{{ onQuickFormEvent()

    function onQuickFormEvent($event, $arg, &$caller)
    {
        if ('updateValue' == $event) {
            // we need to call setValue(), 'cause the default/constant value
            // may be in fact a timestamp, not an array
            return HTML_QuickForm_element::onQuickFormEvent($event, $arg, $caller);
        } else {
            return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }

    // }}}
}
?>
