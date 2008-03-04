<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
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

require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/Action.php';
require_once 'CRM/Core/Permission.php';

require_once 'CRM/Utils/Request.php'; 

/**
 * A Page is basically data in a nice pretty format.
 *
 * Pages should not have any form actions / elements in them. If they
 * do, make sure you use CRM_Core_Form and the related structures. You can
 * embed simple forms in Page and do your own form handling.
 *
 */
class CRM_Core_Page {
    
    /**
     * The name of the page (auto generated from class name)
     *
     * @var string
     * @access protected
     */
    protected $_name;

    /**
     * the title associated with this page
     *
     * @var object
     * @access protected
     */
    protected $_title;

    /**
     * A page can have multiple modes. (i.e. displays
     * a different set of data based on the input
     * @var int
     * @access protected
     */
    protected $_mode;

    /**
     * Is this object being embedded in another object. If
     * so the display routine needs to not do any work. (The
     * parent object takes care of the display)
     *
     * @var boolean
     * @access protected
     */
    protected $_embedded = false;

    /**
     * Are we in print mode? if so we need to modify the display
     * functionality to do a minimal display :)
     *
     * @var boolean
     * @access protected
     */
    protected $_print = false;
    
    /**
     * cache the smarty template for efficiency reasons
     *
     * @var CRM_Core_Smarty
     * @access protected
     * @static
     */
    static protected $_template;

    /**
     * cache the session for efficiency reasons
     *
     * @var CRM_Core_Session
     * @access protected
     * @static
     */
    static protected $_session;

    /**
     * class constructor
     *
     * @param string $title title of the page
     * @param int    $mode  mode of the page
     *
     * @return CRM_Core_Page
     */
    function __construct($title = null, $mode = null)
    {
        $this->_name = CRM_Utils_System::getClassName($this);
        $this->_title = $title;
        $this->_mode  = $mode;

        if ( isset( $_GET['snippet'] ) && $_GET['snippet'] ) {
            $this->_print = CRM_Core_Smarty::PRINT_SNIPPET;
        }

        // let the constructor initialize this, should happen only once
        if ( ! isset( self::$_template ) ) {
            self::$_template =& CRM_Core_Smarty::singleton( );
            self::$_session  =& CRM_Core_Session::singleton( );
        }

        // if the request has a reset value, initialize the controller session
        if ( CRM_Utils_Array::value( 'reset', $_GET ) ) {
            $this->reset( );
        }

    }

    /**
     * This function takes care of all the things common to all
     * pages. This typically involves assigning the appropriate
     * smarty variable :)
     *
     * @return string The content generated by running this page
     */
    function run()
    {
        if ( $this->_embedded ) {
            return;
        }

        self::$_template->assign( 'mode'   , $this->_mode );
        self::$_template->assign( 'tplFile', $this->getTemplateFileName() );

        if ( $this->_print ) {
            if ( $this->_print == CRM_Core_Smarty::PRINT_SNIPPET ) {
                $content = self::$_template->fetch( 'CRM/common/snippet.tpl' );
            } else {
                $content = self::$_template->fetch( 'CRM/common/print.tpl' );
            }
            echo $content;
            exit( );
        }
        $config =& CRM_Core_Config::singleton();
        $content = self::$_template->fetch( 'CRM/common/'. strtolower($config->userFramework) .'.tpl' );
        echo CRM_Utils_System::theme( 'page', $content, true, $this->_print );
        return;
    }

    /**
     * Store the variable with the value in the form scope
     *
     * @param  string|array $name  name  of the variable or an assoc array of name/value pairs
     * @param  mixed        $value value of the variable if string
     *
     * @access public
     * @return void
     *
     */
    function set($name, $value = null)
    {
        self::$_session->set( $name, $value, $this->_name );
    }

    /**
     * Get the variable from the form scope
     *
     * @param  string name  : name  of the variable
     *
     * @access public
     * @return mixed
     *
     */
    function get($name)
    {
        return self::$_session->get( $name, $this->_name );
    }

    /**
     * assign value to name in template
     *
     * @param array|string $name  name  of variable
     * @param mixed $value value of varaible
     *
     * @return void
     * @access public
     */
    function assign( $var, $value = null) {
        self::$_template->assign($var, $value);
    }

    /**
     * assign value to name in template by reference
     *
     * @param array|string $name  name  of variable
     * @param mixed $value (reference) value of varaible
     *
     * @return void
     * @access public
     */
    function assign_by_ref( $var, &$value) {
        self::$_template->assign_by_ref($var, $value);
    }

    
    /**
     * function to destroy all the session state of this page.
     *
     * @access public
     * @return void
     */
    function reset( ) {
        self::$_session->resetScope( $this->_name );
    }

    /**
     * Use the form name to create the tpl file name
     *
     * @return string
     * @access public
     */
    function getTemplateFileName() {
        return (str_replace('_', DIRECTORY_SEPARATOR, $this->_name) . '.tpl');
    }

    /**
     * setter for embedded 
     *
     * @param boolean $embedded
     *
     * @return void
     * @access public
     */
    function setEmbedded( $embedded  ) {
        $this->_embedded = $embedded;
    }

    /**
     * getter for embedded 
     *
     * @return boolean return the embedded value
     * @access public
     */
    function getEmbedded( ) {
        return $this->_embedded;
    }

    /**
     * setter for print 
     *
     * @param boolean $print
     *
     * @return void
     * @access public
     */
    function setPrint( $print  ) {
        $this->_print = $print;
    }

    /**
     * getter for print 
     *
     * @return boolean return the print value
     * @access public
     */
    function getPrint( ) {
        return $this->_print;
    }

    static function &getTemplate( ) {
        return self::$_template;
    }
}


