function verify_page_number ()
{
    var er = true;

    var regstr = "[0-9]+";
    var regexp = new RegExp( regstr );
    if (regexp.exec(document.getElementById('page_no').value) == null) {
 	alert("Please enter a valid page number");
 	er = false;
    } 

    if (parseInt(document.getElementById('page_no').value) >
	parseInt(document.forms['CLIST'].elements['hidden_row_count'].value)) {
	alert("The page number entered is greater than the maximum pages available");
	er = false;
	}

    return er;
}

function select_checkboxes (flag)
{
    var rec_count = document.forms['CLIST'].elements['hidden_row_count'].value;
    
    for (i=0; i<rec_count; i++) {
	document.forms['CLIST'].elements['checkbox_group[checkrecord_' + i + ']'].checked = flag;
    }
    return false;    
}


