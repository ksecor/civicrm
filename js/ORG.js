

	/** 
	*  This function executes when there is an error within a form element and the page is relayed from the server
	*  with errors. The mechanism by which this function is fired is present within the template file where the 
	*  condition for count of form errors is checked and if found greater than 0, the following function is called.
	*
	*  This function is loaded at the top of the template file and is called at the bottom according to the condition.
	*  This function checks for data within different elements, within different blocks which are normally hidden on 
	*  the first display. Thus it checks the values of these elements and determines if the block has to be displayed     	
	*  which is based on the presence of values within a block.  
	*/

	function on_error_execute( ) 
	{  
	   var j = 1;	
	
      	   var email_name_tail = Array ("_secondary",
					"_tertiary"
				       );

	   for (k=0; k<2; k++) {

		//if (document.getElementsByName('location[phone_'+String(k+2)+']').value != '') 
		  if (document.forms['ORG'].elements['location[phone_'+String(k+2)+']'].value != '') {
    	    	      document.getElementById('phone0_'+String(k+2)+'_'+String(j)).style.display = 'block'; 
		      document.getElementById('expand_phone0_'+String(k+2)+'_'+String(j)).style.display = 'none';
 		      if (k<1) {
			document.getElementById('expand_phone0_'+String(k+3)+'_'+String(j)).style.display = 'block';
		    }
		}

		//if (document.getElementByName('location[im_screenname_'+String(k+2)+']').value != '') 
		  if (document.forms['ORG'].elements['location[im_screenname_'+String(k+2)+']'].value != '') {
		    document.getElementById('IM0_'+String(k+2)+'_'+String(j)).style.display = 'block';
	            document.getElementById('expand_IM0_'+String(k+2)+'_'+String(j)).style.display = 'none';
		    if (k<1) {
			document.getElementById('expand_IM0_'+String(k+3)+'_'+String(j)).style.display = 'block';
		    }
		}		        
             
		//if (document.getElementByName('location[email'+email_name_tail[k]+']').value != '') 
		  if (document.forms['ORG'].elements['location[email'+email_name_tail[k]+']'].value != '') {
		   document.getElementById('email0_'+String(k+2)+'_'+String(j)).style.display = 'block'; 
		   document.getElementById('expand_email0_'+String(k+2)+'_'+String(j)).style.display = 'none';
		    if (k<1) {
		      document.getElementById('expand_email0_'+String(k+3)+'_'+String(j)).style.display = 'block';
		    }
		}

	  }

	}



	/** 
	* This function is called by default at the bottom of the template file when the page has finished loading the elements
	* It hides certain blocks which are not to be displayed by default on a fresh load.   
	*/

	function on_load_execute( )
	{
			var sections = 
			new Array( 'phone0_2_1', 	'phone0_3_1',
				   'email0_2_1', 	'email0_3_1',
				   'IM0_2_1','IM0_3_1', 'expand_phone0_3_1',
				   'expand_email0_3_1', 'expand_IM0_3_1'
				 );


		var showit = new Array( "core" );

	        for ( var i = 0; i < showit.length; i++ ) {
			document.getElementById(showit[i]).style.display = 'block';
		}

		for ( var i = 0; i < sections.length; i++ ) { 
			document.getElementById(sections[i]).style.display = 'none';
		}


	}

	/** This function is used to display a block. It is usually called by various links which handle requests to display
	*   hidden blocks. An example is the ~another phone~ link which expands an additional phone block.
	*   The parameter block_id must have the id of the block which has to be displayed
  	*/

	function show(block_id) 
	{
		document.getElementById(block_id).style.display = 'block';
	}

	/** This function is used to hide a block. It is usually called by various links which handle requests to hide
	*   visible blocks. An example is the ~hide phone~ link which expands an additional phone block.
	*   The parameter block_id must have the id of the block which has to be hidden.  
	*/

	function hide(block_id) 
	{
		document.getElementById(block_id).style.display = 'none';
	}


