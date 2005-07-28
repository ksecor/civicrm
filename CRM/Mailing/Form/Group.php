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

require_once 'CRM/Core/Form.php';

/**
 * Choose include / exclude groups and mailings
 *
 */
class CRM_Mailing_Form_Group extends CRM_Core_Form {

    /**
     * The number of groups / mailings we will process
     */
    const NUMBER_OF_ELEMENTS = 5;

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        $group       = array( '' => '-select-' ) + CRM_Core_PseudoConstant::group( );
        $groupCount  = min( self::NUMBER_OF_ELEMENTS, count( $group ) - 1 );
        $groupType   = array( 'include' => 'Include All Members from this Group',
                              'exclude' => 'Exclude All Members from this Group' );

        $mailing      = array( '' => '-select-' ) + CRM_Mailing_PseudoConstant::completed( );
        $mailingCount = min( self::NUMBER_OF_ELEMENTS, count( $mailing ) - 1 );
        $mailingType  = array( 'include' => 'Include All Members from this Mailing',
                               'exclude' => 'Exclude All Members from this Mailing' );


        for ( $i = 1; $i <= $groupCount; $i++ ) {
            $this->add( 'select', "group[$i]"      , null, $group       );
            $this->add( 'select', "groupType[$i]"  , null, $groupType   );
        }
        $this->assign( 'groupCount', $groupCount );
        
        for ( $i = 1; $i <= $mailingCount; $i++ ) {
            $this->add( 'select', "mailing[$i]"    , null, $mailing     );
            $this->add( 'select', "mailingType[$i]", null, $mailingType );
        }
        $this->assign( 'mailingCount', $mailingCount );

        $this->add( 'select', 'mailingHeader', ts( 'Mailing Header' ), CRM_Mailing_PseudoConstant::component( 'Header' ) );
        $this->add( 'select', 'mailingFooter', ts( 'Mailing Footer' ), CRM_Mailing_PseudoConstant::component( 'Footer' ) );

        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('Previous <<') ),
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Next >>'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    public function postProcess() {
        $header = $this->controller->exportValue($this->_name, 'mailingHeader');
        $footer = $this->controller->exportValue($this->_name, 'mailingFooter');

        $this->set('mailingHeader', $header);
        $this->set('mailingFooter', $footer);

    }

    /**
     * Display Name of the form
     *
     * @access public
     * @return string
     */
    public function getTitle( ) {
        return ts( 'Select Mailing Recipients' );
    }

}

?>
