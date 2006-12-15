<?php 
/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.6                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright CiviCRM LLC (c) 2004-2006                                  | 
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       | 
 | about the Affero General Public License or the licensing  of       | 
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   | 
 | http://www.civicrm.org/licensing/                                 | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@civicrm.org> 
 * @copyright CiviCRM LLC (c) 2004-2006 
 * $Id$ 
 * 
 */ 

class CRM_Event_BAO_Query 
{
    
   
    static function buildSearchForm( &$form ) 
    {
        $config =& CRM_Core_Config::singleton( );
        $domainID = CRM_Core_Config::domainID( );
        $dataURL = $config->userFrameworkResourceURL . "extern/ajax.php?q=civicrm/event&d={$domainID}&s=%{searchString}";
        
        $config =& CRM_Core_Config::singleton( );
        if ( $config->userFramework == 'Drupal' &&
             $config->userFrameworkVersion <= 4.6      &&
             function_exists( 'drupal_get_token' ) ) {
            $urlArray['drupalFormToken'] = drupal_get_token( );
        }

        $form->assign( 'dataURL', $dataURL );
        
        // Date selects for date 
        $form->add('date', 'event_date_low', ts('Event Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('event_date_low', ts('Select a valid date.'), 'qfDate'); 
 
        $form->add('date', 'event_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('event_date_high', ts('Select a valid date.'), 'qfDate'); 

        require_once 'CRM/Event/PseudoConstant.php';
        $statusValues = CRM_Event_PseudoConstant::participantStatus(); 

        foreach ( $statusValues as $k => $v ) {
            $status[] = HTML_QuickForm::createElement('advcheckbox', $k , null, $v );
        }
        $form->addGroup($status, 'participant_status', ts('Participant status'));
        
        $form->assign( 'validCiviEvent', true );
    }
  
}

?>
