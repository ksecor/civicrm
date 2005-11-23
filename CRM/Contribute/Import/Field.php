<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/


require_once 'CRM/Utils/Type.php';
require_once 'CRM/Contribute/PseudoConstant.php';

class CRM_Contribute_Import_Field {
  
    /**#@+
     * @access protected
     * @var string
     */

    /**
     * name of the field
     */
    public $_name;

    /**
     * title of the field to be used in display
     */
    public $_title;

    /**
     * type of field
     * @var enum
     */
    public $_type;

    /**
     * is this field required
     * @var boolean
     */
    public $_required;

    /**
     * data to be carried for use by a derived class
     * @var object
     */
    public $_payload;

    /**
     * regexp to match the CSV header of this column/field
     * @var string
     */
     public $_headerPattern;

    /**
     * regexp to match the pattern of data from various column/fields
     * @var string
     */
     public $_dataPattern;

    /**
     * value of this field
     * @var object
     */
    public $_value;



    function __construct( $name, $title, $type = CRM_Utils_Type::T_INT, $headerPattern = '//', $dataPattern = '//') {
        $this->_name      = $name;
        $this->_title     = $title;
        $this->_type      = $type;
        $this->_headerPattern = $headerPattern;
        $this->_dataPattern = $dataPattern;
    
        $this->_value     = null;
    }

    function resetValue( ) {
        $this->_value     = null;
    }

    /**
     * the value is in string format. convert the value to the type of this field
     * and set the field value with the appropriate type
     */
    function setValue( $value ) {
        $this->_value = $value;
    }

    function validate( ) {
        //  echo $this->_value."===========<br>";
        $message = '';

        if ( $this->_value === null ) {
            return true;
        }

        switch ($this->_name) {
        case 'contact_id':
            return CRM_Utils_Rule::integer($this->_value);
            break;
        case 'receive_date':
        case 'cancel_date':
        case 'receipt_date':
        case 'thankyou_date':
            return CRM_Utils_Rule::date($this->_value);
            break;
        case 'non_deductible_amount':
        case 'total_amount':
        case 'fee_amount':
        case 'net_amount':
            return CRM_Utils_Rule::money($this->_value);
            break;
        case 'currency':
            return CRM_Utils_Rule::currencyCode($this->_value);
            break;
        case 'contribution_type':
            static $contributionTypes = null;
            if ($contributionTypes == null) {
                $contributionTypes =& CRM_Contribute_PseudoConstant::contributionType();
            }
            if (!in_array($this->_value, $contributionTypes)) return false;
            break;
        case 'payment_instrument':
            static $paymentInstruments = null;
            if ($paymentInstruments == null) {
                $paymentInstruments =& CRM_Contribute_PseudoConstant::paymentInstrument();
            }
            if (!in_array($this->_value, $paymentInstruments)) return false;
            break;
        default:
            return true;
        }
        return true;
    }

}

?>
