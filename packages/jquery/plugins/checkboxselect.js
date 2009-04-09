/*
 * select / unselect checkboxes plugin for jQuery
 *
 */


var lastChecked = null;

$(document).ready(function() {
    $('.form-checkbox').click(function(event) {
	if ( !lastChecked ) {
	    lastChecked = this;
	    return;
	}
	if ( event.shiftKey ) {
	    var start = $('.form-checkbox').index(this);
	    var end   = $('.form-checkbox').index(lastChecked);
	    if ( start == end ) {
		return;
	    }
	    var min   = Math.min( start, end );
	    var max   = Math.max( start, end );
	    if ( lastChecked.checked && this.checked ) {
		lastChecked.checked = true;
	    } else if ( lastChecked.checked  && !this.checked ) {
	    	lastChecked.checked = false;
	    } else if ( !lastChecked.checked && this.checked  ) {
	    	lastChecked.checked = true;
	    } else if (! lastChecked.checked && !this.checked ) {
		lastChecked.checked = false;
	    } 
	    for( i = min; i <= max; i++ ) {
		//check the checkboxes between the two chech boxes
		$('.form-checkbox')[i].checked = lastChecked.checked;
	    }
	    //add class for tr and remove if it unchecked
	    $('.selector tbody tr td:first-child input:checkbox').each( function() {
		var oldClass = $(this).parent().parent().attr('class');
		if ( this.checked ) {
		    $(this).parent().parent().removeClass().addClass('selected '+ oldClass);
		} else {
		    var lastClass = $(this).parent().parent().attr('class');
		    var str       = lastClass.toString().substring(9);
		    if ( lastClass.substring(0,8) == "selected" && ( str == 'even-row' || str == 'odd-row' ) ) {
			$(this).parent().parent().removeClass().addClass(str);
		    }
		}	
	    });	    
	}
	lastChecked = this;
    });
});
