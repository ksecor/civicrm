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

define(PDFTK_BIN,'pdftk');
require_once "packages/System/Command.php";

/*
* Created on Feb 19, 2007
*
* To change the template for this generated file go to
* Window - Preferences - PHPeclipse - PHP - Code Templates
*/

class CRM_Utils_PDF_PDFtk {
    
    private $metadataFields = array ('Creator','Producer','ModDate','CreationDate','NumberOfPages');

    private $pdftkBin = '';
    
    
     static function &compose( $fileName,
                              $searchPath,
                              &$values,
                              $numPages = 1,
                              $echo    = true,
                              $output  = 'College_Match_App',
                              $creator = 'CiviCRM',
                              $author  = 'http://www.civicrm.org/',
                              $title   = '2006 College Match Scholarship Application' ) {
        try {
            $buffer = CRM_Utils_PDF_PDFtk::fill_form($fileName,CRM_Utils_PDF_PDFtk::createXfdfFromArray($values,$fileName),null,1);
            
            $metaData = array('Creator' => $creator);
            $metaData = array('Author' => $author);
            $metaData = array('title' => $title);
            $buffer = CRM_Utils_PDF_PDFtk::fill_data($buffer,$metaData);
            $outputFile = tmpfile();
            file_put_contents($outputFile);
            CRM_Utils_PDF_PDFtk::fixPDFV7($outputFile);
            
            
            if($echo) {
                CRM_Utils_System::download($output . ".pdf" , 'application/pdf', file_get_contents($outputFile) );                   
            } else {
                return file_get_contents($outputFile);                
            }
            
        }catch (Exception $e) {
            die($e->getMessage());
        }
        
                              }
                              

    
    /**
     * fill_form: Takes a PDF form template, fdf or xfdf data and optionally an output file name.  Merges in the data and outputs.
     *
     * @param string $templateFile
     * @param string $fdfData contents of fdf or xfdf file | a file name
     * @param string $outputFile
     * @param bool $flatten If supplies pdftk will make the form fields no longer editable.
     * @return binary data or a filename.
     */
    function fill_form($templateFile,$fdfData,$outputFile = null,$flatten = 0) {
        
        $cmd = new System_Command();
        $pdftkOptions = array();
        $pdftkOptions[] = PDFTK_BIN;
        $pdftkOptions[] =  "{$templateFile}";
        $pdftkOptions[] =  "fill_form";


        if (is_file($fdfData)) {
            $pdftkOptions[] =  "$fdfData";
        }else {
            $pdftkOptions[] =   "-";
            $cmd->pushCommand('echo',$fdfData);
            $cmd->pushOperator("|");

        }

        if ($outputFile) {
            $pdftkOptions[] =  "output";
            $pdftkOptions[] =  "$outputFile";
        }else {
            $pdftkOptions[] =  "output -";
        }

        if ( $flatten ) {
            $pdftkOptions[] =  "flatten";
        }
        
        $ret = call_user_method_array("pushCommand",$cmd,$pdftkOptions);
        //CRM_Core_Error::debug('$ret',$ret);

        //CRM_Core_Error::debug('$cmd->systemCommand',$cmd->systemCommand);

        try {          
            $res = $cmd->execute();            
            if (!$outputFile) { 
               return $res;
            }
            return $outputFile;
        }catch (Exception $e) {
            die("Error running pdftk");
        }

    }
    
    function dump_data($pdfFile) {
        
    }
    
    /**
     * fills metadata
     *
     * @param unknown_type $pdfFile
     * @param unknown_type $data
     */
    function fill_data($buffer,$data = array()) {
        
    }
    
    function fill_data_from_file($pdfFile,$data = array()) {
        
    }

    /**
     * Takes XML input, runs an xslt transformation to generate xfdf
     *
     * @param string $xml
     * @param string $templateFile // not used, but should generate meta-data in the resultant xfdf
     * @return string xml
     */
    public function createXfdfFromXML($xml,$templateFile) {
        $config = & CRM_Core_Config :: singleton();
        //CRM_Core_Error::debug('a',$xml);
        // Load the XML source
        try {


            $xmlDoc = new DOMDocument;
            $xmlDoc->loadXML($xml);

            $xsl = new DOMDocument;
            //TEMP: Needs to be move to generic resource dir
            echo $xsl->load('/civicrm/v1.6-civicrm-tmf/CRM/TMF/Form/Task/xfdf.xsl');


            // Configure the transformer
            $proc = new XSLTProcessor;
            $proc->importStyleSheet($xsl); // attach the xsl rules

            return $proc->transformToXML($xmlDoc);

        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * fixFDFV7 small hack to get pdftk generated merged forms to be readable by older versions of acrobat.
     *
     * @param string $file
     * @return unknown
     */
    public function fixPDFV7($file) {
        $cmd = new System_Command();
        $pdftkCommand[] = PDFTK_BIN;
        $pdftkCommand[] = "{$file}";
        $pdftkCommand[] = "burst";
        $pdftkCommand[] = "output";
        $pdftkCommand[] = dirname($file) . "/pg%02d_".basename($file);
        call_user_method_array("pushCommand",$cmd,$pdftkCommand);
        //CRM_Core_Error::debug('$parts',$cmd->systemCommand);
        $cmd->execute();

        $pdftkCommand = array();

        $cmd = new System_Command();
        $pdftkCommand[] = PDFTK_BIN;
        $findCmd = dirname($file) . " -maxdepth 0 -name " . "*pg*_" . basename($file);
        //CRM_Core_Error::debug('$findCmd',$findCmd);

        $parts = @System::find($findCmd);
        //CRM_Core_Error::debug('$parts',$parts);
        foreach($parts as $pg_file) {
            $pdftkCommand[] = $pg_file;
        }

        $pdftkCommand[] = "cat";
        $pdftkCommand[] = "output";
        $pdftkCommand[] = "$file";


        call_user_method_array("pushCommand",$cmd,$pdftkCommand);
        //CRM_Core_Error::debug('$parts',$cmd->systemCommand);
        $cmd->execute();

        //Clean up tmp files
        foreach($parts as $pg_file) {
            unlink($pg_file);
        }

        return true;

        //Just run 'pdftk d7.pdf -burst' and then open the resulting 'pg_0001.pdf' in Acrobat 7 Pro and you now have editable form fields!
    }

    /**
     * Joins two pdfs together
     *
     * @param array $inputFiles
     * @param string $outputFile If not provided, output will be returned.
     * @return binary output of cat operation or true;
     */
    public function cat($inputFiles,$outputFile = '-') {
      
      $hackedFileName = '';
      //this is because the PEAR toolkit keeps screwing up the return to buffer        
      // Using a tmp filename instead.
      $config =& CRM_Core_Config::singleton();
      if (!$outputFile || $outputFile == '-') {
        $hackedFileName = tempnam($config->uploadDir,'ccrm_'); 
        $outputFile = $hackedFileName;
      }
        
        $cmd = new System_Command();
        $pdftkCommand[] = PDFTK_BIN;
        foreach($inputFiles as $inputFile) {
            $pdftkCommand[] = "{$inputFile}";
        }
        $pdftkCommand[] = "cat";
        $pdftkCommand[] = "output";
        $pdftkCommand[] = "$outputFile";
        
        call_user_method_array("pushCommand",$cmd,$pdftkCommand);
        //CRM_Core_Error::debug('cat command',$cmd->systemCommand);
        if ($hackedFileName) {
          $cmd->execute();
          return file_get_contents($hackedFileName);
        }else {
          return $cmd->execute();
        }
        

    }

    /**
     * Creates an xfdf from an Array.  This required some hacking to get field names to match up if file is generated in Adobe Designer. 
     * Hopefully this can be eliminated in future versions.  In Adobe Designer, every new page becomes a new subform.  So a field placed
     * on the first page will be called RootForm[0].#subform[0].fieldname[0].  The same field on the 2nd page is RootForm[0].#subform[1].fieldname[0].
     * So an array with simply the key "fieldname" won't work.  This function dumps the current data fields in the template file, and matches
     * against the last portion (after the last dot).  So both of the previous fieldnames using different subforms will match "fieldname"
     *
     * @param array $array
     * @param string $templateFile
     * @return unknown
     */
    public function createXfdfFromArray(&$array,$templateFile = "") {
        
        $xfdf = new DOMDocument('1.0',"UTF-8");
        $xfdf->formatOutput = true;

        $root = $xfdf->appendChild(new DOMElement('xfdf'));

        $root->appendChild(new DOMAttr("xmlns","http://ns.adobe.com/xfdf/"));
        $root->appendChild(new DOMAttr("xml:space","preserve"));
        $fieldsNode = $root->appendChild(new DOMElement('fields'));
        
        if($templateFile) {
            if(!is_file($templateFile)) {
                trigger_error("Tempalte file not found",E_ERROR);
            }
            $form_fields = CRM_Utils_PDF_PDFtk::dump_data_fields($templateFile);            
            
            $fields = array();
            
            $ret = preg_match_all("/FieldName: (.*)\.(.*)(\[[0-9]\])\n/",$form_fields,$fields);        
            
            $dataFields = array_keys($array);
            foreach($fields[1] as $key => $fieldPath) {
                if(isset($array[$fields[2][$key]])) {
                    $fieldNode = $fieldsNode->appendChild(new DOMElement('field'));
                    $fieldNode->appendChild(new DOMAttr("name",$fieldPath . "." . $fields[2][$key] . $fields[3][$key]));
                    $valueNode = $fieldNode->appendChild(new DOMElement('value'));
                    $valueNode->appendChild(new DOMText($array[$fields[2][$key]]));                    
                }
            }
            
            $idsNode = $root->appendChild(new DOMElement('ids'));
            $idsNode->appendChild(new DOMAttr("original"));
            $idsNode->appendChild(new DOMAttr("modified"));
            $fNode = $root->appendChild(new DOMElement('f'));
            $fNode->appendChild(new DOMAttr("href"));
            
     
            return $xfdf->saveXML();
        
        }
    }

    /**
     * Wrapper for dump_data_fields function
     *
     * @param string $templateFile
     * @param array $options not used currently
     * @return string
     */
    public function dump_data_fields($templateFile,$options = "") {
        $cmd = new System_Command();
        $pdftkCommand[] = PDFTK_BIN;        
        $pdftkCommand[] = "{$templateFile}";
        
        $pdftkCommand[] = "dump_data_fields";
        call_user_method_array("pushCommand",$cmd,$pdftkCommand);
        //CRM_Core_Error::debug('$parts',$cmd->systemCommand);
                
        try {
        $form_fields = $cmd->execute();
        } catch (Exception $e) {
          CRM_Core_Error::debug('$e',$e);
          die();
        }
        return $form_fields;
        
    }
}

