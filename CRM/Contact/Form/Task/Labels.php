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
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */
require_once 'CRM/Contact/Form/Task.php';

/**
 * This class provides the functionality to save a search
 * Saved Searches are used for saving frequently used queries
 */
class CRM_Contact_Form_Task_Labels extends CRM_Contact_Form_Task {

  /**
     * all the labels in the system
     *
     * @var array
     */
    protected $_labels;
    /**
     * all the locatons in the system
     *
     * @var array
     */
    protected $_location;

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
        CRM_Utils_System::setTitle( ts('Add Labels') );
        // add select for label
        $label = array("5160" => "5160",
                       "5161" => "5161",
                       "5162" => "5162", 
                       "5163" => "5163", 
                       "5164" => "5164", 
                       "8600" => "8600",
                       "L7163" => "L7163");
        
        $this->addElement('select',
                          'label_id',
                          ts('Select Label'),
                          array( '' => ' - select Label - ')+$label,
                          true);
        $this->addFormRule( array( 'CRM_Contact_Form_Task_Labels', 'formRule' ) );
        // add select for Location Type
        $this->addElement('select', 
                          'location_id',
                          ts('Select Location'),
                          array( '' => ' - select Location - ')+CRM_Core_PseudoConstant::locationType(),
                          true);
        $this->addDefaultButtons( ts('Add Labels'));
       
    }
    
    public function formRule(&$fields)
    {
        $error = array();

         // make sure that Label Type is set
        if (! CRM_Utils_Array::value( 'label_id', $fields ) ) {
            $errors['label_id'] = 'Label type is required to Add Labels ';
        }
          if ( !empty($errors) ) {
            $_flag = 1;
            require_once 'CRM/Core/Page.php';
            $assignError =& new CRM_Core_Page(); 
            $assignError->assign('mappingDetailsError', $_flag);
            return $errors;
        } else {
            return true;
        }
       
    }

    
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess()
    {
        self::preProcess( );
        
        $fv               = $this->controller->exportValues($this->_name); 
        $params           = $this->get( 'queryParams' );
        $returnProperties = $this->get( 'returnProperties' );
        
        // set print view, so that print templates are called
         $this->controller->setPrint( true );

        // create the selector, controller and run - store results in session
        $selector   =& new CRM_Contact_Selector($fv,$params,$returnProperties, $this->_action);
        $controller =& new CRM_Core_Selector_Controller($selector , null, null, CRM_Core_Action::VIEW,
                                                        $this, CRM_Core_Selector_Controller::PDF);
        $controller->setEmbedded( true );
        $controller->run();
    
    }
    
     /**
     * Outputs a result set with a given header
     * in the string buffer result
     *
     * @param   string   $header (reference ) column headers
     * @param   string   $rows   (reference ) result set rows
     * @param   boolean  $print should the output be printed
     *
     * @return  mixed    empty if output is printed, else output
     *
     * @access  public
     */
    function createLabel(&$contactRows, &$format)
    {
        require_once 'CRM/Utils/String.php';
        require_once 'CRM/Utils/fpdf.php';
       
        $pdf = new PDF_Label($format,'mm');
        $pdf->Open();
        $pdf->AddPage();
        
        // Print labels
        foreach ($contactRows as $row) {
            $pdf->Add_PDF_Label(sprintf("%s\n%s\n%s\n%s\n%s", utf8_decode($row[sort_name]),utf8_decode($row[street_address]), utf8_decode($row[city]),  utf8_decode($row[postal_code]), utf8_decode($row[state_province])));
        }
        $pdf->Output();
    }
    
}

 
?>
