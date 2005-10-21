<?php
/**
 * Base PayPal SDK file.
 *
 * @package Services_PayPal
 */

/**
 * Include our error class and the PEAR base class.
 */
require_once 'PEAR.php';
require_once 'Services/PayPal/Error.php';
require_once 'Services/PayPal/SDK/Generator.php';

/**
 * End point for users to access the PayPal API, provides utility
 * methods used internally as well as factory methods.
 *
 * $Id: PayPal.php,v 1.20 2005/06/23 17:03:18 chagenbuch Exp $
 *
 * @static
 * @package Services_PayPal
 */
class Services_PayPal extends PEAR
{
    /**
     * Raise an error when one occurs
     *
     * @static
     */
    function raiseError($message, $code = null)
    {
        return parent::raiseError($message, $code, null, null, null, 'PayPal_Error');
    }

    /**
     * Try to instantiate the class for $type. Looks inside the Type/
     * directory containing all generated types. Allows for run-time
     * loading of needed types.
     *
     * @param string $type  The name of the type (eg. AbstractRequestType).
     *
     * @return mixed XSDType | PayPal_Error  Either an instance of $type or an error.
     *
     * @static
     */
    function &getType($type)
    {
        $type = basename($type);
        @include_once 'Services/PayPal/Type/' . $type . '.php';
        if (!class_exists($type)) {
            return Services_PayPal::raiseError("Type $type is not defined");
        }

        return $t =& new $type();
    }

    /**
     * Load a CallerServices object for making API calls.
     *
     * @param APIProfile $profile  The profile with the username, password,
     *                             and any other information necessary to use
     *                             CallerServices.
     *
     * @return CallerServices  A PayPal API caller object.
     *
     * @static
     */
    function &getCallerServices($profile)
    {
        if (!defined('CURLOPT_SSLCERT')) {
            return Services_PayPal::raiseError("The PayPal SDK requires curl with SSL support");
        }

        if (!is_a($profile, 'APIProfile')) {
            return Services_PayPal::raiseError("You must provide a valid APIProfile");
        }

        $result = $profile->validate();
        if (Services_PayPal::isError($result)) {
            return $result;
        }

        include_once 'Services/PayPal/CallerServices.php';
        return $c =& new CallerServices($profile);
    }

    /**
     * Load an EWPServices object for performing encryption
     * operations.
     *
     * @param EWPProfile $profile  The profile with the username, password,
     *                             and any other information necessary to use
     *                             EWPServices.
     *
     * @return EWPServices  A PayPal EWP services object.
     *
     * @static
     */
    function &getEWPServices($profile)
    {
        if (!is_a($profile, 'EWPProfile')) {
            return parent::raiseError("You must provide a valid EWPProfile");
        }

        $result = $profile->validate();
        if (Services_PayPal::isError($result)) {
            return $result;
        }

        include_once 'Services/PayPal/EWPServices.php';
        return $c =& new EWPServices($profile);
    }

    /**
     * Returns the package root directory.
     *
     * @return string  The path where the package is installed.
     *
     * @static
     */
    function getPackageRoot()
    {
        return dirname(__FILE__) . '/PayPal';
    }

    /**
     * Returns the SDK version.
     *
     * @return string  The SDK version.
     *
     * @static
     */
    function getPackageVersion()
    {
        return '0.0.1';
    }

    /**
     * Returns the version of the WSDL that this SDK is built against.
     *
     * @return float  The WSDL version.
     *
     * @static
     */
    function getWSDLVersion()
    {
        include_once 'Services/PayPal/CallerServices.php';
        return PAYPAL_WSDL_VERSION;
    }

    /**
     * Returns the endpoint map.
     *
     * @return mixed The Paypal endpoint map or a Paypal error object on failure
     * @static
     */
    function getEndpoints()
    {
        $package_root = Services_PayPal::getPackageRoot();
        $file = "$package_root/wsdl/paypal-endpoints.php";
        if (@include $file) {
            if (!isset($PayPalEndpoints)) {
                return Services_PayPal::raiseError("Endpoint map file found, but no data was found.");
            }

            return $PayPalEndpoints;
        }

        return Services_PayPal::raiseError("Could not load endpoint mapping from '$file', please rebuild SDK.");
    }

    /**
     * Get information describing all types provided by the SDK.
     * @static
     */
    function getTypeList()
    {
        $root_dir = Services_PayPal::getPackageRoot();
        $types = "$root_dir/Type/*.php";

        $files = glob($types);

        if (count($files) < 2) {
            return Services_PayPal::raiseError("Types not found in package! (Looked for '$types')");
        }

        $retval = array();

        foreach ($files as $type_files) {
            $retval[] = basename(substr($type_files, 0, strpos($type_files, '.')));
        }

        return $retval;
    }

    /**
     * Get information describing what methods are available.
     * @static
     */
    function getCallerServicesIntrospection()
    {
        $inst =& new PayPal_SDK_Generator();
        return $inst->describeMethods();
    }

}
