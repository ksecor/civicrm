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
 * This function executes when there is an error within a form element and the page is relayed from the server
 * with errors. Its purpose is to display all those blovks which have set data values.
 *
 * The mechanism by which this function is fired is present within the template file where the condition for count 
 * of form errors is checked and if found greater than 0, the following function is called.
 * This function is loaded at the top of the template file and is called at the bottom according to the condition.
 * This function checks for data within different elements, within different blocks which are normally hidden on 
 * the first display. Thus it checks the values of these elements and determines if the block has to be displayed     	
 * which is based on the presence of values within a block. 
 * 
 * @access public
 * @param formname Name of the form.
 * @return none 
 */

function on_error_execute(formname) 
{  

    var i,j,k;	
    var location_name = new Array ("location2",
				   "location3"
				   );
    var email_name_tail = new Array ("_secondary",
				     "_tertiary"
				     );
    
    /* Loop USAGE:
       This loop examines the values those elements within the location 1 block which are not displayed by default on 
       every form display. This is done to identify whether these blocks should be displayed which depends on presence 
       of values. A typical example is the ~phone_2~ block which is hidden for location1 on fresh display. 
       
       MECHANISM:
       The document.forms[formname] returns the add contact form from within the forms collection of document. 
       This reference is used further to access its elements collection with element names and further values to access their 
       values. If their values are set, the corresponding block within the template file containing its HTML code is programmed
       to be displayed. This is done by accessing the block based on its id value using getElementId.
    */
    
    for (i=0; i<2; i++) {
	
	if (document.forms[formname].elements['location1[phone_'+String(i+2)+']'].value != '') {
	    document.getElementById('phone_1_'+String(i+2)).style.display = 'block'; 
	    document.getElementById('expand_phone_1_'+String(i+2)).style.display = 'none';
	    if (i<1) {
		document.getElementById('expand_phone_1_'+String(i+3)).style.display = 'block';
	    }
	}
	
	if (document.forms[formname].elements['location1[im_screenname_'+String(i+2)+']'].value != '') {
	    document.getElementById('IM_1_'+String(i+2)).style.display = 'block';
	    document.getElementById('expand_IM_1_'+String(i+2)).style.display = 'none';
	    if (i<1) {
		document.getElementById('expand_IM_1_'+String(i+3)).style.display = 'block';
	    }
	}		        
	
	if (document.forms[formname].elements['location1[email_'+String(i+2)+']'].value != '') {
	    document.getElementById('email_1_'+String(i+2)).style.display = 'block'; 
	    document.getElementById('expand_email_1_'+String(i+2)).style.display = 'none';
	    if (k<1) {
		document.getElementById('expand_email_1_'+String(i+3)).style.display = 'block';
	    }
	}
	
    }
    
    /* Loop USAGE:
       This loop behaves in the same way as the above loop, except that in this loop we iterate over the other two
       dynamically displayed location blocks using the location_name array. The elements within these blocks are 
       examined for their values to determine if these blocks and the sub-blocks containing these elements should be
       displayed.
       
       MECHANISM:
       the indexOf function used here checks for a location2[ or 3[ prefix to the elements name to identify if they 
       belong to these locations. Further their values are examined using the elements[].value collection attribute.
       If found to be set, their blocks are set up for display. The main location block is set for display given any 
       element within its domain is found with value using the getElementById[].style.display function. 
    */ 
    
    for (i = 0; i < location_name.length; i++) {
	for (j = 0; j < document.forms[formname].length; j++) {
	    
	    if (document.forms[formname].elements[j].name) {
		if (document.forms[formname].elements[j].name.indexOf(location_name[i]) != -1) {
		    if (document.forms[formname].elements[j].type.indexOf("text")!= -1) {
			if (document.forms[formname].elements[j].value != '') { 
			    document.getElementById(location_name[i]).style.display = 'block';
			    if (i<1) {
				document.getElementById('expand_loc'+String(i+3)).style.display = 'block';
			    }
			    for (k=0; k<2; k++) {
				
				if (document.forms[formname].elements[location_name[i]+'[phone_'+String(k+2)+']'].value != '') {
				    document.getElementById('phone_'+String(i+2)+'_'+String(k+2)).style.display = 'block';
				    document.getElementById('expand_phone_'+String(i+2)+'_'+String(k+2)).style.display = 'none';
				    if (k<1) {
					document.getElementById('expand_phone_'+String(i+3)+'_'+String(k+2)).style.display = 'block';
				    }
				}
				
				if (document.forms[formname].elements[location_name[i]+'[im_screenname_'+String(k+2)+']'].value != '') {
				    document.getElementById('IM_'+String(i+2)+'_'+String(k+2)).style.display = 'block';
				    document.getElementById('expand_IM_'+String(i+2)+'_'+String(k+2)).style.display = 'none';
				    if (k<1) {
					document.getElementById('expand_IM_'+String(i+3)+'_'+String(k+2)).style.display = 'block';
				    }
				}		        
				
				if (document.forms[formname].elements[location_name[i]+'[email_'+String(k+2)+']'].value != '') {
				    document.getElementById('email_'+String(i+2)+'_'+String(k+2)).style.display = 'block';
				    document.getElementById('expand_email_'+String(i+2)+'_'+String(k+2)).style.display = 'none';
				    if (k<1) {
					alert('expand_email_'+String(i+3)+'_'+String(k+2));
					document.getElementById('expand_email_'+String(i+3)+'_'+String(k+2)).style.display = 'block';
				    }
				}
				
				
				
			    }
			    break;
			}
		    }
		    
		}
	    }
	}
    }
    
    
    if (document.forms[formname].elements["address_note"].value != '') {
	document.getElementById("notes").style.display = 'block';
    }
    
    if (document.forms[formname].elements["mdyx"].value == 'true') {
	document.getElementById("demographics").style.display = 'block';
    }
    
    
}



/** 
 *  It hides certain blocks which are not to be displayed by default on a fresh load. 
 * 
 *  This function is called by default at the bottom of the template file when the page has finished loading the elements.
 * 
 * @access public
 * @param formname Name of the form.
 * @return none 
 */
function on_load_execute(formname)
{
    /* This array defines the various blocks to be hidden within the form template */
    var hide_blocks = 
	new Array( 'phone_1_2', 	'phone_1_3',
		   'email_1_2', 	'email_1_3',
		   'IM_1_2','IM_1_3',   'expand_phone_1_3',
		   'expand_email_1_3',  'expand_IM_1_3',
		   'phone_2_2', 	'phone_2_3',
		   'email_2_2', 	'email_2_3',
		   'IM_2_2','IM_2_3',   'expand_phone_2_3',
		   'expand_email_2_3',  'expand_IM_2_3',
		   'phone_3_2', 	'phone_3_3',
		   'email_3_2',	        'email_3_3',
		   'IM_3_2','IM_3_3',   'expand_phone_3_3',
		   'expand_email_3_3',  'expand_IM_3_3',
		   'notes','location2', 'demographics',
		   'location3',	        'expand_loc3' 
		   );
    
    
    /* This array stores the blocks to be displayed */	
    var show_blocks = new Array( "core" );
    
    /* This loop is used to display the blocks whose IDs are present within the show_blocks array */ 
    for ( var i = 0; i < show_blocks.length; i++ ) {
	document.getElementById(show_blocks[i]).style.display = 'block';
    }
    
    /* This loop is used to hide the blocks whose IDs are present within the hide_blocks array */ 
    for ( var i = 0; i < hide_blocks.length; i++ ) { 
	document.getElementById(hide_blocks[i]).style.display = 'none';
    }
    
    document.forms[formname].elements['location1[location_type_id]'].options[0].selected = "true";
    document.forms[formname].elements['location1[location_type_id]'].label = '0';
    document.forms[formname].elements['location2[location_type_id]'].options[1].selected = "true";
    document.forms[formname].elements['location2[location_type_id]'].label = '1';
    document.forms[formname].elements['location3[location_type_id]'].options[2].selected = "true";
    document.forms[formname].elements['location3[location_type_id]'].label = '2';
}


/** 
 * This function is used to display a block. 
 * 
 * This function is called by various links which handle requests to display the hidden blocks.
 * An example is the <code>[+] another phone</code> link which expands an additional phone block.
 * The parameter block_id must have the id of the block which has to be displayed.
 *
 * 
 * @access public
 * @param block_id Id value of the block to be displayed.
 * @return none
 */
function show(block_id) 
{
    document.getElementById(block_id).style.display = 'block';
}


/** 
 * This function is used to hide a block. 
 * 
 * This function is called by various links which handle requests to hide the visible blocks.
 * An example is the <code>[-] hide phone</code> link which hides the phone block.
 * The parameter block_id must have the id of the block which has to be hidden.
 *
 * @access public
 * @param block_id Id value of the block to be hidden.
 * @return none
 */
function hide(block_id) 
{
    document.getElementById(block_id).style.display = 'none';
}


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

/**
 * This function is used to validate the selected value of a location_id select element. 
 *
 * This function is called when the location_id select element is updated and is used to implement the rule that each location block
 * must have a unique location_id value.
 * This function returns a false value if the selected location_id value clashes with the location_id value of a valid location block.
 * This block also implements the re-adjusting of selected index in those location_id select elements whose location block is not valid 
 * and whose selected index clashes with the selected index of a location_id select element which belongs to a valid location block.    
 * 
 * 
 * @access public
 * @param formname Name of the form.
 * @param locid Serial number of the location block.
 * @return Boolean value depending on whether the selected index of the location_id select element is valid.
 * @uses index_readjust Calls this function to re-adjust the select index of all location_id select elements whode location is not valid. 
 */
function validate_selected_locationid(formname, locid)
{
    new_index = document.forms[formname].elements['location'+String(locid)+'[location_type_id]'].selectedIndex;
    old_index = document.forms[formname].elements['location'+String(locid)+'[location_type_id]'].label;
    var index_string = "";
    for (i=1; i<4; i++) {
	if (i>1) {
	    //if (document.getElementById('location'+String(i)).style.display == 'block') {
	    if (valid_location(formname, i) || i == locid) {
		
		index = '*'+document.forms[formname].elements['location'+String(i)+'[location_type_id]'].selectedIndex+'*';
	    }
	}
	else { 
	    index = '*'+document.forms[formname].elements['location'+String(i)+'[location_type_id]'].selectedIndex+'*';
	}
	
	index_num = index_string.indexOf(index);
	if (index_num != -1) {
	    
	    document.forms[formname].elements['location'+String(locid)+'[location_type_id]'].options[old_index].selected = "true";
	    //alert("You have selected duplicate location-id options in location"+String((index_num/3)+1)+" and location"+String(i));
	    alert("You cannot select multiple locations with location type : " + 
		  document.forms[formname].elements['location'+String(i)+'[location_type_id]'].options[new_index].text);
	    return false;
	}
	else {
	    if (index != "@") {
		index_string = index_string + index;
	    }
	}
	index = "@";   
    }    
    document.forms[formname].elements['location'+String(locid)+'[location_type_id]'].label = String(new_index);
    index_re_adjust(formname, locid, old_index, new_index);
    return true;
}   


/** 
 * This function is used to verify whether a given location block is valid.
 * 
 * This function is called by the validate_selected_locationid function. An additional location block is considered to be valid
 * if either of its three fields first phone block, first email block or the street_address have set values.  
 * 
 * @access public
 * @param formname Name of the form
 * @param locid Serial number of the location block.
 * @return Boolean value depending on whether a location block is valid under given conditions. 
 */
function valid_location(formname, locid) 
{
    if (locid == 1) { 
	return true;
    }
    
    if (document.forms[formname].elements['location'+String(locid)+'[phone_1]'].value != '') {
	return true;
    }
    
    if (document.forms[formname].elements['location'+String(locid)+'[email_1]'].value != '') {
	return true;
    }
    
    if (document.forms[formname].elements['location'+String(locid)+'[street_address]'].value != '') {
	return true;
    }
    return false;
}

/**
 * This function is used to re-adjust the selected index of a location_id select element within an invalid location block, if its 
 * selected index clashes with the index of a location_id element within a valid location block.  
 *
 * This function is called by the validate_selected_locationid function. It re-orders the selected index for the location_id select
 * elements whose location block is not valid. This is used to prevent the same location_id being assigned to multiple location blocks.
 *
 * @access public
 * @param formname Name of the form
 * @param locid Serial number of the location block.
 * @param old_index The initial index of the selected location_id select element.
 * @param new_index The current index of the selected location_id select element.
 */
function index_re_adjust(formname, locid, old_index, new_index) 
{
    var assign_index = new Array();
    var free_index = new Array();
    var assigned=1;
    assign_index[0]=new_index;
    
    for (i=1; i<4; i++) {
	if (locid != i && valid_location(formname, i)) {
	    assign_index[assigned]= document.forms[formname].elements['location'+String(i)+'[location_type_id]'].label;
	    //alert('uploading select_index '+assign_index[assigned] +'at array index '+assigned);
	    assigned++;
	}
    }
	
    free_from=assigned;
    for (i=0; i<=3; i++) {
	er = 0;
	for (j=0; j<free_from; j++) {
	    if (i == assign_index[j]) {
		er = 1;
		break;
	    }
	}
	if (er == 0) {
	    assign_index[free_from] = i;
	    //alert('freeloading select_index '+assign_index[free_from] +'at array index '+free_from);
	    free_from++;
			
	}
    }
	
    for (i=1; i<=3; i++) {
	if (locid != i && !valid_location(formname, i)) {
	    for (j=0; j<assigned; j++) {

		document.forms[formname].elements['location'+String(i)+'[location_type_id]'].options[assign_index[assigned]].selected = "true";
		document.forms[formname].elements['location'+String(i)+'[location_type_id]'].label = assign_index[assigned];
		//alert('assigning select_index '+assign_index[assigned]+' to location_id '+i); 
		assigned++;
		break;
					
	    }
	}
    }
}
