<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
require_once 'CRM/Quest/BAO/Student.php'; 
require_once 'CRM/Core/OptionGroup.php';


/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_Counselor_Personal extends CRM_Quest_Form_App
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Individual');

        // first_name
        $this->add('text', 'first_name', ts('First Name'), $attributes['first_name'], true );

        //middle_name
        $this->add('text', 'middle_name', ts('Middle Name'), $attributes['middle_name']);
        
        // last_name
        $this->add('text', 'last_name', ts('Last Name'), $attributes['last_name'], true);

        // suffix
        $this->addElement('select', 'suffix_id', ts('Suffix'), array('' => ts('- suffix -')) + CRM_Core_PseudoConstant::individualSuffix());

        // phone
        $this->add('text',
                   "location[1][phone][1][phone]",ts('Phone'),
                   CRM_Core_DAO::getAttribute('CRM_Core_DAO_Phone',
                                              'phone' ),
                   true );

        // email
        $this->add('text',
                   "location[1][email][1][email]",ts('Email'),
                   CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email',
                                              'email' ),
                   true );
        $this->addRule( "location[1][email][1][email]", ts('Email is not valid.'), 'email' );

        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Quest_DAO_StudentRanking' );

        // department
        $this->add('text', 'department', ts( 'Department' ), $attributes['department'], true );

        // relationship to recommender
        $options = array('' => '- select -' ) + CRM_Core_OptionGroup::values( 'recommender_relationship' );
        $extra = array( 'onchange' => "return showHideByValue('recommender_relationship_id','6','rec_rel_other','table-row','select',false);");
        $this->addSelectOther('recommender_relationship',
                              ts( 'Which best describes your relationship to the student' ),
                              $options,
                              null,
                              true,
                              $extra);
        
        parent::buildQuickForm( );

    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess() 
    {
        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            $params = $this->controller->exportValues( $this->_name );

            require_once 'CRM/Quest/BAO/Student.php';
            $params['contact_type'] = 'Individual';
            $params['contact_sub_type'] = 'Student';

            $params['location'][1]['location_type_id'] = 1;
            $params['location'][1]['is_primary'] = 1 ;
            $params['location'][2]['location_type_id'] = 2;
            
            $idParams = array( 'id' => $this->_contactID, 'contact_id' => $this->_contactID );
          
            CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
            $contact = CRM_Contact_BAO_Contact::create($params, $ids, 2);
            
            $dao =& new CRM_Contact_DAO_Contact( );
            $dao->id = $this->_contactID;
            if ( $dao->find( true ) ) {
                $this->set( 'welcome_name',
                            $dao->display_name );
            }

       }

        parent::postProcess( );
     
    } //end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Personal Information');
    }
}

?>
