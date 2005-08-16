?php  
 
 /*  
 +--------------------------------------------------------------------+  
 | CiviCRM version 1.1                                                |  
 +--------------------------------------------------------------------+  
 | Copyright (c) 2005 Social Source Foundation                        |  
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
  * @copyright Donald A. Lobo 01/15/2005  
  * $Id$  
  *  
  */  

require_once 'CRM/Core/Selector/Base.php'; 
require_once 'CRM/Core/Selector/API.php'; 
 
require_once 'CRM/Utils/Pager.php'; 
require_once 'CRM/Utils/Sort.php'; 
 
require_once 'CRM/Contact/BAO/Contact.php'; 
 
class CRM_Contact_Selector_Profile extends CRM_Core_Selector_Base implements CRM_Core_Selector_API  
{
    /** 
     * This defines no action
     * 
     * @var array 
     * @static 
     */ 
    static $_links = null; 
 
    /** 
     * we use desc to remind us what that column is, name is used in the tpl 
     * 
     * @var array 
     * @static 
     */ 
    static $_columnHeaders; 
    
    /** 
     * Class constructor 
     * 
     * @return CRM_Contact_Selector_Profile
     * @access public 
     */ 
    function __construct( &$headers ) {
        self::$_columnHeaders = $headers;
    }

    /** 
     * This method returns the links that are given for each search row. 
     * currently the links added for each row are  
     *  
     * @return array 
     * @access public 
     */ 
    static function &links() { 
        return self::$_links;
    }

}

?>