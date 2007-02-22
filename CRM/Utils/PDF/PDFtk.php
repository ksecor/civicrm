<?php

define(PDFTK_BIN,'pdftk');
require_once "packages/System/Command.php";

/*
* Created on Feb 19, 2007
*
* To change the template for this generated file go to
* Window - Preferences - PHPeclipse - PHP - Code Templates
*/

class CRM_Utils_PDF_PDFtk {

    private $pdftkBin = '';

    /**
 	 * Returns either a true / false (in the case of specifying output file)
 	 * or
 	 * A stream of PDF data (in the case of no output file);
 	 * 
 	 */
    function fill_form($templateFile,$fdfData,$outputFile = null,$flatten = 0) {
        $args = func_get_args();

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
        //$ret = $cmd->pushCommand($pdftkCommand);
        $ret = call_user_method_array("pushCommand",$cmd,$pdftkOptions);
        //CRM_Core_Error::debug('$ret',$ret);

        //CRM_Core_Error::debug('$cmd->systemCommand',$cmd->systemCommand);


        try {
            $res = $cmd->execute();
            return $outputFile;
        }catch (Exception $e) {
            die("Error running pdftk");
        }

    }

    function merge_files ($files) {
        foreach($files as $file) {
            $args .= " " . escapeshellargs($file);
        }

        //run it;
    }

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
        CRM_Core_Error::debug('$parts',$cmd->systemCommand);
        $cmd->execute();

        //Clean up tmp files
        foreach($parts as $pg_file) {
            unlink($pg_file);
        }

        return true;

        //Just run 'pdftk d7.pdf -burst' and then open the resulting 'pg_0001.pdf' in Acrobat 7 Pro and you now have editable form fields!
    }

    public function cat($inputFiles,$outputFile = '-') {

        $cmd = new System_Command();
        $pdftkCommand[] = PDFTK_BIN;
        foreach($inputFiles as $inputFile) {
            $pdftkCommand[] = "{$inputFile}";
        }
        $pdftkCommand[] = "cat";
        $pdftkCommand[] = "output";
        $pdftkCommand[] = "$outputFile";
        call_user_method_array("pushCommand",$cmd,$pdftkCommand);
        CRM_Core_Error::debug('$parts',$cmd->systemCommand);
        return $cmd->execute();

    }

    public function createXfdfFromArray($array,$templateFile = "") {
        
        $xfdf = new DOMDocument('1.0',"UTF-8");
        $xfdf->formatOutput = true;
        //<xfdf xmlns="http://ns.adobe.com/xfdf/" xml:space="preserve">
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
        
        

   
        //load up the template file, dump the fields, take the output
        //foreach field, chop it off after the last dot.
        //match against the keys in the array
        //add a DOM node for xfdf
        //be pissed at adobe for this crap

    }

    public function dump_data_fields($templateFile,$options = "") {
        $cmd = new System_Command();
        $pdftkCommand[] = PDFTK_BIN;        
        $pdftkCommand[] = "{$templateFile}";
        
        $pdftkCommand[] = "dump_data_fields";
        call_user_method_array("pushCommand",$cmd,$pdftkCommand);
        CRM_Core_Error::debug('$parts',$cmd->systemCommand);
        $form_fields = $cmd->execute();
        return $form_fields;
        
    }
}
?>
