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
 *  This function can be used to clear default 'suggestive text' from an input field
 *  When the cursor is moved into the field.
 *  
 *  It is generally invoked by the input field's onFocus event. Use the reserved
 *  word 'this' to pass this object. EX: onFocus="clearFldVal(this);"
 * 
 * @access public
 * @param  fld The form field object whose value is to be cleared
 * @param  hideBlocks Array of element Id's to be hidden
 * @return none 
 */
function clearFldVal(fld) {
    if (fld.value == fld.defaultValue) {
        fld.value = "";
    }
}

/** 
 *  This function is called by default at the bottom of template files which have forms that have
 *  conditionally displayed/hidden sections and elements. The PHP is responsible for generating
 *  a list of 'blocks to show' and 'blocks to hide' and the template passes these parameters to
 *  this function.
 * 
 * @access public
 * @param  showBlocks Array of element Id's to be displayed
 * @param  hideBlocks Array of element Id's to be hidden
 * @param elementType Value to set display style to for showBlocks (e.g. 'block' or 'table-row' or ...)
 * @return none 
 */
function on_load_init_blocks(showBlocks, hideBlocks, elementType)
{   
    if ( elementType == null ) {
        var elementType = 'block';
    }
    
    /* This loop is used to display the blocks whose IDs are present within the showBlocks array */ 
    for ( var i = 0; i < showBlocks.length; i++ ) {
        var myElement = document.getElementById(showBlocks[i]);
        /* getElementById returns null if element id doesn't exist in the document */
        if (myElement != null) {
            myElement.style.display = elementType;
        } else {
            alert('showBlocks array item not in .tpl = ' + showBlocks[i]);
        }
    }
    
    /* This loop is used to hide the blocks whose IDs are present within the hideBlocks array */ 
    for ( var i = 0; i < hideBlocks.length; i++ ) { 
        var myElement = document.getElementById(hideBlocks[i]);
        /* getElementById returns null if element id doesn't exist in the document */
        if (myElement != null) {
            myElement.style.display = 'none';
        } else {
            alert('showBlocks array item not in .tpl = ' + hideBlocks[i]);
        }
    }
    
}


/** 
 * This function is used to display a page element  (e.g. block or table row or...). 
 * 
 * This function is called by various links which handle requests to display the hidden blocks.
 * An example is the <code>[+] another phone</code> link which expands an additional phone block.
 * The parameter block_id must have the id of the block which has to be displayed.
 *
 * 
 * @access public
 * @param block_id Id value of the block (or row) to be displayed.
 * @param elementType Value to set display style to when showing the element (e.g. 'block' or 'table-row' or ...)
 * @return none
 */
function show(block_id,elementType)
{
    if ( elementType == null ) {
        var elementType = 'block';
    }
    var myElement = document.getElementById(block_id);
    if (myElement != null) {
        myElement.style.display = elementType;
    } else {
        alert('Request to show() function failed. Element id undefined = '+ block_id);
    }
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
    var myElement = document.getElementById(block_id);
    if (myElement != null) {
        myElement.style.display = 'none';
    } else {
        alert('Request to hide() function failed. Element id undefined = ' + block_id);
    }
    
    //    document.getElementById(block_id).style.display = 'none';
}

/**
 *
 * Function for checking ALL or unchecking ALL check boxes in a resultset page.
 *
 * @access public
 * @param fldPrefix - common string which precedes unique checkbox ID and identifies field as
 *                    belonging to the resultset's checkbox collection
 * @param action - 'select' = set all to checked; 'deselect' = set all to unchecked
 * @param form - name of form that checkboxes are part of
 * Sample usage: onClick="javascript:changeCheckboxValues('chk_', 'select', myForm );"
 *
 * @return
 */
function toggleCheckboxVals(fldPrefix,form) {
    for( i=0; i < form.elements.length; i++) {
        fpLen = fldPrefix.length;
        if (form.elements[i].type == 'checkbox' && form.elements[i].name.slice(0,fpLen) == fldPrefix ) {
            element = form.elements[i];
            if (form.toggleSelect.checked == false ) {
                element.checked = false; 
            } else {
                element.checked = true;
            }
        }
    }
    /* function called to change the color of selected rows */
    on_load_init_checkboxes(form.name); 
}

function countSelectedCheckboxes(fldPrefix, form) {
    fieldCount = 0;
    for( i=0; i < form.elements.length; i++) {
        fpLen = fldPrefix.length;
        if (form.elements[i].type == 'checkbox' && form.elements[i].name.slice(0,fpLen) == fldPrefix && form.elements[i].checked == true) {
            fieldCount++;
        }
    }
    return fieldCount;
}

/**
 * This function is used to check if any actio is selected and also to check if any contacts are checked.
 *
 * @access public
 * @param fldPrefix - common string which precedes unique checkbox ID and identifies field as
 *                    belonging to the resultset's checkbox collection
 * @param form - name of form that checkboxes are part of
 * Sample usage: onClick="javascript:checkPerformAction('chk_', myForm );"
 *
 */
function checkPerformAction (fldPrefix, form, taskButton) {
    var cnt;
    var gotTask = 0;
    
    // taskButton TRUE means we don't need to check the 'task' field - it's a button-driven task
    if (taskButton == 1) {
        gotTask = 1;
    }   
    
    else if (document.forms[form].task.selectedIndex) {
        // Doesn't matter if any rows are checked for New/Update Saved Search tasks
        if (document.forms[form].task.value == 16 || document.forms[form].task.value == 32) {
            return true;
        }
        gotTask = 1;
    }

    if (gotTask == 1) {
        // If user wants to perform action on ALL records and we have a task, return (no need to check further)
        if (document.forms[form].radio_ts[1].checked) {
            return true;
        }
	
        cnt = countSelectedCheckboxes(fldPrefix, document.forms[form]);
        if (!cnt) {
            alert ("Please select one or more contact(s) for this action. \n\nTo use the entire set of search results, click the 'all records' radio button.");
            return false;
        }
    } else {
        alert ("Please select an action from the drop-down menu.");
        return false;
    }
}

/**
 * This function changes the style for a checkbox block when it is selected.
 *
 * @access public
 * @param chkName - it is name of the checkbox
 * @param form - name of form that checkboxes are part of
 * @return null
 */
function checkSelectedBox (chkName, form) 
{
    var ss = document.forms[form].elements[chkName].name.substring(7,document.forms[form].elements[chkName].name.length);
    
    var row = 'rowid' + ss;
    
    if (document.forms[form].elements[chkName].checked == true) {
        // change 'all records' radio to 'selected' if any row is checked
        document.forms[form].radio_ts[0].checked = true;
        
        if (document.getElementById(row).className == 'even-row') {
            document.getElementById(row).className = 'selected even-row';
        } else {
            document.getElementById(row).className = 'selected odd-row';
        }
	
    } else {
	
        if (document.getElementById(row).className == 'selected even-row') {
            document.getElementById(row).className = 'even-row';
        } else if (document.getElementById(row).className == 'selected odd-row') {
            document.getElementById(row).className = 'odd-row';
        }
    }
}


/**
 * This function is to show the row with  selected checkbox in different color
 * @param form - name of form that checkboxes are part of
 *
 * @access public
 * @return null
 */

function on_load_init_checkboxes(form) 
{
    var fldPrefix = 'mark_x';
    for( i=0; i < document.forms[form].elements.length; i++) {
	fpLen = fldPrefix.length;
	if (document.forms[form].elements[i].type == 'checkbox' && document.forms[form].elements[i].name.slice(0,fpLen) == fldPrefix ) {
	    checkSelectedBox (document.forms[form].elements[i].name, form); 
	}
    }
    
}

/**
 * Function to change the color of the class
 * 
 * @param form - name of the form
 * @param rowid - id of the <tr>, <div> you want to change
 *
 * @access public
 * @return null
 */

function changeRowColor (rowid, form) {
    
    switch (document.getElementById(rowid).className) 	{
    case 'even-row'          : 	document.getElementById(rowid).className = 'selected even-row';
	break;
    case 'odd-row'           : 	document.getElementById(rowid).className = 'selected odd-row';
	break;
    case 'selected even-row' : 	document.getElementById(rowid).className = 'even-row';
	break;
    case 'selected odd-row'  : 	document.getElementById(rowid).className = 'odd-row';
	break;
    case 'form-item'         : 	document.getElementById(rowid).className = 'selected';
	break;
    case 'selected'          : 	document.getElementById(rowid).className = 'form-item';
	
    }
}

/**
 * This function is to show the row with  selected checkbox in different color
 * @param form - name of form that checkboxes are part of
 *
 * @access public
 * @return null
 */

function on_load_init_check(form) 
{
    for( i=0; i < document.forms[form].elements.length; i++) {
	
      if (
          ( document.forms[form].elements[i].type == 'checkbox' && document.forms[form].elements[i].checked == true )
           ||
          ( document.forms[form].elements[i].type == 'hidden' && document.forms[form].elements[i].value == 1 )
         ) {
              var ss = document.forms[form].elements[i].id;
		      var row = 'rowid' + ss;
		      changeRowColor(row, form);
           }
    }
}

/**
 * This function is used to populate html_type depending on
 * the current data_type.
 *
 * @param data_type - select object
 *
 * @access public
 * @return null
 */
function custom_option_data_type(data_type) 
{
    // get the html_type
    html_type = data_type.form.html_type;
    var data_type_name = data_type.options[data_type.selectedIndex].text;
    var data_type_index = data_type.selectedIndex;
    
    html_type.length=0;
    switch(data_type_name) {
    case "Alphanumeric":
	html_type[0] = new Option('Text',     0, true);
	html_type[1] = new Option('Select',   1);
	html_type[2] = new Option('Radio',    2);
	html_type[3] = new Option('Checkbox', 3);
	break;
    case "Integer":
	html_type[0] = new Option('Text',     0, true);
	html_type[1] = new Option('Select',   1);
	html_type[2] = new Option('Radio',    2);
	break;
    case "Number":
	html_type[0] = new Option('Text',     0, true);
	html_type[1] = new Option('Select',   1);
	html_type[2] = new Option('Radio',    2);
	break;
    case "Money":
	html_type[0] = new Option('Text',     0, true);
	html_type[1] = new Option('Select',   1);
	html_type[2] = new Option('Radio',    2);
	break;
    case "Note":
	html_type[0] = new Option('TextArea', 0, true);
	break;
    case "Date":
	html_type[0] = new Option('Select Date', 0, true);
	break;
    case "Yes or No":
	html_type[0] = new Option('Radio', 0, true);
	break;
    case "State/Province":
	html_type[0] = new Option('Select State/Province', 0, true);
	break;
    case "Country":
	html_type[0] = new Option('Select Country', 0, true);
	break;
    }
    
    // need to call onchange for html_type since we may need to hide custom options
    custom_option_html_type(html_type);
}

/**
 * This function is used to show/hide custom options depending on the html types
 *
 * html_types that need to display the section of custom
 * options are 
 *
 * 'Radio'
 * 'Select'
 * 'Checkbox'
 *
 * @param html_type - select object
 *
 * @access public
 * @return null
 */
function custom_option_html_type(html_type) 
{
    var data_type = html_type.form.data_type;
    var html_type_name = html_type.options[html_type.selectedIndex].text;
    var data_type_name = data_type.options[data_type.selectedIndex].text;
    
    if (data_type_name == "Alphanumeric" || data_type_name == "Integer" || data_type_name == "Number" || data_type_name == "Money") {
	if(html_type_name != "Text") {
	    document.getElementById('showoption').style.display="block";
	    document.getElementById('hideDefaultValTxt').style.display="none";
	    document.getElementById('hideDefaultValDef').style.display="none";
	    document.getElementById('hideDescTxt').style.display="none";
	    document.getElementById('hideDescDef').style.display="none";
	} else {
	    document.getElementById('showoption').style.display="none";
	    document.getElementById('hideDefaultValTxt').style.display="block";
	    document.getElementById('hideDefaultValDef').style.display="block";
	    document.getElementById('hideDescTxt').style.display="block";
	    document.getElementById('hideDescDef').style.display="block";
	}
    } else {
	document.getElementById('showoption').style.display="none";
    }
}

/** 
 * This function is used to hide the table row 
 * also checks whether we have reached the 11th row
 * 
 * @param rowid get the id of tablerow
 * @param index current row index
 * @access public
 * @return null
 *
 */
function hiderow(rowid)
{
	hide(rowid);
        if(document.getElementById('optionFieldLink').style.display == 'none') {
	    document.getElementById('additionalOption').style.display = 'none';
            document.getElementById('optionFieldLink').style.display = '';
        }
	rowcounter++;
}

/** 
 * This function is used to show the table row 
 * also checks whether we have reached the 11th row
 * 
 * @param null
 * @access public
 * @return null
 *
 */
function showrow()
{
    var rowid ;
    
    if(rowcounter == 0) {
	for (var i=2; i<=11; i++) {
            rowid = 'optionField['+i+']';

            if (i == 11) {
		    document.getElementById('additionalOption').style.display = '';
                    document.getElementById('optionFieldLink').style.display = 'none';
            }

	    if(document.getElementById(rowid).style.display == 'none') {
                document.getElementById(rowid).style.display = '';
                if (i < 11)
			document.getElementById('additionalOption').style.display = 'none';
	        break;
            }   
        }

    } else {
        rowcounter--;
	
	for (var i=2; i<=11; i++) {
            rowid = 'optionField['+i+']';
	    
	    if (i == 11) {
		    document.getElementById('additionalOption').style.display = '';
                    document.getElementById('optionFieldLink').style.display = 'none';
            }	
       
	    if(document.getElementById(rowid).style.display == 'none') {
                document.getElementById(rowid).style.display = '';
		if (i < 11) {
                   document.getElementById('additionalOption').style.display = 'none';
		   if(rowcounter == 0) {
		   	document.getElementById('optionFieldLink').style.display = 'none';
			document.getElementById('additionalOption').style.display = '';
		   }
                   break;
		}
            }
        }
    }
}
