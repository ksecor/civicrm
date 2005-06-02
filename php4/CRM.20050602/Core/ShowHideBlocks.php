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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

$GLOBALS['_CRM_CORE_SHOWHIDEBLOCKS']['showIcon'] = '';

require_once 'CRM/Core/Smarty.php';
require_once 'CRM/Core/Config.php';
class CRM_Core_ShowHideBlocks {

    /**
     * The array of ids of blocks that will be shown
     *
     * @var array
     */
    var $_show;

    /**
     * The array of ids of blocks that will be hidden
     *
     * @var array
     */
    var $_hide;

    /**
     * class constructor
     *
     * @param array $show initial value of show array
     * @param array $hide initial value of hide array
     *
     * @return Object     the newly created object
     * @access public
     */
    function CRM_Core_ShowHideBlocks( $show = null, $hide = null ) {
        if ( ! empty( $show ) ) {
            $this->_show = $show;
        } else {
            $this->_show = array( );
        }

        if ( ! empty( $hide ) ) {
            $this->_hide = $hide;
        } else {
            $this->_hide = array( );
        }

    }

    /**
     * add the values from this class to the template
     *
     * @return void
     * @access public
     */
    function addToTemplate( ) {
        $hide = $show = '';

        $first = true;
        foreach ( array_keys( $this->_hide ) as $h ) {
            if ( ! $first ) {
                $hide .= ',';
            }
            $hide .= "'$h'";
            $first = false;
        }

        $first = true;
        foreach ( array_keys( $this->_show ) as $s ) {
            if ( ! $first ) {
                $show .= ',';
            }
            $show .= "'$s'";
            $first = false;
        }

        $template = CRM_Core_Smarty::singleton( );
        $template->assign_by_ref( 'hideBlocks', $hide );
        $template->assign_by_ref( 'showBlocks', $show );
    }

    /**
     * Add a value to the show array
     * 
     * @param string $name id to be added
     *
     * @return void
     * @access public
     */
    function addShow( $name ) {
        $this->_show[$name] = 1;
        if ( array_key_exists( $name, $this->_hide ) ) {
            unset( $this->_hide[$name] );
        }
    }

    /**
     * Add a value to the hide array
     * 
     * @param string $name id to be added
     *
     * @return void
     * @access public
     */
    function addHide( $name ) {
        $this->_hide[$name] = 1;
        if ( array_key_exists( $name, $this->_show ) ) {
            unset( $this->_show[$name] );
        }
     }

    /**
     * create a well formatted html link from the smaller pieces
     *
     * @param string $name name of the link
     * @param string $href
     * @param string $text
     * @param string $js
     *
     * @return string      the formatted html link
     * @access public
     */
      function linkHtml( $name, $href, $text, $js ) {
        return '<a name="' . $name . '" id="' . $name . '" href="' . $href . '" ' . $js . ">$text</a>";
    }

    /**
     * Create links that we can use in the form
     *
     * @param CRM_Core_Form $form          the form object
     * @param string        $prefix        the attribute that we are referencing
     * @param string        $showLinkText  the text to be shown for the show link
     * @param string        $hideLinkText  the text to be shown for the hide link
     *
     * @return void
     * @access public
     */
    function links( $form, $prefix, $showLinkText, $hideLinkText ) {
        $showCode = "show('${prefix}'); hide('${prefix}[show]'); return false;";
        $hideCode = "hide('${prefix}'); show('${prefix}[show]'); return false;";
        
        $values = array();
        $values['show'] = CRM_Core_ShowHideBlocks::linkHtml("${prefix}[show]", "#${prefix}", $showLinkText, "onclick=\"$showCode\"");
        $values['hide'] = CRM_Core_ShowHideBlocks::linkHtml("${prefix}[hide]", "#${prefix}", $hideLinkText, "onclick=\"$hideCode\"");
        $form->assign( $prefix, $values);
    }

    /**
     * Create html link elements that we can use in the form
     *
     * @param CRM_Core_Form $form          the form object
     * @param int           $index         the current index of the element being processed
     * @param int           $maxIndex      the max number of elements that will be processed
     * @param string        $prefix        the attribute that we are referencing
     * @param string        $showLinkText  the text to be shown for the show link
     * @param string        $hideLinkText  the text to be shown for the hide link
     *
     * @return void
     * @access public
     */
    function linksForArray( $form, $index, $maxIndex, $prefix, $showLinkText, $hideLinkText ) {
        
        
        if ( $index == $maxIndex ) {
            $showCode = $hideCode = "return false;";
        } else {
            $next = $index + 1;
            $showCode = "show('${prefix}[${next}][show]'); return false;";
            $hideCode = "hide('${prefix}[${next}][show]'); hide('${prefix}[${next}]'); return false;";
        }

        if ( !isset ($GLOBALS['_CRM_CORE_SHOWHIDEBLOCKS']['showIcon'])) {
            $config = CRM_Core_Config::singleton( );
            $GLOBALS['_CRM_CORE_SHOWHIDEBLOCKS']['showIcon'] = '<img src="'.$config->resourceBase.'i/TreePlus.gif" class="action-icon" alt="another field">';
            $hideIcon = '<img src="'.$config->resourceBase.'i/TreeMinus.gif" class="action-icon" alt="remove field">';
        }
        $form->addElement('link', "${prefix}[${index}][show]", null, "#${prefix}[${index}]", $GLOBALS['_CRM_CORE_SHOWHIDEBLOCKS']['showIcon'] . $showLinkText,
                          array( 'onclick' => "hide('${prefix}[${index}][show]'); show('${prefix}[${index}]');" . $showCode));
        $form->addElement('link', "${prefix}[${index}][hide]", null, "#${prefix}[${index}]", $hideIcon . $hideLinkText,
                          array('onclick' => "hide('${prefix}[${index}]'); show('${prefix}[${index}][show]');" . $hideCode));
    }

}

?>