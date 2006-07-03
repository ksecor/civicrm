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

class CRM_Member_BAO_Query {
    
    static function &getFields( ) {
        require_once 'CRM/Member/BAO/Membership.php';
        $fields =& CRM_Member_BAO_Membership::exportableFields( );
        unset( $fields['contact_id']);
        unset( $fields['note'] ); 
        return $fields;
    }
    

    /** 
     * if membership are involved, add the specific membership fields
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) {
        // if membership mode add membership id
        if ( $query->_mode & CRM_Contact_BAO_Query::MODE_MEMBER ) {
            $query->_select['membership_id'] = "civicrm_membership.id as membership_id";
            $query->_element['membership_id'] = 1;
            $query->_tables['civicrm_membership'] = 1;
            $query->_whereTables['civicrm_membership'] = 1;
           
        }

        // get membership_type
        if ( CRM_Utils_Array::value( 'membership_type', $query->_returnProperties ) ) {
            $query->_select['memership_type']  = "civicrm_membership_type.name as membership_type";
            $query->_element['membership_type'] = 1;
            $query->_tables['civicrm_membership'] = 1;
            $query->_tables['civicrm_membership_type'] = 1;
            $query->_whereTables['civicrm_membership'] = 1;
            $query->_whereTables['civicrm_membership_type'] = 1;
        }
    }

    
    static function from( $name, $mode, $side ) {
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
    
    static function where( &$query ) {
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0,7 ) == 'member_' ) {
                self::whereClauseSingle( $query->_params[$id], $query );
            }
        }
    }
    
    static function whereClauseSingle( &$values, &$query ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        switch( $name ) {
        case 'member_since':
        case 'member_start_date_low':
        case 'member_start_date_high':
       
            // process to / from date
            // $query->dateQueryBuilder( $values,
//                                       'civicrm_membership', 'contribution_date', 'receive_date', 'Contribution Date' );
//             
            return;
        }
    }

    static function buildSearchForm( &$form ) {
        
        require_once 'CRM/Member/PseudoConstant.php';
        
        foreach (CRM_Member_PseudoConstant::membershipType( ) as $ID => $Name) {
            $form->_membershipType =& $form->addElement('checkbox', "member_membership_type[$ID]", null,$Name);
        }
        foreach (CRM_Member_PseudoConstant::membershipStatus( ) as $sId => $sName) {
            $form->_membershipStatus =& $form->addElement('checkbox', "member_membership_status[$sId]", null,$sName);
        }

        $form->addElement( 'text', 'member_source', ts( 'Source' ) );
        $form->addElement('date', 'member_since', ts('Member From :'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('member_since', ts('Select a valid date.'), 'qfDate'); 
 
        // Date selects for date 
        $form->add('date', 'member_start_date_low', ts('Sign up/Renew Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('member_start_date_low', ts('Select a valid date.'), 'qfDate'); 
 
        $form->add('date', 'member_start_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('member_start_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->add('date', 'member_end_date_low', ts('End Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('member_end_date_low', ts('Select a valid date.'), 'qfDate'); 
 
        $form->add('date', 'member_end_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('member_end_date_high', ts('Select a valid date.'), 'qfDate'); 

    }

    static function defaultReturnProperties( $mode ) {
        $properties = null;
        if ( $mode & CRM_Contact_BAO_Query::MODE_MEMBER ) {
            $properties = array(  
                                'contact_type'           => 1, 
                                'sort_name'              => 1, 
                                'display_name'           => 1,
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

    static function searchAction( &$row, $id ) {
    }

    static function addShowHide( &$showHide ) {
        //$showHide->addHide( 'memberForm' );
        //$showHide->addShow( 'memberForm[show]' );
    }


}

?>
