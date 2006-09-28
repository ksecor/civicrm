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
 * 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@civicrm.org> 
 * @copyright CiviCRM LLC (c) 2004-2006 
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
            
            self::$_ids = array(
                                'quest_ethnicity'            => 'Ethnicity',
                                'quest_highest_school_level' => 'Parent Graduation College',
                                'quest_class_rank_percent'   => 'Class Rank Percentage',
                                'quest_fed_lunch'            => 'Federal Lunch', 
                                'quest_cmr_disposition'      => 'Reader Disposition' 
                                );
            self::$_terms = array(
                                  'quest_gpa_weighted_calculation'   => 'GPA Weighted Calculation',
                                  'quest_gpa_unweighted_calculation' => 'GPA UnWeighted Calculation',
                                  'quest_class_rank'                 => 'Class Rank',
                                  'quest_SAT_Composite'              => 'SAT Composite',
                                  'quest_SAT_Critical_Reading'       => 'SAT Critical Reading',
                                  'quest_SAT_Math'                   => 'SAT Math',
                                  'quest_ACT_Composite'              => 'ACT Composite',
                                  'quest_Income_Total'               => 'Income Total',
                                  'quest_financial_need_index'       => 'Financial Need Index',
                                  'quest_academic_performance_index' => 'Academic Performance Index',
                                  'quest_household_member_count'     => 'Household Member Count',
                                  'quest_class_num_of_students'      => 'Class Num Of Students'                                  
                                  );
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
        if ( $query->_mode & CRM_Contact_BAO_Query::MODE_QUEST ) {
            $query->_select['student_id'] = "quest_student.id as student_id";
            $query->_element['student_id'] = 1;
            $query->_tables['quest_student'] = $query->_whereTables['quest_student'] = 1;
        }
            
        self::initialize( );
        $fields =& self::getFields();
        foreach ( $fields as $name => $title ) {
            if ( CRM_Utils_Array::value( $name, $query->_returnProperties ) ) {
                if ( substr( $name, -10 ) == 'country_id' ) {
                    $query->_select[$name] = "civicrm_country.name as $name";
                    $query->_tables['civicrm_country'] = 1;
                }  elseif ( substr($name, 0, 4) == 'cmr_' ) {
                    $query->_select[$name] = "{$title['where']} as $name";
                    $query->_tables['quest_student_summary'] = 1;
                    $query->_element[$name] = 1;
                }  elseif ( strpos( $name, '_id' ) !== false ) {
                    $tName = "`civicrm_option_value-{$name}`";
                    $query->_select[$name] = "$tName.label as $name";
                    $query->_tables['quest_student'] = 1;
                    $query->_tables[$tName] = "LEFT JOIN civicrm_option_value $tName ON {$tName}.value = quest_student.{$name}";
                }  else {
                    $query->_select[$name] = "{$title['where']} as $name";
                    $query->_tables['quest_student'] = 1;
                }
            }
        }
    }

    static function where( &$query ) {
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0, 6 ) == 'quest_' ) {
                self::whereClauseSingle( $query->_params[$id], $query );
            }
        }
    }

    static function whereClauseSingle( &$values, &$query ) {        
        list( $name, $op, $value, $grouping, $wildcard ) = $values;

        switch ( $name ) {

        case 'quest_gpa_weighted_calculation':
        case 'quest_gpa_weighted_calculation_low':
        case 'quest_gpa_weighted_calculation_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_gpa_weighted_calculation',
                                        'gpa_weighted_calc', ts( 'GPA Weighted Calculation' ) );
            return;
            
        case 'quest_gpa_unweighted_calculation':
        case 'quest_gpa_unweighted_calculation_low':
        case 'quest_gpa_unweighted_calculation_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_gpa_unweighted_calculation',
                                        'gpa_unweighted_calc', ts( 'GPA Unweighted Calculation' ) );
            return;

        case 'quest_class_rank':
        case 'quest_class_rank_low':
        case 'quest_class_rank_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student', 'quest_class_rank',
                                        'class_rank', ts( 'Class Rank' ) );
            return;
            
        case 'quest_SAT_Composite':
        case 'quest_SAT_Composite_low':
        case 'quest_SAT_Composite_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_SAT_Composite',
                                        'SAT_composite', ts( 'SAT Composite' ) );
            return;
            
        case 'quest_SAT_Critical_Reading':
        case 'quest_SAT_Critical_Reading_low':
        case 'quest_SAT_Critical_Reading_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_SAT_Critical_Reading',
                                        'SAT_reading', ts( 'SAT Critical Reading' ) );
            return;

        case 'quest_SAT_Math':
        case 'quest_SAT_Math_low':
        case 'quest_SAT_Math_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_SAT_Math',
                                        'SAT_math', ts( 'SAT Math' ) );
            return;

        case 'quest_ACT_Composite':
        case 'quest_ACT_Composite_low':
        case 'quest_ACT_Composite_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_ACT_Composite',
                                        'ACT_composite', ts( 'ACT Composite' ) );
            return;
            
        case 'quest_Income_Total':
        case 'quest_Income_Total_low':
        case 'quest_Income_Total_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_Income_Total',
                                        'household_income_total', ts( 'Income Total' ) );
            return;
            
        case 'quest_financial_need_index':
        case 'quest_financial_need_index_low':
        case 'quest_financial_need_index_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_financial_need_index',
                                        'financial_need_index', ts( 'Financial Need Index' ) );
           return; 
            
        case 'quest_academic_performance_index':
        case 'quest_academic_performance_index_low':
        case 'quest_academic_performance_index_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_academic_performance_index',
                                        'academic_index', ts( 'Academic Performance Index' ) );
            return;

        case 'quest_household_member_count':
        case 'quest_household_member_count_low':
        case 'quest_household_member_count_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_household_member_count',
                                        'household_member_count', ts( 'Household Member Count' ) );
            return;

        case 'quest_class_num_of_students':
        case 'quest_class_num_of_students_low':
        case 'quest_class_num_of_students_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student', 'quest_class_num_of_students',
                                        'class_num_students', ts( 'Class Num Students' ) );
            return; 

        case 'quest_ethnicity' :
            require_once "CRM/Core/OptionGroup.php";
            $optionGroups = array( );
            $optionGroups =  CRM_Core_OptionGroup::values( substr( $name, 6) ); 
            $query->_where[$grouping][] = "quest_student.ethnicity_id_1 LIKE '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Ethnicity %1 %2', array( 1 => $op, 2 => $optionGroups[$value] ) );
            $query->_tables['quest_student'] = 1;
            $query->_whereTables['quest_student'] = 1;
            return;

        case 'quest_highest_school_level':
            require_once "CRM/Core/OptionGroup.php";
            $optionGroups = array( );
            $optionGroups =  CRM_Core_OptionGroup::values( substr( $name, 6) ); 
            $query->_where[$grouping][] = "quest_student.parent_grad_college_id LIKE '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Parent Graduation College %1 %2', array( 1 => $op, 2 => $optionGroups[$value] ) );
            $query->_tables['quest_student'] = 1;
            $query->_whereTables['quest_student'] = 1;
            return;
           
        case 'quest_class_rank_percent':
            require_once "CRM/Core/OptionGroup.php";
            $optionGroups = array( );
            $optionGroups =  CRM_Core_OptionGroup::values( substr( $name, 6) ); 
            $query->_where[$grouping][] = "quest_student.class_rank_percent_id LIKE '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Class Rank Percentage %1 %2', array( 1 => $op, 2 => $optionGroups[$value] ) );
            $query->_tables['quest_student'] = 1;
            $query->_whereTables['quest_student'] = 1;
            return;

        case 'quest_fed_lunch':
            require_once "CRM/Core/OptionGroup.php";
            $optionGroups = array( );
            $optionGroups =  CRM_Core_OptionGroup::values( substr( $name, 6) ); 
            $query->_where[$grouping][] = "quest_student.fed_lunch_id LIKE '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Federal Lunch %1 %2', array( 1 => $op, 2 => $optionGroups[$value] ) );
            $query->_tables['quest_student'] = 1;
            $query->_whereTables['quest_student'] = 1;
            return;
        

        case 'quest_cmr_disposition':
            require_once "CRM/Core/OptionGroup.php";
            $optionGroups = array( );
            $optionGroups =  CRM_Core_OptionGroup::values( substr( $name, 6) ); 
            $query->_where[$grouping][] = "quest_student_summary.cmr_disposition_id LIKE '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Reader Disposition %1 %2', array( 1 => $op, 2 => $optionGroups[$value] ) );
            $query->_tables['quest_student_summary'] = 1;
            $query->_whereTables['quest_student_summary'] = 1;
            return;
        }
    }

    
    static function from( $name, $mode, $side ) 
    {
        $from = null;
        if ( $name == 'quest_student' ) {
            $from = " INNER JOIN quest_student  ON quest_student.contact_id = contact_a.id ";
        } else if ($name == 'quest_student_summary') {
            $from = " INNER JOIN quest_student_summary ON quest_student_summary.contact_id = contact_a.id ";
        }

        return $from;
    }

    static function defaultReturnProperties( $mode ) 
    {
        $properties = null;
        if ( $mode & CRM_Contact_BAO_Query::MODE_QUEST ) {
            $properties = array(  
                                'contact_type'           => 1, 
                                'contact_sub_type'       => 1,
                                'sort_name'              => 1, 
                                'display_name'           => 1,
                                );

            self::initialize( );
            foreach ( self::$_terms as $name => $title ) {
                $properties[substr( $name, 6 )] = 1;
            }
        }
        return $properties;
    }

    static function buildSearchForm( &$form ) 
    {
        $form->assign( 'validQuest', true );
        self::initialize( );
        foreach ( self::$_terms as $name => $title ) {
            $form->add('text', $name . '_low', ts('%1 - From', array(1 => $title)));
            $form->add( 'text', $name . '_high', ts( "To" ) );
        }
        
        require_once "CRM/Core/OptionGroup.php";
        foreach ( self::$_ids as $name => $title ) {
            $form->add('select', $name, $title, array("" => "-- Select -- ")+CRM_Core_OptionGroup::values( substr( $name, 6) ) );
        }
    }

    static function addShowHide( &$showHide ) 
    {
        $showHide->addHide( 'questForm' );
        $showHide->addShow( 'questForm_show' ); 
    }

    static function searchAction( &$row, $id ) {
        static $viewLink = null;
        static $editLink = null;

        // add links only if student
        if ( $row['contact_sub_type'] != 'Student' ) {
            return;
        }

        if ( ! $viewLink ) {
            $viewLink = sprintf('<a href="%s">%s</a>',
                                CRM_Utils_System::url( 'civicrm/quest/matchapp/preview',
                                                       'reset=1&action=view&id=%%id%%' ),
                                ts( 'View CM App' ) );
            $editLink = sprintf('<a href="%s">%s</a>',
                                CRM_Utils_System::url( 'civicrm/quest/matchapp',
                                                       'reset=1&action=update&id=%%id%%' ),
                                ts( 'Edit CM App' ) );
        }

        if ( CRM_Core_Permission::check( 'view Quest Application' ) ) {
            $row['action'] .= str_replace( '%%id%%', $id, "|&nbsp;&nbsp;{$viewLink}" );
        }

        if ( CRM_Core_Permission::check( 'edit Quest Application' ) ) {
            $row['action'] .= str_replace( '%%id%%', $id, "|&nbsp;&nbsp;{$editLink}" );
        }

    }

}

?>
