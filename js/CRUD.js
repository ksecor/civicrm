
	/** 
	*  Function USAGE:
	*  This function executes when there is an error within a form element and the page is relayed from the server
	*  with errors.
	*
	*  MECHANISM:
	*  The mechanism by which this function is fired is present within the template file where the condition for count 
	*  of form errors is checked and if found greater than 0, the following function is called.
	*  This function is loaded at the top of the template file and is called at the bottom according to the condition.
	*  This function checks for data within different elements, within different blocks which are normally hidden on 
	*  the first display. Thus it checks the values of these elements and determines if the block has to be displayed     	
	*  which is based on the presence of values within a block.  
	*/

	function on_error_execute( ) 
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
	      The document.forms['CRUD'] returns the CRUD form from within the forms collection of document. This reference 
	      is used further to access its elements collection with element names and further values to access their values.
	      If their values are set, the corresponding block within the template file containing its HTML code is programmed
	      to be displayed. This is done by accessing the block based on its id value using getElementId.
	   */
  
	   for (i=0; i<2; i++) {

		  if (document.forms['CRUD'].elements['location1[phone_'+String(i+2)+']'].value != '') {
    	    	      document.getElementById('phone_1_'+String(i+2)).style.display = 'block'; 
		      document.getElementById('expand_phone_1_'+String(i+2)).style.display = 'none';
 		      if (i<1) {
			document.getElementById('expand_phone_1_'+String(i+3)).style.display = 'block';
		    }
		}

		  if (document.forms['CRUD'].elements['location1[im_screenname_'+String(i+2)+']'].value != '') {
		    document.getElementById('IM_1_'+String(i+2)).style.display = 'block';
	            document.getElementById('expand_IM_1_'+String(i+2)).style.display = 'none';
		    if (i<1) {
			document.getElementById('expand_IM_1_'+String(i+3)).style.display = 'block';
		    }
		}		        
             
		  if (document.forms['CRUD'].elements['location1[email'+email_name_tail[i]+']'].value != '') {
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
		   for (j = 0; j < document.forms['CRUD'].length; j++) {
			if (document.forms['CRUD'].elements[j].name.indexOf(location_name[i]) != -1) {
			    if (document.forms['CRUD'].elements[j].type.indexOf("text")!= -1) {
				if (document.forms['CRUD'].elements[j].value != '') { 
			            document.getElementById(location_name[i]).style.display = 'block';
				    if (i<1) {
					document.getElementById('expand_loc'+String(i+3)).style.display = 'block';
				    }
				    for (k=0; k<2; k++) {
					
		  			  if (document.forms['CRUD'].elements[location_name[i]+'[phone_'+String(k+2)+']'].value != '') {
					    document.getElementById('phone_'+String(i+2)+'_'+String(k+2)).style.display = 'block';
		    		            document.getElementById('expand_phone_'+String(i+2)+'_'+String(k+2)).style.display = 'none';
 		    			    if (k<1) {
					      document.getElementById('expand_phone_'+String(i+3)+'_'+String(k+2)).style.display = 'block';
		    		            }
				        }
				
		  			  if (document.forms['CRUD'].elements[location_name[i]+'[im_screenname_'+String(k+2)+']'].value != '') {
					    document.getElementById('IM_'+String(i+2)+'_'+String(k+2)).style.display = 'block';
					    document.getElementById('expand_IM_'+String(i+2)+'_'+String(k+2)).style.display = 'none';
					    if (k<1) {
					      document.getElementById('expand_IM_'+String(i+3)+'_'+String(k+2)).style.display = 'block';
				            }
					}		        

		  			  if (document.forms['CRUD'].elements[location_name[i]+'[email'+email_name_tail[k]+']'].value != '') {
		   			    document.getElementById('email_'+String(i+2)+'_'+String(k+2)).style.display = 'block';
		                            document.getElementById('expand_email_'+String(i+2)+'_'+String(k+2)).style.display = 'none';
		    			    if (k<1) {
					      document.getElementById('expand_email_'+String(i+3)+'_'+String(k+2)).style.display = 'block';						}
					}



				     }
			             break;
				 }
			     }

			}
	           }
	    }


		  if (document.CRUD.elements["address_note"].value != '') {
			document.getElementById("notes").style.display = 'block';
		}

		  if (document.CRUD.elements["mdyx"].value == 'true') {
			document.getElementById("demographics").style.display = 'block';
		}


	}



	/* Function USAGE:
	*  It hides certain blocks which are not to be displayed by default on a fresh load. 
	*
	*  MECHANISM:
	*  This function is called by default at the bottom of the template file when the page has finished loading the elements. 
       	*/

	function on_load_execute( )
	{
	    /* This array defines the various blocks to be hidden within the form template */
	       var hide_blocks = 
		    new Array( 'phone_1_2', 	     'phone_1_3',
			       'email_1_2', 	     'email_1_3',
			       'IM_1_2','IM_1_3',  'expand_phone_1_3',
			       'expand_email_1_3',  'expand_IM_1_3',
			       'phone_2_2', 	     'phone_2_3',
			       'email_2_2', 	     'email_2_3',
			       'IM_2_2','IM_2_3',  'expand_phone_2_3',
			       'expand_email_2_3',  'expand_IM_2_3',
			       'phone_3_2', 	     'phone_3_3',
			       'email_3_2',	     'email_3_3',
			       'IM_3_2','IM_3_3',  'expand_phone_3_3',
			       'expand_email_3_3',  'expand_IM_3_3',
			       'notes','location2',  'demographics',
			       'location3',	     'expand_loc3' 
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
		//document.forms['CRUD'].elements['exdemo'].value = 'exdemo_onclick'; 
		document.getElementById('exdemo').onclick = exdemof_onclick;

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

        
        
        function exdemof_onclick( ) 
        {
        	show('demographics'); 
                hide('expand_demographics'); 
		return false;

	}



















         
          

        

