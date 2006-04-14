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

class CRM_Contribute_BAO_Query {

    static function &getFields( ) {
        require_once 'CRM/Contribute/BAO/Contribution.php';
        $fields =& CRM_Contribute_BAO_Contribution::exportableFields( );
        unset( $fields['contact_id']);
        return $fields;
    }

    /** 
     * if contributions are involved, add the specific contribute fields
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) {

        // if contribute mode add contribution id
        if ( $query->_mode & CRM_Contact_BAO_Query::MODE_CONTRIBUTE ) {
            $query->_select['contribution_id'] = "civicrm_contribution.id as contribution_id";
            $query->_element['contribution_id'] = 1;
            $query->_tables['civicrm_contribution'] = 1;
            $query->_whereTables['civicrm_contribution'] = 1;
        }

        // get contribution_type
        if ( CRM_Utils_Array::value( 'contribution_type', $query->_returnProperties ) ) {
            $query->_select['contribution_type']  = "civicrm_contribution_type.name as contribution_type";
            $query->_element['contribution_type'] = 1;
            $query->_tables['civicrm_contribution'] = 1;
            $query->_tables['civicrm_contribution_type'] = 1;
            $query->_whereTables['civicrm_contribution'] = 1;
            $query->_whereTables['civicrm_contribution_type'] = 1;
        }
    }

    static function where( &$query ) {
        // process to / from date
        $query->dateQueryBuilder( 'civicrm_contribution', 'contribution_date', 'receive_date', 'Contribution Date' );
        $qill = array( );
        if ( isset( $query->_params['contribution_date_from'] ) ) { 
            $revDate = array_reverse( $query->_params['contribution_date_from'] ); 
            $date    = CRM_Utils_Date::format( $revDate ); 
            $format  = CRM_Utils_Date::customFormat( CRM_Utils_Date::format( $revDate, '-' ) ); 
            if ( $date ) { 
                $query->_where[] = "civicrm_contribution.receive_date >= '$date'";  
                $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1; 
                $qill[] = ts( 'greater than "%1"', array( 1 => $format ) ); 
            } 
        }  
 
        if ( isset( $query->_params['contribution_date_to'] ) ) { 
            $revDate = array_reverse( $query->_params['contribution_date_to'] ); 
            $date    = CRM_Utils_Date::format( $revDate ); 
            $format  = CRM_Utils_Date::customFormat( CRM_Utils_Date::format( $revDate, '-' ) ); 
            if ( $date ) { 
                $query->_where[] = " ( civicrm_contribution.receive_date <= '$date' ) ";  
                $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;  
                $qill[] = ts( 'less than "%1"', array( 1 => $format ) ); 
            } 
        } 
         
        if ( ! empty( $qill ) ) { 
            $query->_qill[] = ts('Contribution Date - %1', array( 1 => implode( ' ' . ts('and') . ' ', $qill ) ) ); 
        } 

        // process min/max amount
        $qill = array( ); 
        if ( isset( $query->_params['contribution_min_amount'] ) ) {  
            $amount = $query->_params['contribution_min_amount'];
            if ( $amount > 0 ) {
                $query->_where[] = "civicrm_contribution.total_amount >= $amount";
                $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;  
                $qill[] = ts( 'greater than "%1"', array( 1 => $amount ) );
            } 
        }
    
        if ( isset( $query->_params['contribution_max_amount'] ) ) {  
            $amount = $query->_params['contribution_max_amount'];
            if ( $amount > 0 ) {
                $query->_where[] = "civicrm_contribution.total_amount <= $amount";
                $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;   
                $qill[] = ts( 'less than "%1"', array( 1 => $amount ) );
            }
        }

        if ( ! empty( $qill ) ) {  
            $query->_qill[] = ts('Contribution Amount - %1', array( 1 => implode( ' ' . ts('and') . ' ', $qill ) ) );  
        }  

        if ( CRM_Utils_Array::value( 'contribution_thankyou_date_isnull', $query->_params ) ) {
            $query->_where[] = "civicrm_contribution.thankyou_date is null";
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            $query->_qill[] = ts( 'Contribution Thank-you date is null' );
        }

        if ( CRM_Utils_Array::value( 'contribution_receipt_date_isnull', $query->_params ) ) {
            $query->_where[] = "civicrm_contribution.receipt_date is null";
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            $query->_qill[] = ts( 'Contribution Receipt date is null' );
        }

        if ( CRM_Utils_Array::value( 'contribution_type_id', $query->_params ) ) {
            require_once 'CRM/Contribute/PseudoConstant.php';
            $cType = $query->_params['contribution_type_id'];
            $types = CRM_Contribute_PseudoConstant::contributionType( );
            $query->_where[] = "civicrm_contribution.contribution_type_id = $cType";
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            $query->_qill[] = ts( 'Contribution Type - %1', array( 1 => $types[$cType] ) );
        }

        if ( CRM_Utils_Array::value( 'payment_instrument_id', $query->_params ) ) {
            require_once 'CRM/Contribute/PseudoConstant.php';
            $pi = $query->_params['payment_instrument_id'];
            $pis = CRM_Contribute_PseudoConstant::paymentInstrument( );
            $query->_where[] = "civicrm_contribution.payment_instrument_id = $pi";
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            $query->_qill[] = ts( 'Paid By - %1', array( 1 => $pis[$pi] ) );
        }

        if ( isset( $query->_params['contribution_status'] ) ) {
            switch( $query->_params['contribution_status'] ) {
            case 'Valid':
                $query->_where[] = "civicrm_contribution.cancel_date is null";
                $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
                $query->_qill[]  = ts( 'Contribution Status - Valid' );
                break;

            case 'Cancelled':
                $query->_where[] = "civicrm_contribution.cancel_date is not null";
                $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
                $query->_qill[]  = ts( 'Contribution Status - Cancelled' );
                break;
            }
        }
    }

    static function from( $name, $mode, $side ) {
        $from = null;
        switch ( $name ) {

        case 'civicrm_contribution':
            $from = " INNER JOIN civicrm_contribution ON civicrm_contribution.contact_id = civicrm_contact.id ";
            break;
            
        case 'civicrm_contribution_type':
            if ( $mode & CRM_Contact_BAO_Query::MODE_CONTRIBUTE ) {
                $from = " INNER JOIN civicrm_contribution_type ON civicrm_contribution.contribution_type_id = civicrm_contribution_type.id ";
            } else {
                $from = " $side JOIN civicrm_contribution_type ON civicrm_contribution.contribution_type_id = civicrm_contribution_type.id ";
            }
            break;
            
        case 'civicrm_contribution_product':
            $from = " $side  JOIN civicrm_contribution_product ON civicrm_contribution_product.contribution_id = civicrm_contribution.id";
            break;
            
        case 'civicrm_product':
            $from = " $side  JOIN civicrm_product ON civicrm_contribution_product.product_id =civicrm_product.id ";
            break;
            
        case 'civicrm_payment_instrument':
            $from = " $side  JOIN civicrm_payment_instrument ON civicrm_contribution.payment_instrument_id =civicrm_payment_instrument.id ";
            break;
            
        }
        return $from;
    }

    static function defaultReturnProperties( $mode ) {
        $properties = null;
        if ( $mode & CRM_Contact_BAO_Query::MODE_CONTRIBUTE ) {
            $properties = array(  
                                'contact_type'           => 1, 
                                'sort_name'              => 1, 
                                'display_name'           => 1,
                                'contribution_type'      => 1,
                                'source'                 => 1,
                                'receive_date'           => 1,
                                'thankyou_date'          => 1,
                                'cancel_date'            => 1,
                                'total_amount'           => 1,
                                'accounting_code'        => 1,
                                'payment_instrument'     => 1,
                                'non_deductible_amount'  => 1,
                                'fee_amount'             => 1,
                                'net_amount'             => 1,
                                'trxn_id'                => 1,
                                'invoice_id'             => 1,
                                'currency'               => 1,
                                'cancel_date'            => 1,
                                'cancel_reason'          => 1,
                                'receipt_date'           => 1,
                                'thankyou_date'          => 1,
                                'source'                 => 1,
                                'note'                   => 1,
                                'name'                   => 1,
                                'sku'                    => 1,
                                'product_option'         => 1,
                                'fulfilled_date'         => 1,
                                'start_date'             => 1,
                                'end_date'               => 1,
                                );

            // also get all the custom contribution properties
            $fields = CRM_Core_BAO_CustomField::getFieldsForImport('Contribution');
            if ( ! empty( $fields ) ) {
                foreach ( $fields as $name => $dontCare ) {
                    $properties[$name] = 1;
                }
            }
        }
        return $properties;
    }

}

?>
