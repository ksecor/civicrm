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

class CRM_Member_BAO_Query 
{
    
    static function &getFields( ) 
    {
        require_once 'CRM/Member/BAO/Membership.php';
        $fields =& CRM_Member_BAO_Membership::exportableFields( );
        //unset( $fields['contact_id']);
        //unset( $fields['note'] ); 
        return $fields;
    }
    

    /** 
     * if membership are involved, add the specific membership fields
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) 
    {
        // if membership mode add membership id
        if ( $query->_mode & CRM_Contact_BAO_Query::MODE_MEMBER ) {

            $query->_select['membership_id'] = "civicrm_membership.id as membership_id";
            $query->_element['membership_id'] = 1;
            $query->_tables['civicrm_membership'] = 1;
            $query->_whereTables['civicrm_membership'] = 1;
           
            //add membership type
            $query->_select['memership_type']  = "civicrm_membership_type.name as membership_type";
            $query->_element['membership_type'] = 1;
            $query->_tables['civicrm_membership_type'] = 1;
            $query->_whereTables['civicrm_membership_type'] = 1;
            
            //add join date
            $query->_select['join_date']  = "civicrm_membership.join_date as join_date";
            $query->_element['join_date'] = 1;
            
            //add source
            $query->_select['source']  = "civicrm_membership.source as source";
            $query->_element['source'] = 1;
            
            //add status
            $query->_select['status_id']  = "civicrm_membership.status_id as status_id";
            $query->_element['status_id'] = 1;
            
            //add start date / end date
            $query->_select['start_date']  = "civicrm_membership.start_date as start_date";
            $query->_element['start_date'] = 1;
            $query->_select['end_date']  = "civicrm_membership.end_date as end_date";
            $query->_element['end_date'] = 1;
        }
    }

    static function where( &$query ) 
    {
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0,7 ) == 'member_' ) {
                self::whereClauseSingle( $query->_params[$id], $query );
            }
        }
    }
    
  
    static function whereClauseSingle( &$values, &$query ) 
    {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        switch( $name ) {

        case 'member_start_date_low':
        case 'member_start_date_high':
            $query->dateQueryBuilder( $values,
                                       'civicrm_membership', 'member_start_date', 'start_date', 'Start Date' );
            return;

        case 'member_end_date_low':
        case 'member_end_date_high':
            $query->dateQueryBuilder( $values,
                                       'civicrm_membership', 'member_end_date', 'end_date', 'End Date' );
            return;

        case 'member_join_date':
            $op = '>=';
            $date = CRM_Utils_Date::format( $value );
            if ( $date ) {
                $query->_where[$grouping][] = "civicrm_membership.join_date {$op} {$date}";
                $date = CRM_Utils_Date::customFormat( $value );
                $format  = CRM_Utils_Date::customFormat( CRM_Utils_Date::format( array_reverse($value), '-' ) );
                $query->_qill[$grouping ][] = ts( 'Member Since %2 %1', array( 1 => $format, 2 => $op) );
            }

            return;
            
        case 'member_source':
            
            $value = strtolower(addslashes(trim($value)));

            $query->_where[$grouping][] = "civicrm_membership.source $op '{$value}'";
            $query->_qill[$grouping ][] = ts( 'Source %2 %1', array( 1 => $value, 2 => $op) );
            $query->_tables['civicrm_membership'] = $query->_whereTables['civicrm_membership'] = 1;
            return;

        case 'member_status_id':
            require_once 'CRM/Member/PseudoConstant.php';
            $status = implode (',' ,array_keys($value));
            
            if (count($value) > 1) {
                $op = 'IN';
                $status = "({$status})";
            }     
            
            $names = array( );
            $statusTypes  = CRM_Member_PseudoConstant::membershipStatus( );
            foreach ( $value as $id => $dontCare ) {
                $names[] = $statusTypes[$id];
            }
            $query->_qill[$grouping][]  = ts('Membership Status %1', array( 1 => $op ) ) . ' ' . implode( ' ' . ts('or') . ' ', $names );
                
            $query->_where[$grouping][] = "civicrm_membership.status_id {$op} {$status}";
            $query->_tables['civicrm_membership'] = $query->_whereTables['civicrm_membership'] = 1;
            return;

        case 'member_membership_type_id':
            require_once 'CRM/Member/PseudoConstant.php';
            $mType = implode (',' , array_keys($value));
            if (count($value) > 1) {
                $op = 'IN';
                $mType = "({$mType})";
            }     

            $names = array( );
            $membershipTypes  = CRM_Member_PseudoConstant::membershipType( );
            foreach ( $value as $id => $dontCare ) {
                $names[] = $membershipTypes[$id];
            }
            $query->_qill[$grouping][]  = ts('Membership Type %1', array( 1 => $op ) ) . ' ' . implode( ' ' . ts('or') . ' ', $names );

            $query->_where[$grouping][] = "civicrm_membership.membership_type_id {$op} {$mType}";
            $query->_tables['civicrm_membership'] = $query->_whereTables['civicrm_membership'] = 1;
            return;
        }
    }

    static function from( $name, $mode, $side ) 
    {
        $from = null;
        switch ( $name ) {
        
        case 'civicrm_membership':
            $from = " INNER JOIN civicrm_membership ON civicrm_membership.contact_id = contact_a.id ";
            break;
    
        case 'civicrm_membership_type':
            if ( $mode & CRM_Contact_BAO_Query::MODE_MEMBER ) {
                $from = " INNER JOIN civicrm_membership_type ON civicrm_membership.membership_type_id = civicrm_membership_type.id ";
            } else {
                $from = " $side JOIN civicrm_membership_type ON civicrm_membership.membership_type_id = civicrm_membership_type.id ";
            }
            break;
            
        case 'civicrm_membership_payment':
            $from = " INNER JOIN civicrm_membership_payment ON civicrm_membership_payment.membership_id = civicrm_membership.id ";
            break;
        }
        return $from;
    }
    
    static function defaultReturnProperties( $mode ) 
    {
        $properties = null;
        if ( $mode & CRM_Contact_BAO_Query::MODE_MEMBER ) {
            $properties = array(  
                                'contact_type'           => 1, 
                                'sort_name'              => 1, 
                                'display_name'           => 1,
                                'membership_type'        => 1,
                                'join_date'              => 1,
                                //'start_date'             => 1,
                                //'end_date'               => 1,
                                //'source'                 => 1,
                                //'status_id'              => 1
                                );

            /*
            // also get all the custom membership properties
            $fields = CRM_Core_BAO_CustomField::getFieldsForImport('Member');
            if ( ! empty( $fields ) ) {
                foreach ( $fields as $name => $dontCare ) {
                    $properties[$name] = 1;
                }
            }
            */
        }
        return $properties;
    }

    static function buildSearchForm( &$form ) 
    {
        
        require_once 'CRM/Member/PseudoConstant.php';
        
        foreach (CRM_Member_PseudoConstant::membershipType( ) as $id => $Name) {
            $form->_membershipType =& $form->addElement('checkbox', "member_membership_type_id[$id]", null,$Name);
        }
        foreach (CRM_Member_PseudoConstant::membershipStatus( ) as $sId => $sName) {
            $form->_membershipStatus =& $form->addElement('checkbox', "member_status_id[$sId]", null,$sName);
        }

        $form->addElement( 'text', 'member_source', ts( 'Source' ) );
        //$form->addElement('date', 'member_join_date', ts('Member Since :'), CRM_Core_SelectValues::date('relative')); 
        //$form->addRule('member_join_date', ts('Select a valid date.'), 'qfDate'); 
 
        // Date selects for date 
        $form->add('date', 'member_start_date_low', ts('Start Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('member_start_date_low', ts('Select a valid date.'), 'qfDate'); 
 
        $form->add('date', 'member_start_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('member_start_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->add('date', 'member_end_date_low', ts('End Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('member_end_date_low', ts('Select a valid date.'), 'qfDate'); 
 
        $form->add('date', 'member_end_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('member_end_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->assign( 'validCiviMember', true );
    }

    static function searchAction( &$row, $id ) 
    {
    }

    static function addShowHide( &$showHide ) 
    {
        $showHide->addHide( 'memberForm' );
        $showHide->addShow( 'memberForm_show' );
    }


}

?>
