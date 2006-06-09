<?php
/**
 * @package Services_PayPal
 */

/**
 * Include parent and package classes.
 */
require_once 'Services/PayPal.php';
require_once 'Services/PayPal/Profile.php';

/**
 * Stores API Profile information used for performing transactions on the PayPal API
 *
 * @package Services_PayPal
 */
class APIProfile extends Profile
{
    /**
     * The API username to make API calls with. Must be a valid PayPal
     * API account (not a paypal.com account or developer.paypal.com
     * account).
     *
     * @access private
     */
    var $_username;

    /**
     * The API password to use. This must be set before making any API
     * calls; it is not stored by the ProfileHandler backend.
     *
     * @see setAPIPassword()
     *
     * @access private
     */
    var $_password;

    /**
     * The location of the user's private certificate. This should be
     * a .pem file.
     *
     * @access private
     */
    var $_certificateFile;

    /**
     * The password, if any, on the user's private certificate. This
     * must be set before making any API calls; it is not stored by
     * the ProfileHandler backend.
     *
     * @see setCertificatePassword()
     *
     * @access private
     */
    var $_certificatePassword;

    /**
     * Subject to be used when making API calls. This is for making
     * calls on behalf of another PayPal user with your own API
     * account.
     *
     * @access private
     */
    var $_subject;

    /**
     * Constructor
     *
     * @param string         $id       A unique id for the profile.
     * @param ProfileHandler $handler  A handler object where the profile is stored.
     *
     */
    function APIProfile($id, &$handler)
    {
        parent::Profile($id, $handler);
    }

    /**
     * Validates the profile data currently loaded before use.
     *
     * @return mixed true if the data is valid, or a PayPal_Error object on failure.
     */
    function validate()
    {
        if (empty($this->_username) ||
            empty($this->_password) ||
            empty($this->_certificateFile) ||
            empty($this->_environment)) {
            return Services_PayPal::raiseError("API Username, Password, Certificate File and Environment must all be set");
        }

        if (!file_exists($this->_certificateFile)) {
            return Services_PayPal::raiseError("Could not find certificate file '{$this->_certificateFile}'");
        }

        if (!in_array(strtolower($this->_environment), $this->_validEnvironments, true)) {
            return Services_PayPal::raiseError("Environment '{$this->_environment}' is not a valid environment.");
        }

        return true;
    }

    /**
     * Sets the API username for the profile.
     *
     * @param string The API username.
     */
    function setAPIUsername($username)
    {
        $this->_username = $username;
    }

    /**
     * Returns the API username for the profile.
     *
     * @return string The API username.
     */
    function getAPIUsername()
    {
        return $this->_username;
    }

    /**
     * Sets the API password for the profile.
     *
     * @param string The password for the profile.
     */
    function setAPIPassword($password)
    {
        $this->_password = $password;
    }

    /**
     * Get the API password for the profile.
     *
     * @return string The password for the profile.
     */
    function getAPIPassword()
    {
        return $this->_password;
    }

    /**
     * Get the Certificate file associated with the profile.
     *
     * @return string The certificate file associated with the profile.
     */
    function getCertificateFile()
    {
        return $this->_certificateFile;
    }

    /**
     * Set the certificate file associated with the profile.
     *
     * @param string The certificate file associated with the profile.
     */
    function setCertificateFile($filename)
    {
        $this->_certificateFile = $filename;
    }

    /**
     * Set the certificate password.
     *
     * @param string The certificate password.
     */
    function setCertificatePassword($password)
    {
        $this->_certificatePassword = $password;
    }

    /**
     * Get the certificate password.
     *
     * @return string  The certificate password.
     */
    function getCertificatePassword()
    {
        return $this->_certificatePassword;
    }

    /**
     * Set the subject associated with the profile.
     *
     * @param string The subject of the profile.
     */
    function setSubject($subject)
    {
        $this->_subject = $subject;
    }

    /**
     * Get the subject of the associated profile.
     *
     * @return string The subject associated with the profile.
     */
    function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Returns an array of member variables names which should be
     * included when storing the profile.
     *
     * @return array An array of member variable names which should be included.
     * @access protected
     */
    function _getSerializeList()
    {
        return array('username', 'certificateFile',
                     'subject', 'environment');
    }

    function getInstance($id, &$handler)
    {
        $classname = __CLASS__;
        $inst = &new $classname($id, $handler);

        $result = $inst->_load();
        if (Services_PayPal::isError($result)) {
            return $result;
        }

        $result = $inst->loadEnvironments();
        if (Services_PayPal::isError($result)) {
            return $result;
        }

        return $inst;
    }

}
