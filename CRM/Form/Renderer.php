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
 * We redefine the Smarty Renderer to make things compliant with Drupal and
 * folks using other Drupal theming packages instead of Smarty
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

class CRM_Form_Renderer extends HTML_QuickForm_Renderer_ArraySmarty {
    static $_sizeMapper = array(
                                2  => 'two',
                                4  => 'four',
                                8  => 'eight',
                                12 => 'twelve',
                                20 => 'medium',
                                30 => 'big',
                                45 => 'huge',
                               );

    /*
     * Creates an array representing an element containing
     * the key for storing this. We allow the parent to do most of the
     * work, but then we add some Drupal specific hacks and call the
     * drupal form theming functions (which in turn will call the
     * theme engine that is installed
     *
     * @access private
     * @param  object    An HTML_QuickForm_element object
     * @param  bool      Whether an element is required
     * @param  string    Error associated with the element
     * @return array
     */
    function _elementToArray(&$element, $required, $error) {
        // return parent::_elementToArray($element, $required, $error);

        self::updateAttributes($element, $required, $error);

        $el = parent::_elementToArray($element, $required, $error);

        // add label html
        if ( ! empty($el['label']) ) {
            $id = $element->getAttribute('id');
            if ( ! empty( $id ) ) {
                $el['label'] = '<label for="' . $id . '">' . $el['label'] . '</label>';
            }
        }

        // $el['required'] = $required ? theme('mark') : null;
        // $el['theme']    = theme( 'form_element', $element->getLabel(), $el['html'], null, $element->getName(), $req, $el['error'] );
                       
        return $el;
    }

    function updateAttributes(&$element, $required, $error) {

        // lets create an id for all input elements, so we can generate nice label tags
        // to make it nice and clean, we'll just use the elementName if it is non null
        if (!$element->getAttribute('id')) {
            $name = $element->getAttribute('name');
            if ($name) {
                $element->updateAttributes(array('id' => $name ));
            } else {
                $element->_generateId( );
            }
        }
        
        $class = $element->getAttribute('class');
        $type  = $element->getType( );
        if ( empty( $class ) ) {
            $class = 'form-' . $type;
            
            if ( $type == 'text' ) {
                $size   = $element->getAttribute('size');
                if (! empty( $size ) ) {
                    if ( array_key_exists( $size, self::$_sizeMapper ) ) {
                        $class = $class . ' ' . self::$_sizeMapper[$size];
                    }
                }
            }
        }

        if ( $required ) {
            $class .= ' required';
        }

        if ( $error ) {
            $class .= ' error';
        }
        $element->updateAttributes( array( 'class' => $class ) );
    }

} // end CRM_Form_Renderer

?>