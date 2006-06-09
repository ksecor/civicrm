<?php
/**
 * Classes for use in generating the PayPal SDK.
 *
 * @package Services_PayPal
 */

/**
 * Required classes.
 */
require_once 'Services/PayPal.php';
require_once 'Services/PayPal/SOAP/WSDL.php';

/**
 * Tool class that handles the PayPal-extended WSDL parsing for
 * generating functional and type classes.
 *
 * $Id: Generator.php,v 1.22 2005/06/23 17:03:21 chagenbuch Exp $
 *
 * @package Services_PayPal
 */
class PayPal_SDK_Generator extends SOAP_WSDL
{
    /**
     * Unique list of types defined in the WSDL.
     *
     * @access protected
     *
     * @var array $_allTypes
     */
    var $_allTypes = array();

    /**
     * Constructor. Calls the parent SOAP_WSDL constructor to parse the WSDL.
     *
     * @param optional string $wsdl  The WSDL. Defaults to what's bundled
     *                               with the SDK.
     */
    function PayPal_SDK_Generator($wsdl = null)
    {
        if (is_null($wsdl)) {
            $wsdl = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'PayPalSvc.wsdl';
        }

        parent::SOAP_WSDL($wsdl, array(), WSDL_CACHE_USE, WSDL_CACHE_MAX_AGE, true);
    }

    /**
     * Returns the version of the WSDL that this SDK is built against.
     *
     * @return float  The WSDL version.
     */
    function getWSDLVersion()
    {
        if (isset($this->definition['ns:version'])) {
            return (float)$this->definition['ns:version'];
        } else {
            return (float)$this->definition['version'];
        }
    }

    /**
     * Builds the endpoint mapping file which is included in the SDK.
     *
     * @param optional string $endpointXmlFile  The endpoint map to use. Defaults
     *                                          to the one bundled with the SDK.
     *
     * @return string  PHP code suitable for writing to a file.
     */
    function buildEndpointMap($endpointXmlFile = null)
    {
        if (is_null($endpointXmlFile)) {
            $endpointXmlFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'paypal-endpoints.xml';
        }

        $xml = @file_get_contents($endpointXmlFile);
        if (!$xml) {
            return Services_PayPal::raiseError("Unable to load the endpoint XML");
        }

        $ep = &new PayPal_SDK_EndpointMappingParser();
        $result = $ep->parse($xml);
        if (!$result) {
            return Services_PayPal::raiseError("Unable to parse the endpoint XML: $ep->error");
        }

        return "<?php\n/**\n * THIS MAPPING IS AUTOMATICALLY GENERATED. DO NOT EDIT.\n" .
            " * Generated on: " . strftime('%x %X') . "\n * @package Services_PayPal\n */\n" .
            "\$PayPalEndpoints = " . var_export($ep->mapping, true) . ";\n";
    }

    /**
     * Builds PHP class definitions for all of the complexTypes found
     * in the WSDL.
     *
     * @return array  An array of class definitions.
     */
    function buildTypes()
    {
        // Make a hash of all type names so we can know what types are
        // defined.
        $this->_allTypes = array();
        foreach ($this->complexTypes as $types) {
            foreach ($types as $name => $type) {
                $this->_allTypes[$name] = $name;
            }
        }

        // Now generate the PHP code.
        $classes = array();
        foreach ($this->complexTypes as $types) {
            foreach ($types as $name => $type) {
                if (empty($name)) {
                    continue;
                }

                $classes[$name] = $this->_buildType($name, $type);
            }
        }

        return $classes;
    }

    /**
     * Builds the dynamically generated portion of the CallerServices
     * API.
     *
     * @return string  The dynamically generated API functions (PHP code).
     */
    function buildMethods()
    {
        $methods = '';
        foreach (array_keys($this->services[$this->service]['ports']) as $key) {
            $portcode = $this->generateProxyCode($this->services[$this->service]['ports'][$key]);
            if (is_null($portcode)) {
                return Services_PayPal::raiseError("Failed to generate code for port $key");
            }
            $methods .= $portcode;
        }

        return $methods;
    }

    /**
     * List all of the methods in all portTypes.
     *
     * @return array  An array of method names.
     */
    function listMethods()
    {
        $methods = array();
        foreach (array_keys($this->services[$this->service]['ports']) as $key) {
            $primaryBinding = preg_replace("/^(.*:)/", '', $this->services[$this->service]['ports'][$key]['binding']);
            $portType = preg_replace("/^(.*:)/", '', $this->bindings[$primaryBinding]['type']);

            $methods = array_merge($methods, array_keys($this->portTypes[$portType]));
        }

        return $methods;
    }

    /**
     * Describe all methods in all portTypes, including their request
     * and return types.
     *
     * @return array  An array of method name => ('request' => 'type', 'response' => 'type')
     */
    function describeMethods()
    {
        $methods = array();

        foreach (array_keys($this->services[$this->service]['ports']) as $key) {
            $primaryBinding = preg_replace("/^(.*:)/", '', $this->services[$this->service]['ports'][$key]['binding']);
            $portType = preg_replace("/^(.*:)/", '', $this->bindings[$primaryBinding]['type']);

            foreach ($this->portTypes[$portType] as $method => $operation) {
                $param = $this->messages[$operation['input']['message']];
                $param = $param[key($param)];
                $param = $this->elements[$param['namespace']][$param['type']];
                $param = $param['elements'][key($param['elements'])];
                if (isset($param['ref'])) {
                    $q =& new QName($param['ref']);
                    $param = $this->elements[$q->ns][$q->name]['type'];
                } elseif (isset($param['type'])) {
                    $param = $param['type'];
                }

                $result = $this->messages[$operation['output']['message']];
                $result = $result[key($result)];
                $result = $this->elements[$result['namespace']][$result['type']]['type'];

                $methods[$method] = array('param' => $param,
                                          'result' => $result);
            }
        }

        return $methods;
    }

    /**
     * Generates stub code from the WSDL that can be saved to a file
     * or eval'd into existence. Overrides the SOAP_WSDL
     * implementation to do PayPal-specific type and endpoint
     * handling. Note that that means there are several assumptions
     * specific to PayPal's services, such as that each method takes
     * only one argument.
     *
     * @param string $port  The WSDL port we're currently generating.
     *
     * @return string  The methods for $port.
     */
    function generateProxyCode($port)
    {
        // Currently do not support HTTP ports.
        if ($port['type'] != 'soap') {
            return null;
        }

        // Get the binding, from that get the port type.
        $primaryBinding = $port['binding'];
        $primaryBinding = preg_replace("/^(.*:)/", '', $primaryBinding);
        $portType = $this->bindings[$primaryBinding]['type'];
        $portType = preg_replace("/^(.*:)/", '', $portType);
        $style = $this->bindings[$primaryBinding]['style'];
        $portName = preg_replace("/^(.*:)/", '', $port['name']);

        $methods = '';

        foreach ($this->portTypes[$portType] as $opname => $operation) {
            $soapaction = isset($this->bindings[$primaryBinding]['operations'][$opname]['soapAction']) ?
                $this->bindings[$primaryBinding]['operations'][$opname]['soapAction'] :
                null;
            if (isset($this->bindings[$primaryBinding]['operations'][$opname]['style'])) {
                $opstyle = $this->bindings[$primaryBinding]['operations'][$opname]['style'];
            } else {
                $opstyle = $style;
            }
            $use = $this->bindings[$primaryBinding]['operations'][$opname]['input']['use'];
            if ($use == 'encoded') {
                $namespace = $this->bindings[$primaryBinding]['operations'][$opname]['input']['namespace'];
            } else {
                $bindingType = $this->bindings[$primaryBinding]['type'];
                $ns = $this->portTypes[$bindingType][$opname]['input']['namespace'];
                $namespace = $this->namespaces[$ns];
            }

            $args = '';
            $argarray = '';
            $comments = '';
            $wrappers = '';

            foreach ($operation['input'] as $argname => $argtype) {
                if ($argname == 'message') {
                    foreach ($this->messages[$argtype] as $_argname => $_argtype) {
                        $comments = '';
                        if ($opstyle == 'document' && $use == 'literal' &&
                            $_argtype['name'] == 'parameters') {
                            // The type or element referred to is used
                            // for parameters.
                            $elattrs = null;
                            $element = $_argtype['element'];
                            $el = $this->elements[$_argtype['namespace']][$_argtype['type']];

                            if ($el['complex']) {
                                $namespace = $this->namespaces[$_argtype['namespace']];
                                // Need to wrap the parameters in a
                                // SOAP_Value.
                            }
                            if (isset($el['elements'])) {
                                foreach ($el['elements'] as $elname => $elattrs) {
                                    // Is the element a complex type?
                                    if (isset($this->complexTypes[$elattrs['namespace']][$elname])) {
                                        $comments .= $this->_complexTypeArg($args, $argarray, $_argtype, $_argname);
                                    } else {
                                        $this->_addArg($args, $argarray, $elname);
                                    }
                                }
                            }
                            if ($el['complex'] && $argarray) {
                                $wrapname = '{' . $this->namespaces[$_argtype['namespace']].'}' . $el['name'];
                                $comments .= "        \${$el['name']} =& new SOAP_Value('$wrapname', false, \$v = array($argarray));\n";
                                $argarray = "'{$el['name']}' => \${$el['name']}";
                            }
                        } else {
                            if (isset($_argtype['element'])) {
                                // Element argument.
                                $comments = $this->_elementArg($args, $argarray, $_argtype, $_argtype['type']);
                                $el = $this->elements[$_argtype['namespace']][$_argtype['type']];
                                $firstargname = array_shift(array_keys($el['elements']));
                            } else {
                                // Complex type argument.
                                $comments = $this->_complexTypeArg($args, $argarray, $_argtype, $_argname);
                            }
                        }
                    }
                }
            }

            // Validate entries.
            if (!$this->_validateString($opname)) {
                return null;
            }
            if (!$this->_validateString($namespace)) {
                return null;
            }
            if (!(is_null($soapaction) || $this->_validateString($soapaction))) {
                return null;
            }

            if ($argarray) {
                $argarray = "array($argarray)";
            } else {
                $argarray = 'null';
            }

            // Include method documentation if we have it.
            if (isset($operation['documentation'])) {
                $operation['documentation'] = preg_replace('/\s+/', ' ', $operation['documentation']);
                $methods .= "    /**\n     * " . wordwrap($operation['documentation'], 80, "\n     * ") . "\n     */\n";
            }

            $methods .= "    function &$opname($args)\n    {\n" .
                "        \$start = \$this->_getMicroseconds();\n\n" .
                "        // Handle type objects.\n" .
                "        if (is_a($args, 'XSDType')) {\n" .
                "            {$args}->setVersion(PAYPAL_WSDL_VERSION);\n" .
                "            $args = {$args}->getSoapValue('$firstargname', '$namespace');\n" .
                "        }\n\n" .
                "        // Make sure we can find a valid WSDL endpoint for this method.\n" .
                "        \$res = \$this->setEndpoint('$portName', PAYPAL_WSDL_VERSION);\n" .
                "        if (Services_PayPal::isError(\$res)) {\n" .
                "            \$this->_logTransaction('$opname', \$this->_getElapsed(\$start), \$res);\n" .
                "            return \$res;\n" .
                "        }\n\n" .
                "$comments$wrappers" .
                "        \$result = \$this->call('$opname',\n" .
                "                              \$v = $argarray,\n" .
                "                              array('namespace' => '$namespace',\n" .
                "                                    'soapaction' => '$soapaction',\n" .
                "                                    'style' => '$opstyle',\n" .
                "                                    'use' => '$use'" .
                ($this->trace?"\n,                                    'trace' => 1" : '') . "));\n\n" .
                "        \$response = \$this->getResponseObject(\$result, '{$this->elements[$operation['output']['namespace']][$operation['output']['message']]['type']}');\n" .
                "        \$this->_logTransaction('$opname', \$this->_getElapsed(\$start), \$response);\n" .
                "        return \$response;\n" .
                "    }\n\n";
        }

        return $methods;
    }

    /**
     * Makes a string safe for use as a PHP variable or function name.
     *
     * @access private
     *
     * @param string $name  The string to sanitize.
     *
     * @return string  The PHP-safe name.
     */
    function _phpName($name)
    {
        return preg_replace('/[ .\-\(\)]+/', '_', $name);
    }

    /**
     * Generate a complete PHP class file for one WSDL type.
     *
     * @access private
     *
     * @param string $name  The name of the type.
     * @param array  $type  A hash with the type's definition.
     *
     * @return string  A PHP class file.
     */
    function _buildType($name, $type)
    {
        $parentClass = 'XSDType';
        if (!empty($type['extends'])) {
            if (isset($this->_allTypes[$type['extends']])) {
                $parentClass = $type['extends'];
            } else {
                // This is probably a complexType using simpleContent.
                $parentClass = 'XSDSimpleType';
            }
        }
        $parentClass = $this->_phpName($parentClass);
        $name = $this->_phpName($name);

        $namespace = isset($type['namespace']) ? (isset($this->namespaces[$type['namespace']]) ?
                                                  $this->namespaces[$type['namespace']] : null) : null;

        $vars = '';
        $funcs = '';
        $elements = array();
        if (!empty($type['elements'])) {
            foreach ($type['elements'] as $ename => $element) {
                $ens = $namespace;
                $ename = $this->_phpName($ename);
                if (isset($element['ref'])) {
                    $ref =& new QName($element['ref']);
                    if (isset($this->elements[$ref->ns][$ref->name])) {
                        $element = array_merge($this->elements[$ref->ns][$ref->name],
                                               array('minOccurs' => isset($element['minOccurs']) ? $element['minOccurs'] : null));
                        $ens = $this->namespaces[$ref->ns];
                    }
                }

                $elements[$ename] = array(
                    'required' => isset($element['minOccurs']) && $element['minOccurs'] == 0 ? false : true,
                    'type' => isset($element['type']) ? $element['type'] : null,
                    'namespace' => $ens,
                    );

                if (isset($element['documentation'])) {
                    $vars.= "    /**\n     * " . wordwrap($element['documentation'], 80, "\n     * ") . "\n     */\n";
                }
                $vars .= "    var \$$ename;\n\n";
                $funcs .= "    function get$ename()\n    {\n        return \$this->$ename;\n    }\n";
                $funcs .= "    function set$ename(\$$ename, \$charset = 'iso-8859-1')\n    {\n        \$this->$ename = \$$ename;\n        \$this->_elements['$ename']['charset'] = \$charset;\n    }\n";
            }
        }

        $attributes = array();
        if (!empty($type['attribute'])) {
            $attributes = $type['attribute'];
        }

        $const = "    function $name()\n    {\n";
        $const .= "        parent::$parentClass();\n";
        $const .= "        \$this->_namespace = " . var_export($namespace, true) . ";\n";
        if (count($elements)) {
            $const .= "        \$this->_elements = array_merge(\$this->_elements,\n            " .
                str_replace("\n", "\n            ", var_export($elements, true)) . ");\n";
        }
        if (count($attributes)) {
            $const .= "        \$this->_attributes = array_merge(\$this->_attributes,\n            " .
                str_replace("\n", "\n            ", var_export($attributes, true)) . ");\n";
        }
        $const .= "    }\n";

        $docs = $name;
        if (isset($type['documentation'])) {
            $docs .= "\n * \n * " . wordwrap($type['documentation'], 80, "\n * ");
        }

        return "<?php\n" .
            "/**\n" .
            " * @package Services_PayPal\n" .
            " */\n\n" .
            "/**\n" .
            " * Make sure our parent class is defined.\n" .
            " */\n" .
            "require_once 'Services/PayPal/Type/$parentClass.php';\n\n" .
            "/**\n" .
            " * $docs\n" .
            " *\n" .
            " * @package Services_PayPal\n" .
            " */\n" .
            "class $name extends $parentClass\n{\n" . $vars . $const . "\n" . $funcs . "}\n";
    }

}

/**
 * Parser for the XML endpoints mapping file.
 *
 * @package Services_PayPal
 */
class PayPal_SDK_EndpointMappingParser
{
    /**
     * XML parser resource.
     * @var resource $parser
     */
    var $parser = null;

    /**
     * The array that all the parsed information gets dumped into.
     * @var array $structure
     */
    var $mapping = array();

    /**
     * Pointer to current tree point.
     * @var array $pointers
     */
    var $pointers = array();

    /**
     * Error string.
     * @var string $error
     */
    var $error = '';

    /**
     * Actually do the parsing. Separated from the constructor just in
     * case you want to set any other options on the parser, load
     * initial data, whatever.
     *
     * @access protected
     *
     * @param $data  The XML data to parse as RSS.
     */
    function parse($data)
    {
        $this->_init();

        // Sanity checks.
        if (!$this->parser) {
            $this->error = 'Could not find xml parser handle';
            return false;
        }

        // Parse.
        if (!@xml_parse($this->parser, $data)) {
            $this->error = sprintf('XML error: %s at line %d', xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser));
            return false;
        }

        // Clean up.
        xml_parser_free($this->parser);

        return true;
    }

    /**
     * Start collecting data about a new element.
     */
    function startElement($parser, $name, $attribs)
    {
        $parts = explode(':', $name);
        $tag = array_pop($parts);
        $ns = implode(':', $parts);

        switch ($tag) {
        case 'endpoints':
            $this->mapping = array();
            $this->pointers = array();
            break;

        case 'wsdl':
            $this->mapping[] = array('min' => $attribs['min-version'],
                                     'max' => $attribs['max-version'],
                                     'environments' => array());
            $this->pointers[] =& $this->mapping[count($this->mapping) - 1]['environments'];
            break;

        case 'environment':
            $this->pointers[count($this->pointers) - 1][strtolower($attribs['name'])] = array();
            $this->pointers[] =& $this->pointers[count($this->pointers) - 1][strtolower($attribs['name'])];
            break;

        case 'port':
            $this->pointers[] =& $this->pointers[count($this->pointers) - 1][$attribs['name']];
            break;
        }
    }

    /**
     * Handle the ends of XML elements - wrap up whatever we've been
     * putting together and store it for safekeeping.
     */
    function endElement($parser, $name)
    {
        $parts = explode(':', $name);
        $tag = array_pop($parts);
        $ns = implode(':', $parts);

        switch ($tag) {
        case 'endpoints':
            unset($this->pointers);
            break;

        case 'wsdl':
        case 'environment':
        case 'port':
            array_pop($this->pointers);
            break;
        }
    }

    /**
     * The handler for character data encountered in the XML file.
     */
    function characterData($parser, $data)
    {
        if (preg_match('|\S|', $data)) {
            $this->pointers[count($this->pointers) - 1] = trim($data);
        }
    }

    /**
     * Handles things that we don't recognize.
     */
    function defaultHandler($parser, $data)
    {
    }

    /**
     * Initialize the XML parser.
     *
     * @access private
     */
    function _init()
    {
        // Create the XML parser.
        $this->parser = xml_parser_create();
        xml_set_object($this->parser, $this);
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
        xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
        xml_set_element_handler($this->parser, 'startElement', 'endElement');
        xml_set_character_data_handler($this->parser, 'characterData');
        xml_set_default_handler($this->parser, 'defaultHandler');
    }

}
