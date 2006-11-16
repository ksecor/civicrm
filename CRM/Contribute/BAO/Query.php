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

class CRM_Contribute_BAO_Query 
{
    
    /**
     * static field for all the export/import contribution fields
     *
     * @var array
     * @static
     */
    static $_contributionFields = null;
    
    /**
     * Function get the import/export fields for contribution
     *
     * @return array self::$_contributionFields  associative array of contribution fields
     * @static
     */
    static function &getFields( ) 
    {
        if ( ! self::$_contributionFields ) {
            self::$_contributionFields = array( );
            
            require_once 'CRM/Contribute/BAO/Contribution.php';
            $fields =& CRM_Contribute_BAO_Contribution::exportableFields( );
            
            //fix for adding the option values filed in 
            require_once "CRM/Core/DAO/OptionValue.php";
            $option = CRM_Core_DAO_OptionValue::import( );
            
            foreach (array_keys( $option ) as $id ) {
                $optionName = $option[$id];
            }
            $nameTitle = array('contrib_status'            => array('name' => 'contrib_status',
                                                                    'title'=> 'Contribution Status'),
                               );
            foreach ( $nameTitle as $name => $attribs ) {
                $optionFields[$name] = $optionName;
                list( $tableName, $fieldName ) = explode( '.', $optionName['where'] );  
                $optionFields[$name]['where'] = $name . '.' . $fieldName;
                foreach ( $attribs as $key => $val ) {
                    $optionFields[$name][$key] = $val;
                }
            }
            if ( !empty ( $optionFields ) ) {
                $fields =  array_merge( $fields ,$optionFields );
            }
            unset( $fields['contact_id']);
            unset( $fields['note'] ); 
            self::$_contributionFields = $fields;
        }
        return self::$_contributionFields;
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
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0, 13 ) == 'contribution_' ) {
                self::whereClauseSingle( $query->_params[$id], $query );
            }
            
        }
    }

    static function whereClauseSingle( &$values, &$query ) {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
        
        $fields = array( );
        $fields = self::getFields();

        switch ( $name ) {
       
        case 'contribution_date':
        case 'contribution_date_low':
        case 'contribution_date_high':
            // process to / from date
            $query->dateQueryBuilder( $values,
                                      'civicrm_contribution', 'contribution_date', 'receive_date', 'Contribution Date' );
            return;

        case 'contribution_amount':
        case 'contribution_amount_low':
        case 'contribution_amount_high':
            // process min/max amount
            $query->numberRangeBuilder( $values,
                                        'civicrm_contribution', 'contribution_amount', 'total_amount', 'Contribution Amount' );
            return;

        case 'contribution_total_amount':
            $query->_where[$grouping][] = "civicrm_contribution.total_amount $op " . CRM_Utils_Type::escape( $value, "Money" );
            $query->_qill[$grouping ][] = ts( 'Contribution Total Amount %1 %2', array( 1 => $op, 2 => $value ) );
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            return;
            
        case 'contribution_thankyou_date_isnull':
            $query->_where[$grouping][] = "civicrm_contribution.thankyou_date is null";
            $query->_qill[$grouping ][] = ts( 'Contribution Thank-you date is null' );
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            return;

        case 'contribution_receipt_date_isnull':
            $query->_where[$grouping][] = "civicrm_contribution.receipt_date is null";
            $query->_qill[$grouping ][] = ts( 'Contribution Receipt date is null' );
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            return;

        case 'contribution_type_id':
            require_once 'CRM/Contribute/PseudoConstant.php';
            $cType = $value;
            $types = CRM_Contribute_PseudoConstant::contributionType( );
            $query->_where[$grouping][] = "civicrm_contribution.contribution_type_id = $cType";
            $query->_qill[$grouping ][] = ts( 'Contribution Type - %1', array( 1 => $types[$cType] ) );
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            return;
            
        case 'contribution_page_id':
            require_once 'CRM/Contribute/PseudoConstant.php';
            $cPage = $value;
            $pages = CRM_Contribute_PseudoConstant::contributionPage( );
            $query->_where[$grouping][] = "civicrm_contribution.contribution_page_id = $cPage";
            $query->_qill[$grouping ][] = ts( 'Contribution Page - %1', array( 1 => $pages[$cPage] ) );
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            return;
            
        case 'contribution_payment_instrument_id':
            require_once 'CRM/Contribute/PseudoConstant.php';
            $pi = $value;
            $pis = CRM_Contribute_PseudoConstant::paymentInstrument( );
            $query->_where[$grouping][] = "civicrm_contribution.payment_instrument_id = $pi";
            $query->_qill[$grouping ][] = ts( 'Paid By - %1', array( 1 => $pis[$pi] ) );
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            return;
        case 'contribution_in_honor_of':
            $name = trim( $value ); 
            $newName = str_replace(',' , " " ,$name );
            $pieces =  explode( ' ', $newName ); 
            foreach ( $pieces as $piece ) { 
                $value = strtolower(addslashes(trim($piece)));
                $value = "'%$value%'";
                $sub[] = " ( LOWER(contact_b.sort_name) LIKE $value )";
            }
            
            $query->_where[$grouping][] = ' ( ' . implode( '  OR ', $sub ) . ' ) '; 
            $query->_qill[$grouping][]  = ts( 'Honor name like - "%1"', array( 1 => $name ) );
            $query->_tables['civicrm_contact_b'] = $query->_whereTables['civicrm_contact_b'] = 1;
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            
            return;
        case 'contribution_status':
            require_once "CRM/Core/OptionGroup.php";
            $statusValues = CRM_Core_OptionGroup::values("contribution_status");
            if ($value != "All") {
                $query->_where[$grouping][] = "civicrm_contribution.contribution_status_id $op " .$value ;
                $query->_qill[$grouping ][]  = ts( 'Contribution Status -' ) . $statusValues[$value];
                $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
                return;
            }
            return;
            
        case 'contribution_source':
            $value = strtolower( addslashes( $value ) );
            if ( $wildcard ) {
                $value = "%$value%"; 
                $op    = 'LIKE';
            }
            $query->_where[$grouping][] = "LOWER( civicrm_contribution.source ) $op '$value'";
            $query->_qill[$grouping][]  = "Contribution Source $op \"$value\"";
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            
            return;

        case 'contribution_test':
            $query->_where[$grouping][] = " civicrm_contribution.is_test $op '$value'";
            if ( $value ) {
                $query->_qill[$grouping][]  = "Test Contributions Only";
            }
            $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            
            return;

        default :
            //all other elements are handle in this case
            $fldName = substr($name, 13 );
            $whereTable = $fields[$fldName];
            $value = trim($value);
            //date fields
            $dateFields = array ( 'receive_date', 'cancel_date', 'receipt_date', 'thankyou_date', 'fulfilled_date' ) ;
            if ( in_array($fldName, $dateFields) ) {
                $date = CRM_Utils_Date::format( $value );
                if ( $date ) {
                    $query->_where[$grouping][] = "$whereTable[where] $op $date";
                    $value = CRM_Utils_Date::customFormat( $value );
                }
            } else {
                if ($op == "IN" ||$op == "NOT IN" ) {
                    $query->_where[$grouping][] = "LOWER( $whereTable[where] ) $op $value";
                } else {
                    $value = strtolower( addslashes( $value ) );
                    if ( $wildcard ) {
                        $value = "%$value%"; 
                        $op    = 'LIKE';
                    }
                    $query->_where[$grouping][] = "LOWER( $whereTable[where] ) $op '$value'";
                }
            }

            $query->_qill[$grouping][]  = "$whereTable[title] $op \"$value\"";            
            list( $tableName, $fieldName ) = explode( '.', $whereTable['where'], 2 );  
            $query->_tables[$tableName] = $query->_whereTables[$tableName] = 1;
            if ($tableName == 'civicrm_contribution_product') {
                $query->_tables['civicrm_product']      = $query->_whereTables['civicrm_product'     ] = 1;
                $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            } else {
                $query->_tables['civicrm_contribution'] = $query->_whereTables['civicrm_contribution'] = 1;
            }
        }        
    }

    static function from( $name, $mode, $side ) {
        $from = null;
        switch ( $name ) {

        case 'civicrm_contribution':
            $from = " INNER JOIN civicrm_contribution ON civicrm_contribution.contact_id = contact_a.id ";
            break;
            
        case 'civicrm_contribution_type':
            if ( $mode & CRM_Contact_BAO_Query::MODE_CONTRIBUTE ) {
                $from = " INNER JOIN civicrm_contribution_type ON civicrm_contribution.contribution_type_id = civicrm_contribution_type.id ";
            } else {
                $from = " $side JOIN civicrm_contribution_type ON civicrm_contribution.contribution_type_id = civicrm_contribution_type.id ";
            }
            break;

        case 'civicrm_contribution_page':
            $from = " $side JOIN civicrm_contribution_page ON civicrm_contribution.contribution_page ON civicrm_contribution.contribution_page.id";
            break;

        case 'civicrm_product':
            $from = " $side  JOIN civicrm_contribution_product ON civicrm_contribution_product.contribution_id = civicrm_contribution.id";
            $from .= " $side  JOIN civicrm_product ON civicrm_contribution_product.product_id =civicrm_product.id ";
            
            break;
            
        case 'civicrm_payment_instrument':
            $from = " $side  JOIN civicrm_payment_instrument ON civicrm_contribution.payment_instrument_id =civicrm_payment_instrument.id ";
            break;
        case 'civicrm_contact_b':
            $from .= " $side JOIN civicrm_contact contact_b ON (civicrm_contribution.honor_contact_id = contact_b.id )";
            
            break;

        case 'contrib_status':
            $from .= " $side JOIN civicrm_option_group option_group_contrib_status ON (option_group_contrib_status.name = 'contribution_status')";
            $from .= " $side JOIN civicrm_option_value contrib_status ON (civicrm_contribution.contribution_status_id = contrib_status.value AND option_group_contrib_status.id = contrib_status.option_group_id ) ";
            break;

        }
        return $from;
    }

    static function defaultReturnProperties( $mode ) {
        $properties = null;
        if ( $mode & CRM_Contact_BAO_Query::MODE_CONTRIBUTE ) {
            $properties = array(  
                                'contact_type'            => 1, 
                                'sort_name'               => 1, 
                                'display_name'            => 1,
                                'contribution_type'       => 1,
                                'contribution_source'     => 1,
                                'receive_date'            => 1,
                                'thankyou_date'           => 1,
                                'cancel_date'             => 1,
                                'total_amount'            => 1,
                                'accounting_code'         => 1,
                                'payment_instrument'      => 1,
                                'non_deductible_amount'   => 1,
                                'fee_amount'              => 1,
                                'net_amount'              => 1,
                                'trxn_id'                 => 1,
                                'invoice_id'              => 1,
                                'currency'                => 1,
                                'cancel_date'             => 1,
                                'cancel_reason'           => 1,
                                'receipt_date'            => 1,
                                'thankyou_date'           => 1,
                                'product_name'            => 1,
                                'sku'                     => 1,
                                'product_option'          => 1,
                                'fulfilled_date'          => 1,
                                'contribution_start_date' => 1,
                                'contribution_end_date'   => 1,
                                'is_test'                 => 1,
                                'contribution_status_id'  => 1,
                                'contrib_status'          => 1,
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


    /**
     * add all the elements shared between contribute search and advnaced search
     *
     * @access public 
     * @return void
     * @static
     */ 
    static function buildSearchForm( &$form ) {
        
        //added contribution source
        $form->addElement('text', 'contribution_source', ts('Contribution Source'), CRM_Core_DAO::getAttribute('CRM_Contribute_DAO_Contribution', 'source') );
        
        // Date selects for date 
        $form->add('date', 'contribution_date_low', ts('Contribution Dates - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('contribution_date_low', ts('Select a valid date.'), 'qfDate'); 
 
        $form->add('date', 'contribution_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('contribution_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->add('text', 'contribution_amount_low', ts('Minimum Amount'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $form->addRule( 'contribution_amount_low', ts( 'Please enter a valid money value (e.g. 9.99).' ), 'money' );

        $form->add('text', 'contribution_amount_high', ts('Maximum Amount'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $form->addRule( 'contribution_amount_high', ts( 'Please enter a valid money value (e.g. 99.99).' ), 'money' );

        require_once 'CRM/Contribute/PseudoConstant.php';
        $form->add('select', 'contribution_type_id', 
                   ts( 'Contribution Type' ),
                   array( '' => ts( '- select -' ) ) +
                   CRM_Contribute_PseudoConstant::contributionType( ) );

        $form->add('select', 'contribution_page_id', 
                   ts( 'Contribution Page' ),
                   array( '' => ts( '- select -' ) ) +
                   CRM_Contribute_PseudoConstant::contributionPage( ) );

        
        $form->add('select', 'contribution_payment_instrument_id', 
                   ts( 'Payment Instrument' ), 
                   array( '' => ts( '- select -' ) ) +
                   CRM_Contribute_PseudoConstant::paymentInstrument( ) );

        $status = array( );
        
        require_once "CRM/Core/OptionGroup.php";
        $statusValues = CRM_Core_OptionGroup::values("contribution_status");
        foreach ( $statusValues as $key => $val ) {
            $status[] = $form->createElement( 'radio', null, null, $val , $key );    
        }
        
  
        $status[] = $form->createElement( 'radio', null, null, ts( 'All' )      , 'All'       );

        $form->addGroup( $status, 'contribution_status', ts( 'Contribution Status' ) );

        // add null checkboxes for thank you and receipt
        $form->addElement( 'checkbox', 'contribution_thankyou_date_isnull', ts( 'Thank-you date not set?' ) );
        $form->addElement( 'checkbox', 'contribution_receipt_date_isnull' , ts( 'Receipt date not set?' ) );

        //add fields for honor search
        $form->addElement( 'text', 'contribution_in_honor_of', ts( "In Honor Of" ) );
        $form->addElement( 'checkbox', 'contribution_test' , ts( 'Find Test Contributions ?' ) );
        
        // add all the custom  searchable fields
        require_once 'CRM/Core/BAO/CustomGroup.php';
        $groupDetails = CRM_Core_BAO_CustomGroup::getGroupDetail( null, true, array( 'Contribution' ) );
        if ( $groupDetails ) {
            require_once 'CRM/Core/BAO/CustomField.php';
            $form->assign('contributeGroupTree', $groupDetails);
            foreach ($groupDetails as $group) {
                foreach ($group['fields'] as $field) {
                    $fieldId = $field['id'];                
                    $elementName = 'custom_' . $fieldId;
                    CRM_Core_BAO_CustomField::addQuickFormElement( $form,
                                                                   $elementName,
                                                                   $fieldId,
                                                                   false, false, true );
                }
            }
        }

        $form->assign( 'validCiviContribute', true );
    }

    static function addShowHide( &$showHide ) {
        $showHide->addHide( 'contributeForm' );
        $showHide->addShow( 'contributeForm_show' );
    }

    static function searchAction( &$row, $id ) {
    }

}

?>
