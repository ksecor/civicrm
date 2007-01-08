<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                  |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
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
    public function postProcess()
    {
        $fv = $this->controller->exportValues($this->_name); 
        
        $config =& CRM_Core_Config::singleton();

        //get the address format sequence from the config file
        foreach ($config->addressSequence as $v) {
            $address[$v] = 1;
        }
        
        //build the returnproperties
        $returnProperties = array ('display_name' => 1) ;

        if ($fv['location_type_id']) {
            $locType = CRM_Core_PseudoConstant::locationType();
            $locName = $locType[$fv['location_type_id']];
            $location = array ('location' => array("{$locName}"  => $address)) ;
            $returnProperties = array_merge($returnProperties , $location);
        } else {
            $returnProperties = array_merge($returnProperties , $address);
        }
        
        //get the contact information
        foreach ($this->_contactIds as $value) {
            $params  = array( 'contact_id'=> $value );
            require_once 'api/Contact.php';
            $contact[$value] =& crm_fetch_contact( $params, $returnProperties );
            if ( is_a( $contact, 'CRM_Core_Error' ) ) {
                return null;
            }
        }
        //format the contact array before sending tp pdf
        foreach ($contact as $k => $v) {
            foreach ($v as $k1 => $v1) {
                if ( substr($k1, -3, 3) == '_id' ) {
                    continue;
                }
                if (is_array($v1)) {
                    foreach ($v1 as $k2 => $v2) {
                        if ( substr($k2, -3, 3) == '_id' || $k2 == 'location_type' ) {
                            continue;
                        }
                        $rows[$k][$k2] = $v2;
                    }
                } else {
                    $rows[$k][$k1] = $v1;
                }
            }
        }

        // format the addresses according to CIVICRM_ADDRESS_FORMAT (CRM-1327)
        require_once 'CRM/Utils/Address.php';
        foreach ($rows as $id => $row) {
            $formatted = CRM_Utils_Address::format($row);
            $rows[$id] = array($row['display_name'], $formatted);
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
