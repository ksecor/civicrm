<?php
/**
 * @package Services_PayPal
 */

/**
 * Include parent and package classes.
 */
require_once 'Services/PayPal.php';
require_once 'Services/PayPal/Profile/Handler.php';

/**
 * Array handler class for hardcoding a PayPal profile.
 *
 * @package Services_PayPal
 */
class ProfileHandler_Array extends ProfileHandler
{
    function ProfileHandler_Array($parameters)
    {
        parent::ProfileHandler($parameters);
    }

    function loadProfile($id)
    {
        return $this->_params;
    }

    function saveProfile($data)
    {
        $this->_params = $data;
        return 'default';
    }

    function deleteProfile($id)
    {
        $this->_params = null;
    }

    function getParamInfo()
    {
        return null;
    }

    function listProfiles()
    {
        return array('default');
    }

    function &getInstance($params)
    {
        $classname = __CLASS__;
        return $inst =& new $classname($params);
    }

}
