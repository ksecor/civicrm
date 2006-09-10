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
 *  This function is called when we need to show or hide a related form element (target_element)
 *  based on the value (trigger_value) of another form field (trigger_field).
 * 
 * @access public
 * @param  trigger_field_id     HTML id of field whose onchange is the trigger
 * @param  trigger_value        List of integers - option value(s) which trigger show-element action for target_field
 * @param  target_element_id    HTML id of element to be shown or hidden
 * @param  target_element_type  Type of element to be shown or hidden ('block' or 'table-row')
 * @param  field_type           Type of element radio/select
 * @param  invert               Boolean - if true, we HIDE target on value match; if false, we SHOW target on value match
 * @return none 
*/
function showHideByValue(trigger_field_id, trigger_value, target_element_id, target_element_type, field_type, invert ) {
    if ( target_element_type == null ) {
        var target_element_type = 'block';
    } else if ( target_element_type == 'table-row' ) {
	var target_element_type = '';
    }
    
    if (field_type == 'select') {
        var trigger = trigger_value.split("|");
        var selectedOptionValue = document.getElementById(trigger_field_id).options[document.getElementById(trigger_field_id).selectedIndex].value;	
        
        var target = target_element_id.split("|");
        for(var j = 0; j < target.length; j++) {
            if ( invert ) {  
                show(target[j], target_element_type);
            } else {
                hide(target[j],target_element_type);
            }
            for(var i = 0; i < trigger.length; i++) {
                if (selectedOptionValue == trigger[i]) {
                    if ( invert ) {  
                        hide(target[j],target_element_type);
                    } else {
                        show(target[j],target_element_type);
                    }	
                }
            }
        }
 
    } else if (field_type == 'radio') {

        var target = target_element_id.split("|");
        for(var j = 0; j < target.length; j++) {
	    if (document.getElementsByName(trigger_field_id)[0].checked) {
		if ( invert ) {  
		    hide(target[j], target_element_type);
		} else {
		    show(target[j], target_element_type);
		 }
	    } else {
		if ( invert ) {  
		    show(target[j], target_element_type);
		} else {
		    hide(target[j], target_element_type);
		}
	    }
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
        if (document.forms[form].task.value == 13 || document.forms[form].task.value == 14) {
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
  if (form == 'Search') {
    var formName = form;
  } else {
    var formName = "Advanced";
  } 

    var fldPrefix = 'mark_x';
    for( i=0; i < document.forms[formName].elements.length; i++) {
	fpLen = fldPrefix.length;
	if (document.forms[formName].elements[i].type == 'checkbox' && document.forms[formName].elements[i].name.slice(0,fpLen) == fldPrefix ) {
	    checkSelectedBox (document.forms[formName].elements[i].name, formName); 
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
            rowid = 'optionField_'+i;

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
            rowid = 'optionField_'+i;
	    
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

/**
 * reset all the radio buttons with a given name
 *
 * @param string fieldName
 * @param object form
 * @return null
 */
function unselectRadio(fieldName, form)
{
  for( i=0; i < document.forms[form].elements.length; i++) {
    if (document.forms[form].elements[i].name == fieldName) {
      document.forms[form].elements[i].checked = false;
    }
  }
  return;
}

/**
 * Function to change button text and disable one it is clicked
 *
 * @param obj object - the button clicked
 * @param formID string - the id of the form being submitted
 * @param string procText - button text after user clicks it
 * @return null
 */
var submitcount=0;
/* Changes button label on submit, and disables button after submit for newer browsers.
Puts up alert for older browsers. */
function submitOnce(obj,formId,procText) {
    // if named button clicked, change text
    if (obj.value != null) {
        obj.value = procText + " ...";
    }
    if (document.getElementById) { // disable submit button for newer browsers
        obj.disabled = true;
        document.getElementById(formId).submit();
        return true;
    }
    else { // for older browsers
        if (submitcount == 0) {
            submitcount++;
            return true;
        } else {
            alert("Your request is currently being processed ... Please wait.");
            return false;
        }
    }
}

/**
 * Function submits referenced form on click of wizard nav link.
 * Populates targetPage hidden field prior to POST.
 *
 * @param formID string - the id of the form being submitted
 * @param targetPage - identifier of wizard section target
 * @return null
 */
function submitCurrentForm(formId,targetPage) {
    alert(formId + ' ' + targetPage);
    document.getElementById(formId).targetPage.value = targetPage;
    document.getElementById(formId).submit();
}

/**
 * Function counts and controls maximum word count for textareas.
 *
 * @param essay_id string - the id of the essay (textarea) field
 * @param wc - int - number of words allowed
 * @return null
 */
function countit(essay_id,wc){
    var text_area       = document.getElementById("essay_" + essay_id);
    var count_element   = document.getElementById("word_count_" + essay_id);
    var count           = 0;
    var text_area_value = text_area.value;
    var regex           = /\n/g; 
    var essay           = text_area_value.replace(regex," ");
    var words           = essay.split(' ');
    
    for (z=0; z<words.length; z++){
        if (words[z].length>0){
            count++;
        }
    }
    
    count_element.value     = count;
    if (count>=wc) {
        /*text_area.value     = essay;*/

        var dataString = '';
        for (z=0; z<wc; z++){
	  if (words[z].length>0) {
	    dataString = dataString + words[z] + ' '; 
	  }
	}

	text_area.value = dataString; 
        text_area.blur();
	count = wc;
        count_element.value = count;
        alert("You have reached the "+ wc +" word limit.");
    }
}
