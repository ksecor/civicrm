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

require_once "CRM/Core/Form.php";

/**
 * This class generates form components for OpenCase Activity
 * 
 */
class CRM_Case_Form_Activity_ChangeCaseType
{

    static function preProcess( &$form ) 
    {        
        if ( !isset($form->_id) ) {
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

        $defaults['is_reset_timeline'] = 1;
        
        $defaults['reset_date_time'] = array( );
        CRM_Utils_Date::getAllDefaultValues( $defaults['reset_date_time'] );
        $defaults['reset_date_time']['i'] = (int ) ( $defaults['activity_date_time']['i'] / 15 ) * 15;

        return $defaults;
    }

    static function buildQuickForm( &$form ) 
    { 
        require_once 'CRM/Core/OptionGroup.php';        
        $caseType = CRM_Core_OptionGroup::values('case_type');
        $form->add('select', 'case_type_id',  ts( 'New Case Type' ),  
                   $caseType , true);

        // timeline
        $form->addYesNo( 'is_reset_timeline', ts( 'Reset Case Timeline?' ),null, true, array('onclick' =>"return showHideByValue('is_reset_timeline','','resetTimeline','table-row','radio',false);") );
        $form->add( 'date', 'reset_date_time', ts('Reset Start Date'),
                    CRM_Core_SelectValues::date('activityDatetime' ), false );   
        $form->addRule('reset_date_time', ts('Select a valid date.'), 'qfDate');
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

        if ( CRM_Utils_Array::value('is_reset_timeline', $params ) == 0 ) {
            unset($params['reset_date_time']);
        } else {
            // store the date with proper format
            $params['reset_date_time'] = CRM_Utils_Date::format( $params['reset_date_time'] );
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
        if ( !CRM_Utils_Array::value('case_id', $params) && $form->_context == 'activity' ) {
            return;
        }

        if (!$form->_clientId   ||
            !$form->_uid        ||
            !$params['case_id'] ||
            !$params['case_type']
            ) {
            CRM_Core_Error::fatal('Required parameter missing for ChangeCaseType - end post processing');
        }

        // 1. initiate xml processor
        $xmlProcessor = new CRM_Case_XMLProcessor_Process( );
        $xmlProcessorParams = array( 'clientID'         => $form->_clientId,
                                     'creatorID'        => $form->_uid,
                                     'standardTimeline' => 1,
                                     'activityTypeName' => 'Change Case Type',
                                     'dueDateTime'      => $params['reset_date_time'],
                                     'caseID'           => $params['case_id'],
                                     );

        $xmlProcessor->run( $params['case_type'], $xmlProcessorParams );

        // status msg
        $params['statusMsg'] = ts('Case Type changed successfully.');
    }
}
