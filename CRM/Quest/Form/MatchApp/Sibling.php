<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
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
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_MatchApp_Sibling extends CRM_Quest_Form_App
{
    protected $_siblingID;

    protected $_deleteButtonName = null;

     /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        $this->_siblingID  = CRM_Utils_Array::value( 'siblingID', $this->_options );
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
        if ( $this->_siblingID ) {
            require_once 'CRM/Quest/DAO/Person.php';
            $dao = & new CRM_Quest_DAO_Person();
            $dao->id = $this->_siblingID;
            if ($dao->find(true)) {
                CRM_Core_DAO::storeValues( $dao , $defaults );
            }
            $defaults['sibling_relationship_id'] = $defaults['relationship_id'];
        }
        
        if ( !$defaults['lived_with_from_age'] &&  ! $defaults['lived_with_to_age'] ) {
            $defaults['all_life'] = 1;
        } else {
            $defaults['all_life'] = 0;
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Person');

        $this->addElement( 'text', "first_name",
                           ts('First Name'),
                           $attributes['first_name'] );
        $this->addRule("first_name",ts('Please Enter First Name'),'required');

        $this->addElement( 'text', "last_name",
                           ts('Last Name'),
                           $attributes['last_name'],'required' );
        $this->addRule("last_name",ts('Please Enter Last Name'),'required');
        
        $this->addSelect('sibling_relationship', ts( 'Relationship to you' ),null ,true );
       
        $this->addElement('date', 'birth_date',
                          ts('Birthdate (month/day/year)'),
                          CRM_Core_SelectValues::date('custom', 60, 0, "M\001d\001Y" ),
                          true);
        $this->addRule('birth_date', ts('Select a valid date for Birthdate.'), 'qfDate');
        $this->addRule("birth_date",ts('Please Enter a Valid date for Birthdate'),'required');
                
        $extra2 = array ('onchange' => "return showHideByValue('all_life', '1', 'lived_with_from_age|lived_with_to_age', 'table-row', 'radio', true);");
        $choice = array( );
        $choice[] = $this->createElement( 'radio', null, '11', ts( 'All my life' ), '1', $extra2 );
        $choice[] = $this->createElement( 'radio', null, '11', ts( 'From' ) , '0', $extra2 );

        $this->addGroup( $choice, 'all_life', null );

        $this->addElement( 'text', "lived_with_from_age", ts( 'From Age' ),
                           $attributed['lived_with_from_age'] );
        $this->addRule('lived_with_from_age',ts('Please enter a valid number for From Age.'),'positiveInteger');

        $this->addElement( 'text', "lived_with_to_age", ts( 'To Age' ),
                           $attributed['lived_with_to_age'] );
        $this->addRule('lived_with_to_age',ts('Please enter a valid number for To Age.'),'positiveInteger');

        $extra1 = array( 'onchange' => "return showHideByValue('current_school_level_id', '141', 'highest_school_level|college_country|college_grad_year|college_major|prof_school_name|prof_school_degree|prof_grad_year', 'table-row', 'select', true);" );

        $this->addSelect('current_school_level', ts('Current year in school'), null, true, $extra1);


        $extra2 = array( 'onchange' => "showHideByValue('highest_school_level_id', '118|119|120|121|122', 'college_country|college_grad_year|college_major', 'table-row', 'select', false); return showHideByValue('highest_school_level_id', '122', 'prof_school_name|prof_school_degree|prof_grad_year', 'table-row', 'select', false);" );
        $this->addSelect('highest_school_level', ts('Highest level of schooling'),null,false,$extra2);
        $this->addCountry( 'college_country_id', ts('Which country is the college located in?'));
        $this->addElement( 'date',
                           'college_grad_year',
                           ts('Year of college completion'),
                           CRM_Core_SelectValues::date( 'custom', 50, 1, "Y" ) ); 

        $this->addElement( 'text',
                           'college_major',
                           ts('Area of concentration'),
                           $attributes['college_major'] );
        $this->addElement( 'text',
                           'prof_school_name',
                           ts('Name of professional or graduate school'),
                           $attributes['prof_school_name'] );
        $this->addElement( 'date',
                           'prof_grad_year',
                           ts('Year in which graduate degree was received'),
                           CRM_Core_SelectValues::date( 'custom', 50, 1, "Y" ) );
        $this->addSelect( 'prof_school_degree', ts('Degree received in professional or graduate school ') );           

        $this->addElement( 'text', 'college_name', ts('College attending or attended (if any)'),
                           $attributes['college_name'] );
        $this->addElement( 'text', 'job_occupation',
                           ts('Occupation/Job Title'),
                           $attributes['job_occupation'] );
        $this->addElement( 'textarea',
                           'description',
                           ts('If important information regarding your relationship with this sibling is not captured above, please enter it here.'),
                           $attributes['description'] );

        $this->_deleteButtonName = $this->getButtonName( 'next'   , 'delete' );
        $this->assign( 'deleteButtonName', $this->_deleteButtonName );
        $this->add( 'submit', $this->_deleteButtonName, ts( 'Delete this Sibling' ) );
       
        $this->addFormRule(array('CRM_Quest_Form_MatchApp_Sibling', 'formRule'));
 
        parent::buildQuickForm();
    }
    
    function validate( ) 
    {
        // check if the delete button has been submitted 
        // if so skip all validation
        $buttonName = $this->controller->getButtonName( ); 
        if ( $buttonName == $this->_deleteButtonName ) { 
            return true;
        } 

        return parent::validate( );
    }


    /**
     * Function for form rules
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule(&$params)
    {
        $errors = array( );
        
        if ( $params['all_life'] == 0 ) {
            if ( !$params['lived_with_from_age'] ) {
                $errors['lived_with_from_age'] = "Please enter the From Age.";
            }

            if ( !$params['lived_with_to_age'] ) {
                $errors['lived_with_to_age'] = "Please enter the To Age.";
            }
        }
        
        return empty($errors) ? true : $errors;
    } 


    public function postProcess()  
    {
        if ( ! ( $this->_action &  CRM_Core_Action::VIEW ) ) {
            // check if the delete button has been submitted
            $buttonName = $this->controller->getButtonName( );
            if ( $buttonName == $this->_deleteButtonName ) {
                // delete the sibling record
                if ( $this->_siblingID ) {
                    require_once 'CRM/Quest/DAO/Person.php';
                    $dao = & new CRM_Quest_DAO_Person();
                    $dao->id = $this->_siblingID;
                    $dao->delete( );
                }

                // also decrement total sibling count by 1
                $number_siblings = CRM_Core_DAO::getFieldValue( 'CRM_Quest_DAO_Student',
                                                                $this->_studentID,
                                                                'number_siblings' );
                if ( $number_siblings > 0 ) {
                    $dao = & new CRM_Quest_DAO_Student( );
                    $dao->id = $this->_studentID;
                    $dao->number_siblings = $number_siblings - 1;
                    $dao->save( );

                    // we also need to reset the Personal form so we retrieve the right stuff from the db
                    $this->controller->resetPage( 'Personal', true );
                } else {
                    CRM_Core_Error::fatal( "The student table in the database is inconsistent." );
                }

                // also adjust the details array
                CRM_Quest_Form_MatchApp_Sibling::getPages( $this->controller, true );

                return;
            }
            
            $params  = $this->controller->exportValues( $this->_name );
            
            $params['relationship_id'] = $params['sibling_relationship_id'];
            $params['contact_id']      = $this->_contactID;
            $params['is_sibling']      = true;
            $params['birth_date']      = CRM_Utils_Date::format( $params['birth_date'] );
            
            require_once 'CRM/Quest/BAO/Person.php';
            
            $ids = array();
            $ids['id'] = $this->_siblingID;

            require_once 'CRM/Utils/Date.php';
            $params['college_grad_year'] = CRM_Utils_Date::format($params['college_grad_year']) ;
            $params['prof_grad_year']    = CRM_Utils_Date::format($params['prof_grad_year']) ;

            
            $sibling = CRM_Quest_BAO_Person::create( $params , $ids);
            
            // fix the details array
            $details = $this->controller->get( 'siblingDetails' );
            $details[$this->_name]['title']   = "{$params['first_name']} {$params['last_name']}";
            $details[$this->_name]['options']['siblingID'] = $sibling->id;
            $this->controller->set( 'siblingDetails', $details );
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
        return $this->_title ? $this->_title : ts('Sibling Information'); 
    }

    public function getRootTitle( ) {
       return "Sibling Information: ";
    }

    static function &getPages( &$controller, $reset = false )
    {
        $details = $controller->get( 'siblingDetails' );
        if ( ! $details || $reset ) {
            $cid = $controller->get( 'contactID' );
            // now adjust the ones that have a record in them
            $i = 1;
            require_once 'CRM/Quest/DAO/Student.php';
            $dao =& new CRM_Quest_DAO_Student( );
            $dao->contact_id = $cid;
            if ( $dao->find( true ) ) {
                $totalSiblings = $dao->number_siblings;
            } else {
                // its still too early, so return an empty array
                return array( );
            }

            require_once 'CRM/Quest/DAO/Person.php';
            $dao = & new CRM_Quest_DAO_Person();
            $dao->contact_id = $cid;
            $dao->is_sibling = true;
            $dao->find();

            $details = array( );
            while ( $dao->fetch( ) ) {
                if ( $i > $totalSiblings ) {
                    // delete this object
                    $dao->delete( );
                } else {
                    $details["Sibling-{$i}"] = array( 'className' => 'CRM_Quest_Form_MatchApp_Sibling',
                                                      'title' => trim( "{$dao->first_name} {$dao->last_name}" ),
                                                      'options' => array( 'index' => $i,
                                                                          'siblingID' => $dao->id ) );
                }
                $i++;
            }

            if ( is_numeric( $totalSiblings ) && $totalSiblings >= $i ) {
                for ( ; $i <= $totalSiblings; $i++ ) {
                    $details["Sibling-{$i}"] = array( 'className' => 'CRM_Quest_Form_MatchApp_Sibling', 
                                                      'title'   => "Sibling $i",
                                                      'options' => array( 'index' => $i ) );
                }
            }

            $controller->set( 'siblingDetails', $details );
        }

        if ( ! $details ) {
            $details = array( );
        }

        return $details;
    } 
    
}

?>
