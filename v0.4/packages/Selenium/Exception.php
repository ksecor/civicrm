<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Exception Class for Selenium
 *
 * PHP versions 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 */

/**
 * uses PEAR_Exception
 */
require_once 'PEAR/Exception.php';

/**
 * Testing_Selenium_Exception
 *
 * @category   Testing
 * @package    Selenium
 * @author     Shin Ohno <ganchiku@gmail.com>
 * @version    0.1.4
 */
class Selenium_Exception extends PEAR_Exception
{
}
?>
