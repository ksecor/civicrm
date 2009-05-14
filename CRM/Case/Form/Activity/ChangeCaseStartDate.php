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

require_once "CRM/Core/Form.php";

/**
 * This class generates form components for OpenCase Activity
 * 
 */
class CRM_Case_Form_Activity_ChangeCaseStartDate
{

    static function preProcess( &$form ) 
    {
        if ( !isset($form->_caseId) ) {
            CRM_Core_Error::fatal(ts('Case Id not found.'));
        }
    }

    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( &$form ) 
    {
        $defaults = array( );

        $defaults['start_date'] = array( );
        CRM_Utils_Date::getAllDefaultValues( $defaults['start_date'] );

        return $defaults;
    }

    static function buildQuickForm( &$form ) 
    { 
        $currentStartDate = CRM_Core_DAO::getFieldValue( 'CRM_Case_DAO_Case',
                                                         $form->_caseId,
                                                         'start_date' );

        $form->assign('current_start_date',  $currentStartDate );
        $form->add( 'date', 'start_date', ts('New Start Date'), CRM_Core_SelectValues::date( ), false );   
        $form->addRule('start_date', ts('Select a valid date.'), 'qfDate');
    }

    /**
     * global validation rules for the form
     *
     * @param array $values posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$values, $files, &$form ) 
    {
        return true;
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function beginPostProcess( &$form, &$params ) 
    {
        if ( $form->_context == 'case' ) {
            $params['id'] = $form->_id;
        }
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function endPostProcess( &$form, &$params ) 
    {
        if ( CRM_Utils_Array::value('start_date', $params ) ) {
            $params['start_date'] = CRM_Utils_Date::format( $params['start_date'] );
        }
       
        // status msg
        $params['statusMsg'] = ts('Case Start Date changed successfully.');
    }
}
