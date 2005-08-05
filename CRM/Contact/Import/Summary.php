<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */
require_once 'CRM/Import/Base.php';

class CRM_Contacts_Import_Summary extends CRM_Import_Base {
  protected $_selectFields = null;

  protected $_fieldIndex   = null;

  protected $_rows         = null;

  protected $_maxFields    = 0;

  function __construct() {
    parent::__construct();
  }

  function init() {
    $this->_rows = array();
    $this->_maxFields = 0;
  }


  function process($line) {
    $elements = CRM_Utils_String::explode( $line, CRM_Utils_String::COMMA, true );

    if ( $this->_maxFields > count($elements) ) {
      $this->_maxFields = count($elements);
    }

    $this->_rows[] = $elements;

    if ( count($this->_rows) == 5 ) {
      return self::STOP;
    }
    return self::VALID;
  }

  function fini() {
    $this->_allFields    = array();
    $this->_selectFields = array();

    for ( $i = 0; $i < $this->_maxFields; $i++ ) {
      
    }

  }
  
}

?>