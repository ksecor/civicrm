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
            self::$_terms = array( 'quest_score_SAT'  => 'SAT Score',
                                   'quest_score_ACT'  => 'ACT Score',
                                   'quest_score_PLAN' => 'PLAN Score',
                                   'quest_household_income_total' => 'Total Household Income' );
            self::$_ids   = array( 'quest_college_interest' => 'College Interest' );
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
            $query->_tables['quest_student_summary'] = $query->_whereTables['quest_student_summary'] = 1;
        }

        self::initialize( );
        $fields =& self::getFields();
        foreach ( $fields as $name => $title ) {
            if ( CRM_Utils_Array::value( $name, $query->_returnProperties ) ) {
                if ( substr( $name, -10 ) == 'country_id' ) {
                    $query->_select[$name] = "civicrm_country.name as $name";
                    $query->_tables['civicrm_country'] = 1;
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

        case 'quest_score_SAT':
        case 'quest_score_SAT_low':
        case 'quest_score_SAT_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_score_SAT',
                                        'SAT_composite', ts( 'SAT Score' ) );
            return;

        case 'quest_score_ACT':
        case 'quest_score_ACT_low':
        case 'quest_score_ACT_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_score_ACT',
                                        'ACT_composite', ts( 'ACT Score' ) );
            return;

        case 'quest_score_PLAN':
        case 'quest_score_PLAN_low':
        case 'quest_score_PLAN_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_score_PLAN', 
                                        'PLAN_composite', ts( 'PLAN Score' ) );
            return;

        case 'quest_household_income_total':
        case 'quest_household_income_total_low':
        case 'quest_household_income_total_high':
            $query->numberRangeBuilder( $values,
                                        'quest_student_summary', 'quest_household_income_total',
                                        'household_income_total', ts( 'Total Household Income' ) );
            return;

        case 'quest_college_interest':
            require_once "CRM/Core/OptionGroup.php";
            $optionGroups = array( );
            $optionGroups =  CRM_Core_OptionGroup::values( substr( $name, 6) ); 
            $query->_where[$grouping][] = "quest_student.college_interest LIKE '{$value}'";
            $query->_qill[$grouping ][] = ts( 'College interest %1 %2', array( 1 => $op, 2 => $optionGroups[$value] ) );
            $query->_tables['quest_student'] = 1;
            $query->_whereTables['quest_student'] = 1;
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
            $form->add('select', $name, $title, CRM_Core_OptionGroup::values( substr( $name, 6) ) );
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
                                CRM_Utils_System::url( 'civicrm/quest/preapp',
                                                       'reset=1&action=view&id=%%id%%' ),
                                ts( 'View Preapp' ) );
            $editLink = sprintf('<a href="%s">%s</a>',
                                CRM_Utils_System::url( 'civicrm/quest/preapp',
                                                       'reset=1&action=edit&id=%%id%%' ),
                                ts( 'Edit Preapp' ) );
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
