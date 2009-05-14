<div id="contact-dialog" style="display:none;"/>
<script type="text/javascript">
{literal}
function newContact( ) {
    cj("#newContact").toggle( );
    var dataURL = {/literal}"{crmURL p='civicrm/profile/create?reset=1&gid=1&snippet=5' h=0 }"{literal};
    cj.ajax({
       url: dataURL,
       success: function( content ){
           //var profileForm = '<form id="profileContact" action="/civicrm/profile/create" method="POST">' + content + '</form>';
           cj("#contact-dialog").show( ).html( content ).dialog({
       	    	title: "Create New Contact",
           		modal: true,
           		width: 680, 
           		overlay: { 
           			opacity: 0.5, 
           			background: "black" 
           		},

               beforeclose: function(event, ui) {
                   cj(this).dialog("destroy");
               }
           	});
           	
           	var options = { 
                //target:        '#newContact',   // target element(s) to be updated with server response
                beforeSubmit:  showRequest,  // pre-submit callback  
                success:       showResponse  // post-submit callback 
            }; 
            
           	// bind to the form's submit event 
            cj('#Edit').submit(function() { 
                // inside event callbacks 'this' is the DOM element so we first 
                // wrap it in a jQuery object and then invoke ajaxSubmit 
                cj(this).ajaxSubmit(options); 

                // !!! Important !!! 
                // always return false to prevent standard browser submit and page navigation 
                return false; 
            }); 
            
       }
     });
}

// pre-submit callback 
function showRequest(formData, jqForm, options) { 
    // formData is an array; here we use $.param to convert it to a string to display it 
    // but the form plugin does this for you automatically when it submits the data 
    var queryString = cj.param(formData); 

    // jqForm is a jQuery object encapsulating the form element.  To access the 
    // DOM element for the form do this: 
    // var formElement = jqForm[0]; 

    alert('About to submit: \n\n' + queryString); 

    var dataUrl = {/literal}"{crmURL p='civicrm/profile/create' h=0 }"{literal}; 
    cj.ajax({
       type: "POST",
       url: dataUrl,
       async: false,
       data: queryString + '&snippet=5&gid=1',
       success: function( response ) {
           cj("#contact-dialog").html( response );
           //alert( "Data Saved: " + msg );
       }
     });
    
    // cj("#contact-dialog").dialog("close");

    // here we could return false to prevent the form from being submitted; 
    // returning anything other than false will allow the form submit to continue 
    return false; 
}

// post-submit callback 
function showResponse(responseText, statusText)  { 
    // for normal html responses, the first argument to the success callback 
    // is the XMLHttpRequest object's responseText property 

    // if the ajaxSubmit method was passed an Options Object with the dataType 
    // property set to 'xml' then the first argument to the success callback 
    // is the XMLHttpRequest object's responseXML property 

    // if the ajaxSubmit method was passed an Options Object with the dataType 
    // property set to 'json' then the first argument to the success callback 
    // is the json data object returned by the server 
    
    //cj("#contact-dialog").html(responseText);
    /*
    alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + 
        '\n\nThe output div should have already been updated with the responseText.'); 
        */
}

{/literal}
</script>


