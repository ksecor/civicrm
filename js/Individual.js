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
 * @return none
 */
function location_is_primary_onclick(formname, locid) 
{
    /*
    if (document.forms[formname].elements['location1[is_primary]'].checked = 'checked') {
	if ( confirm('Location 1 is selected as primary location do you want to change it? ') == "true" ) {
	    document.forms[formname].elements['location2[is_primary]'].checked = null;
	    document.forms[formname].elements['location3[is_primary]'].checked = null;
	}
	
    }
    */
    switch(locid) {
    case 1:  
	if ( confirm('Do you want to set Location 1 as primary location') == true ) {
	    document.forms[formname].elements['location1[is_primary]'].checked = 'checked';
	    document.forms[formname].elements['location2[is_primary]'].checked = null;
	    document.forms[formname].elements['location3[is_primary]'].checked = null;
	} else {
	    if ((document.forms[formname].elements['location2[is_primary]'].checked = 'checked') || (document.forms[formname].elements['location3[is_primary]'].checked = 'checked')) { 
		document.forms[formname].elements['location1[is_primary]'].checked = null;
	    } else {
		document.forms[formname].elements['location1[is_primary]'].checked = 'checked';
	    }	
	}
	
	break;
	
    case 2:  
	if ( confirm('Do you want to set Location 2 as primary location') == true ) {
	    document.forms[formname].elements['location1[is_primary]'].checked = null;
	    document.forms[formname].elements['location2[is_primary]'].checked = 'checked';
	    document.forms[formname].elements['location3[is_primary]'].checked = null;
	} else {
	    document.forms[formname].elements['location2[is_primary]'].checked = null;
	}
	
	break;
	
    case 3:  
	if ( confirm('Do you want to set Location 3 as primary location') == true ) {
	    document.forms[formname].elements['location1[is_primary]'].checked = null;
	    document.forms[formname].elements['location2[is_primary]'].checked = null;
	    document.forms[formname].elements['location3[is_primary]'].checked = 'checked';
	} else {
	    document.forms[formname].elements['location3[is_primary]'].checked = null;
	}
   
	break;
    }
    
}

