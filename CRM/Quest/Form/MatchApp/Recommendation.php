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
    protected $_defaults = null;

    protected $_oldParams = null;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();

        // also set up the default values and old params
        $this->setDefaultValues( );
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
        if ( ! $this->_defaults ) {

            $query = "
SELECT cr.id           as contact_id,
       i.first_name    as first_name,
       i.last_name     as last_name ,
       e.email         as email     ,
       rc.contact_id_b as school_id ,
       rs.relationship_type_id as rs_type_id,
       rc.relationship_type_id as rc_type_id,
       t.status_id             as status_id
  FROM civicrm_contact      cs,
       civicrm_contact      cr,
       civicrm_individual   i,
       civicrm_email        e,
       civicrm_location     l,
       civicrm_relationship rs,
       civicrm_relationship rc,
       civicrm_task_status  t
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
   AND t.responsible_entity_table = 'civicrm_contact'
   AND t.responsible_entity_id    = cr.id
   AND t.target_entity_table      = 'civicrm_contact'
   AND t.target_entity_id         = cs.id
";

            $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );

            $this->_oldParams = array( );
            $count = 1;
            while ( $dao->fetch( ) ) {
                $this->_oldParams[$count] = array( );
                $this->_oldParams[$count]['contact_id'] = $dao->contact_id;
                $this->_oldParams[$count]['first_name'] = $dao->first_name;
                $this->_oldParams[$count]['last_name' ] = $dao->last_name ;
                $this->_oldParams[$count]['email'     ] = $dao->email;
                $this->_oldParams[$count]['school_id' ] = $dao->school_id;
                $this->_oldParams[$count]['rs_type_id'] = $dao->rs_type_id;
                $this->_oldParams[$count]['rc_type_id'] = $dao->rc_type_id;
                $this->_oldParams[$count]['status_id' ] = $dao->status_id;
                $count++;
            }

            // make sure we have all 3 recommenders
            if ( $count != 1 && $count != 4 ) {
                CRM_Core_Error::fatal( "We could not retrieve your old recommenders" );
            }

            $this->_defaults = array( );
            foreach ( $this->_oldParams as $count => $values ) {
                foreach ( $values as $name => $value ) {
                    $this->_defaults["{$name}_{$count}"] = $value;
                }
            }
        }
        return $this->_defaults;
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
        require_once "CRM/Quest/BAO/Student.php";

        for ( $i = 1; $i <= 3; $i++ ) {
            $firstName =& $this->add( 'text',
                                      "first_name_$i",
                                      ts( 'First Name' ),
                                      $attributes['first_name'],
                                      true );
            $lastName =& $this->add( 'text',
                                     "last_name_$i",
                                     ts( 'Last Name' ),
                                     $attributes['last_name'],
                                     true );
            $email =& $this->add( 'text',
                                  "email_$i",
                                  ts( 'Email' ),
                                  $attributes['first_name'],
                                  true );
            $this->addRule( "email_$i",
                            ts('Email is not valid.'), 'email' );

            $school =& $this->add( 'select',
                                   "school_id_$i",
                                   ts( 'School' ),
                                   array('' => ts('- select -')) + CRM_Quest_BAO_Student::getSchoolSelect( $this->_contactID ),
                                   true );

            if ( array_key_exists( $i, $this->_oldParams ) &&
                 ( $this->_oldParams[$i]['status_id'] == 328 ) ) {
                // freeze all the elements
                $firstName->freeze( );
                $lastName->freeze( );
                $email->freeze( );
                $school->freeze( );
            }
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

        require_once 'CRM/Quest/BAO/Recommendation.php';
        $params = $this->controller->exportValues( $this->_name );

        $ignore  = array( 1 => false, 2 => false, 3 => false );
        $cleanup = array( 1 => true , 2 => true , 3 => true  );

        // if we have old recommenders
        if ( ! empty( $this->_oldParams ) ) {
            for ( $i = 1; $i <= 3; $i++ ) {
                
                // make sure we dont mix counselors and teachers :)
                if ( $i <= 2 ) {
                    $start = 1;
                    $end   = 2;
                } else {
                    $start = $end  = 3;
                }
                
                // for all the old recommenders
                for ( $j = $start; $j <= $end; $j++ ) {
                    // if the new recommender is present and has the same value as the old
                    // ignore and do not process
                    if ( $params["email_$i"]     == $this->_oldParams[$j]['email'] &&
                         $params["school_id_$i"] == $this->_oldParams[$j]['school_id'] ) {
                        $ignore [$i] = true ;
                        $cleanup[$j] = false;
                        break;
                    }
                }
            }

            // now cleanup all the old recommenders
            for ( $i = 1; $i <= 3; $i++ ) {
                if ( ! $cleanup[$i] ) {
                    continue;
                }
                // cleanup oldParams
                CRM_Quest_BAO_Recommendation::cleanup( $this->_contactID,
                                                       $this->_oldParams[$i]['contact_id'],
                                                       $this->_oldParams[$i]['school_id' ],
                                                       $this->_oldParams[$i]['rs_type_id'],
                                                       $this->_oldParams[$i]['rc_type_id'],
                                                       $this->_oldParams[$i]['first_name'],
                                                       $this->_oldParams[$i]['last_name' ],
                                                       $this->_oldParams[$i]['email'     ]
                                                       );
            }
        }


        $result = true;
        for ( $i = 1; $i <= 3; $i++ ) {
            if ( $ignore[$i] ) {
                continue;
            }

            $type = ( $i <= 2 ) ?
                CRM_Quest_BAO_Recommendation::TEACHER :
                CRM_Quest_BAO_Recommendation::COUNSELOR;

            
            // make sure we unlink the old relationships
            $result = $result & CRM_Quest_BAO_Recommendation::process( $this->_contactID,
                                                                       $params["first_name_$i"],
                                                                       $params["last_name_$i" ],
                                                                       $params["email_$i"     ],
                                                                       $params["school_id_$i" ],
                                                                       $type );
        }

        if ( ! $result ) {
            CRM_Core_Error::fatal( ts( "there was an error when processing some of your recommendations" ) );
        }

        parent::postProcess( );
    }
   
}

?>