{* template for adding form elements for selecting existing or creating new contact*}
{if $context ne 'search'}
<tr id="contact-success" style="display:none;"><td></td><td><span class="success-status">{ts}New contact has been created.{/ts}</span></td></tr>
<tr>
    <td class="label">{$form.contact.label}</td><td>{$form.contact.html}&nbsp;&nbsp;{ts}OR{/ts}&nbsp;&nbsp;{$form.profiles.html}<div id="contact-dialog" style="display:none;"/>
    </td>
</tr>
{/if}
{literal}
<script type="text/javascript">
  cj( function( ) {
      var contactUrl = {/literal}"{crmURL p='civicrm/ajax/contactlist' h=0 }"{literal};

      cj("#contact").autocomplete( contactUrl, {
      	selectFirst: false 
      }).focus();

      cj("#contact").result(function(event, data, formatted) {
      	cj("input[name=contact_select_id]").val(data[1]);
      });

      cj("#contact").bind("keypress keyup", function(e) {
          if ( e.keyCode == 13 ) {
              return false;
          }
      });
  });

  function newContact( gid ) {
      var dataURL = {/literal}"{crmURL p='civicrm/profile/create' q='reset=1&snippet=5&context=dialog' h=0 }"{literal};
      dataURL = dataURL + '&gid=' + gid;
      cj.ajax({
         url: dataURL,
         success: function( content ) {
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
         }
      });
  }
        
</script>
{/literal}

