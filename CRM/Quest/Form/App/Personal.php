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
        $this->addSelect('citizenship_status', ts( 'Your U.S. citizenship status' ) );
        
        // citizenship country
        $this->addCountry('citizenship_country_id', ts( 'Country of citizenship' ) );

        // ethnicity 
        $this->addSelect( 'ethnicity', ts( 'Race/Ethnicity' ), "_1" );
        $this->addSelect( 'ethnicity', ts( 'Race/Ethnicity' ), "_2" );
       
        $this->addElement('date', 'birth_date', ts('Date of birth'), CRM_Core_SelectValues::date('birth'));
        $this->addRule('birth_date', ts('Select a valid date.'), 'qfDate');

        $this->addRadio( 'home_area_id',
                         ts('Would you describe your home area as'),
                         CRM_Core_OptionGroup::values('home_area') );

        // grow up area
        $this->addCountry('growup_country_id', ts( 'Where did you grow up' ));

        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Student' );

        // years in US
        $this->addElement('text', 'years_in_us', ts( 'Number of years in U.S.' ), $attributes['years_in_us'] );

        //Country of Heritage/Nationality
        $this->addCountry( 'nationality_country_id', ts( 'Country of Heritage/Nationality' ));

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

