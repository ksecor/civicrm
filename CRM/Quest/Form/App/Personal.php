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

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_App_Personal extends CRM_Quest_Form_App
{
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
        $this->addElement('text', 'first_name', ts('First Name'), $attributes['first_name'] );
        
        //middle_name
        $this->addElement('text', 'middle_name', ts('Middle Name'), $attributes['middle_name'] );
        
        // last_name
        $this->addElement('text', 'last_name', ts('Last Name'), $attributes['last_name'] );
        
        // suffix
        $this->addElement('select', 'suffix_id', ts('Suffix'), array('' => ts('- suffix -')) + CRM_Core_PseudoConstant::individualSuffix());

        // nick_name
        $this->addElement('text', 'nick_name', ts('Nick Name'),
                          CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'nick_name') );
        
        // radio button for gender
        $this->addRadio( 'gender_id', ts('Gender'),
                         CRM_Core_PseudoConstant::gender() );

        // email
        $this->addElement('text',
                          "location[1][email][1][email]",
                          CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email',
                                                     'email' ) );
        $this->addRule( "location[1][email][1][email]", ts('Email is not valid.'), 'email' );

        $this->buildAddressBlock( 1, ts( 'Permanent Address' ),
                                  ts( 'Permanent Telephone' ) );

        $this->buildAddressBlock( 2, ts( 'Mailing Adddress' ),
                                  ts( 'Mailing Telephone' ),
                                  ts( 'Alternate Telephone' ) );


        // citizenship status
        $this->addElement('select', 'citizenship_status_id', ts( 'Your U.S. citizenship status' ),
                          array('' => ts('- Select -')) + CRM_Core_OptionGroup::values('citizenship_status') );
        
        // citizenship country
        $this->addElement('select', 'citizenship_country_id', ts( 'Country of citizenship' ),
                         array('' => ts('- Select -')) + CRM_Core_PseudoConstant::country());

        // ethnicity
        $this->addElement('select', 'ethnicity_1_id', ts( 'Race/Ethnicity' ),
                         array('' => ts('- Select -')) + CRM_Core_OptionGroup::values('ethnicity'));
        // ethnicity
        $this->addElement('select', 'ethnicity_2_id', ts( 'Race/Ethnicity' ),
                          array('' => ts('- Select -')) + CRM_Core_OptionGroup::values('ethnicity'));
        
        $this->addElement('date', 'birth_date', ts('Date of birth'), CRM_Core_SelectValues::date('birth'));
        $this->addRule('birth_date', ts('Select a valid date.'), 'qfDate');

        $this->addRadio( 'home_area_id',
                         ts('Would you describe your home area as'),
                         CRM_Core_OptionGroup::values('home_area') );

        // grow up area
        $this->addElement('select', 'growup_country_id', ts( 'Where did you grow up' ),
                         array('' => ts('- Select -')) + CRM_Core_PseudoConstant::country());

        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student' );

        // years in US
        $this->addElement('text', 'years_in_us', ts( 'Number of years in U.S.' ), $attributes['years_in_us'] );

        //Country of Heritage/Nationality
        $this->addElement('select', 'nationality_country_id', ts( 'Country of Heritage/Nationality' ),
                         array('' => ts('- Select -')) + CRM_Core_PseudoConstant::country());

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

        parent::buildQuickForm( );
    }

    function buildAddressBlock( $locationId, $title, $phone, $alternatePhone  = null ) {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address');

        $location[$locationId]['address']['street_address']         =
            $this->addElement('text', "location[$locationId][address][street_address]", $title,
                              $attributes['street_address']);
        $location[$locationId]['address']['supplemental_address_1'] =
            $this->addElement('text', "location[$locationId][address][supplemental_address_1]", ts('Addt\'l Address 1'),
                              $attributes['supplemental_address_1']);
        $location[$locationId]['address']['supplemental_address_2'] =
            $this->addElement('text', "location[$locationId][address][supplemental_address_2]", ts('Addt\'l Address 2'),
                              $attributes['supplemental_address_2']);

        $location[$locationId]['address']['city']                   =
            $this->addElement('text', "location[$locationId][address][city]", ts('City'),
                              $attributes['city']);
        $location[$locationId]['address']['postal_code']            =
            $this->addElement('text', "location[$locationId][address][postal_code]", ts('Zip / Postal Code'),
                              $attributes['postal_code']);
        $location[$locationId]['address']['postal_code_suffix']            =
            $this->addElement('text', "location[$locationId][address][postal_code_suffix]", ts('Add-on Code'),
                              array( 'size' => 4, 'maxlength' => 12 ));
         $location[$locationId]['address']['state_province_id']      =
             $this->addElement('select', "location[$locationId][address][state_province_id]", ts('State / Province'),
                               array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince());
         $location[$locationId]['address']['country_id']             =
             $this->addElement('select', "location[$locationId][address][country_id]", ts('Country'),
                               array('' => ts('- select -')) + CRM_Core_PseudoConstant::country());

         $location[$locationId]['phone'][1]['phone_type'] = $this->addElement('select',
                                                                              "location[$locationId][phone][1][phone_type]",
                                                                              null,
                                                                              CRM_Core_SelectValues::phoneType());
         
         $location[$locationId]['phone'][1]['phone']      = $this->addElement('text',
                                                                              "location[$locationId][phone][1][phone]", 
                                                                              $phone,
                                                                              CRM_Core_DAO::getAttribute('CRM_Core_DAO_Phone',
                                                                                                         'phone'));

         if ( $alternatePhone ) {
             $location[$locationId]['phone'][2]['phone_type'] = $this->addElement('select',
                                                                                  "location[$locationId][phone][2][phone_type]",
                                                                                  null,
                                                                                  CRM_Core_SelectValues::phoneType());
             
             $location[$locationId]['phone'][2]['phone']      = $this->addElement('text',
                                                                                  "location[$locationId][phone][2][phone]", 
                                                                                  $phoneTitle,
                                                                                  CRM_Core_DAO::getAttribute('CRM_Core_DAO_Phone',
                                                                                                             'phone'));
         }
    }
       
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess() 
    {
    }//end of function

}

?>

