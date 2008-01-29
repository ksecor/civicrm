<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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

require_once 'CRM/Contact/Form/Task.php';

/**
 * This class helps to print the labels for contacts
 * 
 */
class CRM_Contact_Form_Task_Label extends CRM_Contact_Form_Task 
{

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess()
    {
        $this->set( 'contactIds', $this->_contactIds );
        parent::preProcess( );
    }

    /**
     * Build the form 
     *    
     * @access public
     * @return void
     */
    function buildQuickForm()
    {
        CRM_Utils_System::setTitle( ts('Make Mailing Labels') );

        //add select for label
        $label = array("5160" => "5160",
                       "5161" => "5161",
                       "5162" => "5162", 
                       "5163" => "5163", 
                       "5164" => "5164", 
                       "8600" => "8600",
                       "L7160" => "L7160",
                       "L7161" => "L7161",
                       "L7163" => "L7163");
        
        $this->add('select', 'label_id', ts('Select Label'), array( '' => ts('- select label -')) + $label, true);

        // add select for Location Type
        $this->addElement('select', 'location_type_id', ts('Select Location'),
                          array( '' => ts('Primary')) + CRM_Core_PseudoConstant::locationType(), true);
        $this->addDefaultButtons( ts('Make Mailing Labels'));
       
    }
    
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess ( )
    {
        $fv = $this->controller->exportValues($this->_name); 
        $config =& CRM_Core_Config::singleton();

        //get the address format sequence from the config file
        require_once 'CRM/Core/BAO/Preferences.php';
       
        $sequence = CRM_Core_BAO_Preferences::value( 'mailing_sequence' );
        
        foreach ($sequence as $v) {
            $address[$v] = 1;
        }
        if ( array_key_exists( 'postal_code',$address ) ) {
            $address['postal_code_suffix'] = 1;
        }
        
        //build the returnproperties
        $returnProperties = array ('display_name' => 1 );
        $format  = CRM_Core_BAO_Preferences::value( 'individual_name_format' );
        $format .= CRM_Core_BAO_Preferences::value( 'mailing_format' );
        $matches = array();
        preg_match_all( '/(?<!\{|\\\\)\{(\w+\.\w+)\}(?!\})/',
                        $format,
                        $matches,
                        PREG_PATTERN_ORDER);
        if ( $matches[1] ) {
            foreach ( $matches[1] as $token ) {
                list($type,$name) = split( '\.', $token, 2 );
                if ( $name ) {
                    $returnProperties["{$name}"] = 1;
                }
            }
        }
        
        if ($fv['location_type_id']) {
            $locType = CRM_Core_PseudoConstant::locationType();
            $locName = $locType[$fv['location_type_id']];
            $location = array ('location' => array("{$locName}"  => $address ) ) ;
            $returnProperties = array_merge( $returnProperties , $location );
        } else {
            $returnProperties = array_merge( $returnProperties , $address );
        }
        
        $rows = array( );
        
        //get the contacts information
        $params = array( );
        foreach ( $this->_contactIds  as $key => $contactID ) {
            $params[] = array( CRM_Core_Form::CB_PREFIX . $contactID,
                               '=', 1, 0, 1);
        }
        
        $custom = array( );
        foreach ( $returnProperties as $name => $dontCare ) {
            $cfID = CRM_Core_BAO_CustomField::getKeyID( $name );
            if ( $cfID ) {
                $custom[] = $cfID;
            }
        }
        require_once 'CRM/Contact/BAO/Query.php';
        
        $query   =& new CRM_Contact_BAO_Query( $params, $returnProperties );
        $details = $query->apiQuery( $params, $returnProperties );
        
        foreach ( $this->_contactIds as $value ) {
            foreach ( $custom as $cfID ) {
                if ( isset ( $details[0][$value]["custom_{$cfID}"] ) ) {
                    $details[0][$value]["custom_{$cfID}"] = 
                        CRM_Core_BAO_CustomField::getDisplayValue( $details[0][$value]["custom_{$cfID}"],$cfID, $details[1] );
                }
            }
            $contact = $details['0']["{$value}"];
            
            if ( is_a( $contact, 'CRM_Core_Error' ) ) {
                return null;
            }
            
            // we need to remove all the "_id"
            unset( $contact['contact_id'] );
            
            if ( $locName && CRM_Utils_Array::value( $locName, $contact ) ) {
                // If location type is not priamry, $contact contains
                // one more array as "$contact[$locName] = array( values... )"
                $found = false;
                
                foreach ( $sequence as $sequenceName ) {
                    // we are interested in only those
                    // $contact[$locName] which contains any
                    // of the address sequences
                    if ( CRM_Utils_Array::value( $sequenceName, $contact[$locName] ) ) {
                        $found = true;
                        break;
                    }
                }
                
                if ( ! $found ) {
                    continue;
                }
                
                // again unset all "_id" from $contact[$locName]
                // except country_id, state_province_id
                
                if ( CRM_Utils_Array::value( 'location_type_id', $contact[$locName] ) ) {
                    unset( $contact[$locName]['location_type_id'] );
                }
                
                if ( CRM_Utils_Array::value( 'address_id', $contact[$locName] ) ) {
                    unset( $contact[$locName]['address_id'] );
                }
                
                if (  CRM_Utils_Array::value( 'county_id', $contact )  ) {
                    unset( $contact['county_id'] );
                }
                
                //Add contact Details
                if( CRM_Contact_BAO_Contact::getContactType( $value ) == 'Individual' ) {
                    $rows[$value]['first_name']           = $contact['first_name'];
                    $rows[$value]['middle_name']          = $contact['middle_name'];
                    $rows[$value]['last_name']            = $contact['last_name'];
                    $rows[$value]['display_name']         = $contact['display_name'];
                    $rows[$value]['individual_prefix']    = $contact['individual_prefix'];
                    $rows[$value]['individual_suffix']    = $contact['individual_suffix'];
                } else {
                    $rows[$value]['display_name'] = $contact['display_name'];
                }
                
                // now create the rows for generating mailing labels
                foreach( CRM_Utils_Array::value( $locName, $contact ) as $field => $fieldValue ) {
                    $rows[$value][$field] = $fieldValue;
                }
            } else {
                $found = false;
                
                foreach ( $sequence as $sequenceName) {
                    // we are interested in only those
                    // $contact which contains any
                    // of the address sequences
                    
                    if ( CRM_Utils_Array::value( $sequenceName, $contact ) ) {
                        $found = true;
                        break;
                    }
                }
                
                if ( ! $found ) {
                    continue;
                }
                
                // again unset all "_id" from $contact
                // except country_id, state_province_id
                
                if (  CRM_Utils_Array::value( 'address_id', $contact )  ) {
                    unset( $contact['address_id'] );
                }
                if (  CRM_Utils_Array::value( 'county_id', $contact )  ) {
                    unset( $contact['county_id'] );
                }
                
                // now create the rows for generating mailing labels
                foreach ( $contact as $field => $fieldValue ) {
                    $rows[$value][$field] = $fieldValue;
                }
            }
        }
        
        // format the addresses according to CIVICRM_ADDRESS_FORMAT (CRM-1327)
        require_once 'CRM/Utils/Address.php';
        foreach ($rows as $id => $row) {
            $row['id'] = $id;
            $formatted = CRM_Utils_Address::format( $row, 'mailing_format', null, true );
            // CRM-2211: UFPDF doesn't have bidi support; use the PECL fribidi package to fix it.
            // On Ubuntu (possibly Debian?) be aware of http://pecl.php.net/bugs/bug.php?id=12366
            // Due to FriBidi peculiarities, this can't be called on
            // a multi-line string, hence the explode+implode approach.
            if (function_exists('fribidi_log2vis')) {
                $lines = explode("\n", $formatted);
                foreach($lines as $i => $line) {
                    $lines[$i] = fribidi_log2vis($line, FRIBIDI_AUTO, FRIBIDI_CHARSET_UTF8);
                }
                $formatted = implode("\n", $lines);
            }
            $rows[$id]= array( $formatted );
        }

        //call function to create labels
        self::createLabel($rows, $fv['label_id']);
        exit(1);
    }
    
     /**
      * function to create labels (pdf)
      *
      * @param   array    $contactRows   assciated array of contact data
      * @param   string   $format   format in which labels needs to be printed
      *
      * @return  null      
      * @access  public
      */
    function createLabel(&$contactRows, &$format)
    {
        require_once 'CRM/Utils/String.php';
        require_once 'CRM/Utils/PDF/Label.php';
        
        $pdf = new CRM_Utils_PDF_Label($format,'mm');
        $pdf->Open();
        $pdf->AddPage();
        $pdf->AddFont('DejaVu Sans', '', 'DejaVuSans.php');
        $pdf->SetFont('DejaVu Sans');
        
        //build contact string that needs to be printed
        $val = null;
        foreach ($contactRows as $row => $value) {
            foreach ($value as $k => $v) {
                $val .= "$v\n";
            }

            $pdf->AddPdfLabel($val);
            $val = '';
        }
        $pdf->Output();
    }
}

 
?>
