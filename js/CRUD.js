

	function on_error_execute( ) 
	{  
	   var j = 1;	
           var analyit = Array ("location2",
		             "location3"
		         );
	
	   for (k=0; k<2; k++) {
		if (document.getElementById('ph'+String(k+2)+String(j)).value != '') {
		    document.getElementById('phone0_'+String(k+2)+'_'+String(j)).style.display = 'block'; 					                document.getElementById('expand_phone0_'+String(k+2)+'_'+String(j)).style.display = 'none';
 		    if (k<1) {
			document.getElementById('expand_phone0_'+String(k+3)+'_'+String(j)).style.display = 'block';
		    }
		}

		if (document.getElementById('im'+String(k+2)+String(j)).value != '') {
		    document.getElementById('IM0_'+String(k+2)+'_'+String(j)).style.display = 'block'; 				                                document.getElementById('expand_IM0_'+String(k+2)+'_'+String(j)).style.display = 'none';
		    if (k<1) {
			document.getElementById('expand_IM0_'+String(k+3)+'_'+String(j)).style.display = 'block';
		    }
		}		                        
		if (document.getElementById('em'+String(k+2)+String(j)).value != '') {
		    document.getElementById('email0_'+String(k+2)+'_'+String(j)).style.display = 'block'; 				                        document.getElementById('expand_email0_'+String(k+2)+'_'+String(j)).style.display = 'none';
		    if (k<1) {
			document.getElementById('expand_email0_'+String(k+3)+'_'+String(j)).style.display = 'block';
		    }
		}

	  }
	
	   for (j = 0; j < analyit.length; j++) {
		   for (i = 0; i < document.CRUD.length; i++) {
			if (document.CRUD.elements[i].name.indexOf(analyit[j]) != -1) {
			    if (document.CRUD.elements[i].type.indexOf("text")!= -1){
				if (document.CRUD.elements[i].value != ''){
			            document.getElementById(analyit[j]).style.display = 'block';
				    if (j<1) {
					document.getElementById('expand_loc'+String(j+3)).style.display = 'block';
				    }
				    for (k=0; k<2; k++) {
				         if (document.getElementById('ph'+String(k+2)+String(j+2)).value != '') {
					     document.getElementById('phone0_'+String(k+2)+'_'+String(j+2)).style.display = 'block'; 					                 document.getElementById('expand_phone0_'+String(k+2)+'_'+String(j+2)).style.display = 'none';
			 		    if (k<1) {
						document.getElementById('expand_phone0_'+String(k+3)+'_'+String(j+2)).style.display = 'block';                   		    	        }			                         
					  }

				         if (document.getElementById('im'+String(k+2)+String(j+2)).value != '') {
					     document.getElementById('IM0_'+String(k+2)+'_'+String(j+2)).style.display = 'block'; 				                 	 document.getElementById('expand_IM0_'+String(k+2)+'_'+String(j+2)).style.display = 'none';
		    			     if (k<1) {
						document.getElementById('expand_IM0_'+String(k+3)+'_'+String(j+2)).style.display = 'block';
		    			     }			                         
					 }

				         if (document.getElementById('em'+String(k+2)+String(j+2)).value != '') {
		   			     document.getElementById('email0_'+String(k+2)+'_'+String(j+2)).style.display = 'block'; 				                         document.getElementById('expand_email0_'+String(k+2)+'_'+String(j+2)).style.display = 'none';			                             if (k<1) {
						document.getElementById('expand_email0_'+String(k+3)+'_'+String(j+2)).style.display = 'block';
		    			     }			                         
					 }             	
						

				     }
			             break;
				 }
			     }

			}
	           }
	    }


		if (document.getElementById("addnote").value != '') {
			document.getElementById("notes").style.display = 'block';
		}

		if (document.getElementById('mdy').value == "click") {
			document.getElementById("demographics").style.display = 'block';
		}

	}


	function on_load_execute( )
	{
			var sections = 
			new Array( 'phone0_2_1', 	'phone0_3_1',
				   'email0_2_1', 	'email0_3_1',
				   'IM0_2_1','IM0_3_1', 'expand_phone0_3_1',
				   'expand_email0_3_1', 'expand_IM0_3_1',
				   'phone0_2_2', 	'phone0_3_2',
				   'email0_2_2', 	'email0_3_2',
				   'IM0_2_2','IM0_3_2', 'expand_phone0_3_2',
				   'expand_email0_3_2', 'expand_IM0_3_2',
				   'phone0_2_3', 	'phone0_3_3',
				   'email0_2_3',	'email0_3_3',
				   'IM0_2_3','IM0_3_3',	'expand_phone0_3_3',
				   'expand_email0_3_3',	'expand_IM0_3_3',
				   'notes','location2',	'demographics',
				   'location3',		'expand_loc3' 
				 );


		var showit = new Array( "core" );

	        for ( var i = 0; i < showit.length; i++ ) {
			document.getElementById(showit[i]).style.display = 'block';
		}

		for ( var i = 0; i < sections.length; i++ ) { 
			document.getElementById(sections[i]).style.display = 'none';
		}

		document.getElementById('fem').checked = 'checked';
	}


	function show(block_id) 
	{
		document.getElementById(block_id).style.display = 'block';
	}


	function hide(block_id) 
	{
		document.getElementById(block_id).style.display = 'none';
	}


	function trim(text_value) 
	{
		chrx = String.fromCharCode(32);

		for(i = 0; i < text_value.length; i++) {
			if (text_value.charAt(i) == chrx) {
				  text_value = text_value.substring(i+1,text_value.length);
			}
 	 
	  		if (text_value.charAt(text_value.length - 1) == chrx) {
	  			text_value = text_value.substring(0,text_value.length -2);
			}
		}
	return text_value;
	}

	function verify_on_submit( ) 
	{

		er = 0;

		if (trim(document.getElementById('firstname').value) == '') {
			alert( 'Please enter the first name' );
			er = 1;
		}
	
		if (document.getElementById('lastname').value == '') {
			alert( 'Please enter the last name' );
			er = 1;
		}

		var regstr = "^[a-z A-Z]+[a-z A-Z 0-9]*[@][a-z A-Z]+[a-z A-Z 0-9]*[.][a-z A-Z][a-z A-Z][a-z A-Z]$"
		var r = "^[@][a-z A-Z]+[a-z A-Z 0-9]*[.][a-z A-Z][a-z A-Z][a-z A-Z]$"
		var regexp = new RegExp( regstr );

		if (regexp.exec(document.getElementById('em11').value) == null) {
			alert("Please enter a valid email address");
			er = 1;
		} 

		if (er ==0) {
			return true;
		}
		else {
			return false;
		}
	}


