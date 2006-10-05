<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                 |
 +--------------------------------------------------------------------+
*/


/**
 * Recommendation v2 to catch and trap errors
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
class CRM_Quest_Form_MatchApp_RecommendationV2 extends CRM_Quest_Form_App
{
    protected $_defaults = null;

    protected $_oldParams = null;

    protected $_counselorStart = null;
    protected $_counselorCount = null;

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

            $this->_oldParams = array( );

            // also get all the schools the student goes to, so we restrict the recommenders
            // from that school only
            require_once 'CRM/Quest/BAO/Student.php';
            $schoolSelect = CRM_Quest_BAO_Student::getSchoolSelect( $this->_contactID );
            unset( $schoolSelect[''] );
            $schoolIDs = array_keys( $schoolSelect );
            //CRM_Core_Error::debug( 'i', $schoolIDs );

            if ( $schoolIDs ) {
                $query = "
SELECT cr.id  as contact_id,
       i.first_name    as first_name,
       i.last_name     as last_name ,
       e.email         as email     ,
       rc.contact_id_b as school_id ,
       rs.relationship_type_id as rs_type_id,
       rc.relationship_type_id as rc_type_id,
       t.status_id             as status_id,
       t.id                    as task_id
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
   AND rc.contact_id_a = cr.id
   AND rs.is_active    = 1
   AND rc.is_active    = 1
   AND rc.contact_id_a = cr.id
   AND cs.id           = {$this->_contactID}
   AND i.contact_id    = cr.id
   AND l.entity_table  = 'civicrm_contact'
   AND l.entity_id     = cr.id
   AND e.location_id   = l.id
   AND t.task_id       = 10
   AND t.responsible_entity_table = 'civicrm_contact'
   AND t.responsible_entity_id    = cr.id
   AND t.target_entity_table      = 'civicrm_contact'
   AND t.target_entity_id         = cs.id
   AND t.status_id               IN (326, 327, 328)
 ORDER BY rs.relationship_type_id, contact_id
";

                $dao =& CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );

                $count = 0;
                $processed = array( );
                while ( $dao->fetch( ) ) {
                    if ( array_key_exists( $dao->contact_id, $processed ) ) {
                        // check if past school was in schoolIDs
                        if ( in_array( $this->_oldParams[$processed[$dao->contact_id]]['school_id'],
                                       $schoolIDs ) ) {
                            continue;
                        }
                        if ( in_array( $dao->school_id, $schoolIDs ) ) {
                            $count = $processed[$dao->contact_id];
                        }
                    } else {
                        $count++;
                    }
                    $processed[$dao->contact_id] = $count;
                    $this->_oldParams[$count] = array( );
		    $this->_oldParams[$count]['task_id'   ] = $dao->task_id;
                    $this->_oldParams[$count]['contact_id'] = $dao->contact_id;
                    $this->_oldParams[$count]['first_name'] = $dao->first_name;
                    $this->_oldParams[$count]['last_name' ] = $dao->last_name ;
                    $this->_oldParams[$count]['email'     ] = $dao->email;
                    $this->_oldParams[$count]['school_id' ] = $dao->school_id;
                    $this->_oldParams[$count]['rs_type_id'] = $dao->rs_type_id;
                    $this->_oldParams[$count]['rc_type_id'] = $dao->rc_type_id;
                    $this->_oldParams[$count]['status_id' ] = $dao->status_id;
                }
            }
            //CRM_Core_Error::debug( 'o', $this->_oldParams );
	    
            $this->_defaults = array( );
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

        $schoolSelect = array('' => ts('- select -')) + CRM_Quest_BAO_Student::getSchoolSelect( $this->_contactID );

        // first add all the teachers
        $count = count( $this->_oldParams );
        $j = 1;
        for ( $i = 1; $i <= $count; $i++ ) {
            if ( $this->_oldParams[$i]['rs_type_id'] != 9 ) {
                break;
            }
            $this->addRecommendation( $this->_oldParams[$i], $j, $schoolSelect );
            $this->_oldParams[$i]['formIndex'] = $j;
            $j++;
        }

        // now add 2 empty rows for more teachers
        $this->addRecommendation( null, $j, $schoolSelect );
        $this->addRecommendation( null, $j + 1, $schoolSelect );
        $j += 2;

        $this->assign( 'teacherCount', $j );

        $this->_counselorStart = $j;
        for ( ; $i <= $count; $i++ ) {
            $this->addRecommendation( $this->_oldParams[$i], $j, $schoolSelect ); 
            $this->_oldParams[$i]['formIndex'] = $j;
            $j++;
        }

        // now add 1 more row for the counselor
        $this->addRecommendation( null, $j, $schoolSelect );
        $j++;

        $this->_counselorCount = $j;
        $this->assign( 'counselorStart', $this->_counselorStart );
        $this->assign( 'counselorCount', $this->_counselorCount );

        // add form rule
        $this->addFormRule(array('CRM_Quest_Form_MatchApp_RecommendationV2', 'formRule'), $this);

        parent::buildQuickForm( );
                
    }//end of function


    function addRecommendation( $values, $index, &$schoolSelect ) {
        $cb        =& $this->add( 'checkbox',
                                  "mark_cb_$index",
                                  null, null, null );
        $firstName =& $this->add( 'text',
                                  "first_name_$index",
                                  ts( 'First Name' ),
                                  $attributes['first_name'] );
        $lastName =& $this->add( 'text',
                                 "last_name_$index",
                                 ts( 'Last Name' ),
                                 $attributes['last_name'] );
	$status   =& $this->add( 'text',
                                 "status_$index",
                                 ts( 'Status' ),
                                 $attributes['last_name'] );
        $email    =& $this->add( 'text',
				 "email_$index",
				 ts( 'Email' ),
				 $attributes['first_name'] );
        $this->addRule( "email_$index",
                        ts('Email is not valid.'), 'email' );
        
        $school =& $this->add( 'select',
                               "school_id_$index",
                               ts( 'School' ),
                               $schoolSelect );

	$status->freeze( );
        if ( $values ) {
            foreach ( $values as $name => $value ) {
                $this->_defaults["{$name}_{$index}"] = $value;
            }
	    
	    if ( $values['status_id'] ) {
	      switch( $values['status_id'] ) {
	      case 326:
		$this->_defaults["status_{$index}"] = ts('Not Started');
		break;
	      case 327:
		$this->_defaults["status_{$index}"] = ts('In Progress');
		break;
	      case 328:
		$this->_defaults["status_{$index}"] = ts('Completed');
		break;
	      }
	    }
            
            if ( $values['status_id'] == 328 ) {
                $this->_defaults["mark_cb_{$index}"] = 1;
                
                // freeze all the elements
                $cb->freeze( );
                $school->freeze( );
            }
            $firstName->freeze( );
            $lastName->freeze( );
            $email->freeze( );
        }
        // CRM_Core_Error::debug( 'd', $this->_defaults );
    }
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
    public function formRule( &$params, &$files, &$form ) {

        // make sure we have 2 teachers and one counselor checked
        $teacherCount = $counselorCount = 0;
        for ( $i = 1; $i < $form->_counselorStart; $i++ ) {
            if ( array_key_exists( "mark_cb_$i", $params ) ) {
                $teacherCount++;
            }
        }
        for ( $i = $form->_counselorStart; $i < $form->_counselorCount; $i++ ) {
            if ( array_key_exists( "mark_cb_$i", $params ) ) {
                $counselorCount++;
            }
        }

        // CRM_Core_Error::debug( "$teacherCount, $counselorCount, {$form->_counselorStart}, {$form->_counselorCount}", $params );
        if ( $teacherCount != 2 || $counselorCount != 1 ) {
            $errors = array( );
            $errors['_qf_default'] = ts( 'Please select exactly two teachers and one counselor to use as recommenders by checking the box to the left of their names.' );
            return $errors;
        }

        // make sure we have all the information for the checked ones
        $fieldNames = array( 'first_name' => ts( 'First Name' ),
                             'last_name'  => ts( 'Last Name'  ),
                             'email'      => ts( 'Email'      ),
                             'school_id'  => ts( 'School'     ) );
        $errors = array( );
        $emails = array( );
        for ( $i = 1; $i <= $form->_counselorCount; $i++ ) {
            if ( array_key_exists( "mark_cb_$i", $params ) ) {
                foreach ( $fieldNames as $name => $title ) {
                    if ( array_key_exists( "{$name}_{$i}", $params ) &&
                         ! empty( $params["{$name}_{$i}"] ) ) {
                        if ( $name == 'email' ) {
                            $emails[] = trim( $params["{$name}_{$i}"] );
                        }
                    } else {
                        $errors["{$name}_{$i}"] = ts( " %1 is a required field", array( 1 => $title ) );
                    } 
                }
            }
        }

        if ( !empty( $errors ) ) {
            return $errors;
        }

        if ( count( $emails ) != 3 ) {
            $errors['_qf_default'] = ts( 'Error in email address collection, contact Quest support' );
            return $errors;
        }

        // make sure all 3 emails are distinct and not 
        // the same email as the student
        if ( $emails[0] == $emails[1] ||
             $emails[0] == $emails[2] ||
             $emails[1] == $emails[2] ) {
            $errors['_qf_default'] = ts( 'Your recommenders need distinct email address' );
            return $errors;
        }

        // add validation to make sure that this email is not in the db as a student
        // and if present is in as a recommender
        $userEmail = CRM_Contact_BAO_Contact::getPrimaryEmail( $form->_contactID );
        if ( $emails[0] == $userEmail ||
             $emails[1] == $userEmail ||
             $emails[2] == $userEmail ) {
            $errors = array( );
            $errors['_qf_default'] = ts( 'You cannot serve as your own recommender' );
            return $errors;
        }

        return true;
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

        CRM_Core_DAO::transaction( 'BEGIN' );


        // first cleanup all the oldParams which are not selected
        $lookupTable = array( );
        for ( $i = 1; $i <= count( $this->_oldParams ); $i++ ) {
            $lookupTable[$this->_oldParams[$i]['formIndex']] = $i;
            if ( ! array_key_exists( "mark_cb_" . $this->_oldParams[$i]['formIndex'], $params ) ) {
                // clean up this entry
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
        $count = 0;
        for ( $i = 1; $i < $this->_counselorCount; $i++ ) {
            if ( array_key_exists( "mark_cb_$i", $params ) ) {
                $count++;
                $type = ( $count <= 2 ) ?
                    CRM_Quest_BAO_Recommendation::TEACHER :
                    CRM_Quest_BAO_Recommendation::COUNSELOR;
                

                // if recommendation is complete, dont do any work
                if ( $this->_oldParams[$lookupTable[$i]]['status_id'] == 328 ) {
                    continue;
                }
                $result = $result & CRM_Quest_BAO_Recommendation::process( $this->_contactID,
                                                                           $params["first_name_$i"],
                                                                           $params["last_name_$i" ],
                                                                           $params["email_$i"     ],
                                                                           $params["school_id_$i" ],
                                                                           $type );
            }
        }

        if ( ! $result ) {
            CRM_Core_DAO::transaction( 'ROLLBACK' );
            CRM_Core_Error::fatal( ts( "there was an error when processing some of your recommendations" ) );
        }

        CRM_Core_DAO::transaction( 'COMMIT' );

        parent::postProcess( );
    }
   
}

?>