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
* @return none 
*/
function on_load_init_blocks(showBlocks, hideBlocks)
{   
    /* This loop is used to display the blocks whose IDs are present within the showBlocks array */ 
    for ( var i = 0; i < showBlocks.length; i++ ) {
        var myElement = document.getElementById(showBlocks[i]);
        /* getElementById returns null if element id doesn't exist in the document */
        if (myElement != null) {
            myElement.style.display = 'block';
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
    var myElement = document.getElementById(block_id);
    if (myElement != null) {
        myElement.style.display = 'block';
    } else {
        alert('Request to show() function failed. Element id undefined = '+ block_id);
    }
    //    document.getElementById(block_id).style.display = 'block';
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
function changeCheckboxVals(fldPrefix, action, form) {
    for( i=0; i < form.elements.length; i++) {
        fpLen = fldPrefix.length;
        if (form.elements[i].type == 'checkbox' && form.elements[i].name.slice(0,fpLen) == fldPrefix ) {
            element = form.elements[i];
            if (action == 'deselect') {
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
function checkPerformAction (fldPrefix, form) {
    var cnt;
    
    if (document.forms[form].task.selectedIndex ) {

	if (document.forms[form].radio_ts[1].checked) {
	    return true;
	}
	if (document.forms[form].task.value == 128) {
	    return true;
	}
	cnt = countSelectedCheckboxes(fldPrefix, document.forms[form]);
	if (!cnt) {
	    alert ("Please select one or more contact(s) for this action. To use the entire set of search results, click the 'all records' radio button.");
	    return false;
	}
    } else {
	alert ("Please select an action from the drop-down menu.");
	return false;
    }
}

/**
 * This function is used to check if any actio is selected and also to check if any contacts are checked.
 *
 * @access public
 * @param chkName - it is name of the checkbox
 * @param form - name of form that checkboxes are part of
 * @return null
 */
function checkSelectedBox (chkName, form) 
{
    var ss = document.forms[form].elements[chkName].name.substring(7,document.forms[form].elements[chkName].name.length);
    
    var row = 'rowid'+ss;

    if (document.forms[form].elements[chkName].checked == true) {
	
	if (document.getElementById(row).getAttribute('class') == 'even-row') {
	    document.getElementById(row).setAttribute('class','selected-even-row');
	} else {
	    document.getElementById(row).setAttribute('class','selected-odd-row');
	}

    } else {

	if (document.getElementById(row).getAttribute('class') == 'selected-even-row') {
	    document.getElementById(row).setAttribute('class','even-row');
	} else {
	    document.getElementById(row).setAttribute('class','odd-row');
	}

    }
    
}


/**
 * This function is to show the row with  selected checkbox in different color
 * @param form - name of form that checkboxes are part of
 *
 * @access public
 * @param form - name of form that checkboxes are part of
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
