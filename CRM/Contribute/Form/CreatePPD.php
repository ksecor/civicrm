<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form.php';

/**
 * This class generates form components for Contribution Type
 * 
 */
class CRM_Contribute_Form_CreatePPD extends CRM_Contribute_Form
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );
        
        $this->applyFilter('__ALL__', 'trim');
        $this->add('text', 'api_username', ts( 'API Username' ), array( 'size' => 30, 'maxlen' => 30 ), true );
        $this->add('text', 'api_subject' , ts( 'API Subject' ) , array( 'size' => 30, 'maxlen' => 30 ) );
        
        $environment = array( 'live'         => 'live',
                              'sandbox'      => 'sandbox',
                              'beta-sandbox' => 'beta-sandbox' );
        $this->add('select', 'api_environment', ts( 'API Environment' ), $environment, true );

        $this->add( 'file', 'uploadFile', ts( 'API SSL Certificate' ), "size=30 maxlength=60", true );
        $this->addRule( 'uploadFile', ts('File size should be less than 8192 bytes'), 'maxfilesize', 8192 );
        $this->addRule( 'uploadFile', ts('A valid file must be uploaded.'), 'uploadedfile' );
        
        $this->addButtons( array( 
                                 array ( 'type'      => 'upload', 
                                         'name'      => ts('Save'), 
                                         'isDefault' => true   ), 
                                 array ( 'type'      => 'cancel', 
                                         'name'      => ts('Cancel') ), 
                                 ) 
                           );
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $params  = $this->controller->exportValues( $this->_name );

        $config =& CRM_Core_Config::singleton( );
        
        if ( $params['api_environment'] == 'live' ) {
            $savePath = $config->paymentCertPath['live'];
        } else {
            $savePath = $config->paymentCertPath['test'];
        }

        $certName = $this->controller->exportValue( $this->_name, 'uploadFile' );

        $cert  = file_get_contents( $certName );

        require_once 'Services/PayPal.php'; 
        require_once 'Services/PayPal/Profile/Handler/File.php';                      
        require_once 'Services/PayPal/Profile/API.php'; 

        $handler =& ProfileHandler_File::getInstance( array( 
                                                            'path'    => $savePath,
                                                            'charset' => 'iso-8859-1',
                                                            )
                                                      );
        
        $pid = ProfileHandler::generateID();
        $profile =& new APIProfile($pid, $handler);
        $certFile = "$savePath/$pid.cert";
        $fd = fopen( $certFile, "w" );
        if ( ! $fd ) {
            CRM_Core_Error::fatal( ts( "Could not open %1 file for writing", array( 1 => $certFile ) ) );
        }

        if ( ! fwrite( $fd, $cert ) ) {
            CRM_Core_Error::fatal( "Could not write into $certFile<p>" );
        }

        if ( ! fclose( $fd ) ) {
            CRM_Core_Error::fatal( "Could not close $certFile<p>" );
        }

        $profile->setAPIUsername    ( $params['api_username']    );
        $profile->setSubject        ( $params['api_subject']     );
        $profile->setEnvironment    ( $params['api_environment'] );
        $profile->setCertificateFile( $certFile                  );

        $result = $profile->save();                  
 
        if (Services_PayPal::isError($result)) {
            CRM_Core_Error::statusBounce( "Could not create new profile: ".$result->getMessage() );
        } else {
            if ( $params['api_environment'] == 'live' ) {
                $name = 'CIVICRM_CONTRIBUTE_PAYMENT_KEY';
            } else {
                $name = 'CIVICRM_CONTRIBUTE_PAYMENT_TEST_KEY';
            }
            $message = ts( 'Your %1 value is: "%2". This value must be entered in the Payment Processor section of the CiviCRM configuration file.',
                           array( 1 => $name, 2 => $pid ) );
            CRM_Core_Session::setStatus( $message );
        }
    }

}

?>
