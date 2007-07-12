<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PHP Client for the Selenium Remote Control test tool
 *
 * Selenium Remote Control (SRC) is a test tool that allows you to write
 * automated web application UI tests in any programming language against
 * any HTTP website using any mainstream JavaScript-enabled browser.  SRC
 * provides a Selenium Server, which can automatically start/stop/control
 * any supported browser. It works by using Selenium Core, a pure-HTML+JS
 * library that performs automated tasks in JavaScript; the Selenium
 * Server communicates directly with the browser using AJAX
 * (XmlHttpRequest).
 * L<http://www.openqa.org/selenium-rc/>
 *
 * This module sends commands directly to the Server using simple HTTP
 * GET/POST requests.  Using this module together with the Selenium
 * Server, you can automatically control any supported browser.
 *
 * To use this module, you need to have already downloaded and started
 * the Selenium Server.  (The Selenium Server is a Java application.)
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 *
 * @category   Testing
 * @package    Selenium
 * @author     Shin Ohno <ganchiku@gmail.com>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    0.1.4
 * @see        http://www.openqa.org/selenium-rc/
 * @since      0.1.0
 */

/**
 * Selenium exception class
 */
require_once 'Selenium/Exception.php';

/**
 * Selenium
 *
 * @package Selenium
 * @version 0.1.4
 * @author Shin Ohno <ganchiku@gmail.com>
 */
class Selenium
{
    // {{{ class vars
    /**
     * @var    string
     * @access private
     */
    private $browser;

    /**
     * @var    string
     * @access private
     */
    private $browserUrl;

    /**
     * @var    string
     * @access private
     */
    private $host;

    /**
     * @var    int
     * @access private
     */
    private $port;

    /**
     * @var    string
     * @access private
     */
    private $sessionId;

    /**
     * @var    string
     * @access private
     */
    private $timeout;

    /**
     * @var    string
     * @access private
     */
    private $driver;
    // }}}
    // {{{ __construct($browser, $browserUrl, $host, $port, $timeout, $driver)
    /**
     * Constructor
     *
     * @param string $browser
     * @param string $browserUrl
     * @param string $host
     * @param int $port
     * @param int $timeout
     * @param string $driver
     * @access public
     * @throws Selenium_Exception
     */
    function __construct($browser, $browserUrl, $host = 'localhost', $port = 4444, $timeout = 30000, $driver = 'curl')
    {
        $this->browser = $browser;
        $this->browserUrl = $browserUrl;
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->setDriver($driver);
    }
    // }}}
    // {{{ public methods
    // {{{ setDriver
    /**
     * Set driver for HTTP Request.
     *
     * @param string $driver
     * @access public
     * @return void
     * @throws Selenium_Exception
     */
    public function setDriver($driver)
    {
        if ($driver == 'curl' or $driver == 'pear' or $driver == 'native') {
            $this->driver = $driver;
        } else {
            throw new Selenium_Exception('Driver has to be "curl" or "pear" or "native"');
        }
    }
    // }}}
    // {{{ start
    /**
     * Run the browser and set session id.
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function start()
    {
        $this->sessionId = $this->getString('getNewBrowserSession', array($this->browser, $this->browserUrl));
        return $this->sessionId;
    }
    // }}}
    // {{{ stop
    /**
     * Close the browser and set session id null
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function stop()
    {
        $result = $this->doCommand('testComplete');
        $this->sessionId = null;
    }
    // }}}
    // {{{ click($locator)
    /**
     * Clicks on a link, button, checkbox or radio button. If the click action
     * cause a new page to load (like a link usually does), call waitForPageToLoad.
     *
     * @param string $locator an element locator
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function click($locator)
    {
        $this->doCommand('click', array($locator));
    }
    // }}}
    // {{{ fireEvent($locator, $eventName)
    /**
     * Explicitly simulate an event, to trigger the corresponding "on<em>event</em>"
     * handler
     *
     * @param string $locator an element locator
     * @param string $eventName the event name, e.g. "focus" or "blur"
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function fireEvent($locator, $eventName)
    {
        $this->doCommand('fireEvent', array($locator, $eventName));
    }
    // }}}
    // {{{ keyPress($locator, $keycode)
    /**
     * Simulates a user pressing and releasing a key.
     *
     * @param string $locator an element locator
     * @param string $keycode the numeric keycode of the key to be pressed, normally the ASCII value of that key.
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function keyPress($locator, $keycode)
    {
        $this->doCommand('keyPress', array($locator, $keycode));
    }
    // }}}
    // {{{ keyDown($locator, $keycode)
    /**
     * Simulates a user pressing and pressing a key (without releasing it yet).
     *
     * @param string $locator an element locator
     * @param string $keycode the numeric keycode of the key to be pressed, normally the ASCII value of that key.
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function keyDown($locator, $keycode)
    {
        $this->doCommand('keyDown', array($locator, $keycode));
    }
    // }}}
    // {{{ keyUp($locator, $keycode)
    /**
     * Simulates a user releasing a key
     *
     * @param string $locator an element locator
     * @param string $keycode the numeric keycode of the key to be pressed, normally the ASCII value of that key.
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function keyUp($locator, $keycode)
    {
        $this->doCommand('keyUp', array($locator, $keycode));
    }
    // }}}
    // {{{ mouseOver($locator)
    /**
     * Simulates a user hovering a mouse over the specified element.
     *
     * @param string $locator an element locator
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function mouseOver($locator)
    {
        $this->doCommand('mouseOver', array($locator));
    }
    // }}}
    // {{{ mouseDown($locator)
    /**
     * Simulates a user pressing the mouse button (without releasing it yet) on
     * the specified element.
     *
     * @param string $locator an element locator
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function mouseDown($locator)
    {
        $this->doCommand('mouseDown', array($locator));
    }
    // }}}
    // {{{ type($locator, $value)
    /**
     * Set the value of an input field, as though you typed it in.
     *
     * can also be used to set the value of combo boxes, check boxes, etc. In these cases,
     * value should be the value of the option selected, not the visible text.
     *
     * @param string $locator an element locator
     * @param string $value the value to type
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function type($locator, $value)
    {
        $this->doCommand('type', array($locator, $value));
    }
    // }}}
    // {{{ check($locator)
    /**
     * Check a toggle-button (checkbox/radio)
     *
     * @param string $locator an element locator
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function check($locator)
    {
        $this->doCommand('check', array($locator));
    }
    // }}}
    // {{{ uncheck($locator)
    /**
     * Uncheck a toggle-button (checkbox/radio)
     *
     * @param string $locator an element locator
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function uncheck($locator)
    {
        $this->doCommand('uncheck', array($locator));
    }
    // }}}
    // {{{ select($selectLocator, $optionLocator)
    /**
     * Select an option from a drop-down using an option locator.
     *
     * Option locators provide different ways of specifying options of an HTML
     * Select element (e.g. for selecting a specific option, or for asserting
     * that the selected option satisfies a specification). There are several
     * forms of Select Option Locator.
     *
     *   <b>label</b>=<em>labelPattern</em>::
     * matches options based on their labels, i.e. the visible text. (This
     * is the default.)
     *   label=regexp:^[Oo]ther
     *
     *   <b>value</b>=<em>valuePattern</em>::
     * matches options based on their values.
     *    value=other
     *
     *   <b>id</b>=<em>id</em>::
     * matches options based on their ids.
     *    id=option1
     *
     *   <b>index</b>=<em>index</em>::
     * matches an option based on its index (offset from zero).
     *    index=2
     *
     * If no option locator prefix is provided, the default behaviour is to match on <b>label</b>.
     *
     * @param string $selectLocator an element locator identifying a drop-down menu
     * @param string $optionLocator an option locator (a label by default)
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function select($selectLocator, $optionLocator)
    {
        $this->doCommand('select', array($selectLocator, $optionLocator));
    }
    // }}}
    // {{{ addselect($selectLocator, $optionLocator)
    /**
     * Add a selection to the set of selected options in a multi-select element using an option locator.
     *
     * @param string $locator an element locator identifying a multi-select box
     * @param string $optionLocator an option locator (a label by default)
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function addSelection($locator, $optionLocator)
    {
        $this->doCommand('addSelection', array($locator, $optionLocator));
    }
    // }}}
    // {{{ removeSelect($selectLocator, $optionLocator)
    /**
     * Remove a selection to the set of selected options in a multi-select element using an option locator.
     *
     * @param string $locator an element locator identifying a multi-select box
     * @param string $optionLocator an option locator (a label by default)
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function removeSelection($locator, $optionLocator)
    {
        $this->doCommand('removeSelection', array($locator, $optionLocator));
    }
    // }}}
    // {{{ submit($locator)
    /**
     * Submit the specified form. This is particularly useful for forms without
     * submit buttons, e.g. single-input "Search" forms.
     *
     * @param string $locator an element locator
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function submit($locator)
    {
        $this->doCommand('submit', array($locator));
    }
    // }}}
    // {{{ open($url)
    /**
     * Open the UrL in the test frame. This accepts both relative and absolute
     * URLs.
     *
     * The "open" command waits for the page to load before proceeding.
     * ie. the "AndWait" suffix is implicit.
     *
     * <em>Note</em>: The URL must be on the same domain as the runner HTML
     * due to security restrictions in the browser (Same Origin Policy). If you
     * need to open an URL on another domain, use the Selenium Server to start a
     * new browser session on that domain.
     *
     * @param string $url the URL to open; may be relative or absolute
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function open($url)
    {
        $this->doCommand('open', array($url));
    }
    // }}}
    // {{{ selectWindow($windowId)
    /**
     * Selects a popup window; once a popup window has been selected, all
     * commands go to that window. To select the main window again, use "null"
     * as the target.
     *
     * @param string $windowId the JavaScript window ID of the window to select
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function selectWindow($windowId)
    {
        $this->doCommand('selectWindow', array($windowId));
    }
    // }}}
    // {{{ waitForPopUp($windowId, $timeout)
    /**
     * Wait for a popup window to appear and load up.
     *
     * @param string $windowId the JavaScript window ID of the window to select
     * @param int $timeout a timeout in milliseconds, after which the action will return with an error
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function waitForPopUp($windowId, $timeout = null)
    {
        if (empty($timeout)) {
            $timeout = $this->timeout;
        }
        $this->doCommand('waitForPopUp', array($windowId, $timeout));
    }
    // }}}
    // {{{ chooseCancelOnNextConfirmation()
    /**
     * By default, Selenium's overridden window.confirm() function will
     * return true, as if the user had manually clicked OK.  After running
     * this command, the next call to confirm() will return false, as if
     * the user had clicked Cancel.
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function chooseCancelOnNextConfirmation()
    {
        $this->doCommand('chooseCancelOnNextConfirmation');
    }
    // }}}
    // {{{ answerOnNextPrompt($answer)
    /**
     * Instructs Selenium to return the specified answer string in response to
     * the next JavaScript prompt [window.prompt()].
     *
     * @param string $answer the answer to give in response to the prompt pop-up
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function answerOnNextPrompt($answer)
    {
        $this->doCommand('answerOnNextPrompt', array($answer));
    }
    // }}}
    // {{{ goBack()
    /**
     * Simulates the user clicking the "back" button" on their browser.
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function goBack()
    {
        $this->doCommand('goBack');
    }
    // }}}
    // {{{ refresh()
    /**
     * Simulates the user clicking the "Refresh" button" on their browser.
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function refresh()
    {
        $this->doCommand('refresh');
    }
    // }}}
    // {{{ close()
    /**
     * Simulates the user clicking the "close" button" in the titlebar of a popup
     * window or tab.
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function close()
    {
        $this->doCommand('close');
    }
    // }}}
    // {{{ isAlertPresent()
    /**
     * Has an alert occured?
     *
     * @access public
     * @return boolean on success, error object on failure
     * @throws Selenium_Exception
     */
    public function isAlertPresent()
    {
        return $this->getBoolean('isAlertPresent');
    }
    // }}}
    // {{{ isPromptPresent()
    /**
     * Has a prompt occured?
     *
     * @access public
     * @return boolean on success, error object on failure
     * @throws Selenium_Exception
     */
    public function isPromptPresent()
    {
        return $this->getBoolean('isPromptPresent');
    }
    // }}}
    // {{{ isConfirmationPresent()
    /**
     * Has confirm() been called?
     *
     * @access public
     * @return boolean on success, error object on failure
     * @throws Selenium_Exception
     */
    public function isConfirmationPresent()
    {
        return $this->getBoolean('isConfirmationPresent');
    }
    // }}}
    // {{{ getAlert()

    /**
     * Retrieves the message of a JavaScript alert generated during the previous action, or fail if there were no alerts.
     * Getting an alert has the same effect as manually clicking OK. If an
     * alert is generated but you do not get/verify it, the next Selenium action
     * will fail.
     * NOTE: under Selenium, JavaScript alerts will NOT pop up a visible alert
     * dialog.
     * NOTE: Selenium does NOT support JavaScript alerts that are generated in a
     * page's onload() event handler. In this case a visible dialog WILL be
     * generated and Selenium will hang until someone manually clicks OK.
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getAlert()
    {
        return $this->getString('getAlert');
    }
    // }}}
    // {{{ getConfirmation()
    /**
     * Retrieves the message of a JavaScript confirmation dialog generated during
     * the previous action.
     * By default, the confirm function will return true, having the same effect
     * as manually clicking OK. This can be changed by prior execution of the
     * chooseCancelOnNextConfirmation command. If an confirmation is generated
     * but you do not get/verify it, the next Selenium action will fail.
     * NOTE: under Selenium, JavaScript confirmations will NOT pop up a visible
     * dialog.
     * NOTE: Selenium does NOT support JavaScript confirmations that are
     * generated in a page's onload() event handler. In this case a visible
     * dialog WILL be generated and Selenium will hang until you manually click
     * OK.
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getConfirmation()
    {
        return $this->getString('getConfirmation');
    }
    // }}}
    // {{{ getPrompt()
    /**
     * Retrieves the message of a JavaScript question prompt dialog generated during
     * the previous action.
     *
     * Successful handling of the prompt requires prior execution of the
     * answerOnNextPrompt command. If a prompt is generated but you
     * do not get/verify it, the next Selenium action will fail.
     * NOTE: under Selenium, JavaScript prompts will NOT pop up a visible
     * dialog.
     * NOTE: Selenium does NOT support JavaScript prompts that are generated in a
     * page's onload() event handler. In this case a visible dialog WILL be
     * generated and Selenium will hang until someone manually clicks OK
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getPrompt()
    {
        return $this->getString('getPrompt');
    }
    // }}}
    // {{{ getLocation()
    /**
     * Gets the absolute URL of the current page.
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getLocation()
    {
        return $this->getString('getLocation');
    }
    // }}}
    // {{{ getTitle()
    /**
     * Gets the title of the current page.
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getTitle()
    {
        return $this->getString('getTitle');
    }
    // }}}
    // {{{ getBodyText()
    /**
     * Gets the entire text of the page.
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getBodyText()
    {
        return $this->getString('getBodyText');
    }
    // }}}
    // {{{ getValue($locator)
    /**
     * Gets the (whitespace-trimmed) value of an input field (or anything else with a value parameter).
     * For checkbox/radio elements, the value will be "on" or "off" depending on
     * whether the element is checked or not.
     *
     * @param string $locator an element locator
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getValue($locator)
    {
        return $this->getString('getValue', array($locator));
    }
    // }}}
    // {{{ getText($locator)
    /**
     * Gets the text of an element. This works for any element that contains
     * text. This command uses either the textContent (Mozilla-like browsers) or
     * the innerText (IE-like browsers) of the element, which is the rendered
     * text shown to the user
     *
     * @param string $locator an element locator
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getText($locator)
    {
        return $this->getString('getText', array($locator));
    }
    // }}}
    // {{{ getEval($locator)
    /**
     * Gets the result of evaluating the specified JavaScript snippet.  The snippet may
     * have multiple lines, but only the result of the last line will be returned.
     *
     * Note that, by default, the snippet will run in the context of the "selenium"
     * object itself, so <tt>this</tt> will refer to the Selenium object, and <tt>window</tt> will
     * refer to the top-level runner test window, not the window of your application.
     * If you need a reference to the window of your application, you can refer
     * to <tt>this.browserbot.getCurrentWindow()</tt> and if you need to use
     * a locator to refer to a single element in your application page, you can
     * use <tt>this.page().findElement("foo")</tt> where "foo" is your locator.
     *
     * @param string $locator an element locator
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getEval($script)
    {
        return $this->getString('getEval', array($script));
    }
    // }}}
    // {{{ isChecked($locator)
    /**
     * Gets whether a toggle-button (checkbox/radio) is checked. Fails if the specified element does't exist or isn't a toggle button.
     *
     * @param string $locator an element locator
     * @access public
     * @return boolean on success, error object on failure
     * @throws Selenium_Exception
     */
    public function isChecked($locator)
    {
        return $this->getBoolean('isChecked', array($locator));
    }
    // }}}
    // {{{ getTable($tableCellAddress)
    /**
     * Gets the text from a cell of a table. The cellAddress syntax
     * tableLocator.row.column, where row and column start at 0.
     *
     * @param string $tableCellAddress a cell address, e.g. "foo.1.4"
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getTable($tableCellAddress)
    {
        return $this->getString('getTable', array($tableCellAddress));
    }
    // }}}
    // {{{ getSelectedLabels($selectLocator)
    /**
     * Getsall option labels (visible text) for selected options in the specified select or multi-select element.
     *
     * @param string $optionLocator an option locator (a label by default)
     * @access public
     * @return array on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getSelectedLabels($selectLocator)
    {
        return $this->getStringArray('getSelectedLabels', array($selectLocator));
    }
    // }}}
    // {{{ getSelectedLabel($selectLocator)
    /**
     * Gets all option labels (visible text) for selected options in the specified selector multi-select element.
     *
     * @param string $selectLocator an element locator identifying a drop-down menu
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getSelectedLabel($selectLocator)
    {
        return $this->getString('getSelectedLabel', array($selectLocator));
    }
    // }}}
    // {{{ getSelectedValues($selectLocator)
    /**
     * Gets all option values (value attributes) for selected options in the specified select or multi-select element.
     *
     * @param string $selectLocator an element locator identifying a drop-down menu
     * @access public
     * @return array on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getSelectedValues($selectLocator)
    {
        return $this->getStringArray('getSelectedValues', array($selectLocator));
    }
    // }}}
    // {{{ getSelectedValue($selectLocator)
    /**
     * Gets option value (value attribute) for selected option in the specified select element.
     *
     * @param string $selectLocator an element locator identifying a drop-down menu
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getSelectedValue($selectLocator)
    {
        return $this->getString('getSelectedValue', array($selectLocator));
    }
    // }}}
    // {{{ getSelectedIndexes($selectLocator)
    /**
     * Gets all option indexes (option number, starting at 0) for selected options in the specified select or multi-select element.
     *
     * @param string $selectLocator an element locator identifying a drop-down menu
     * @access public
     * @return array on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getSelectedIndexes($selectLocator)
    {
        return $this->getStringArray('getSelectedIndexes', array($selectLocator));
    }
    // }}}
    // {{{ getSelectedIndex($selectLocator)
    /**
     * Gets option index (option number, starting at 0) for selected option in the specified select element.
     *
     * @param string $selectLocator an element locator identifying a drop-down menu
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getSelectedIndex($selectLocator)
    {
        return $this->getString('getSelectedIndex', array($selectLocator));
    }
    // }}}
    // {{{ getSelectedIds($selectLocator)
    /**
     * Gets all option element IDs for selected options in the specified select or multi-select element.
     *
     * @param string $selectLocator an element locator identifying a drop-down menu
     * @access public
     * @return array on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getSelectedIds($selectLocator)
    {
        return $this->getStringArray('getSelectedIds', array($selectLocator));
    }
    // }}}
    // {{{ getSelectedId($selectLocator)
    /**
     * Gets option element ID for selected option in the specified select element.
     *
     * @param string $selectLocator an element locator identifying a drop-down menu
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getSelectedId($selectLocator)
    {
        return $this->getString('getSelectedId', array($selectLocator));
    }
    // }}}
    // {{{ getSomethingSelected($selectLocator)
    /**
     * Determines whether some option in a drop-down menu is selected.
     *
     * @param string $selectLocator an element locator identifying a drop-down menu
     * @access public
     * @return boolean on success, error object on failure
     * @throws Selenium_Exception
     */
    public function isSomethingSelected($selectLocator)
    {
        return $this->getBoolean('isSomethingSelected', array($selectLocator));
    }
    // }}}
    // {{{ getSelectOptions($selectLocator)
    /**
     * Gets all option labels in the specified select drop-down.
     *
     * @param string $selectLocator an element locator identifying a drop-down menu
     * @access public
     * @return array on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getSelectOptions($selectLocator)
    {
        return $this->getStringArray('getSelectOptions', array($selectLocator));
    }
    // }}}
    // {{{ getAttribute($attributeLocator)
    /**
     * Gets the value of an element attribute
     *
     * @param string $attributeLocator  an element locator followd by an
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getAttribute($attributeLocator)
    {
        return $this->getString('getAttribute', array($attributeLocator));
    }
    // }}}
    // {{{ isTextPattern($pattern)
    /**
     * Verifies that the specified text pattern appears somewhere on the rendered page shown to the user.
     *
     * @param string $pattern a pattern to match with the text of the page
     * @access public
     * @return boolean on success, error object on failure
     * @throws Selenium_Exception
     */
    public function isTextPattern($pattern)
    {
        return $this->getBoolean('isTextPattern', array($pattern));
    }
    // }}}
    // {{{ isElementPresent($locator)
    /**
     * Verifies that the specified element is somewhere on the page.
     *
     * @param string $locator an element locator
     * @access public
     * @return boolean on success, error object on failure
     * @throws Selenium_Exception
     */
    public function isElementPresent($locator)
    {
        return $this->getBoolean('isElementPresent', array($locator));
    }
    // }}}
    // {{{ isVisible($locator)
    /**
     * Determines if the specified element is visible. An
     * element can be rendered invisible by setting the CSS "visibility"
     * property to "hidden", or the "display" property to "none", either for the
     * element itself or one if its ancestors.  This method will fail if
     * the element is not present.
     *
     * @param string $locator an element locator
     * @access public
     * @return boolean on success, error object on failure
     * @throws Selenium_Exception
     */
    public function isVisible($locator)
    {
        return $this->getBoolean('isVisible', array($locator));
    }
    // }}}
    // {{{ isEditable($locator)
    /**
     * Determines whether the specified input element is editable, ie hasn't been disabled.
     * This method will fail if the specified element isn't an input element.
     *
     * @param string $locator an element locator
     * @access public
     * @return boolean on success, error object on failure
     * @throws Selenium_Exception
     */
    public function isEditable($locator)
    {
        return $this->getBoolean('isEditable', array($locator));
    }
    // }}}
    // {{{ getAllButtons()
    /**
     * Returns the IDs of all buttons on the page.
     * If a given button has no ID, it will appear as "" in the array.
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getAllButtons()
    {
        return $this->getStringArray('getAllButtons');
    }
    // }}}
    // {{{ getAllLinks()
    /**
     * Returns the IDs of all links on the page.
     * If a given link has no ID, it will appear as "" in the array.
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getAllLinks()
    {
        return $this->getStringArray('getAllLinks');
    }
    // }}}
    // {{{ getAllFields()
    /**
     * Returns the IDs of all nput fields on the page.
     * If a given field has no ID, it will appear as "" in the array.
     *
     * @access public
     * @return array on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getAllFields()
    {
        return $this->getStringArray('getAllFields');
    }
    // }}}
    // {{{ getHtmlSource()
    /**
     * Returns the entire HTML source between the opening and
     * closing "html" tags.
     *
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getHtmlSource()
    {
        // XXX Thanks to Asad!
        // The initial release of the this method name was getAllSource,
        return $this->getString('getHtmlSource');
    }
    // }}}
    // {{{ setCursorPosition($locator, $position)
    /**
     * Moves the text cursor to the specified position in the given input element or textarea.
     * This method will fail if the specified element isn't an input element or textarea
     *
     * @param string $locator an element locator pointing to an input element or textarea
     * @param int $position the numerical position of the cursor in the field; position should be 0 to move the position to the beginning of the field. You can also set the cursor to -1 to move it to the endo of the field.
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function setCursorPosition($locator, $position)
    {
        $this->doCommand('setCursorPosition', array($locator, $position));
    }
    // }}}
    // {{{ getCursorPosition($locator)
    /**
     * Retrieves the text cursor position in the given input element or textarea; beware, this may not work perfectly on all browsers.
     * Specifically, if the cursor/selection has been cleared by JavaScript, this command will tend to
     * return the position of the last location of the cursor, even though the cursor is now gone from the page.  This is filed as SEL-243.
     * This method will fail if the specified element isn't an input element or textarea, or there is no cursor in the element.
     *
     * @param string $locator an element locator poiting to an input element or textarea
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getCursorPosition($locator)
    {
        return $this->getString('getCursorPosition', array($locator));
    }
    // }}}
    // {{{ setContext($context, $logLevelThreashould)
    /**
     * Writes a message to the status bar and adds a note to the browser-side
     * log.
     *
     * If logLevelThreshold is specified, set the threshold for logging
     * to that level (debug, info, warn, error).
     * (Note that the browser-side logs will <em>not</em> be sent back to the
     * server, and are invisible to the Client Driver.)
     *
     * @param string $context the message to be sent to the browser
     * @param string $logLevelThreashould one of "debug", "info", "warn" , "error", sets the thrshould for browser-side logging
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function setContext($context, $logLevelThreashould)
    {
        $this->doCommand('setContext', array($context, $logLevelThreshould));
    }
    // }}}
    // {{{ getExpression($expression)
    /**
     * Returns the specified expression.
     *
     * This is useful because of JavaScript preprocessing.
     * It is used to generate commands like assertExpression and waitForExpression.
     *
     * @param string $expression the value to return
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function getExpression($expression)
    {
        return $this->getString('getExpression', array($expression));
    }
    // }}}
    // {{{ waitForCondition($script, $timeout = null)
    /**
     * Runs the specified JavaScript snippet repeatedly until it evaluates to "true".
     * The snippet may have multiple lines, but only the result of the last line
     * will be considered.
     * Note that, by default, the snippet will be run in the runner's test window, not in the window
     * of your application.  To get the window of your application, you can use
     * the JavaScript snippet <tt>selenium.browserbot.getCurrentWindow()</tt>, and then
     * run your JavaScript in there
     *
     * @param string $script the JavaScript snippet to run
     * @param int $timeout in milliseconds, after which this command will return with an error
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function waitForCondition($script, $timeout = null)
    {
        if (empty($timeout)) {
            $timeout = $this->timeout;
        }
        $this->doCommand('waitForCondition', array($script, $timeout));
    }
    // }}}
    // {{{ setTimeout($timeout)
    /**
     * Specifies the amount of time that Selenium will wait for actions to complete.
     * Actions that require waiting include "open" and the "waitFor*" actions.
     * The default timeout is 30 seconds.
     *
     * @param int $timeout in milliseconds, after which the action will return with an error
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        $this->doCommand('setTimeout', array($timeout));
    }
    // }}}
    // {{{ waitForPageToLoad($timeout)
    /**
     * Waits for a new page to load.
     * You can use this command instead of the "AndWait" suffixes, "clickAndWait", "selectAndWait", "typeAndWait" etc.
     * (which are only available in the JS API).
     * Selenium constantly keeps track of new pages loading, and sets a "newPageLoaded"
     * flag when it first notices a page load.  Running any other Selenium command after
     * turns the flag to false.  Hence, if you want to wait for a page to load, you must
     * wait immediately after a Selenium command that caused a page-load.
     *
     * @param string $timeout a timeout in milliseconds, after which this command will return with an error
     * @access public
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    public function waitForPageToLoad($timeout = null)
    {
        if (empty($timeout)) {
            $timeout = $this->timeout;
        }
        $this->doCommand('waitForPageToLoad', array($timeout));
    }
    // }}}
    // }}}
    // {{{  private methods
    // {{{ doCommand
    /**
     * Send the specified Selenese command to the browser to be performed
     *
     * @param string $verb
     * @param array $args
     * @access private
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    private function doCommand($verb, $args = array())
    {
        $url = sprintf('http://%s:%s/selenium-server/driver/?cmd=%s', $this->host, $this->port, htmlspecialchars($verb));
        for ($i = 0; $i < count($args); $i++) {
            $argNum = strval($i + 1);
            $url .= sprintf('&%s=%s', $argNum, htmlspecialchars($args[$i]));
        }

        if (isset($this->sessionId)) {
            $url .= sprintf('&%s=%s', 'sessionId', $this->sessionId);
        }
        if ($this->driver == 'curl') {
            $response = $this->useCurl($verb, $args, $url);
        } elseif ($this->driver == 'pear') {
            $response = $this->useHTTP_Request($verb, $args, $url);
        } else {
            $response = $this->useNative($verb, $args, $url);
        }

        if (!preg_match('/^OK/', $response)) {
            throw new Selenium_Exception('The Response of the Selenium RC is invalid: ' . $response);
        }
        return $response;
    }
    // }}}
    // {{{ use PEAR HTTP_Request
    /**
     * useHTTP_Request
     *
     * @param string $verb
     * @param string $args
     * @param string $url
     * @access private
     * @return string
     * @throws Selenium_Exception
     */
    private function useHTTP_Request($verb, $args, $url)
    {
        require_once 'HTTP/Request.php';
        $request = new HTTP_Request($url);
        $request->_sock->blocking = false;
        $result = $request->sendRequest();
        if (PEAR::isError($result)) {
            throw Selenium_Exception('Can not connect to Selenium RC Server: '. $result->getMessage(), $request->getResponseCode());
        }
        return $request->getResponseBody();
    }
    // }}}
    // {{{ use PHP native functions
    /**
     * useNative
     *
     * @param string $verb
     * @param string $args
     * @param string $url
     * @access private
     * @return string
     * @throws Selenium_Exception
     */
    private function useNative($verb, $args, $url)
    {
        if (!$handle = fopen($url, 'r')) {
            throw new Selenium_Exception('Cannot connected to Selenium RC Server');
        }
        // no socket block
        stream_set_blocking($handle, false);
        $response = stream_get_contents($handle);
        fclose($handle);

        return $response;
    }
    // }}}
    // {{{ use PHP curl extension functions
    /**
     * useCurl
     *
     * @param string $verb
     * @param string $args
     * @param string $url
     * @access private
     * @return string
     * @throws Selenium_Exception
     */
    private function useCurl($verb, $args, $url)
    {
        if (!function_exists('curl_init')) {
            throw new Selenium_Exception('cannot use curl exntensions. chosse "pear" or "native"');
        }

        if (!$ch = curl_init($url)) {
            throw new Selenium_Exception('Unable to setup curl');
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        if (($errno = curl_errno($ch)) != 0) {
            throw new Selenium_Exception('Curl returned non-null errno ' . $errno . ':' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
    // }}}
    // {{{ getString
    /**
     * Get the string result of the Selenese Command
     *
     * @param string $verb
     * @param array $arg
     * @access protected
     * @return string on success, error object on failure
     * @throws Selenium_Exception
     */
    private function getString($verb, $args = array())
    {
        try {
            $result = $this->doCommand($verb, $args);
        } catch (Selenium_Exception $e) {
            return $e;
        }
        return substr($result, 3);
    }
    // }}}
    // {{{ getStringArray
    /**
     * Get the array result of the Selenese Command
     *
     * @param string $verb
     * @param array $args
     * @access protected
     * @return array on success, error object on failure
     * @throws Selenium_Exception
     */
    private function getStringArray($verb, $args = array())
    {
        $csv = $this->getString($verb, $args);

        $token = '';
        $tokens = array();
        $letters = preg_split('//', $csv, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0; $i < count($letters); $i++) {
            $letter = $letters[$i];
            switch($letter) {
            case '\\':
                $i++;
                $letter = $letters[$i];
                $token = $token . $letter;
                break;
            case ',':
                array_push($tokens, $token);
                $token = '';
                break;
            default:
                $token = $token . $letter;
                break;
            }
        }
        array_push($tokens, $token);
        return $tokens;
    }
    // }}}
    // {{{ getBoolean
    /**
     * Get the boolean result of the Selenese Command
     *
     * @param string $verb
     * @param array $args
     * @access private
     * @return boolean on success, error object on failure
     * @throws Selenium_Exception
     */
    private function getBoolean($verb, $args = array())
    {
        $result = $this->getString($verb, $args);

        switch ($result) {
        case 'true':
            return true;
        case 'false':
            return false;
        default:
            throw new Selenium_Exception('result is neither "true" or "false": ' . $result);
        }
    }
    // }}}
    // }}}
}
?>
