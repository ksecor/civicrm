<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Admin/Form.php';

/**
 * This class generates form components for Location Type
 * 
 */
class CRM_Admin_Form_PreferencesDate extends CRM_Admin_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
       
        parent::buildQuickForm( );
       
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }
        
        $this->applyFilter('__ALL__', 'trim');
        $name =& $this->add('text',
                            'name',
                            ts('Name'),
                            CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_PreferencesDate', 'name' ),
                            true );
        $name->freeze( );
        
        $attribute  = CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_PreferencesDate', 'start' );
        $formatAttr = CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_PreferencesDate', 'format' );
        $descAttr = CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_PreferencesDate', 'description' );

        $this->add('text', 'description'     , ts('Description'     ), $descAttr  , false );
        $this->add('text', 'start'           , ts('Start Offset'    ), $attribute , true  );
        $this->add('text', 'end'             , ts('End Offset'      ), $attribute , true  );
        $this->add('text', 'minute_increment', ts('Minute Increment'), $attribute , false );
        $this->add('text', 'format'          , ts('Format')          , $formatAttr, false );

        $this->addRule( 'start'           , ts( 'Value should be a positive number' ) , 'positiveInteger');
        $this->addRule( 'end'             , ts( 'Value should be a positive number' ) , 'positiveInteger');
        $this->addRule( 'minute_increment', ts( 'Value should be a positive number' ) , 'positiveInteger');
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        if ( ! ( $this->_action & CRM_Core_Action::UPDATE ) ) {
            CRM_Core_Session::setStatus( ts('Preferences Date Options can only be updated' ) );
            return;
        }
        
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        
        // action is taken depending upon the mode
        $dao                   =& new CRM_Core_DAO_PreferencesDate( );
        $dao->id               =  $this->_id;
        $dao->description      =  $params['description'];  
        $dao->start            =  $params['start'];  
        $dao->end              =  $params['end'];
        $dao->minute_increment =  $params['minute_increment'];
        $dao->format           =  $params['format'];
        
        $dao->save( );
        
        CRM_Core_Session::setStatus( ts('The date type \'%1\' has been saved.',
                                        array( 1 => $params['name'] )) );
    }//end of function

}


