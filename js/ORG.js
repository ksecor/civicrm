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
 * with errors.
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
	var j = 1;	
	
	var email_name_tail = Array ("_secondary",
								 "_tertiary"
								 );
	
	/* Loop USAGE:
	   This loop examines the values those elements within the location 1 block which are not displayed by default on 
	   every form display. This is done to identify whether these blocks should be displayed which depends on presence 
	   of values. A typical example is the ~phone_2~ block which is hidden for location1 on fresh display. 
	   
	   MECHANISM:
	   The document.forms[formname] returns the add House form from within the forms collection of document. This reference 
	   is used further to access its elements collection with element names and further values to access their values.
	   If their values are set, the corresponding block within the template file containing its HTML code is programmed
	   to be displayed. This is done by accessing the block based on its id value using getElementId.
	*/

	for (i=0; i<2; i++) {
		
		//if (document.getElementsByName('location[phone_'+String(i+2)+']').value != '') 
		if (document.forms[formname].elements['location[phone_'+String(i+2)+']'].value != '') {
			document.getElementById('phone_'+String(i+2)+'_'+String(j)).style.display = 'block'; 
			document.getElementById('expand_phone_'+String(i+2)+'_'+String(j)).style.display = 'none';
			if (i<1) {
				document.getElementById('expand_phone_'+String(i+3)+'_'+String(j)).style.display = 'block';
		    }
		}
		
		//if (document.getElementByName('location[im_screenname_'+String(i+2)+']').value != '') 
		if (document.forms[formname].elements['location[im_screenname_'+String(i+2)+']'].value != '') {
		    document.getElementById('IM_'+String(i+2)+'_'+String(j)).style.display = 'block';
			document.getElementById('expand_IM_'+String(i+2)+'_'+String(j)).style.display = 'none';
		    if (i<1) {
				document.getElementById('expand_IM_'+String(i+3)+'_'+String(j)).style.display = 'block';
		    }
		}		        
		
		//if (document.getElementByName('location[email'+email_name_tail[i]+']').value != '') 
		if (document.forms[formname].elements['location[email'+email_name_tail[i]+']'].value != '') {
			document.getElementById('email_'+String(i+2)+'_'+String(j)).style.display = 'block'; 
			document.getElementById('expand_email_'+String(i+2)+'_'+String(j)).style.display = 'none';
		    if (i<1) {
				document.getElementById('expand_email_'+String(i+3)+'_'+String(j)).style.display = 'block';
		    }
		}
		
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
function on_load_execute( )
{  
	/* This array defines the various blocks to be hidden within the form template */
	var hide_blocks = 
		new Array( 'phone_1_2', 	  'phone_1_3',
				   'email_1_2', 	  'email_1_3',
				   'IM_1_2','IM_1_3', 'expand_phone_1_3',
				   'expand_email_1_3','expand_IM_1_3'
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


