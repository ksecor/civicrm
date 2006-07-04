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
     *  Class to print labels in Avery or custom formats
     * functionality and smarts to the base PDF_Label.
     *
     * @author Donald A. Lobo <lobo@yahoo.com>
     * @copyright Donald A. Lobo (c) 2005
     * 
     *
     */
require_once ('packages/ufpdf/ufpdf.php');
class PDF_Label extends UFPDF {

    // Private properties
    var $_Avery_Name    = '';                 // Name of format
    var $_Margin_Left    = 0;                 // Left margin of labels
    var $_Margin_Top    = 0;                  // Top margin of labels
    var $_X_Space         = 0;                // Horizontal space between 2 labels
    var $_Y_Space         = 0;                // Vertical space between 2 labels
    var $_X_Number         = 0;               // Number of labels horizontally
    var $_Y_Number         = 0;               // Number of labels vertically
    var $_Width         = 0;                  // Width of label
    var $_Height         = 0;                 // Height of label
    var $_Char_Size        = 10;              // Character size
    var $_Line_Height    = 10;                // Default line height
    var $_Metric         = 'mm';              // Type of metric for labels.. Will help to calculate good values
    var $_Metric_Doc     = 'mm';              // Type of metric for the document
    //    var $_Font_Name        = 'Arial';       // Name of the font
    var $_Font_Name        = 'symbol';        // Name of the font
    var $_COUNTX = 1;
    var $_COUNTY = 1;
    
    // Listing of labels size
    protected  $_Avery_Labels = array (
                                       '5160'=>array('name'=>'5160', 'paper-size'=>'letter', 'metric'=>'mm',
                                                     'lMargin'=>1.762, 'tMargin'=>10.7, 'NX'=>3, 'NY'=>10,
                                                     'SpaceX'=>3.175, 'SpaceY'=>0, 'width'=>66.675, 'height'=>25.4,
                                                     'font-size'=>8),
                                       '5161'=>array('name'=>'5161', 'paper-size'=>'letter', 'metric'=>'mm',  
                                                     'lMargin'=>0.967, 'tMargin'=>10.7, 'NX'=>2, 'NY'=>10, 
                                                     'SpaceX'=>3.967, 'SpaceY'=>0, 'width'=>101.6,
                                                     'height'=>25.4, 'font-size'=>8),
                                       '5162'=>array('name'=>'5162', 'paper-size'=>'letter', 'metric'=>'mm', 
                                                     'lMargin'=>0.97, 'tMargin'=>20.224, 'NX'=>2, 'NY'=>7, 
                                                     'SpaceX'=>4.762, 'SpaceY'=>0, 'width'=>100.807, 
                                                     'height'=>35.72, 'font-size'=>8),
                                       '5163'=>array('name'=>'5163', 'paper-size'=>'letter', 'metric'=>'mm',
                                                     'lMargin'=>1.762,'tMargin'=>10.7, 'NX'=>2,
                                                     'NY'=>5, 'SpaceX'=>3.175, 'SpaceY'=>0, 'width'=>101.6,
                                                     'height'=>50.8, 'font-size'=>8),
                                       '5164'=>array('name'=>'5164', 'paper-size'=>'letter', 'metric'=>'in',
                                                     'lMargin'=>0.148, 'tMargin'=>0.5, 'NX'=>2, 'NY'=>3, 
                                                     'SpaceX'=>0.2031, 'SpaceY'=>0, 'width'=>4.0, 'height'=>3.33,
                                                     'font-size'=>12),
                                       '8600'=>array('name'=>'8600', 'paper-size'=>'letter', 'metric'=>'mm',
                                                     'lMargin'=>7.1, 'tMargin'=>19, 'NX'=>3, 'NY'=>10,
                                                     'SpaceX'=>9.5, 'SpaceY'=>3.1, 'width'=>66.6,
                                                     'height'=>25.4, 'font-size'=>8),
                                       'L7163'=>array('name'=>'L7163', 'paper-size'=>'A4', 'metric'=>'mm', 'lMargin'=>5,
                                                      'tMargin'=>15, 'NX'=>2, 'NY'=>7, 'SpaceX'=>25, 'SpaceY'=>0,
                                                      'width'=>99.1, 'height'=>38.1, 'font-size'=>9)
                                       );
   
    /**
     * Constructor 
     *
     * @param $format type of label ($_AveryValues)
     * @param unit type of unit used we can define your label properties in inches by setting metric to 'in'
     *
     * @access public
     */

   function PDF_Label ($format, $unit='mm') {
       if (is_array($format)) {
           // Custom format
           $Tformat = $format;
       } else {
           // Avery format
           $Tformat = $this->_Avery_Labels[$format];
       }
       
       parent::UFPDF('P', $Tformat['metric'], $Tformat['paper-size']);
       $this->_Set_Format($Tformat);
       $this->Set_Font_Name('Arial');
       $this->SetMargins(0,0);
       $this->SetAutoPageBreak(false);
       
       $this->_Metric_Doc = $unit;
       // Start at the given label position
       //  if ($posX > 1) $posX--; else $posX=0;
       //         if ($posY > 1) $posY--; else $posY=0;
       //         if ($posX >=  $this->_X_Number) $posX =  $this->_X_Number-1;
       //         if ($posY >=  $this->_Y_Number) $posY =  $this->_Y_Number-1;
       //         $this->_COUNTX = $posX;
       //         $this->_COUNTY = $posY;
       
       if($format == $_Avery_Labels['name']){
           if ($_Avery_Labels['lMargin'] > 1) $_Avery_Labels['lMargin']--; else $_Avery_Labels['lMargin']=0;
           if ($_Avery_Labels['tMargin'] > 1) $_Avery_Labels['tMargin']--; else $_Avery_Labels['tMargin']=0;
           if ($_Avery_Labels['lMargin'] >=  $this->_X_Number) $_Avery_Labels['lMargin'] =  $this->_X_Number-1;
           if ($_Avery_Labels['tMargin'] >=  $this->_Y_Number) $_Avery_Labels['tMargin'] =  $this->_Y_Number-1;
           $this->_COUNTX = $_Avery_Labels['lMargin'];
           $this->_COUNTY = $_Avery_Labels['tMargin'];
       }
   }
    
   /*
    * function to convert units (in to mm, mm to in)
    *
    */ 
    function _Convert_Metric ($value, $src, $dest) {
        if ($src != $dest) {
            $tab['in'] = 39.37008;
            $tab['mm'] = 1000;
            return $value * $tab[$dest] / $tab[$src];
        } else {
            return $value;
        }
    }
    /*
     * function to Give the height for a char size given.
     */
    function _Get_Height_Chars($pt) {
        // Array matching character sizes and line heights
        $_Table_Hauteur_Chars = array(6=>2, 7=>2.5, 8=>3, 9=>4, 10=>5, 11=>6, 12=>7, 13=>8, 14=>9, 15=>10);
        if (in_array($pt, array_keys($_Table_Hauteur_Chars))) {
            return $_Table_Hauteur_Chars[$pt];
        } else {
            return 100; // There is a prob..
        }
    }
    /*
     * function to convert units (in to mm, mm to in)
     * $format Type of $_Avery_Name
     */ 
    function _Set_Format($format) {
        $this->_Metric         = $format['metric'];
        $this->_Avery_Name     = $format['name'];
        $this->_Margin_Left    = $this->_Convert_Metric ($format['lMargin'], $this->_Metric, $this->_Metric_Doc);
        $this->_Margin_Top    = $this->_Convert_Metric ($format['tMargin'], $this->_Metric, $this->_Metric_Doc);
        $this->_X_Space     = $this->_Convert_Metric ($format['SpaceX'], $this->_Metric, $this->_Metric_Doc);
        $this->_Y_Space     = $this->_Convert_Metric ($format['SpaceY'], $this->_Metric, $this->_Metric_Doc);
        $this->_X_Number     = $format['NX'];
        $this->_Y_Number     = $format['NY'];
        $this->_Width         = $this->_Convert_Metric ($format['width'], $this->_Metric, $this->_Metric_Doc);
        $this->_Height         = $this->_Convert_Metric ($format['height'], $this->_Metric, $this->_Metric_Doc);
        $this->Set_Font_Size($format['font-size']);
    }
    /*
     * function to set the character size
     * $pt weight of character
     */
    function Set_Font_Size($pt) {
        if ($pt > 3) {
            $this->_Char_Size = $pt;
            $this->_Line_Height = $this->_Get_Height_Chars($pt);
            $this->SetFontSize($this->_Char_Size);
        }
    }
    /*
     * Method to change font name
     *
     * $fontname name of font 
     */
    function Set_Font_Name($fontname) {
        if ($fontname != '') {
            $this->_Font_Name = $fontname;
            $this->SetFont($this->_Font_Name);
        }
    }
    
    /*
     * function to Print a label
     */
    function Add_PDF_Label($texte) {
        // We are in a new page, then we must add a page
        if (($this->_COUNTX ==0) and ($this->_COUNTY==0)) {
            $this->AddPage();
        }
        
        $_PosX = $this->_Margin_Left+($this->_COUNTX*($this->_Width+$this->_X_Space));
        $_PosY = $this->_Margin_Top+($this->_COUNTY*($this->_Height+$this->_Y_Space));
        $this->SetXY($_PosX+3, $_PosY+3);
        $this->MultiCell($this->_Width, $this->_Line_Height, $texte);
        $this->_COUNTY++;
        
        if ($this->_COUNTY == $this->_Y_Number) {
            // End of column reached, we start a new one
            $this->_COUNTX++;
            $this->_COUNTY=0;
        }
        
        if ($this->_COUNTX == $this->_X_Number) {
            // Page full, we start a new one
            $this->_COUNTX=0;
            $this->_COUNTY=0;
        }
    }
    
}
?>
