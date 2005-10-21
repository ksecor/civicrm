<?php
/**
 * File containing the API calling code. Partially generated from the
 * WSDL - CallerServices.php.in contains the base file with a
 * placeholder for generated functions and the WSDL version.
 *
 * @package Services_PayPal
 */

/**
 * Load files we depend on.
 */
require_once 'Services/PayPal.php';
require_once 'Services/PayPal/SOAP/Client.php';
require_once 'Services/PayPal/Type/XSDType.php';
require_once 'Log.php';

/**
 * The WSDL version the SDK is built against.
 */
define('PAYPAL_WSDL_VERSION', 2);

/**
 * Interface class that wraps all WSDL ports into a unified API for
 * the user. Also handles PayPal-specific details like type handling,
 * error handling, etc.
 *
 * $Id: CallerServices.php.in,v 1.22 2005/06/23 17:03:21 chagenbuch Exp $
 *
 * @package Services_PayPal
 */
class CallerServices extends SOAP_Client
{
    /**
     * The profile to use in API calls.
     *
     * @access protected
     *
     * @var APIProfile $_profile
     */
    var $_profile;

    /**
     * The portType/environment -> endpoint map.
     *
     * @access protected
     *
     * @var array $_endpointMap
     */
    var $_endpointMap;

    /**
     * What level should we log at? Valid levels are:
     *   PEAR_LOG_ERR   - Log only severe errors.
     *   PEAR_LOG_INFO  - (default) Date/time of operation, operation name, elapsed time, success or failure indication.
     *   PEAR_LOG_DEBUG - Full text of SOAP requests and responses and other debugging messages.
     *
     * See the PayPal SDK User Guide for more details on these log
     * levels.
     *
     * @access protected
     *
     * @var integer $_logLevel
     */
    var $_logLevel = PEAR_LOG_INFO;

    /**
     * If we're logging, what directory should we create log files in?
     * Note that a log name coincides with a symlink, logging will
     * *not* be done to avoid security problems. File names are
     * <DateStamp>.PayPal.log.
     *
     * @access protected
     *
     * @var string $_logFile
     */
    var $_logDir = '/tmp';

    /**
     * The PEAR Log object we use for logging.
     *
     * @access protected
     *
     * @var Log $_logger
     */
    var $_logger;

    /**
     * Construct a new CallerServices object.
     *
     * @param APIProfile $profile  The profile with the username, password,
     *                             and any other information necessary to use
     *                             the SDK.
     */
    function CallerServices($profile)
    {
        // Initialize the SOAP Client.
        parent::SOAP_Client(null);

        // Store the API profile.
        $this->setAPIProfile($profile);

        // SSL CA certificate.
        $this->setOpt('curl', CURLOPT_CAINFO, dirname(__FILE__) . '/cert/api_cert_chain.crt');

        // Set options from the profile.
        $this->setOpt('curl', CURLOPT_SSLCERT, $profile->getCertificateFile());
        if ($profile->getCertificatePassword()) {
            $this->setOpt('curl', CURLOPT_SSLCERTPASSWD, $profile->getCertificatePassword());
        }
        $this->setOpt('trace', 1);

        // Load the endpoint map.
        include 'Services/PayPal/wsdl/paypal-endpoints.php';
        $this->_endpointMap = $PayPalEndpoints;

        // Load SDK settings.
        if (@include 'Services/PayPal/conf/paypal-sdk.php') {
            if (isset($__PP_CONFIG['log_level'])) {
                $this->_logLevel = $__PP_CONFIG['log_level'];
            }
            if (isset($__PP_CONFIG['log_dir'])) {
                $this->_logDir = $__PP_CONFIG['log_dir'];
            }
        }
    }

    /**
     * Sets the WSDL endpoint based on $portType and on the environment
     * set in the user's profile.
     *
     * @param string $portType  The portType the current operation is part of.
     * @param string $version   The WSDL version being used.
     *
     * @return boolean | PayPal_Error  An error if mapping can't be done, else true.
     */
    function setEndpoint($portType, $version)
    {
        $version = (float)$version;

        foreach ($this->_endpointMap as $range) {
            if ($version >= $range['min'] &&
                $version <= $range['max'] &&
                isset($range['environments'][$this->_profile->getEnvironment()][$portType])) {
                $this->_endpoint = $range['environments'][$this->_profile->getEnvironment()][$portType];
                return true;
            }
        }

        return Services_PayPal::raiseError("Invalid version/environment/portType combination.");
    }

    /**
     * Take the decoded array from SOAP_Client::__call() and turn it
     * into an object of the appropriate AbstractResponseType
     * subclass.
     *
     * @param array $values  The decoded SOAP response.
     * @param string $type   The type of the response object.
     *
     * @return AbstractResponseType  The response object.
     */
    function &getResponseObject($values, $type)
    {
        // Check for SOAP Faults.
        if (Services_PayPal::isError($values)) {
            return $values;
        }

        // Check for already translated objects.
        if (is_object($values) && strtolower(get_class($values)) != 'xsdtype') {
            return $values;
        }

        $object =& Services_PayPal::getType($type);
        if (Services_PayPal::isError($object)) {
            return $values;
        }

        foreach ($values as $name => $value) {
            if (method_exists($object, 'set' . $name)) {
                if (is_object($value)) {
                    if (strtolower(get_class($value)) == 'xsdtype') {
                        $value =& $this->getResponseObject((array)$value, $object->_elements[$name]['type']);
                    }
                } elseif (is_array($value)) {
                    $values = $value;
                    $value = array();
                    foreach ($values as $v) {
                        $value[] =& $this->getResponseObject($v, $object->_elements[$name]['type']);
                    }
                }
                call_user_func(array(&$object, 'set' . $name), $value);
            }
        }

        return $object;
    }

    /**
     * Use a given profile.
     *
     * @param APIProfile $profile  The profile with the username, password,
     *                             and any other information necessary to use
     *                             the SDK.
     */
    function setAPIProfile(&$profile)
    {
        $this->_profile = &$profile;
    }

    /**
     * Get the current profile.
     *
     * @return APIProfile  The current profile.
     */
    function &getAPIProfile()
    {
        return $this->_profile;
    }

    /**
     * Gets the PEAR Log object to use.
     *
     * @return Log  A Log object, either provided by the user or
     *              created by this function.
     */
    function &getLogger()
    {
        if (!$this->_logger) {
            $file = $this->_logDir . '/' . date('Ymd') . '.PayPal.log';
            if (is_link($file) || (file_exists($file) && !is_writable($file))) {
                // Don't overwrite symlinks.
                return Services_PayPal::raiseError('bad logfile');
            }

            $this->_logger = &Log::singleton('file', $file, $this->_profile->getAPIUsername(), array('append' => true));
        }

        return $this->_logger;
    }

    /**
     * Sets a custom PEAR Log object to use in logging.
     *
     * @param Log  A PEAR Log instance.
     */
    function setLogger(&$logger)
    {
        if (is_a($logger, 'Log')) {
            $this->_logger =& $logger;
        }
    }

    /**
     * Override SOAP_Client::call() to always add our security header
     * first.
     */
    function &call($method, &$params, $namespace = false, $soapAction = false)
    {
        // Create the security header.
        $this->addHeader($rc = new SOAP_Header('RequesterCredentials', 'struct', array(
            new SOAP_Value('{urn:ebay:apis:eBLBaseComponents}Credentials', 'struct',
                           array(new SOAP_Value('{urn:ebay:apis:eBLBaseComponents}Username', '', $this->_profile->getAPIUsername()),
                                 new SOAP_Value('{urn:ebay:apis:eBLBaseComponents}Password', '', $this->_profile->getAPIPassword()),
                                 new SOAP_Value('{urn:ebay:apis:eBLBaseComponents}Subject', '', $this->_profile->getSubject())),
                           array('xmlns:ebl' => 'urn:ebay:apis:eBLBaseComponents'))),
            1, array('xmlns' => 'urn:ebay:api:PayPalAPI')));

        return parent::call($method, $params, $namespace, $soapAction);
    }

    /**
     * Override some of the default SOAP:: package _decode behavior to
     * handle simpleTypes and complexTypes with simpleContent.
     */
    function &_decode(&$soapval)
    {
        if (count($soapval->attributes)) {
            $attributes = $soapval->attributes;
        }

        $object =& Services_PayPal::getType($soapval->type);
        if (Services_PayPal::isError($object)) {
            return parent::_decode($soapval);
        }

        $this->_type_translation[$soapval->type] = $soapval->type;

        $result =& parent::_decode($soapval);
        if (!is_a($result, 'XSDType') && is_a($object, 'XSDSimpleType')) {
            $object->setval($result);
            if (isset($attributes)) {
                foreach ($attributes as $aname => $attribute) {
                    $object->setattr($aname, $attribute);
                }
            }
            $result =& $object;
        }

        return $result;
    }

    /**
     * Log the current transaction depending on the current log level.
     *
     * @access protected
     *
     * @param string $operation  The operation called.
     * @param integer $elapsed   Microseconds taken.
     * @param object $response   The response object.
     */
    function _logTransaction($operation, $elapsed, $response)
    {
        $logger =& $this->getLogger();
        if (Services_PayPal::isError($logger)) {
            return $logger;
        }

        switch ($this->_logLevel) {
        case PEAR_LOG_DEBUG:
            $logger->log('Request XML: ' . $this->_sanitizeLog($this->__last_request), PEAR_LOG_DEBUG);
            $logger->log('Response XML: ' . $this->_sanitizeLog($this->__last_response), PEAR_LOG_DEBUG);

        case PEAR_LOG_INFO:
            $ack = is_object($response) && method_exists($response, 'getAck') ? ', Ack: ' . $response->getAck() : '';
            $logger->log($operation . ', Elapsed: ' . $elapsed . 'ms' . $ack, PEAR_LOG_INFO);

        case PEAR_LOG_ERR:
            if (Services_PayPal::isError($response)) {
                $logger->log($response, PEAR_LOG_ERR);
            }
        }
    }

    /**
     * Strip sensitive information (API passwords and credit card
     * numbers) from raw XML requests/responses.
     *
     * @access protected
     *
     * @param string $xml  The XML to sanitize.
     *
     * @return string  The sanitized XML.
     */
    function _sanitizeLog($xml)
    {
        return preg_replace(array('/<(.*?Password.*?)>(.*?)<\/(.*?Password)>/i',
                                  '/<(.*?CreditCard.*?)>(.*?)<\/(.*?CreditCard)>/i'),
                            '<\1>***</\3>',
                            $xml);
    }

    /**
     * Return the current time including microseconds.
     *
     * @access protected
     *
     * @return integer  Current time with microseconds.
     */
    function _getMicroseconds()
    {
        list($ms, $s) = explode(' ', microtime());
        return floor($ms * 1000) + 1000 * $s;
    }

    /**
     * Return the difference between now and $start in microseconds.
     *
     * @access protected
     *
     * @param integer $start  Start time including microseconds.
     *
     * @return integer  Number of microseconds elapsed since $start
     */
    function _getElapsed($start)
    {
        return $this->_getMicroseconds() - $start;
    }

    function &RefundTransaction($RefundTransactionReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($RefundTransactionReq, 'XSDType')) {
            $RefundTransactionReq->setVersion(PAYPAL_WSDL_VERSION);
            $RefundTransactionReq = $RefundTransactionReq->getSoapValue('RefundTransactionRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('RefundTransaction', $this->_getElapsed($start), $res);
            return $res;
        }

        // RefundTransactionReq is a ComplexType, refer to the WSDL for more info.
        $RefundTransactionReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $RefundTransactionReq =& new SOAP_Value('RefundTransactionReq', false, $RefundTransactionReq, $RefundTransactionReq_attr);
        $result = $this->call('RefundTransaction',
                              $v = array("RefundTransactionReq" => $RefundTransactionReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'RefundTransactionResponseType');
        $this->_logTransaction('RefundTransaction', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetTransactionDetails($GetTransactionDetailsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetTransactionDetailsReq, 'XSDType')) {
            $GetTransactionDetailsReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetTransactionDetailsReq = $GetTransactionDetailsReq->getSoapValue('GetTransactionDetailsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('GetTransactionDetails', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetTransactionDetailsReq is a ComplexType, refer to the WSDL for more info.
        $GetTransactionDetailsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetTransactionDetailsReq =& new SOAP_Value('GetTransactionDetailsReq', false, $GetTransactionDetailsReq, $GetTransactionDetailsReq_attr);
        $result = $this->call('GetTransactionDetails',
                              $v = array("GetTransactionDetailsReq" => $GetTransactionDetailsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetTransactionDetailsResponseType');
        $this->_logTransaction('GetTransactionDetails', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BillUser($BillUserReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BillUserReq, 'XSDType')) {
            $BillUserReq->setVersion(PAYPAL_WSDL_VERSION);
            $BillUserReq = $BillUserReq->getSoapValue('BillUserRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('BillUser', $this->_getElapsed($start), $res);
            return $res;
        }

        // BillUserReq is a ComplexType, refer to the WSDL for more info.
        $BillUserReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BillUserReq =& new SOAP_Value('BillUserReq', false, $BillUserReq, $BillUserReq_attr);
        $result = $this->call('BillUser',
                              $v = array("BillUserReq" => $BillUserReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BillUserResponseType');
        $this->_logTransaction('BillUser', $this->_getElapsed($start), $response);
        return $response;
    }

    function &TransactionSearch($TransactionSearchReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($TransactionSearchReq, 'XSDType')) {
            $TransactionSearchReq->setVersion(PAYPAL_WSDL_VERSION);
            $TransactionSearchReq = $TransactionSearchReq->getSoapValue('TransactionSearchRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('TransactionSearch', $this->_getElapsed($start), $res);
            return $res;
        }

        // TransactionSearchReq is a ComplexType, refer to the WSDL for more info.
        $TransactionSearchReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $TransactionSearchReq =& new SOAP_Value('TransactionSearchReq', false, $TransactionSearchReq, $TransactionSearchReq_attr);
        $result = $this->call('TransactionSearch',
                              $v = array("TransactionSearchReq" => $TransactionSearchReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'TransactionSearchResponseType');
        $this->_logTransaction('TransactionSearch', $this->_getElapsed($start), $response);
        return $response;
    }

    function &MassPay($MassPayReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($MassPayReq, 'XSDType')) {
            $MassPayReq->setVersion(PAYPAL_WSDL_VERSION);
            $MassPayReq = $MassPayReq->getSoapValue('MassPayRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('MassPay', $this->_getElapsed($start), $res);
            return $res;
        }

        // MassPayReq is a ComplexType, refer to the WSDL for more info.
        $MassPayReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $MassPayReq =& new SOAP_Value('MassPayReq', false, $MassPayReq, $MassPayReq_attr);
        $result = $this->call('MassPay',
                              $v = array("MassPayReq" => $MassPayReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'MassPayResponseType');
        $this->_logTransaction('MassPay', $this->_getElapsed($start), $response);
        return $response;
    }

    function &BillAgreementUpdate($BillAgreementUpdateReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($BillAgreementUpdateReq, 'XSDType')) {
            $BillAgreementUpdateReq->setVersion(PAYPAL_WSDL_VERSION);
            $BillAgreementUpdateReq = $BillAgreementUpdateReq->getSoapValue('BAUpdateRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('BillAgreementUpdate', $this->_getElapsed($start), $res);
            return $res;
        }

        // BillAgreementUpdateReq is a ComplexType, refer to the WSDL for more info.
        $BillAgreementUpdateReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $BillAgreementUpdateReq =& new SOAP_Value('BillAgreementUpdateReq', false, $BillAgreementUpdateReq, $BillAgreementUpdateReq_attr);
        $result = $this->call('BillAgreementUpdate',
                              $v = array("BillAgreementUpdateReq" => $BillAgreementUpdateReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'BAUpdateResponseType');
        $this->_logTransaction('BillAgreementUpdate', $this->_getElapsed($start), $response);
        return $response;
    }

    function &AddressVerify($AddressVerifyReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($AddressVerifyReq, 'XSDType')) {
            $AddressVerifyReq->setVersion(PAYPAL_WSDL_VERSION);
            $AddressVerifyReq = $AddressVerifyReq->getSoapValue('AddressVerifyRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPI', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('AddressVerify', $this->_getElapsed($start), $res);
            return $res;
        }

        // AddressVerifyReq is a ComplexType, refer to the WSDL for more info.
        $AddressVerifyReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $AddressVerifyReq =& new SOAP_Value('AddressVerifyReq', false, $AddressVerifyReq, $AddressVerifyReq_attr);
        $result = $this->call('AddressVerify',
                              $v = array("AddressVerifyReq" => $AddressVerifyReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'AddressVerifyResponseType');
        $this->_logTransaction('AddressVerify', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoExpressCheckoutPayment($DoExpressCheckoutPaymentReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoExpressCheckoutPaymentReq, 'XSDType')) {
            $DoExpressCheckoutPaymentReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoExpressCheckoutPaymentReq = $DoExpressCheckoutPaymentReq->getSoapValue('DoExpressCheckoutPaymentRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('DoExpressCheckoutPayment', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoExpressCheckoutPaymentReq is a ComplexType, refer to the WSDL for more info.
        $DoExpressCheckoutPaymentReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoExpressCheckoutPaymentReq =& new SOAP_Value('DoExpressCheckoutPaymentReq', false, $DoExpressCheckoutPaymentReq, $DoExpressCheckoutPaymentReq_attr);
        $result = $this->call('DoExpressCheckoutPayment',
                              $v = array("DoExpressCheckoutPaymentReq" => $DoExpressCheckoutPaymentReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoExpressCheckoutPaymentResponseType');
        $this->_logTransaction('DoExpressCheckoutPayment', $this->_getElapsed($start), $response);
        return $response;
    }

    function &SetExpressCheckout($SetExpressCheckoutReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($SetExpressCheckoutReq, 'XSDType')) {
            $SetExpressCheckoutReq->setVersion(PAYPAL_WSDL_VERSION);
            $SetExpressCheckoutReq = $SetExpressCheckoutReq->getSoapValue('SetExpressCheckoutRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('SetExpressCheckout', $this->_getElapsed($start), $res);
            return $res;
        }

        // SetExpressCheckoutReq is a ComplexType, refer to the WSDL for more info.
        $SetExpressCheckoutReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $SetExpressCheckoutReq =& new SOAP_Value('SetExpressCheckoutReq', false, $SetExpressCheckoutReq, $SetExpressCheckoutReq_attr);
        $result = $this->call('SetExpressCheckout',
                              $v = array("SetExpressCheckoutReq" => $SetExpressCheckoutReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'SetExpressCheckoutResponseType');
        $this->_logTransaction('SetExpressCheckout', $this->_getElapsed($start), $response);
        return $response;
    }

    function &GetExpressCheckoutDetails($GetExpressCheckoutDetailsReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($GetExpressCheckoutDetailsReq, 'XSDType')) {
            $GetExpressCheckoutDetailsReq->setVersion(PAYPAL_WSDL_VERSION);
            $GetExpressCheckoutDetailsReq = $GetExpressCheckoutDetailsReq->getSoapValue('GetExpressCheckoutDetailsRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('GetExpressCheckoutDetails', $this->_getElapsed($start), $res);
            return $res;
        }

        // GetExpressCheckoutDetailsReq is a ComplexType, refer to the WSDL for more info.
        $GetExpressCheckoutDetailsReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $GetExpressCheckoutDetailsReq =& new SOAP_Value('GetExpressCheckoutDetailsReq', false, $GetExpressCheckoutDetailsReq, $GetExpressCheckoutDetailsReq_attr);
        $result = $this->call('GetExpressCheckoutDetails',
                              $v = array("GetExpressCheckoutDetailsReq" => $GetExpressCheckoutDetailsReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'GetExpressCheckoutDetailsResponseType');
        $this->_logTransaction('GetExpressCheckoutDetails', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoDirectPayment($DoDirectPaymentReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoDirectPaymentReq, 'XSDType')) {
            $DoDirectPaymentReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoDirectPaymentReq = $DoDirectPaymentReq->getSoapValue('DoDirectPaymentRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('DoDirectPayment', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoDirectPaymentReq is a ComplexType, refer to the WSDL for more info.
        $DoDirectPaymentReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoDirectPaymentReq =& new SOAP_Value('DoDirectPaymentReq', false, $DoDirectPaymentReq, $DoDirectPaymentReq_attr);
        $result = $this->call('DoDirectPayment',
                              $v = array("DoDirectPaymentReq" => $DoDirectPaymentReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoDirectPaymentResponseType');
        $this->_logTransaction('DoDirectPayment', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoCapture($DoCaptureReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoCaptureReq, 'XSDType')) {
            $DoCaptureReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoCaptureReq = $DoCaptureReq->getSoapValue('DoCaptureRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('DoCapture', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoCaptureReq is a ComplexType, refer to the WSDL for more info.
        $DoCaptureReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoCaptureReq =& new SOAP_Value('DoCaptureReq', false, $DoCaptureReq, $DoCaptureReq_attr);
        $result = $this->call('DoCapture',
                              $v = array("DoCaptureReq" => $DoCaptureReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoCaptureResponseType');
        $this->_logTransaction('DoCapture', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoReauthorization($DoReauthorizationReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoReauthorizationReq, 'XSDType')) {
            $DoReauthorizationReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoReauthorizationReq = $DoReauthorizationReq->getSoapValue('DoReauthorizationRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('DoReauthorization', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoReauthorizationReq is a ComplexType, refer to the WSDL for more info.
        $DoReauthorizationReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoReauthorizationReq =& new SOAP_Value('DoReauthorizationReq', false, $DoReauthorizationReq, $DoReauthorizationReq_attr);
        $result = $this->call('DoReauthorization',
                              $v = array("DoReauthorizationReq" => $DoReauthorizationReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoReauthorizationResponseType');
        $this->_logTransaction('DoReauthorization', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoVoid($DoVoidReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoVoidReq, 'XSDType')) {
            $DoVoidReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoVoidReq = $DoVoidReq->getSoapValue('DoVoidRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('DoVoid', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoVoidReq is a ComplexType, refer to the WSDL for more info.
        $DoVoidReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoVoidReq =& new SOAP_Value('DoVoidReq', false, $DoVoidReq, $DoVoidReq_attr);
        $result = $this->call('DoVoid',
                              $v = array("DoVoidReq" => $DoVoidReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoVoidResponseType');
        $this->_logTransaction('DoVoid', $this->_getElapsed($start), $response);
        return $response;
    }

    function &DoAuthorization($DoAuthorizationReq)
    {
        $start = $this->_getMicroseconds();

        // Handle type objects.
        if (is_a($DoAuthorizationReq, 'XSDType')) {
            $DoAuthorizationReq->setVersion(PAYPAL_WSDL_VERSION);
            $DoAuthorizationReq = $DoAuthorizationReq->getSoapValue('DoAuthorizationRequest', 'urn:ebay:api:PayPalAPI');
        }

        // Make sure we can find a valid WSDL endpoint for this method.
        $res = $this->setEndpoint('PayPalAPIAA', PAYPAL_WSDL_VERSION);
        if (Services_PayPal::isError($res)) {
            $this->_logTransaction('DoAuthorization', $this->_getElapsed($start), $res);
            return $res;
        }

        // DoAuthorizationReq is a ComplexType, refer to the WSDL for more info.
        $DoAuthorizationReq_attr['xmlns'] = 'urn:ebay:api:PayPalAPI';
        $DoAuthorizationReq =& new SOAP_Value('DoAuthorizationReq', false, $DoAuthorizationReq, $DoAuthorizationReq_attr);
        $result = $this->call('DoAuthorization',
                              $v = array("DoAuthorizationReq" => $DoAuthorizationReq),
                              array('namespace' => 'urn:ebay:api:PayPalAPI',
                                    'soapaction' => '',
                                    'style' => 'document',
                                    'use' => 'literal'));

        $response = $this->getResponseObject($result, 'DoAuthorizationResponseType');
        $this->_logTransaction('DoAuthorization', $this->_getElapsed($start), $response);
        return $response;
    }
}
