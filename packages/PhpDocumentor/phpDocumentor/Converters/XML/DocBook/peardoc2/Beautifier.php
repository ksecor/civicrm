<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Greg Beaver <cellog@php.net>                                |
// +----------------------------------------------------------------------+


/**
 * XML/Beautifier.php
 *
 * Format XML files containing unknown entities (like all of peardoc)
 *
 * @package phpDocumentor
 * @subpackage Parsers
 * @author   Gregory Beaver <cellog@php.net>
 * @since 1.3.0
 */


/**
 * This is just like XML_Beautifier, but uses {@link phpDocumentor_XML_Beautifier_Tokenizer}
 * @package phpDocumentor
 * @subpackage Parsers
 * @since 1.3.0
 */
class phpDocumentor_peardoc2_XML_Beautifier extends XML_Beautifier {

   /**
    * format a file or URL
    *
    * @access public
    * @param  string    $file       filename
    * @param  mixed     $newFile    filename for beautified XML file (if none is given, the XML string will be returned.)
    *                               if you want overwrite the original file, use XML_BEAUTIFIER_OVERWRITE
    * @param  string    $renderer   Renderer to use, default is the plain xml renderer
    * @return mixed                 XML string of no file should be written, true if file could be written
    * @throws PEAR_Error
    * @uses   _loadRenderer() to load the desired renderer
    */   
    function formatFile($file, $newFile = null, $renderer = "Plain")
    {
        if ($this->apiVersion() != '1.0') {
            return $this->raiseError('API version must be 1.0');
        }
        /**
         * Split the document into tokens
         * using the XML_Tokenizer
         */
        require_once dirname(__FILE__) . '/Tokenizer.php';
        $tokenizer = new phpDocumentor_XML_Beautifier_Tokenizer();
        
        $tokens = $tokenizer->tokenize( $file, true );

        if (PEAR::isError($tokens)) {
            return $tokens;
        }
        
        include_once dirname(__FILE__) . '/Plain.php';
        $renderer = new PHPDoc_XML_Beautifier_Renderer_Plain($this->_options);
        
        $xml = $renderer->serialize($tokens);
        
        if ($newFile == null) {
            return $xml;
        }
        
        $fp = @fopen($newFile, "w");
        if (!$fp) {
            return PEAR::raiseError("Could not write to output file", XML_BEAUTIFIER_ERROR_NO_OUTPUT_FILE);
        }
        
        flock($fp, LOCK_EX);
        fwrite($fp, $xml);
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;    }

   /**
    * format an XML string
    *
    * @access public
    * @param  string    $string     XML
    * @return string    formatted XML string
    * @throws PEAR_Error
    */   
    function formatString($string, $renderer = "Plain")
    {
        if ($this->apiVersion() != '1.0') {
            return $this->raiseError('API version must be 1.0');
        }
        /**
         * Split the document into tokens
         * using the XML_Tokenizer
         */
        require_once dirname(__FILE__) . '/Tokenizer.php';
        $tokenizer = new phpDocumentor_XML_Beautifier_Tokenizer();
        
        $tokens = $tokenizer->tokenize( $string, false );

        if (PEAR::isError($tokens)) {
            return $tokens;
        }

        include_once dirname(__FILE__) . '/Plain.php';
        $renderer = new PHPDoc_XML_Beautifier_Renderer_Plain($this->_options);
        
        $xml = $renderer->serialize($tokens);
        
        return $xml;
    }
}
?>