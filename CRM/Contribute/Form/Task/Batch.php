<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */


require_once 'CRM/Profile/Form.php';

/**
 * This class provides the functionality for batch profile update for contributions
 */
class CRM_Contribute_Form_Task_Batch extends CRM_Contribute_Form_Task {

    /**
     * the title of the group
     *
     * @var string
     */
    protected $_title;

    /**
     * maximum contributions that should be allowed to update
     *
     */
    protected $_maxContributions = 100;

    /**
     * maximum profile fields that will be displayed
     *
     */
    protected $_maxFields = 9;


    /**
     * variable to store redirect path
     *
     */
    protected $_userContext;


    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        /*
         * initialize the task and row fields
         */
        parent::preProcess( );
    }
  
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        $ufGroupId = $this->get('ufGroupId');
        
        if ( ! $ufGroupId ) {
            CRM_Core_Error::fatal( 'ufGroupId is missing' );
        }

        $this->_title = ts('Batch Update for Contributions') . ' - ' . CRM_Core_BAO_UFGroup::getTitle ( $ufGroupId );
        CRM_Utils_System::setTitle( $this->_title );
        
        $this->addDefaultButtons( ts('Save') );
        $this->_fields  = array( );
        $this->_fields  = CRM_Core_BAO_UFGroup::getFields( $ufGroupId, false, CRM_Core_Action::VIEW );
        $this->_fields  = array_slice($this->_fields, 0, $this->_maxFields);
        
        $this->addButtons( array(
                                 array ( 'type'      => 'submit',
                                         'name'      => ts('Update Contribution(s)'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
        
        $this->assign( 'fields', $this->_fields     );
        $this->assign( 'profileTitle', $this->_title );
        $this->assign( 'contributionIds', $this->_contributionIds );
        
        foreach ($this->_contributionIds as $contributionId) {
            foreach ($this->_fields as $name => $field ) {
                CRM_Core_BAO_UFGroup::buildProfile($this, $field, null, $contributionId );
            }
        }
        
        $this->addDefaultButtons( ts( 'Update Contributions' ) );
    }

    /**
     * This function sets the default values for the form.
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        if (empty($this->_fields)) {
            return;
        }
        
        foreach ($this->_contributionIds as $contributionId) {
            $details[$contributionId] = array( );
            //build sortname
            $sortName[$contributionId] = CRM_Contribute_BAO_Contribution::sortName($contributionId);
            CRM_Core_BAO_UFGroup::setProfileDefaults( null, $this->_fields, $defaults, false, $contributionId );
        }
        
        $this->assign('sortName', $sortName);
        return $defaults;
    }


    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params     = $this->exportValues( );
        $dates = array( 'receive_date',
                        'receipt_date',
                        'thankyou_date',
                        'cancel_date'
                        );
        
        foreach ( $params['field'] as $key => $value ) {
            foreach ( $dates as $d ) {
                if ( ! CRM_Utils_System::isNull( $value[$d] ) ) {
                    $value[$d]['H'] = '00';
                    $value[$d]['i'] = '00';
                    $value[$d]['s'] = '00';
                    $value[$d]      =  CRM_Utils_Date::format( $value[$d] );
                }   
            }
            
            $ids['contribution'] = $key;
            if ($value['contribution_type']) {
                $value['contribution_type_id'] = $value['contribution_type'];
            }
            unset($value['contribution_type']);
            CRM_Contribute_BAO_Contribution::add( $value ,$ids );   
        }
        CRM_Core_Session::setStatus("Your updates have been saved.");
    }//end of function
}
?>
