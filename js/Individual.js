/**
   +----------------------------------------------------------------------+
   | CiviCRM version 1.0                                                  |
   +----------------------------------------------------------------------+
   | Copyright (c) 2005 Donald A. Lobo                                    |
   +----------------------------------------------------------------------+
   | This file is a part of CiviCRM.                                      |
   |                                                                      |
   | CiviCRM is free software; you can redistribute it and/or modify it   |
   | under the terms of the Affero General Public License Version 1,      |
   | March 2002.                                                          |
   |                                                                      |
   | CiviCRM is distributed in the hope that it will be useful, but       |
   | WITHOUT ANY WARRANTY; without even the implied warranty of           |
   | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
   | See the Affero General Public License for more details at            |
   | http://www.affero.org/oagpl.html                                     |
   |                                                                      |
   | A copy of the Affero General Public License has been been            |
   | distributed along with this program (affero_gpl.txt)                 |
   +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
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
 * @param maxLocs How many location blocks are offered
 * @return none
 */
function location_is_primary_onclick(formname, locid, maxLocs) {
    if (locid == 1) {
        // don't need to confirm selecting 1st location as primary
        return;
    }
    
    var changedKey = 'location[' + locid + '][is_primary]';
    var notPrimary = [];
    for (var j = 1; j <= maxLocs; j++) {
        if (j != locid) {
            notPrimary.push(j);
        }
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

