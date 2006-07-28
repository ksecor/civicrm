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
class CRM_Quest_Form_MatchApp_Personal extends CRM_Quest_Form_App
{
    const MAX_SIBLINGS = 10, MAX_NATIONALITY_COUNTRY = 3;

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
        $studentDefaults = array();
        $contactDefaults = array();
        $this->_countryPresent = array();
        $params = array( 'contact_id' => $this->_contactID,
                         'id'         => $this->_contactID );
        $defaults = array( );

        $options = array( );
        $ids = array();
        $contact =& CRM_Contact_BAO_Contact::retrieve( $params, $defaults, $ids );
        
        $ids    = array( );
        CRM_Quest_BAO_Student::retrieve( $params, $defaults, $ids );

        require_once 'CRM/Utils/Date.php';
        $defaults['high_school_grad_year'] = CRM_Utils_Date::unformat($defaults['high_school_grad_year'],'-') ;

        if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
            $showHide =& new CRM_Core_ShowHideBlocks( );
            for ( $i = 2; $i <= self::MAX_NATIONALITY_COUNTRY; $i++ ) {
                if ( CRM_Utils_Array::value( "nationality_country_id_$i", $defaults )) {
                    $showHide->addShow( "id_nationalityCountry_$i" );
                } else {
                    $showHide->addHide( "id_nationalityCountry_$i" );
                }
                $showHide->addToTemplate( );
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
        $this->add('text', 'first_name', ts('First Name'), $attributes['first_name'], true );

        //middle_name
        $this->add('text', 'middle_name', ts('Middle Name'), $attributes['middle_name']);
        
        // last_name
        $this->add('text', 'last_name', ts('Last Name'), $attributes['last_name'], true);

        // suffix
        $this->addElement('select', 'suffix_id', ts('Suffix'), array('' => ts('- suffix -')) + CRM_Core_PseudoConstant::individualSuffix());

        // nick_name
        $this->addElement('text', 'nick_name', ts('Preferred name'),
                          CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'nick_name') );
        
        // radio button for gender
        $this->addRadio( 'gender_id', ts('Gender'),
                         CRM_Core_PseudoConstant::gender() );
        $this->addRule('gender_id',ts('Please select Gender'),'required');
        
        // email
        $this->addElement('text',
                          "location[1][email][1][email]",ts('Email'),
                          CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email',
                                                     'email' ) );
        $this->addRule( "location[1][email][1][email]", ts('Email is not valid.'), 'email' );

        // buildAddressBlock() is Quest/App.php
        $this->buildAddressBlock( 1, ts( 'Permanent Address' ),
                                  ts( 'Permanent Telephone' ),
                                  '',
                                  true, true );
        
        $this->buildAddressBlock( 2, ts( 'Mailing Address' ),
                                  ts( 'Mailing Telephone' ),
                                  ts( 'Alternate Telephone' ),
                                  true, false, false );
        
        // citizenship status
        $extra1 = array( 'onchange' => "return showHideByValue('citizenship_status_id','234|235|236','citizenship_country_id','','select',false);");
        $this->addSelect('citizenship_status', ts( 'U.S. Citizenship Status' ), null , true, $extra1);
        
        // citizenship country
        $this->addCountry('citizenship_country_id', ts( 'Country of Citizenship' ), false );
       
        // ethnicity 
        $extra2 = array( 'onchange' => "showHideByValue('ethnicity_id_1','18','ethnicity_other','','select',false); return showHideByValue('ethnicity_id_1','1','tribe_affiliation|tribe_date', '','select',false );");
        $this->addSelect( 'ethnicity', ts( 'Race/Ethnicity' ), "_1" , true, $extra2);

        require_once 'CRM/Core/ShowHideBlocks.php';

        CRM_Core_ShowHideBlocks::links( $this,"ethnicity_id_2", ts('add another Race/Ethnicity'), ts('hide this Race/Ethnicity field'));
       
        $this->add('date', 'birth_date',
                   ts(' Birthdate (month/day/year)'),
                   CRM_Core_SelectValues::date('custom', 20, 0, "M\001d\001Y" ),
                   true);        
        $this->addRule('birth_date', ts('Select a valid date for Birthdate.'), 'qfDate');

        $this->addRadio( 'home_area_id',
                         ts('Would you describe your home area as'),
                         CRM_Core_OptionGroup::values('home_area') );

        // grow up area
        $this->addCountry('growup_country_id', ts( 'Where did you grow up (if different from address)' ));

        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student' );

        // years in US
        $this->addElement('text', 'years_in_us', ts( 'Number of years in U.S.' ), $attributes['years_in_us'] );
        $this->addRule('years_in_us', ts("Please enter value for Number of Years in U.S."),'required');
        $this->addRule( "years_in_us", ts('Number not valid.'), 'integer' );
        
        $siblings = array();
        for ( $i = 0; $i <= self::MAX_SIBLINGS; $i++ ) {
            $siblings[] = $i;
        }
        $this->addElement('select', 'number_siblings', ts( 'Number of siblings ' ), array(''=>ts( '-select-' )) + $siblings );
        $this->addRule('number_siblings', ts("Please enter Number of Siblings "),'required');
        $this->addRule( "number_siblings", ts('Number of Siblings not valid.'), 'positiveInteger' );

        //Country of Heritage/Nationality
        $nationalityCountry = array();
        for ( $i = 1; $i <= self::MAX_NATIONALITY_COUNTRY; $i++ ) {
            $this->addCountry( "nationality_country_id_$i" , ts( 'Country(ries) of family\'s origin' ), false);
            if ( ! ( $this->_action & CRM_Core_Action::VIEW ) ) {
                $nationalityCountry[$i] = CRM_Core_ShowHideBlocks::links( $this,"nationalityCountry_$i",
                                                                          ts('add another country'),
                                                                          ts('hide this country'),
                                                                          false );
            }
        }
        $this->assign( 'nationalityCountry', $nationalityCountry );

        $maxNationalityCountry = self::MAX_NATIONALITY_COUNTRY;
        $this->assign( 'maxNationalityCountry', $maxNationalityCountry + 1 );

        // first language
        $this->addElement('text', 'first_language', ts( 'First language(s)' ), $attributes['first_language'] );

        // primary language
        $this->addElement('text', 'primary_language', ts( 'Primary language spoken at home' ), $attributes['primary_language'] );

        // year of high school graduation
        $this->addElement('date', 'high_school_grad_year', ts( 'Year of high school graduation'),
                          CRM_Core_SelectValues::date( 'custom', 0, 2, "Y" ) );
        $this->addRule('high_school_grad_year', ts('Select a valid graduation year.'), 'qfDate');
        $this->addRule('high_school_grad_year', ts('Select a valid graduation year.'), 'required');

        
        //file upload
        $this->addElement('file', 'upload_pics', ts( 'Upload your picture' ), $attributes['upload_pics'] );

        // tribe affiliation
        $this->addElement('text', 'tribe_affiliation', ts( 'Tribe affiliation' ), $attributes['tribe_affiliation'] );

        //Date enrolled in tribe
        $this->add('date', 'tribe_enroll_date', ts('Date enrolled in tribe'), 
                   CRM_Core_SelectValues::date('custom', 20, 0, 'M Y'), false);

        // race other option
        $this->addElement('text', 'ethnicity_other', ts( 'Please specify' ), $attributes['ethnicity_other'] );

        $this->addRadio( 'home_area_id',
                         ts('Would you describe your home area as'),
                         CRM_Core_OptionGroup::values('home_area') );

        $this->addFormRule(array('CRM_Quest_Form_MatchApp_Personal', 'formRule'));
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

        if ($params['citizenship_status_id'] == '234') {
            if (!$params['citizenship_country_id']) {
                $errors['citizenship_country_id'] = "Please enter the citizenship country.";
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
            
            $ids = array();
            if ( $this->_studentID ) {
                $ids['id']  = $this->_studentID;
            }
            $params['contact_id'] = $this->_contactID;
            
            require_once 'CRM/Utils/Date.php';
            $params['high_school_grad_year'] = CRM_Utils_Date::format($params['high_school_grad_year']) ;
            
            $student =& CRM_Quest_BAO_Student::create( $params , $ids);
            
            $this->set( 'studentID', $student->id );
            //$this->set( 'welcome_name', $params['first_name'] ); 
            
            $dao =& new CRM_Contact_DAO_Contact( );
            $dao->id = $this->_contactID;
            if ( $dao->find( true ) ) {
                $this->set( 'welcome_name',
                            $dao->display_name );
            }

            // also trigger the sibling generation in case number_siblings has changes
            require_once 'CRM/Quest/Form/MatchApp/Sibling.php';
            CRM_Quest_Form_MatchApp_Sibling::getPages( $this->controller, true );
       }

        parent::postProcess( );
     
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
