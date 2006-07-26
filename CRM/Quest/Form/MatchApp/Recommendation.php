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
class CRM_Quest_Form_MatchApp_Recommendation extends CRM_Quest_Form_App
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

        $query = "
SELECT cr.id           as contact_id,
       i.first_name    as first_name,
       i.last_name     as last_name ,
       e.email         as email     ,
       rc.contact_id_b as organization_id
  FROM civicrm_contact      cs,
       civicrm_contact      cr,
       civicrm_individual   i,
       civicrm_email        e,
       civicrm_location     l,
       civicrm_relationship rs,
       civicrm_relationship rc
 WHERE rs.relationship_type_id IN ( 9, 10 )
   AND rc.relationship_type_id IN ( 11, 12 )
   AND rs.contact_id_a = cs.id
   AND rs.contact_id_b = cr.id
   AND rs.is_active    = 1
   AND rc.is_active    = 1
   AND rc.contact_id_a = cr.id
   AND cs.id           = {$this->_contactID}
   AND i.contact_id    = cr.id
   AND l.entity_table  = 'civicrm_contact'
   AND l.entity_id     = cr.id
   AND e.location_id   = l.id
";

        $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );

        $this->_oldParams = array( );
        $count = 1;
        while ( $dao->fetch( ) ) {
            $this->_oldParams[$count] = array( );
            $this->_oldParams[$count]['contact_id'     ] = $dao->contact_id;
            $this->_oldParams[$count]['first_name'     ] = $dao->first_name;
            $this->_oldParams[$count]['last_name'      ] = $dao->last_name ;
            $this->_oldParams[$count]['email'          ] = $dao->email;
            $this->_oldParams[$count]['organization_id'] = $dao->organization_id;
            $count++;
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

        for ( $i = 1; $i <= 3; $i++ ) {

            $this->add( 'text',
                        "first_name_$i",
                        ts( 'First Name' ),
                        $attributes['first_name'],
                        true );
            $this->add( 'text',
                        "last_name_$i",
                        ts( 'Last Name' ),
                        $attributes['last_name'],
                        true );
            $this->add( 'text',
                        "email_$i",
                        ts( 'Email' ),
                        $attributes['first_name'],
                        true );
            $this->addRule( "email_$i",
                            ts('Email is not valid.'), 'email' );

            $this->add( 'select',
                        "school_id_$i",
                        ts( 'School' ),
                        array('' => ts('- select -')) + CRM_Quest_BAO_Student::getSchoolSelect( $this->_contactID ),
                        true );

        }

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
         return ts('Recommendations');
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

        // make sure all 3 emails are distinct
        if ( $params['email_1'] == $params['email_2'] ||
             $params['email_1'] == $params['email_3'] ||
             $params['email_2'] == $params['email_3'] ) {
            $errors = array( );
            $errors['_qf_default'] = ts( 'You need to have different recommenders' );
            return $errors;
        }

        // add validation to make sure that this email is not in the db as a student
        // and if present is in as a recommender

        return false;
    }

    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess() {
        if ( $this->_action &  CRM_Core_Action::VIEW ) {
            return;
        }

        require_once "CRM/Quest/BAO/Recommendation.php";
        $result = true;

        require_once 'CRM/Quest/BAO/Recommendation.php';
        $params = $this->controller->exportValues( $this->_name );

        for ( $i = 1; $i <= 3; $i++ ) {
            $type = ( $i <= 2 ) ?
                CRM_Quest_BAO_Recommendation::TEACHER :
                CRM_Quest_BAO_Recommendation::COUNSELOR;

            $process = false;
            
            // only process if email and/or school address has changed
            if ( array_key_exists( $i, $this->_oldParams ) ) {
                if ( $params["email_$i"    ] != $this->_oldParams[$i]['email'] ||
                     $params["school_id_$i"] != $this->_oldParams[$i]['organizationID'] ) {
                    $process = true;

                    // clean up old junk
                    // remove the relationship between 
                }
            } else {
                $process = true;
            }

            if ( $process ) {
                // make sure we unlink the old relationships
                $result = $result & CRM_Quest_BAO_Recommendation::process( $this->_contactID,
                                                                           $params["first_name_$i"],
                                                                           $params["last_name_$i" ],
                                                                           $params["email_$i"     ],
                                                                           $params["school_id_$i" ],
                                                                           $type );
            }
        }

        if ( ! $result ) {
            CRM_Core_Error::fatal( ts( "there was an error when processing some of your recommendations" ) );
            return;
        }

        parent::postProcess( );
    } 
   
}

?>