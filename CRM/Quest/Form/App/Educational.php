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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/


/**
 * Personal Information Form Page
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_App_Educational extends CRM_Quest_Form_App
{
    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
    }
    
    /**
     * This function sets the default values for the form. Relationship that in edit/view action
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        $this->_contactId = $this->get( 'contact_id' );
        if ( $this->_contactId ) {
            $dao = & new CRM_Quest_DAO_Student();
            $dao->contact_id = $this->_contactId ;
            if ($dao->find(true)) {
                $this->_studentId = $dao->id;
                CRM_Core_DAO::storeValues( $dao , $defaults );
            }
        }
        
        $fields = array( 'educational_interest','college_type','college_interest' );
        foreach( $fields as $field ) {
            if ( $defaults[$field] ) {
                $value = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR , $defaults[$field] );
            }
            $defaults[$field] = array();
            if ( is_array( $value ) ) {
                foreach( $value as $v ) {
                    $defaults[$field][$v] = 1;
                }
            }
            $grouping = array();
            // retrieve the grouping property
            $grouping = CRM_Core_OptionGroup::values( $field, true, true );
            $this->assign( $field . '_grouping', $grouping); 
        }

        return $defaults;
    }
    

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student');

        $this->addCheckBox( 'educational_interest',
                            ts( 'Please select all of your educational interests' ),
                            CRM_Core_OptionGroup::values( 'educational_interest', true ),
                            true, '<br/>',true, array ('onclick' => "return showHideByValue('educational_interest[245]', '1', 'educational_interest_other', '', 'radio', false);") );

        $this->addElement('text', 'educational_interest_other', ts( 'Other Educational Interest' ), $attributes['educational_interest_other'] );

        $this->addCheckBox( 'college_type',
                            ts( 'Please select the type(s) of college you are interested in attending' ),
                            CRM_Core_OptionGroup::values( 'college_type', true ),
                            false, null,true );

        $this->addCheckBox( 'college_interest',
                            ts( 'Please do some research on the following colleges. Select the ones that you are interested in attending. Schools in green are our current partner colleges. In parentheses, we indicate the state where the college is located. By checking a box, your information will be forwarded to the partner college and may be forwarded to the non-partner college.' ),
                            CRM_Core_OptionGroup::values( 'college_interest', true ),
                            false, null,true);
        
        $this->addElement( 'textarea',
                           'college_interest_other',
                           ts( 'List any other colleges that you could see yourself attending. (List one per line)' ),
                           $attributes['college_interest_other'] );

        parent::buildQuickForm( );

    }//end of function
    /**
      * process the form after the input has been submitted and validated
      *
      * @access public
      * @return void
      */
    public function postProcess() 
    {
        if ($this->_action !=  CRM_Core_Action::VIEW ) {
            $params = $this->controller->exportValues( $this->_name );
            $values = $this->controller->exportValues( 'Personal' );
            $params = array_merge( $params,$values );
            
            if ( $params['educational_interest'] ) {
                $params['educational_interest'] = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,array_keys($params['educational_interest']));
            }
            if ( $params['college_interest'] ) {
                $params['college_interest']       = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,array_keys($params['college_interest']));
            }
            
            if ( $params['college_type'] ) {
                $params['college_type']       = implode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR,array_keys($params['college_type']));
            }
            
            $id = $this->get('studId');
            $contact_id = $this->get('contact_id');
            //$ids = array('id'=>$id ,'contact_id' => $contact_id);
            $ids = array();
            $ids['id'] = $id;
            $ids['contact_id'] = $contact_id;
            
            
            require_once 'CRM/Quest/BAO/Student.php';
            require_once 'CRM/Utils/Date.php';
            $params['high_school_grad_year'] = CRM_Utils_Date::format($params['high_school_grad_year']) ;
            
            $student = CRM_Quest_BAO_Student::create( $params, $ids);
        }
        parent::postProcess( );
    }
    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Educational Interests');
    }

}

?>
