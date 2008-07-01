<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Pledge_BAO_Query 
{
    static function &getFields( ) 
    {
        $fields = array( );
        require_once 'CRM/Pledge/DAO/Pledge.php';
        $fields = array_merge( $fields, CRM_Pledge_DAO_Pledge::import( ) );
        return $fields;
    }

    /** 
     * build select for Pledge
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) 
    {
        if ( ( $query->_mode & CRM_Contact_BAO_Query::MODE_PLEDGE ) ||
             CRM_Utils_Array::value( 'id', $query->_returnProperties ) ) {
            $query->_select['pledge_id'] = "civicrm_pledge.id as pledge_id";
            $query->_element['id'] = 1;
            $query->_tables['civicrm_pledge'] = $query->_whereTables['civicrm_pledge'] = 1;
        }
        
//         //add pledge select
//         if ( CRM_Utils_Array::value( 'pb_pledge_name', $query->_returnProperties ) ) {
//             $query->_select['pb_pledge_name'] = "civicrm_pb_pledge.creator_pledge_desc as pb_pledge_name";
//             $query->_element['pb_pledge_name'] = 1;
//             $query->_select['pb_pledge_id'] = "civicrm_pb_pledge.id as pb_pledge_id";
//             $query->_element['pb_pledge_id'] = 1;
//             $query->_tables['civicrm_pb_pledge'] = 1;
//             $query->_whereTables['civicrm_pb_pledge'] = 1;
//         }
        
//         if ( CRM_Utils_Array::value( 'pb_pledge_is_active', $query->_returnProperties ) ) {
//             $query->_select['pb_pledge_is_active']  = "civicrm_pb_pledge.is_active as pb_pledge_is_active";
//             $query->_element['pb_pledge_is_active'] = 1;
//             $query->_tables['civicrm_pb_pledge'] = 1;
//             $query->_whereTables['civicrm_pb_pledge'] = 1;
//         }
        
//         if ( CRM_Utils_Array::value( 'pb_signer_is_done', $query->_returnProperties ) ) {
//             $query->_select['pb_signer_is_done']  = "civicrm_pb_signer.is_done as pb_signer_is_done";
//             $query->_element['pb_signer_is_done'] = 1;
//             $query->_tables['civicrm_pb_signer'] = $query->_whereTables['civicrm_pb_signer'] = 1;
//         }
         
//         if ( CRM_Utils_Array::value( 'pb_signer_pledge_desc', $query->_returnProperties ) ) {
//             $query->_select['pb_signer_pledge_desc']  = "civicrm_pb_pledge.signer_pledge_desc as pb_signer_pledge_desc";
//             $query->_element['pb_signer_pledge_desc'] = 1;
//             $query->_tables['civicrm_pb_pledge'] = $query->_whereTables['civicrm_pb_pledge'] = 1;
//         }
        
//         if ( CRM_Utils_Array::value( 'pb_signer_signing_date', $query->_returnProperties ) ) {
//             $query->_select['pb_signer_signing_date']  = "civicrm_pb_signer.signing_date as pb_signer_signing_date";
//             $query->_element['pb_signer_signing_date'] = 1;
//             $query->_tables['civicrm_pb_signer'] = $query->_whereTables['civicrm_pb_signer'] = 1;
//         }
        
//         if ( CRM_Utils_Array::value( 'pb_signer_is_anonymous', $query->_returnProperties ) ) {
//             $query->_select['pb_signer_is_anonymous']  = "civicrm_pb_signer.is_anonymous as pb_signer_is_anonymous";
//             $query->_element['pb_signer_is_anonymous'] = 1;
//             $query->_tables['civicrm_pb_signer'] = $query->_whereTables['civicrm_pb_signer'] = 1;
//         }
        
    }

    static function where( &$query ) 
    {
        $grouping = null;
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0, 3) == 'pb_') {
                if ( $query->_mode == CRM_Contact_BAO_QUERY::MODE_CONTACTS ) {
                    $query->_useDistinct = true;
                }
                self::whereClauseSingle( $query->_params[$id], $query );
            }
        }
    }
    
  
    static function whereClauseSingle( &$values, &$query ) 
    {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
             
        switch( $name ) {

        case 'pledge_id':
            $query->_where[$grouping][] = "civicrm_pledge.id $op $value";
            $query->_tables['civicrm_pledge'] = $query->_whereTables['civicrm_pledge'] = 1;
            return;
            
        case 'pb_pledge_name':
            $op = 'LIKE';
            if ( $value ){
                $pledgeValue = CRM_Core_DAO::getFieldValue('CRM_Pledge_DAO_Pledge', $value, 'creator_pledge_desc' );
                $query->_where[$grouping][] = "civicrm_pb_pledge.creator_pledge_desc $op '%$pledgeValue%'";
                $query->_qill[$grouping][]  = ts("Pledge" ) . " $op - '$pledgeValue'";
            }
            $query->_tables['civicrm_pb_pledge'] = $query->_whereTables['civicrm_pb_pledge'] = 1;
            return;
            
        case 'pb_pledge_is_active':
            $query->_where[$grouping][] = "civicrm_pb_pledge.is_active $op $value";
            if ( $value ) {
                $query->_qill[$grouping][]  = ts("Find Active Pledges");
            }
            $query->_tables['civicrm_pb_pledge'] = $query->_whereTables['civicrm_pb_pledge'] = 1;
            return;

        case 'pb_signer_is_done':
            $query->_where[$grouping][] = "civicrm_pb_signer.is_done $op $value";
            if ( $value ) {
                $query->_qill[$grouping][]  = ts("Find Done Pledges");
            }
            $query->_tables['civicrm_pb_signer'] = $query->_whereTables['civicrm_pb_signer'] = 1;
            return;
        }
    }

    static function from( $name, $mode, $side ) 
    {
        $from = null;

        switch ( $name ) {
       
        case 'civicrm_pledge':
            $from = " INNER JOIN civicrm_pledge ON civicrm_pledge.contact_id = contact_a.id JOIN civicrm_pledge_payment ON civicrm_pledge.id = civicrm_pledge_payment.pledge_id ";
            break;
            
        }
        return $from;
    }

    /**
     * getter for the qill object
     *
     * @return string
     * @access public
     */
    function qill( ) {
        return (isset($this->_qill)) ? $this->_qill : "";
    }
   
    static function defaultReturnProperties( $mode ) 
    {
        $properties = null;
     
        if ( $mode & CRM_Contact_BAO_Query::MODE_PLEDGE ) {
            $properties = array(  
                                'contact_id'             => 1, 
                                'sort_name'              => 1, 
                                'display_name'           => 1,
                                'pledge_id'              => 1
                                /*                                'amount'                 => 1,
                                'frequency_unit'         => 1,
                                'frequency_interval'     => 1,
                                'create_date'            => 1,
                                'start_date'             => 1,
                                'status_id'              => 1*/
                               );
        }
        return $properties;
    }

    static function buildSearchForm( &$form ) 
    {
        
        // Date selects for date 
        $form->add('date', 'pledge_start_date_low', ts('Pledge Start Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('pledge_start_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->add('date', 'pledge_start_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('pledge_start_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->add('date', 'pledge_end_date_low', ts('Pledge End Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('pledge_end_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->add('date', 'pledge_end_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('pledge_end_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->addElement( 'checkbox', 'pledge_test' , ts( 'Find Test Pledge?' ) );

        $form->add('text', 'pledge_amount_low', ts('From'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $form->addRule( 'pledge_amount_low', ts( 'Please enter a valid money value (e.g. 9.99).' ), 'money' );
        
        $form->add('text', 'pledge_amount_high', ts('To'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $form->addRule( 'pledge_amount_high', ts( 'Please enter a valid money value (e.g. 99.99).' ), 'money' );

        require_once "CRM/Core/OptionGroup.php";
        $statusValues = CRM_Core_OptionGroup::values("contribution_status");
        // Remove status values that are only used for recurring contributions for now (Failed and In Progress).
        unset( $statusValues['4']);
        unset( $statusValues['5']);
 
        foreach ( $statusValues as $key => $val ) {
            $status[] =  $form->createElement('advcheckbox',$key, null, $val );
        }
        
        $form->addGroup( $status, 'pledge_status_id', ts( 'Pledge Status' ) );

        require_once 'CRM/Contribute/PseudoConstant.php';
        $form->add('select', 'contribution_type_id', 
                   ts( 'Contribution Type' ),
                   array( '' => ts( '- select -' ) ) +
                   CRM_Contribute_PseudoConstant::contributionType( ) );
       
        $form->add('select', 'contribution_page_id', 
                   ts( 'Contribution Page' ),
                   array( '' => ts( '- select -' ) ) +
                   CRM_Contribute_PseudoConstant::contributionPage( ) );
        
        //add fields for honor search
        $form->addElement( 'text', 'pledge_in_honor_of', ts( "In Honor Of" ) );
        
        $form->assign( 'validPledge', true );
    }
    
    static function searchAction( &$row, $id ) 
    {
    }

    static function tableNames( &$tables ) 
    {
        //add signer table 
        if ( CRM_Utils_Array::value( 'civicrm_pb_pledge', $tables ) ) {
            $tables = array_merge( array( 'civicrm_pb_signer' => 1), $tables );
        }
    }
  
}


