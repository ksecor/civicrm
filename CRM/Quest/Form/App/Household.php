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
class CRM_Quest_Form_App_Household extends CRM_Quest_Form_App
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
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Household');

        for ( $i = 1; $i <= 2; $i++ ) {
            if ( $i == 1 ) {
                $title = ts( 'How many people live with you in your current household?' );
            } else {
                $title = ts( 'How many people lived with you in your previous household?' );
            }
            $this->addElement( 'text',
                               'member_count_' . $i,
                               $title,
                               $attributes['member_count'] );
            if ( $i == 1 ) {
                $this->addRule('member_count_'.$i,ts('Please Enter the number of people live with u'),'required');
            }

            for ( $j = 1; $j <= 2; $j++ ) {
                $this->addSelect( "relationship",
                                   ts( 'Relationship' ),
                                  "_".$i."_".$j );
                $this->addElement( 'text', "first_name_".$i."_".$j,
                                   ts('First Name'),
                                   $attributes['first_name'] );
                
                $this->addElement( 'text', "last_name_".$i."_".$j,
                                   ts('Last Name'),
                                   $attributes['last_name'] );
                
                if ( $i == 2 ) {
                    $this->addElement( 'checkbox', "same_".$i."_".$j, null, null );
                }
            }

            $this->addSelect( "years_lived",
                              ts( 'How long have you lived in this household?' ),
                              "_".$i );
        }

        $this->addElement('textarea',
                          'household_note',
                          ts( 'List and describe the factors in your life that have most shaped you (1500 characters max).' ),
                          CRM_Core_DAO::getAttribute( 'CRM_Quest_DAO_Student', 'household_note' ) );

        $this->addFormRule(array('CRM_Quest_Form_App_Household', 'formRule'));

        parent::buildQuickForm( );

    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Household Information');
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
        
        /*for ( $i = 1; $i <= 2; $i++ ) {
            for ( $j = 1; $j <= 2; $j++ ) {
                if ($params["relationship_id_".$i."_".$j]) {
                    $errors["first_name_".$i."_".$j] = "Please enter first name";
                    $errors["last_name_".$i."_".$j] = "Please enter last name";
                }
            }
        }*/
        
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
        // get all the relevant details so we can decide the detailed information we need
        $params  = $this->controller->exportValues( 'Household' );
        $relationship = CRM_Core_OptionGroup::values( 'relationship' );

        $details = array( );
        for ( $i = 1; $i <= 2; $i++ ) {
            for ( $j = 1; $j <= 2; $j++ ) {
                $this->getRelationshipDetail( $details, $relationship, $params, $i, $j );
            }
        }

        // make sure we have a mother and father in there
        if ( ! CRM_Utils_Array::value( 'Mother', $details ) ) {
            $details['Mother'] = 'Mother Details';
        }

        if ( ! CRM_Utils_Array::value( 'Father', $details ) ) {
            $details['Father'] = 'Father Details';
        }

        $householdType = array();
        for ( $i = 1; $i <= 2; $i++ ) {
            for ( $j = 1; $j <= 2; $j++ ) {
                if ( $i == 1 ) {
                    $name = trim( 
                     CRM_Utils_Array::value( "first_name_{$i}_{$j}", $params ) . ' ' .
                     CRM_Utils_Array::value( "last_name_{$i}_{$j}" , $params )
                     );

                    if ( $name ) {
                        $rid = CRM_Utils_Array::value( "relationship_id_{$i}_{$j}", $params );
                        $householdType[$rid] = 'current';
                    }
                  
                } else {
                    $name = trim( 
                     CRM_Utils_Array::value( "first_name_{$i}_{$j}", $params ) . ' ' .
                     CRM_Utils_Array::value( "last_name_{$i}_{$j}" , $params )
                     );
                    
                    if ( $name ) {
                        $rid = CRM_Utils_Array::value( "relationship_id_{$i}_{$j}", $params);
                        $householdType[$rid] = 'previous';
                    }
                }
            }
        }
       
        $this->set( 'householdType', $householdType );
        $this->set( 'householdDetails', $details );
    }//end of function 

    public function getRelationshipDetail( &$details, &$relationship, &$params, $i, $j ) {
        $name = trim( 
                     CRM_Utils_Array::value( "first_name_{$i}_{$j}", $params ) . ' ' .
                     CRM_Utils_Array::value( "last_name_{$i}_{$j}" , $params )
                     );
        if ( ! $name ) {
            return;
        }

        $relationshipName = trim( CRM_Utils_Array::value( CRM_Utils_Array::value( "relationship_id_{$i}_{$j}", $params ),
                                                          $relationship ) );

        if ( ! $relationshipName ) {
            return;
        }

        if ( CRM_Utils_Array::value( "same_{$i}_{$j}", $params ) ) {
            return;
        }

        $details[$relationshipName] = "$name Details";
    }
}

?>