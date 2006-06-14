<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Member/Form.php';
require_once 'CRM/Member/PseudoConstant.php';

/**
 * This class generates form components for Membership Type
 * 
 */
class CRM_Member_Form_Membership extends CRM_Member_Form
{

    public function preProcess()  
    {  
        // action
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String',
                                                      $this, false, 'add' );
        $this->_id        = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                                         $this );
        
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            return;
        }
        
        $this->_contactID = CRM_Utils_Request::retrieve( 'cid', 'Positive',
                                                         $this );

        parent::preProcess( );
    }

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

        $this->add('select', 'membership_type_id', 
                   ts( 'Membership Type' ), 
                   array(''=>ts( '-select-' )) + CRM_Member_PseudoConstant::membershipType( ),
                   true );

        $this->add('date', 'join_date', ts('Join Date'), CRM_Core_SelectValues::date('manual', 3, 1), false );         
        $this->addRule('join_date', ts('Select a valid date.'), 'qfDate');
        $this->add('date', 'start_date', ts('Start Date'), CRM_Core_SelectValues::date('manual', 3, 1), false );         
        $this->addRule('start_date', ts('Select a valid date.'), 'qfDate');
        $this->add('date', 'end_date', ts('End Date'), CRM_Core_SelectValues::date('manual', 3, 1), false );         
        $this->addRule('end_date', ts('Select a valid date.'), 'qfDate');
        
        $this->add('text', 'source', ts('Source'), 
                   CRM_Core_DAO::getAttribute( 'CRM_Member_DAO_Membership', 'source' ) );
        $this->add('select', 'calculated_status_id', ts( 'Status' ), 
                   array(''=>ts( '-select-' )) + CRM_Member_PseudoConstant::membershipStatus( ) );
        $this->add('select', 'override_status_id', ts('Status Override'), 
                   array(''=>ts( '-select-' )) + CRM_Member_PseudoConstant::membershipStatus( ) );

        $this->add('checkbox', 'is_active', ts('Enabled?'));

    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        require_once 'CRM/Member/BAO/Membership.php';
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            CRM_Member_BAO_Membership::deleteContribution( $this->_id );
            return;
        }

        // get the submitted form values.  
        $formValues = $this->controller->exportValues( $this->_name );
        //print_r($formValues);

        $params = array( );
        $ids    = array( );

        $params['contact_id'] = $this->_contactID;

        $fields = array( 'membership_type_id',
                         'calculated_status_id',
                         'override_status_id',
                         'source'
                         );

        foreach ( $fields as $f ) {
            $params[$f] = CRM_Utils_Array::value( $f, $formValues );
        }

        $dates = array( 'join_date',
                        'start_date',
                        'end_date'
                        );
        $currentTime = getDate();        
        foreach ( $dates as $d ) {
            if ( ! CRM_Utils_System::isNull( $formValues[$d] ) ) {
                $formValues[$d]['H'] = $currentTime['hours'];
                $formValues[$d]['i'] = $currentTime['minutes'];
                $formValues[$d]['s'] = '00';
                $params[$d] = CRM_Utils_Date::format( $formValues[$d] );
            }
        }
        
        $ids['membership'] = $params['id'] = $this->_id;
        
        $membership =& CRM_Member_BAO_Membership::create( $params, $ids );
        CRM_Core_Session::setStatus( ts('The membership information has been saved.') );

    }
}

?>
