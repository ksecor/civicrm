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
 * 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@yahoo.com> 
 * @copyright Donald A. Lobo (c) 2005 
 * $Id$ 
 * 
 */ 

class CRM_Quest_BAO_Query 
{
    static $_terms = null;

    static $_ids = null;

    static function initialize( ) 
    {
        if ( ! self::$_terms ) {
            self::$_terms = array( 'score_SAT'  => 'SAT Score',
                                   'score_ACT'  => 'ACT Score',
                                   'score_PLAN' => 'PLAN Score',
                                   'household_income_total' => 'Total Household Income' );
            self::$_ids   = array( 'ethnicity_id_1', 'gpa_id' );
        }
    }

    static function &getFields( ) 
    {
        require_once 'CRM/Quest/BAO/Student.php';
        $fields =& CRM_Quest_BAO_Student::exportableFields( );
        unset( $fields['contact_id']);
        return $fields;
    }

    /** 
     * if student is involved, add explicit student fields
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) 
    {
        // if contribute mode add contribution id
        if ( $query->_mode & CRM_Contact_BAO_Query::MODE_QUEST ) {
            $query->_select['student_id'] = "quest_student.id as student_id";
            $query->_element['student_id'] = 1;
            $query->_tables['quest_student'] = $query->_whereTables['quest_student'] = 1;
        }

        //self::initialize( );
        $fields = self::getFields();
        
        foreach ( $fields as $name => $title ) {
            if ( CRM_Utils_Array::value( $name, $query->_returnProperties ) ) {
                //$query->_select[$name] = "quest_student.$name as $name";
                //$query->_tables['quest_student'] = $query->_whereTables['quest_student'] = 1;
                if ( substr($name,( strlen($name) - 10), strlen($name) ) == 'country_id' ) { 
                    $query->_select[$name] = "civicrm_country.name as $name";
                    $query->_select[$name] = "civicrm_country.name as $name";
                }  elseif (substr($name,( strlen($name) - 2), strlen($name)) == 'id') {
                    $tName = "civicrm_option_value-" . $name;
                    $query->_select[$name] = "`$tName`.title as $name";
                }  else {
                    $query->_select[$name] = "quest_student.$name as $name";
                    $query->_tables['quest_student'] = $query->_whereTables['quest_student'] = 1;
                }
            }
        }
    }

    static function where( &$query ) 
    {
        //self::initialize( );
        $fields = self::getFields();
        foreach ( $fields as $name => $title ) {
            if ( CRM_Utils_Array::value( $name, $query->_returnProperties ) ) {
                $query->numberRangeBuilder( 'quest_student', $name, $name, $title );
            }
        }
    }

    static function from( $name, $mode, $side, &$query ) 
    {
        
        if ( $name == 'quest_student' ) {
            $from = " INNER JOIN quest_student  ON quest_student.contact_id = contact_a.id " ;
            $fields = self::getFields();

            foreach ( $fields as $name => $title ) {
                if ( CRM_Utils_Array::value( $name, $query->_returnProperties ) ) {
                    if ( substr($name,( strlen($name) - 10), strlen($name) ) == 'country_id' ) { 
                        $from .= " LEFT JOIN  civicrm_country ON (quest_student.$name = civicrm_country.id )";
                    } elseif (substr($name,( strlen($name) - 2), strlen($name)) == 'id') {
                        $tName = "civicrm_option_value-" . $name;
                        $from .= " LEFT JOIN  civicrm_option_value as `$tName` ON (quest_student.$name = `$tName`.id )";
                    }
                }
            }

            return $from;
            //return " INNER JOIN quest_student  ON quest_student.contact_id = contact_a.id ";
        }

        return null;
    }

    static function defaultReturnProperties( $mode ) 
    {
        $properties = null;
        if ( $mode & CRM_Contact_BAO_Query::MODE_QUEST ) {
            $properties = array(  
                                'contact_type'           => 1, 
                                'sort_name'              => 1, 
                                'display_name'           => 1,
                                );

            self::initialize( );
            foreach ( self::$_terms as $name => $title ) {
                $properties[$name] = 1;
            }
        }
        return $properties;
    }

    static function buildSearchForm( &$form ) 
    {
        $form->assign( 'validQuest', true );
        self::initialize( );
        foreach ( self::$_terms as $name => $title ) {
            $form->add( 'text', $name . '_low' , ts( "$title - From" ) );
            $form->add( 'text', $name . '_high', ts( "To" ) );
        }
    }

    static function addShowHide( &$showHide ) 
    {
        $showHide->addHide( 'questForm' );
        $showHide->addShow( 'questForm[show]' ); 
    }
    
}

?>
