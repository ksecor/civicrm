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
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_MatchApp_Guardian extends CRM_Quest_Form_App
{
    protected $_personID;
    protected $_relationshipID;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        $this->_personID        = $this->_options['personID'];
      
        $this->_relationshipID  = $this->_options['relationshipID'];
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
        if ( $this->_personID ) {
            $dao = & new CRM_Quest_DAO_Person();
            $dao->id = $this->_personID ;
            if ($dao->find(true)) {
                CRM_Core_DAO::storeValues( $dao , $defaults );
            }
            
            // format date
            require_once 'CRM/Utils/Date.php';
            $dateFields = array('deceased_year','separated_year','college_grad_year','prof_grad_year','birth_date');
            foreach( $dateFields as $field ) {
                $date = CRM_Utils_Date::unformat( $defaults[$field],'-' );  
                if (! empty( $date) ) {
                    $defaults[$field] = $date;
                } else {
                    $defaults[$field] = '';
                }
            }
        }
        //fix for deceased_year
        $defaults['deceased_year_date']= $defaults['deceased_year'] ;
        if ( !$defaults['lived_with_from_age'] &&  ! $defaults['lived_with_to_age'] ) {
            $defaults['all_life'] = 1;
        } else {
            $defaults['all_life'] = 0;
        }

        $gaurdParams = array('entity_id' => $this->_personID, 'entity_table' => 'quest_person');
        CRM_Core_BAO_Location::getValues( $gaurdParams, $defaults, $ids, 3);
        $this->_locationIds = $ids;
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
        
        $personDAO = & new CRM_Quest_DAO_Person();
        $personDAO->id = $this->_personID ;
        $personDAO->find(true);

        require_once 'CRM/Quest/DAO/Household.php';
        $householdDAO = & new CRM_Quest_DAO_Household();
        $householdDAO->household_type= 'Previous';
        $householdDAO->contact_id = $this->_contactID;
        $householdDAO->find(true);
        
        if (($personDAO->relationship_id == 29 || $personDAO->relationship_id ==28 ) && ( $householdDAO->person_1_id != $this->_personID  && $householdDAO->person_2_id != $this->_personID )) {
            $this->addYesNo( 'is_contact_with_student', ts( 'Do you have contact with this parent?' ), null,false);
        }

        $this->addElement( 'text', "first_name",
                           ts('First name'),
                           $attributes['first_name'] );
        //$this->addRule('first_name',ts('Please enter First Name'),'required');

        $this->addElement( 'text', "last_name",
                           ts('Last name'),
                           $attributes['last_name'] );
        //$this->addRule('last_name',ts('Please enter Last Name'),'required');

        $extra = array( 'onchange' => "return showHideByValue('marital_status_id', '43|44|336', 'separated-year', '', 'select', false);" );
        $this->addSelect('marital_status', ts( 'Marital Status?' ), null, null, $extra );
       
        $this->addElement( 'date', 'separated_year', 
                           ts( 'Year your parents separated or divorced' ),
                           CRM_Core_SelectValues::date( 'custom', 30, 1, "Y" ) );
        
        $this->addYesNo( 'is_deceased', ts( 'Deceased?' ), null,false, array ('onchange' => "return showHideByValue('is_deceased', '1', 'deceased_year_date', 'table-row', 'radio', false);"));

        $this->addElement( 'date', 'deceased_year_date', 
                           ts( 'Year Deceased' ),
                           CRM_Core_SelectValues::date( 'custom', 70, 1, "Y" ) );
        
        $this->addElement('date', 'birth_date',
                          ts(' Birthdate (month/day/year)'),
                          CRM_Core_SelectValues::date('custom', 100, 0, "M\001d\001Y" ),
                          true);
        $this->addRule('birth_date', ts('Select a valid date for Birthdate.'), 'qfDate');
        
        // citizenship status
        $this->addYesNo( 'citizenship_status', ts( 'Is this guardian a U.S. Citizen?' ), null,false);
        $this->addRule('citizenship_status', ts('Please select guardian citizenship.'), 'required');
        // place of birth
        $this->addElement( 'text', "birth_place", ts('Place of birth'), null );
        
        // country of birth
        $this->addCountry('citizenship_country_id', ts( 'Country of birth' ), true );
        
        $extra2 = array ('onclick' => "return showHideByValue('all_life', '1', 'lived_with_from_age|lived_with_to_age', '', 'radio', true);");
        $choice = array( );
        $choice[] = $this->createElement( 'radio', null, '11', ts( 'All my life' ), '1', $extra2 );
        $choice[] = $this->createElement( 'radio', null, '11', ts( 'From' ) , '0', $extra2 );

        $this->addGroup( $choice, 'all_life', null );

        $this->add( 'text', "lived_with_from_age", ts( 'From Age' ),
                           $attributes['lived_with_from_age']);
        $this->addRule('lived_with_from_age',ts('Please enter a valid number for From Age.'),'positiveInteger');

        $this->add( 'text', "lived_with_to_age", ts( 'To Age' ),
                           $attributes['lived_with_to_age']);
        $this->addRule('lived_with_to_age',ts('Please enter a valid number for To Age.'),'positiveInteger');

        $extra1 = array( 'onchange' => "return showHideByValue('industry_id', '47|339|301', 'job_organization|job_occupation|job_current_years', '', 'select', true);" );
        $this->addSelect('industry', ts( 'Industry' ),null, false, $extra1 );


        $this->addElement( 'text', "job_organization",
                           ts( 'Name of business or organization' ),
                           $attributes['job_organization'] );
        $this->addElement( 'text', 'job_occupation',
                           ts('Occupation/Job Title'),
                           $attributes['job_occupation'] );
        $this->addElement( 'text', 'job_current_years',
                           ts('Number of years in current occupation'),
                           $attributes['job_current_years']);
        $this->addRule('job_current_years',ts('not a valid number'),'positiveInteger');

        $extra2 = array( 'onchange' => "showHideByValue('highest_school_level_id', '118|119|120|121|122|302', 'college_name|college_country|college_grad_year|college_major', '', 'select', false); return showHideByValue('highest_school_level_id', '122|302', 'prof_school_name|prof_school_degree|prof_grad_year', '', 'select', false);" );
        $this->addSelect('highest_school_level', ts('Highest level of schooling'),null,false,$extra2);
        $this->addElement( 'text', 'college_name', ts('College Name'),
                           $attributes['college_name'] );
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
        $this->addElement( 'textarea',
                           'description',
                           ts('If there are any extenuating circumstances, or details regarding your parent(s), guardian(s), or household situation that you would like to add or clarify, please do so here'),
                           $attributes['description'] );
        
       
        $this->buildAddressBlock( 1, ts( 'Permanent Address' ),
                                  ts( 'Permanent Telephone' ),
                                  '',
                                  false, false );

        $this->addFormRule(array('CRM_Quest_Form_MatchApp_Guardian', 'formRule'));

        parent::buildQuickForm();
        $params = array("contact_id" => $this->_contactID,"entity_table" => "civicrm_contact");
        
        require_once 'CRM/Contact/BAO/Contact.php';
        $contact = CRM_Contact_BAO_Contact::retrieve($params,$values,$ids);
        $this->assign("studentLoaction",$values['location'][1]);
        
}
    //end of function
    
    /**
     * Function for validation
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

        if ( $params['is_contact_with_student'] || (!array_key_exists('is_contact_with_student', $params)) ) {
            
            $fields = array('first_name'             => 'first name', 
                            'last_name'              => 'last name',
                            'industry_id'            => 'industry',
                            'highest_school_level_id'=> 'highest level of schooling');
            foreach ($fields as $field => $title) {
                if (!$params[$field]) {
                    $errors[$field] = "Please enter the $title";
                }
            }

            if ((!$params['birth_date']['M']) && (!$params['birth_date']['D']) && (!$params['birth_date']['Y']) ) {
                $errors["birth_date"] = "Please enter the Birthdate for this person.";
            }
            if (!array_key_exists('is_deceased', $params)) {
                $errors["is_deceased"] = "Please enter the deceased information";
            }
            if ( $params['is_deceased'] && empty($params['deceased_year_date']['Y'])) {
                $errors["deceased_year_date"] = "Please enter the Year Deceased date.";
            }
            if ( !$params['location']['1']['address']['city'] ) {
                $errors['location[1][address][city]'] = "Please enter the city";
            }
            if ( !$params['location']['1']['address']['country_id'] ) {
                $errors['location[1][address][country_id]'] = "Please enter the country";
            }
            if ( !$params['location']['1']['phone']['1']['phone'] ) {
                $errors['location[1][phone][1][phone]'] = "Please enter the Permanent Telephone";
            }
           
        }

        return empty($errors) ? true : $errors;
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
            $params  = $this->controller->exportValues( $this->_name );
            
            $params['relationship_id'] = $this->_relationshipID;
            
            $relationship = CRM_Core_OptionGroup::values( 'relationship' );
            $relationshipName = trim( CRM_Utils_Array::value( $this->_relationshipID,
                                                              $relationship ) );
            
            $params['contact_id']         = $this->_contactID;
            $params['is_parent_guardian'] = true;
            $params['is_income_source'  ] = true;
            
          
            $ids['id'] = $this->_personID;
            $deceasedYear = $params['deceased_year_date']['Y'];

            // format date
            require_once 'CRM/Utils/Date.php';
            $dateFields = array('deceased_year_date','separated_year','college_grad_year','prof_grad_year','birth_date');
            foreach( $dateFields as $field ) {
                $date = CRM_Utils_Date::format( $params[$field]);  
                if (! empty( $date) ) {
                    $params[$field] = $date;  
                } else {
                    $params[$field] = '';
                }
            }
            
            //fix for deceased_year
            $params['deceased_year'] = $params['deceased_year_date'];
            
            require_once 'CRM/Quest/BAO/Person.php';
            $person = CRM_Quest_BAO_Person::create( $params , $ids );
            unset($params['contact_id']);
            $params['entity_id'] = $person->id;
            $params['entity_table'] = 'quest_person';
            $params['location']['1']['location_type_id'] = 1;
            CRM_Core_BAO_Location::add($params, $this->_locationIds, 1);

            
            // fix the details array
            $details = $this->controller->get( 'householdDetails' );
            $details[$this->_name]['title']   = "{$params['first_name']} {$params['last_name']}";
            $details[$this->_name]['options']['personID'] = $person->id;
            $details[$this->_name]['options']['relationshipID'] = $this->_relationshipID;
            $details[$this->_name]['options']['relationshipName'] = $relationshipName;
            $this->set( 'householdDetails', $details );
            
            //Household Income: if someone is deceased, they should not appear unless they died this year.
            if ( ! $params['is_deceased'] || $deceasedYear == date("Y")  ) {
                // add an income form for this person
                $incomeDetails = $this->controller->get( 'incomeDetails' );
                $incomeID = null;
                if ( CRM_Utils_Array::value( "Income-{$person->id}", $incomeDetails ) ) {
                    $incomeID = $incomeDetails[ "Income-{$person->id}" ]['options']['incomeID'];
                }
                $incomeDetails[ "Income-{$person->id}" ] =
                    array( 'className' => 'CRM_Quest_Form_MatchApp_Income',
                           'title'     => "{$params['first_name']} {$params['last_name']}",
                           'options'   => array( 'personID'   => $person->id,
                                                 'incomeID'   => $incomeID,
                                                 'lastSource' => false ) );
                $keys = array_keys( $incomeDetails );
                $last = array_pop( $keys );
                $incomeDetails[$last]['options']['lastSource'] = true;
                $this->controller->set( 'incomeDetails', $incomeDetails );
            }
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
        return $this->_title ? $this->_title : ts('Parent/Guardian Detail');
    }

    public function getRootTitle( ) {
       return "Parent/Guardian Detail: ";
    }

}

?>
