/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

/**
 * This function is used to set primary status to a location block.  
 * 
 * Upon calling this function, the is primary checkbox within the target location block will be checked while the same checkbox
 * in all the other location blocks will be unchecked. This function is used to enforce the rule that at a time only one location
 * block can be considered primary. 
 * 
 * @access public
 * @param formname Name of the form.
 * @param locid Serial number of the location block.
 * @return none
 */
function location_is_primary_onclick(formname, locid) {
    var changedKey = 'location[' + locid + '][is_primary]';
    switch(locid) {

    case 1: 
        var notPrimary = new Array(2,3); 
    break;
            
    case 2: 
        var notPrimary = new Array(1,3);
    break;
	
    case 3:  
        var notPrimary = new Array(1,2);
    }

    if (document.forms[formname].elements[changedKey].checked) {
        if ( confirm('Do you want to make this the primary location?') == true ) {
            for (var i = 0; i < notPrimary.length; i++) {
                otherKey = 'location[' + notPrimary[i] + '][is_primary]';
                document.forms[formname].elements[otherKey].checked = null;
            }
        } else {
            document.forms[formname].elements[changedKey].checked = null;
        }
    } 	
    
}

