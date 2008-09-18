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
 * This class generates form components for Activity Links
 * 
 */
class CRM_Activity_Form_ActivityLinks extends CRM_Core_Form
{

    public function buildQuickForm( ) 
    {
        $this->applyFilter('__ALL__', 'trim');                                                                       
        $contactId = CRM_Utils_Request::retrieve( 'cid' , 'Positive', $this );
        
        $urlParams = "action=add&reset=1&cid={$contactId}&selectedChild=activity&atype=";
        
        $url = CRM_Utils_System::url( 'civicrm/contact/view/activity', 
                                      $urlParams, false, null, false ); 

        $activityType = CRM_Core_PseudoConstant::activityType( false );
        
        //unset Phone and Meeting
        unset( $activityType[1] );
        unset( $activityType[2] );

        $this->assign( 'emailSetting', false );
        require_once 'CRM/Utils/Mail.php';
        if ( CRM_Utils_Mail::validOutBoundMail() ) { 
            $this->assign( 'emailSetting', true );
        }
        $this->applyFilter('__ALL__', 'trim');
        $this->add('select', 'other_activity', ts('Other Activities'),
                   array('' => ts('- select -')) + $activityType,
                   false, array('onchange' => "if (this.value) window.location='{$url}'+ this.value; else return false"));

        $this->assign( 'suppressForm', true );
    }
}


