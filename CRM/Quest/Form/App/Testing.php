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
class CRM_Quest_Form_App_Testing extends CRM_Quest_Form_App
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Test' );

        $params = array( 'English'          => 1,
                         'Reading'          => 1,
                         'CriticalReading'  => 6,
                         'Writing'          => 7,
                         'Math'             => 7,
                         'Science'          => 1,
                         'Composite'        => 1,
                         'Total'            => 6 );

        $tests = array( 'act'  => 1,
                        'psat' => 2,
                        'sat'  => 4 );

        foreach ( $tests as $testName => $testValue ) {
            foreach ( $params as $name => $value ) {
                if ( $value & $testValue ) {
                    $this->addElement( 'text',
                                       $testName . '_' . strtolower( $name ),
                                       ts( $name . ' Score' ),
                                       $attributes['score_english'] );
                }
            }

            // add the date field
            $this->addElement('date', $testName . '_date',
                              ts( 'Date Taken (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 5, 1, "M\001Y" ) );
        }

        // add 5 Sat II tests
        for ( $i = 1; $i <= 5; $i++ ) {
            $this->addElement( 'text',
                               'satII_' . $i . '_subject',
                               ts( 'Subject' ),
                               $attributes['subject'] );
            $this->addElement( 'text',
                               'satII_' . $i . '_score',
                               ts( 'Score' ),
                               $attributes['score_english'] );
            $this->addElement('date', 'satII_' . $i . '_date',
                              ts( 'Date Taken (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 5, 1, "M\001Y" ) );
        }

        // add 32 AP test
        for ( $i = 1; $i <= 32; $i++ ) {
            $this->addElement( 'text',
                               'ap_' . $i . '_subject',
                               ts( 'Subject' ),
                               $attributes['subject'] );
            $this->addElement( 'text',
                               'ap_' . $i . '_score',
                               ts( 'Score' ),
                               $attributes['score_english'] );
            $this->addElement('date', 'ap_' . $i . '_date',
                              ts( 'Date Taken (month/year)' ),
                              CRM_Core_SelectValues::date( 'custom', 5, 1, "M\001Y" ) );
        }

        $this->addYesNo( 'is_test_tutoring',
                         ts( 'Have you received tutoring for any of the standardized tests above?' ) );
        
        $this->addCheckBox( 'test_tutoring',
                            ts( 'If yes, for which tests?' ),
                            CRM_Core_OptionGroup::values( 'test_tutoring' ),
                            false ,null);
     
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

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Testing Information');
    }
}

?>