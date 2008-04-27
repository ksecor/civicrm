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

require_once 'CRM/Core/Form.php';

/**
 * This class gets the name of the file to upload
 */
class CRM_Import_Form_Mapper extends CRM_Core_Form 
{
    protected $_maxMapper = 4;

    function buildQuickForm( ) {
        $this->assign( 'dojoIncludes', "dojo.require('civicrm.HierSelect');" );

        $this->assign( 'maxMapper', $this->_maxMapper + 1 );

        for ($i = 1; $i <= $this->_maxMapper; $i++) {
            $attributes    = array( 'dojoType'     => 'civicrm.HierSelect',
                                    'url1'         => CRM_Utils_System::url('civicrm/ajax/mapper/select', 'index=1'),
                                    'url2'         => CRM_Utils_System::url('civicrm/ajax/mapper/select', 'index=2'),
                                    'url3'         => CRM_Utils_System::url('civicrm/ajax/mapper/select', 'index=3'),
                                    'url4'         => CRM_Utils_System::url('civicrm/ajax/mapper/select', 'index=4'),
//                                     'default1'     => "3",
//                                     'default2'     => "3",
//                                     'default3'     => "3",
//                                     'default4'     => "3",
                                    'firstInList'  => "true",
                                    'jsMethod1'    => "showHideSelector2( this.name, e )",
                                    'jsMethod2'    => "showHideSelector3( this.name, e )",
                                    'jsMethod3'    => "showHideSelector4( this.name, e )",
                                    );
            $this->add( 'text', "mapper[$i]", ts( 'Select Mapper %1', array( 1 => $i ) ), $attributes );
        }

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Continue >>'),
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    function postProcess( ) {
        // store the submitted values in an array
        CRM_Core_Error::debug( 'POST', $_POST );
        $params = $this->controller->exportValues( $this->_name );
        CRM_Core_Error::debug( 'params', $params );
        exit( );
    }
}
