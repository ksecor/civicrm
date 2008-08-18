{literal}
<script type="text/javascript">
    function copyValues(fieldName) 
    {
        var cId = new Array();	
        var i = 0;{/literal}
        {foreach from=$componentIds item=field}
        {literal}cId[i++]{/literal} = {$field}
        {/foreach}
	{literal}        
	
        if ( document.getElementById("field_"+cId[0]+"_"+fieldName ) ) {
	    for ( k=0; k<cId.length; k++ ) {
                document.getElementById("field_"+cId[k]+"_"+fieldName).value = document.getElementById("field_"+cId[0]+"_"+fieldName).value;           }  
    	} else if ( document.getElementsByName("field"+"["+cId[0]+"]"+"["+fieldName+"]") && 
                    document.getElementsByName("field"+"["+cId[0]+"]"+"["+fieldName+"]").length > 0 ) {
  	  if ( document.getElementsByName("field"+"["+cId[0]+"]"+"["+fieldName+"]")[0].type == "radio" ) {
	    for ( t=0; t<document.getElementsByName("field"+"["+cId[0]+"]"+"["+fieldName+"]").length; t++ ) { 
                if  (document.getElementsByName("field"+"["+cId[0]+"]"+"["+fieldName+"]")[t].checked == true ) {break}
	    }
	    if ( t == document.getElementsByName("field"+"["+cId[0]+"]"+"["+fieldName+"]").length ) {
		for ( k=0; k<cId.length; k++ ) {
		    for ( t=0; t<document.getElementsByName("field"+"["+cId[0]+"]"+"["+fieldName+"]").length; t++ ) {
			document.getElementsByName("field"+"["+cId[k]+"]"+"["+fieldName+"]")[t].checked = false;
		    }
		}
	    } else {
		for ( k=0; k<cId.length; k++ ) {
		    document.getElementsByName("field"+"["+cId[k]+"]"+"["+fieldName+"]")[t].checked = document.getElementsByName("field"+"["+cId[0]+"]"+"["+fieldName+"]")[t].checked;
		}
	    }
	  } else if ( document.getElementsByName("field"+"["+cId[0]+"]"+"["+fieldName+"]")[0].type == "checkbox" ) {
	    for ( k=0; k<cId.length; k++ ) {
		document.getElementsByName("field"+"["+cId[k]+"]"+"["+fieldName+"]")[0].checked = document.getElementsByName("field"+"["+cId[0]+"]"+"["+fieldName+"]")[0].checked;
	    }   
	  }
       } else {
         if ( f = document.getElementById('Batch') ) {
           if ( ts = f.getElementsByTagName('table') ) {
             if ( t = ts[0] ) {
               tRows = t.getElementsByTagName('tr') ;
               if ( tRows[1] ) {
                 secondRow = tRows[1] ;
                 inputs = secondRow.getElementsByTagName('input') ;
                 for ( ii = 0 ; ii<inputs.length ; ii++ ) {
                   pattern = 'field['+cId[0]+']['+fieldName+']';
                   if ( inputs[ii].name.search(pattern) && inputs[ii].type == 'checkbox' ) {
                     for ( k=1; k<cId.length; k++ ) {
                       target = document.getElementsByName(inputs[ii].name.replace('field['+cId[0]+']', 'field['+cId[k]+']')) ;
                       if ( target.length > 0 ) {
                         target[0].checked = inputs[ii].checked ;
                       }
                     }
                   }
                 }
               }
             }
           } 
         }
       }    
    }

    function copyValuesDate(fieldName) 
    {
        var cId = new Array();	
        var i = 0;{/literal}
        {foreach from=$contactIds item=field}
        {literal}cId[i++]{/literal} = {$field}
        {/foreach}
	{literal}        
	
        for ( k=0; k<cId.length; k++ ) {
            document.getElementById("field["+cId[k]+"]["+fieldName+"][Y]").value = document.getElementById("field["+cId[0]+"]["+fieldName+"][Y]").value;
            document.getElementById("field["+cId[k]+"]["+fieldName+"][M]").value = document.getElementById("field["+cId[0]+"]["+fieldName+"][M]").value;
            document.getElementById("field["+cId[k]+"]["+fieldName+"][d]").value = document.getElementById("field["+cId[0]+"]["+fieldName+"][d]").value;
        }
    }
</script>
{/literal}
