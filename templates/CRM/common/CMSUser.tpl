{if $showCMS }{*true if is_cms_user field is set *}
 {* NOTE: We are currently not supporting the Drupal registration mode where user enters their password. But logic is left here for when we figure it out. *}

   <fieldset>
      <div class="messages help">
	 {if !$isCMS}
	    {ts}If you would like to create an account on this site, check the box below and enter a user name{/ts}
	    {if $form.cms_pass}
	       {ts}and a password{/ts}
	    {/if}
	 {else}
	    {ts}Please enter a user name to create an account{/ts}
	 {/if}.
	 {ts 1=$loginUrl}If you already have an account, <a href='%1'>please login</a> before completing this form.{/ts}
      </div>
      <div>{$form.cms_create_account.html} {$form.cms_create_account.label}</div>
      <div id="details">
	 <table class="form-layout-compressed">
	    <tr>
	       <td>{$form.cms_name.label}</td>
	       <td>{$form.cms_name.html} <a id="checkavailability" href="#">{ts}<strong>Check Availability</strong>{/ts}</a>
	          <span id="msgbox" style="display:none"></span><br />
	          <span class="description">{ts}Your preferred username; punctuation is not allowed except for periods, hyphens, and underscores.{/ts}</span>
	       </td>
	    </tr>
    
	    {if $form.cms_pass}
	       <tr>
	          <td>{$form.cms_pass.label}</td>
	          <td>{$form.cms_pass.html}</td>
	       </tr>        
	       <tr>
	          <td>{$form.cms_confirm_pass.label}</td>
	          <td>{$form.cms_confirm_pass.html}<br />
	             <span class="description">{ts}Provide a password for the new account in both fields.{/ts}
	          </td>
	       </tr>
	    {/if}
	 </table>        
      </div>
   </fieldset>

   {literal}
   <script type="text/javascript">
   {/literal}
   {if !$isCMS}
      {literal}
      if ( document.getElementsByName("cms_create_account")[0].checked ) {
	 show('details');
      } else {
	 hide('details');
      }
      {/literal}
   {/if}
   {literal}
   function showMessage( frm )
   {
      var cId = {/literal}'{$cId}'{literal};
      if ( cId ) {
	 alert("You are logged-in user");
	 frm.checked = false;
      } else {
	 var siteName = {/literal}'{$config->userFrameworkBaseURL}'{literal};
	 alert("Please login if you have an account on this site with the link " + siteName  );
      }
   }
   var lastName = null;
   cj("#checkavailability").click(function() {
      var cmsUserName = cj.trim(cj("#cms_name").val());
      if ( lastName == cmsUserName) {
	 /*if user checking the same user name more than one times. avoid the ajax call*/
	 return;
      }
      if (cmsUserName) {
	 /*take all messages in javascript variable*/
	 var check        = "{/literal}{ts}Checking...{/ts}{literal}";
	 var available    = "{/literal}{ts}Username available to register{/ts}{literal}";
	 var notavailable = "{/literal}{ts}Username Already exists{/ts}{literal}";
         
         //remove all the class add the messagebox classes and start fading
         cj("#msgbox").removeClass().addClass('cmsmessagebox').css({"color":"#000","backgroundColor":"#FFC","border":"1px solid #c93"}).text(check).fadeIn("slow");
	 
      	 //check the username exists or not from ajax
	 var contactUrl = {/literal}"{crmURL p='civicrm/ajax/cmsuser' h=0 }"{literal};
	 
	 cj.post(contactUrl,{ cms_name:cj("#cms_name").val() } ,function(data) {
	    if ( data.name == "no") {/*if username not avaiable*/
	       cj("#msgbox").fadeTo(200,0.1,function() {
		  cj(this).html(notavailable).addClass('cmsmessagebox').css({"color":"#CC0000","backgroundColor":"#F7CBCA","border":"1px solid #CC0000"}).fadeTo(900,1);
	       });
	    } else {
	       cj("#msgbox").fadeTo(200,0.1,function() {
		  cj(this).html(available).addClass('cmsmessagebox').css({"color":"#008000","backgroundColor":"#C9FFCA", "border": "1px solid #349534"}).fadeTo(900,1);
	       });
	    }	    
	 }, "json");
	 lastName = cmsUserName;
      } else {
	 cj("#msgbox").removeClass().text('').fadeIn("fast");
      }
   });

   </script>
   {/literal}
   {if !$isCMS}	
      {include file="CRM/common/showHideByFieldValue.tpl" 
      trigger_field_id    ="cms_create_account"
      trigger_value       =""
      target_element_id   ="details" 
      target_element_type ="block"
      field_type          ="radio"
      invert              = 0
      }
   {/if}
{/if}
