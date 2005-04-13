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
 * This function is used for adding the elements to the group select box
 * 
 * @param form - name of form 
 * @access public
 * @return none 
 */
function addRemoveSelect(formname , status)
{   
    var txt = '';
    var txt1 = '';
    var select1 = '';
    var select2 = '';

    if (!status) {
	select1 = document.forms[formname].elements['allgroups'];
	select2 = document.forms[formname].elements['contactgroups'];
    } else {

	select2 = document.forms[formname].elements['allgroups'];
	select1 = document.forms[formname].elements['contactgroups'];
    }
    
    for (var i = 0; i <= select1.length; i++) {

	if (select1.options[i].selected) {

	    txt = select1[i].value;
	    txt1 = select1[i].text;

	    addOption = new Option(txt1,txt);
	    numItems = select2.length;
	    select2.options[numItems] = addOption;

	    select1.remove(i);
	}
    }
    
    return true; 

 }
