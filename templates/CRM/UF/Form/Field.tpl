<fieldset><legend>{if $action eq 8}{ts}Delete CiviCRM Profile Field{/ts}{else}{ts}CiviCRM Profile Field{/ts}{/if}</legend>
{if $action ne 8} {* do not display stuff for delete function *}
    <div id="crm-submit-buttons-top" class="form-item"> 
    <dl> 
    {if $action ne 4} 
        <dt>&nbsp;</dt><dd>&nbsp;{$form.buttons.html}</dd> 
    {else} 
        <dt>&nbsp;</dt><dd>&nbsp;{$form.done.html}</dd> 
    {/if} {* $action ne view *} 
    </dl> 
    </div>
{/if} {* action ne delete *}
    
    <div class="form-item">
    {if $action eq 8}
      	<div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
            {ts}WARNING: Deleting this profile field will remove it from Profile forms and listings. If this field is used in any 'stand-alone' Profile forms, you will need to update those forms to remove this field.{/ts} {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
    {else}
        <dl>
        <dt>{$form.field_name.label}</dt><dd>&nbsp;{$form.field_name.html}</dd>
        {edit}
            <dt> </dt><dd class="description">&nbsp;{ts}Select the type of CiviCRM record and the field you want to include in this Profile.{/ts}</dd>
        {/edit}  
        <dt>{$form.label.label}</dt><dd>&nbsp;{$form.label.html}</dd>       
        {edit}
            <dt> </dt><dd class="description">&nbsp;{ts}The field label displayed on the form (over-ride the default field label here, if desired).{/ts}</dd>
        {/edit}  
        <dt>{$form.is_required.label}</dt><dd>&nbsp;{$form.is_required.html}</dd>
        {edit}
            <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Are users required to complete this field?{/ts}</dd>
        {/edit}
        <dt>{$form.is_view.label}</dt><dd>&nbsp;{$form.is_view.html}</dd>
        {edit}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}If checked, users can view but not edit this field.{/ts}</dd>
        {/edit}
        <dt>{$form.visibility.label}</dt><dd>&nbsp;{$form.visibility.html}</dd>
        {edit}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Is this field hidden from other users ('User and User Admin Only'), or is it visible to others ('Public User Pages')? Select 'Public User Pages and Listings' to make the field searchable (in the Profile Search form). When visibility is 'Public User Pages and Listings', users can also click the field value when viewing a contact in order to locate other contacts with the same value(s) (i.e. other contacts who live in Poland).{/ts}</dd>
        {/edit}
        <dt id="is_search_label">{$form.is_searchable.label}</dt><dd id="is_search_html">&nbsp;{$form.is_searchable.html}</dd>
        {edit}
        <dt id="is_search_desDt">&nbsp;</dt><dd class="description" id="is_search_desDd">&nbsp;{ts}Do you want to include this field in the Profile's Search form?{/ts}</dd>
        {/edit}
        <dt id="in_selector_label">{$form.in_selector.label}</dt><dd id="in_selector_html">&nbsp;{$form.in_selector.html}</dd>        
        {edit}
        <dt id="in_selector_desDt">&nbsp;</dt><dd id="in_selector_desDd" class="description">&nbsp;{ts}Is this field included as a column in the search results table? This setting applies only to fields with 'Public User Pages and Listings' visibility.{/ts}</dd>
        {/edit}
        <dt>{$form.help_post.label}</dt><dd>&nbsp;{$form.help_post.html|crmReplace:class:huge}</dd>
        {edit}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Explanatory text displayed to users for this field (can include HTML formatting tags).{/ts}</dd>
        {/edit}
        <dt>{$form.weight.label}</dt><dd>&nbsp;{$form.weight.html}</dd>
        {edit}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Weight controls the order in which fields are displayed within a profile. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
        {/edit}
        <dt>{$form.is_active.label}</dt><dd>&nbsp;{$form.is_active.html}</dd>
        </dl>
    
    {/if}
    </div>
    <div id="crm-submit-buttons-bottom" class="form-item">
    <dl>
    {if $action ne 4}
    
        <dt>&nbsp;</dt><dd>&nbsp;{$form.buttons.html}</dd>
    
    {else}
    
        <dt>&nbsp;</dt><dd>&nbsp;{$form.done.html}</dd>
    
    {/if} {* $action ne view *}
    </dl>
    </div>
</fieldset>

 {$initHideBoxes}

{literal}
<script type="text/javascript">
    function showLabel( ) {

       /* Code to set the Field Label */		
       if (document.forms.Field['field_name[0]'].options[document.forms.Field['field_name[0]'].selectedIndex].value) { 
          var labelValue = document.forms.Field['field_name[1]'].options[document.forms.Field['field_name[1]'].selectedIndex].text; 

          if (document.forms.Field['field_name[3]'].value) { 
              labelValue = labelValue + '-' + document.forms.Field['field_name[3]'].options[document.forms.Field['field_name[3]'].selectedIndex].text + ''; 
          }   
          if (document.forms.Field['field_name[2]'].value) { 
              labelValue = labelValue + ' (' + document.forms.Field['field_name[2]'].options[document.forms.Field['field_name[2]'].selectedIndex].text + ')'; 
           }   
       } else {
           labelValue = '';  
       }

       var input = document.getElementById('label');
       input.value = labelValue;

       /* Code to hide searchable attribute for no searchable fields */
       show("is_search_label");
       show("is_search_html");
       show("is_search_desDt");
       show("is_search_desDd");
       
       if (document.getElementsByName("field_name[1]")[0].selectedIndex == -1) {
        return;
       }
       var field2 = document.getElementsByName("field_name[1]")[0][document.getElementsByName("field_name[1]")[0].selectedIndex].text;
       var noSearch;
       {/literal}
         {foreach from=$noSearchable key=dnc item=val}
	   {literal}noSearch = "{/literal}{$val}{literal}";
		if (field2 == noSearch) {
		     hide("is_search_label");
		     hide("is_search_html");
		     hide("is_search_desDt");
		     hide("is_search_desDd");
		}
  	   {/literal}
	 {/foreach}
       {literal}

       /* Code to set Profile Field help, from custom data field help */
        var custom = document.forms.Field['field_name[1]'].value;
        var fieldId = null;

        if ( custom.substring( 0, 7 ) == 'custom_' ) {
           fieldId = custom.substring( custom.length, 7);
        } else {
           dojo.byId('help_post').value = "";
	   return;
	}

	var dataUrl = {/literal}"{crmURL p='civicrm/ajax/custom' h=0 q='id='}"{literal} + fieldId;

        var result = dojo.xhrGet({
        url: dataUrl,
        handleAs: "text",
        timeout: 5000, //Time in milliseconds
        handle: function(response, ioArgs){
                if(response instanceof Error){
                        if(response.dojoType == "cancel"){
                                //The request was canceled by some other JavaScript code.
                                console.debug("Request canceled.");
                        }else if(response.dojoType == "timeout"){
                                //The request took over 5 seconds to complete.
                                console.debug("Request timed out.");
                        }else{
                                //Some other error happened.
                                console.error(response);
                        }
                }else{
		   // on success
                   dojo.byId('help_post').value = response;
                }
        }
     });


    } 

showHideSeletorSearch();
	
function showHideSeletorSearch()
{
	var vsbl= document.getElementById("visibility").options[document.getElementById("visibility").selectedIndex].text;
		if ( vsbl == "User and User Admin Only" ){
			hide("is_search_label");
			hide("is_search_html");
		     	hide("is_search_desDt");
		     	hide("is_search_desDd");
		   	hide("in_selector_label");
		     	hide("in_selector_html");
		     	hide("in_selector_desDt");
		     	hide("in_selector_desDd");
		} else {
	 		show("is_search_label");
       			show("is_search_html");
       			show("is_search_desDt");
	     		show("is_search_desDd");
			show("in_selector_label");
			show("in_selector_html");
			show("in_selector_desDt");
			show("in_selector_desDd");
		}	
}

</script> 
{/literal}
