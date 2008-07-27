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
 * This class generates form components for Participant
 * 
 */
class CRM_Event_Form_ParticipantView extends CRM_Core_Form
{
    /**  
     * Function to set variables up before form is built  
     *                                                            
     * @return void  
     * @access public  
     */
    public function preProcess( ) 
    {
        require_once 'CRM/Event/BAO/Participant.php';

        $values = array( ); 
        $ids    = array( ); 
        $params = array( 'id' => $this->get( 'id' ) ); 

        CRM_Event_BAO_Participant::getValues( $params, 
                                              $values, 
                                              $ids );
        
        CRM_Event_BAO_Participant::resolveDefaults( $values[$this->get( 'id' )] );
        
        if ( $values[$this->get( 'id' )]['fee_level'] ) {
            CRM_Event_BAO_Participant::fixEventLevel( $values[$this->get( 'id' )]['fee_level'] );
        }
        
        if( $values[$this->get( 'id' )]['is_test'] ) {
            $values[$this->get( 'id' )]['status'] .= ' (test) ';
        }
        
        // Get Note
        $noteValue = CRM_Core_BAO_Note::getNote( $values[$this->get( 'id' )]['id'], 'civicrm_participant' );
        $values[$this->get( 'id' )]['note'] = array_values( $noteValue );
        
        // Get Contribution Line Items
        $values[$this->get( 'id' )]['line_items'] = CRM_Event_BAO_Participant::getLineItems( $this->get( 'id' ) );
        
        $groupTree =& CRM_Core_BAO_CustomGroup::getTree( 'Participant', $this->get( 'id' ), 0, 
                                                         $values[$this->get( 'id' )]['role_id'] );
        CRM_Core_BAO_CustomGroup::buildViewHTML( $this, $groupTree );

        $this->assign( $values[$this->get( 'id' )] );
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->addButtons(array(  
                                array ( 'type'      => 'next',  
                                        'name'      => ts('Done'),  
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',  
                                        'isDefault' => true   )
                                )
                          );
    }

}


