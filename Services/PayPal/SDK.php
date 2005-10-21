<?php
/**
 * @package Services_PayPal
 */

/**
 * Include needed files.
 */
require_once 'Services/PayPal.php';
require_once 'Services/PayPal/SDK/Generator.php';

/**
 * Base SDK maintenance class that provides methods for generating and updating
 * the PayPal SDK.
 *
 * $Id: SDK.php,v 1.21 2005/06/23 17:03:21 chagenbuch Exp $
 *
 * @package Services_PayPal
 */
class PayPal_SDK
{
    /**
     * The PayPal_SDK_Generator we use to make PHP code.
     *
     * @access private
     */
    var $_generator;

    /**
     * Constructor.
     *
     * @param optional string $wsdl  The WSDL. Defaults to what's bundled
     *                               with the SDK.
     */
    function PayPal_SDK($wsdl = null)
    {
        $this->_generator =& new PayPal_SDK_Generator($wsdl);
    }

    /**
     * Write the endpoint mapping file.
     *
     * @param string $endpointPhpFile  The full file path to write to.
     * @param string $endpointXmlFile  (optional) The endpoint XML to use.
     *                                 Defaults to the bundled version.
     */
    function writeEndpointMap($endpointPhpFile, $endpointXmlFile = null)
    {
        $phpcode = $this->_generator->buildEndpointMap($endpointXmlFile);
        if (Services_PayPal::isError($phpcode)) {
            return $phpcode;
        }

        $fp = fopen($endpointPhpFile, 'w');
        if (!$fp) {
            return Services_PayPal::raiseError("Unable to write $endpointPhpFile.");
        }
        if (!fwrite($fp, $phpcode)) {
            return Services_PayPal::raiseError("Unable to write $endpointPhpFile.");
        }
        return fclose($fp);
    }

    /**
     * Write the generated type class files.
     *
     * @param string $typesDir  The directory to write the .php files in.
     */
    function writeTypes($typesDir)
    {
        $types = $this->_generator->buildTypes();
        if (Services_PayPal::isError($types)) {
            return $types;
        }

        foreach ($types as $name => $phpcode) {
            $file = $typesDir . DIRECTORY_SEPARATOR . $name . '.php';
            $fp = fopen($file, 'w');
            if (!$fp) {
                return Services_PayPal::raiseError("Unable to write $file.");
            }
            if (!fwrite($fp, $phpcode)) {
                return Services_PayPal::raiseError("Unable to write $file.");
            }
            fclose($fp);
        }

        return true;
    }

    /**
     * Write a new CallerServices.php based on CallerServices.php.in
     *
     * @param string $phpFile  The full file path to write to.
     */
    function writeCallerServices($phpFile)
    {
        $methods = $this->_generator->buildMethods();
        if (Services_PayPal::isError($methods)) {
            return $methods;
        }

        $phpTemplate = file_get_contents(dirname(__FILE__) . '/CallerServices.php.in');
        if (!$phpTemplate) {
            return Services_PayPal::raiseError('Unable to read CallerServices template.');
        }

        $phpTemplate = str_replace('@@GENERATED_FUNCTIONS@@', trim($methods), $phpTemplate);
        $phpTemplate = str_replace('@@WSDL_VERSION@@', $this->_generator->getWSDLVersion(), $phpTemplate);

        $fp = fopen($phpFile, 'w');
        if (!$fp) {
            return Services_PayPal::raiseError("Unable to write $phpFile.");
        }
        if (!fwrite($fp, $phpTemplate)) {
            return Services_PayPal::raiseError("Unable to write $phpFile.");
        }
        return fclose($fp);
    }

}
