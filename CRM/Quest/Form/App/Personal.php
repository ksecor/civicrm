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
class CRM_Quest_Form_App_Personal extends CRM_Quest_Form_App
{

    static $_contactId;
    static $_studentId;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
        $session =& CRM_Core_Session::singleton( );
        $this->_contactId = $session->get( 'userID' );
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
        
        if ( $this->_contactId ) {
            $dao = & new CRM_Quest_DAO_Student();
            $dao->contact_id = $this->_contactId ;
            if ($dao->find(true)) {
                $this->_studentId = $dao->id;
                CRM_Core_DAO::storeValues( $dao , $defaults);
            }
        }
        if ( $this->_contactId ) {
            $options = array( );
            $contact =& CRM_Contact_BAO_Contact::contactDetails( $this->_contactId , $option );
            $fields = array('first_name'=>'first_name','last_name'=>'last_name','email'=>'location[1][email][1][email]') ;
            foreach( $fields as $key=>$value ) {
                $defaults[ $value] = $contact->$key;
            }
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Individual');

        // first_name
        $this->addElement('text', 'first_name', ts('First Name'), $attributes['first_name'] );
        $this->addRule('first_name',ts('Please Eneter First Name'),'required');

        //middle_name
        $this->addElement('text', 'middle_name', ts('Middle Name'), $attributes['middle_name']);
        
        // last_name
        $this->addElement('text', 'last_name', ts('Last Name'), $attributes['last_name']);
        $this->addRule('last_name',ts('Please Eneter  Last Name'),'required');

        // suffix
        $this->addElement('select', 'suffix_id', ts('Suffix'), array('' => ts('- suffix -')) + CRM_Core_PseudoConstant::individualSuffix());

        // nick_name
        $this->addElement('text', 'nick_name', ts('Nick Name'),
                          CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'nick_name') );
        
        // radio button for gender
        $this->addRadio( 'gender_id', ts('Gender'),
                         CRM_Core_PseudoConstant::gender() );
        $this->addRule('gender_id',ts('Please Select Gender'),'required');
        
        // email
        $this->addElement('text',
                          "location[1][email][1][email]",ts('Email'),
                          CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email',
                                                     'email' ) );
        $this->addRule( "location[1][email][1][email]", ts('Email is not valid.'), 'email' );

        $this->buildAddressBlock( 1, ts( 'Permanent Address' ),
                                  ts( 'Permanent Telephone' ) );
        
        $this->buildAddressBlock( 2, ts( 'Mailing Adddress' ),
                                  ts( 'Mailing Telephone' ),
                                  ts( 'Alternate Telephone' ) );
        
        // citizenship status
        $this->addSelect('citizenship_status', ts( 'Your U.S. citizenship status' ), null , true);
        
        // citizenship country
        $this->addCountry('citizenship_country_id', ts( 'Country of citizenship' ),true );
       
        // ethnicity 
        $this->addSelect( 'ethnicity', ts( 'Race/Ethnicity' ), "_1" );
        $this->addSelect( 'ethnicity', ts( 'Race/Ethnicity' ), "_2" );
       
        $this->addElement('date', 'birth_date', ts('Date of birth'), CRM_Core_SelectValues::date('birth'));
        
        $this->addRule('birth_date', ts("Please enter your Date of birth"),'required');
        $this->addRule('birth_date', ts('Select a valid date.'), 'qfDate');

        $this->addRadio( 'home_area_id',
                         ts('Would you describe your home area as'),
                         CRM_Core_OptionGroup::values('home_area') );

        // grow up area
        $this->addCountry('growup_country_id', ts( 'Where did you grow up' ));

        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student' );

        // years in US
        $this->addElement('text', 'years_in_us', ts( 'Number of years in U.S.' ), $attributes['years_in_us'] );
        $this->addRule('years_in_us', ts("Please enter value for Number of years in U.S."),'required');
        $this->addRule( "years_in_us", ts('Number not valid.'), 'integer' );
        
        $this->addElement('text', 'number_siblings', ts( 'Number of siblings ' ), $attributes['number_siblings'] );
        $this->addRule('number_siblings', ts("Please enter Number of siblings "),'required');
        $this->addRule( "number_siblings", ts('Number not valid.'), 'integer' );

        //Country of Heritage/Nationality
        $this->addCountry( 'nationality_country_id', ts( 'Country of Heritage/Nationality' ),true);

        // first language
        $this->addElement('text', 'first_language', ts( 'First language(s)' ), $attributes['first_language'] );

        // primary language
        $this->addElement('text', 'primary_language', ts( 'Primary language spoken at home' ), $attributes['primary_language'] );

        // year of high school graduation
        $this->addElement('date', 'high_school_grad_year', ts( 'Year of high school graduation'),
                          CRM_Core_SelectValues::date( 'custom', 5, 1, "Y" ) );
        $this->addRule('high_school_grad_year', ts('Select a valid date.'), 'qfDate');

        $this->addRadio( 'home_area_id',
                         ts('Would you describe your home area as'),
                         CRM_Core_OptionGroup::values('home_area') );

        $this->addFormRule(array('CRM_Quest_Form_App_Personal', 'formRule'));

        parent::buildQuickForm( );
    }

    /**
     * Function for validation
     *
     * @param array $params (ref.) an assoc array of name/value pairs
     *
     * @return mixed true or array of errors
     * @access public
     * @static
     */
    public function formRule(&$params) {

        $errors = array( );
        $locNo = 1;
        foreach ($params['location'] as $location) {
            if ( ( $location['address']['country_id'] == 1228 ||
                   $location['address']['country_id'] == 1039 ||
                   $location['address']['country_id'] == 1140 ) &&
                 ! $location['address']['state_province_id'] ) {
                $errors["location[$locNo][address][state_province_id]"]= "Please select the state";
            }
            $locNo++;
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
      $params = $this->controller->exportValues( $this->_name );
      
      require_once 'CRM/Quest/BAO/Student.php';
               
      $params['contact_type'] = 'Individual';
       
    
      $params['location'][1]['location_type_id'] = 1;
      $params['location'][2]['location_type_id'] = 2;
      
      
      $idParams = array( 'id' => $this->_contactId, 'contact_id' => $this->_contactId );
          
      CRM_Contact_BAO_Contact::retrieve( $idParams, $defaults, $ids );
      $contact = CRM_Contact_BAO_Contact::create($params, $ids, 2);
     
      $ids = array();
      if ( $this->_studentId ) {
          $ids['id']  = $this->_studentId;
      }
      $params['contact_id'] = $contact->id;
      $student = CRM_Quest_BAO_Student::create( $params , $ids);
      $this->set('id', $student->id );
      $this->set('contact_id',$student->contact_id );

    }//end of function

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