<?php
/**
 * @package Services_PayPal
 */

/**
 * Include SDK base class.
 */
require_once 'Services/PayPal.php';

/**
 * The base class for all Profile handlers
 *
 * @package Services_PayPal
 * @abstract
 */
class ProfileHandler
{
    var $_params;

    function ProfileHandler($parameters)
    {
        $this->_params = $parameters;
    }

    function listProfiles()
    {
        return Services_PayPal::raiseError("Cannot call this method from the base ProfileHandler class");
    }

    function loadProfile($id)
    {
        return Services_PayPal::raiseError("Cannot call this method from the base ProfileHandler class");
    }

    function saveProfile($data)
    {
        return Services_PayPal::raiseError("Cannot call this method from the base ProfileHandler class");
    }

    function getParamInfo()
    {
        return null;
    }

    function deleteProfile($id)
    {
        return Services_PayPal::raiseError("Cannot call this method from the base ProfileHandler class");
    }

    function generateID()
    {
        return md5(uniqid(mt_rand(), true));
    }

    function validateParams()
    {
        return true;
    }

    function &getInstance($params)
    {
        return Services_PayPal::raiseError("Cannot call this method from the base ProfileHandler class");
    }

    function initialize()
    {
        return true;
    }

}
