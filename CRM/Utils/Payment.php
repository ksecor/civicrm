<?php  
  /*  
 +--------------------------------------------------------------------+  
 | CiviCRM version 1.3                                                |  
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
 | at http://www.openngo.org/faqs/licensing.html                      |  
 +--------------------------------------------------------------------+  
  */  
  
  /**  
   *  
   * @package CRM  
   * @author Donald A. Lobo <lobo@yahoo.com>  
   * @copyright Donald A. Lobo (c) 2005  
   * $Id$  
   *  
   */  
 
abstract class CRM_Utils_Payment {
    /**
     * how are we getting billing information?
     *
     * FORM   - we collect it on the same page
     * BUTTON - the processor collects it and sends it back to us via some protocol
     */
    const
        BILLING_MODE_FORM   = 1,
        BILLING_MODE_BUTTON = 2;

    /**  
     * singleton function used to manage this object  
     *  
     * @param string $mode the mode of operation: live or test
     *  
     * @return object  
     * @static  
     *  
     */  
    static function &singleton( $mode = 'test' ) {
        $config   =& CRM_Core_Config::singleton( );
        
        $classPath = str_replace( '_', '/', $config->paymentClass ) . '.php';
        require_once($classPath);
        return eval( 'return ' . $config->paymentClass . '::singleton( $mode );' );
    }

    abstract function doDirectPayment( &$params );
}

?>