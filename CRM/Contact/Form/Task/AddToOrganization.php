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

/**
 * This class provides the functionality to add contact(s) to Organization
 */
class CRM_Contact_Form_Task_AddToOrganization extends CRM_Contact_Form_Task {
    /**
     * Build the form
     *
     * @access public
     * @return void
     */

    function preProcess( ) {
        /*
         * initialize the task and row fields
         */
        parent::preProcess( );
      

    }



    function buildQuickForm( ) {

        CRM_Utils_System::setTitle( ts('Add Members To Organization') );
        $this->addElement('text', 'name'      , ts('Find Target Organization') );

        $searchRows    = $this->get( 'searchRows' );
        $searchCount   = $this->get( 'searchCount' );
        if ( $searchRows ) {
            $checkBoxes = array( );
            $chekFlag = 0;
            foreach ( $searchRows as $id => $row ) {
                $checked = '';
                if (!$chekFlag) {
                    $checked = array( 'checked' => null);
                    $chekFlag++;
                }
                
                $checkBoxes[$id] = $this->createElement('radio',null, null,null,$id, $checked );
            }
            
            $this->addGroup($checkBoxes, 'contact_check');
            $this->assign('searchRows', $searchRows );

        }


        $this->assign( 'searchCount', $searchCount );
        $this->assign( 'searchDone'  , $this->get( 'searchDone'   ) );

        $this->addElement( 'submit', $this->getButtonName('refresh'), ts('Search'), array( 'class' => 'form-submit' ) );
        $this->addElement( 'submit', $this->getButtonName('cancel' ), ts('Cancel'), array( 'class' => 'form-submit' ) );


        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Add To Organization'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );

    }



    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {

        // store the submitted values in an array
        
        $params = $this->controller->exportValues( $this->_name );
       
        $this->set( 'searchDone', 0 );
        if ( CRM_Utils_Array::value( '_qf_AddToOrganization_refresh', $_POST ) ) {
            $params['contact_type'] = array('Organization' => 'Organization');
            CRM_Contact_Form_Relationship::search( $params );
            $this->set( 'searchDone', 1 );
            return;
        }
       
        $data = array ();
        $params['relationship_type_id']='4_a_b';
        $invalid = 0;
        $valid = 0;
        $duplicate = 0;
        if ( is_array($this->_contactIds)) {
            foreach ( $this->_contactIds as $value) {
                $ids = array();
                $data['relationship_type_id'] = '4_a_b';
                $ids['contact'] = $value;
                //contact b --> houshold
                // contact a  -> individual
                $errors = CRM_Contact_BAO_Relationship::checkValidRelationship( $params, $ids, $params['contact_check']);
                if($errors)
                    {
                        //$status =$errors;
                        $invalid=$invalid+1;
                        continue;
                    }
                
                if ( CRM_Contact_BAO_Relationship::checkDuplicateRelationship( CRM_Utils_Array::value( 'relationship_type_id',
                                                                                                       $params ),
                                                                               CRM_Utils_Array::value( 'contact', $ids ),
                                                                               $params['contact_check'])) { // step 2
                    $duplicate++;
                    continue;
                }
                CRM_Contact_BAO_Relationship::add($data, $ids, $params['contact_check']);
                $valid++;
                
            }
            
            
            $status = array(
                        ts('Added Contact(s) to Organization'),
                        ts('Total Selected Contact(s): %1', array(1 => $valid+$invalid+$duplicate))
                        );
            if ( $valid ) {
                $status []= ts('New relationship record(s) created: %1.<br>', array(1 => $valid));
            }
            if ( $invalid ) {
                $status[]= ts('Relationship record(s) not created due to invalid target contact type: %1.<br>', array(1 => $invalid));
            }
            if ( $duplicate ) {
                $status[]= ts('Relationship record(s) not created - duplicate of existing relationship: %1.<br>', array(1 => $duplicate));
            }
            
            CRM_Core_Session::setStatus( $status );
            
        }

    }//end of function

}

?>
