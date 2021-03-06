{* this template is used for adding/editing group (name and description only)  *}
<fieldset>
    <legend>{ts}Group Settings{/ts}</legend>
    <div id="help">
	{if $action eq 2}
	    {capture assign=crmURL}{crmURL p="civicrm/group/search" q="reset=1&force=1&context=smog&gid=`$group.id`"}{/capture}
	    {ts 1=$crmURL}You can edit the Name and Description for this group here. Click <a href='%1'>Show Group Members</a> to view, add or remove contacts in this group.{/ts}
	{else}
	    {ts}Enter a unique name and a description for your new group here. Then click 'Continue' to find contacts to add to your new group.{/ts}
	{/if}
    </div>
    <table class="form-layout">
        <tr>
	    <td class="label">{$form.title.label}</td>
            <td>{$form.title.html|crmReplace:class:huge}
                {if $group.saved_search_id}&nbsp;({ts}Smart Group{/ts}){/if}
            </td>
        </tr>
	
        <tr>
	    <td class="label">{$form.description.label}</td>
	    <td>{$form.description.html}<br />
		<span class="description">{ts}Group description is displayed when groups are listed in Profiles and Mailing List Subscribe forms.{/ts}</span>
            </td>
        </tr>

	{if $form.group_type}
	    <tr>
		<td class="label">{$form.group_type.label}</td>
		<td>{$form.group_type.html} {help id="id-group-type" file="CRM/Group/Page/Group.hlp"}</td>
	    </tr>
	{/if}
    
        <tr>
	    <td class="label">{$form.visibility.label}</td>
	    <td>{$form.visibility.html|crmReplace:class:huge} {help id="id-group-visibility" file="CRM/Group/Page/Group.hlp"}</td>
	</tr>
	
	<tr>
	    <td colspan=2>{include file="CRM/Custom/Form/CustomData.tpl"}</td>
	</tr> 
    </table>

    <fieldset>
	<legend>{ts}Parent Groups{/ts} {help id="id-group-parent" file="CRM/Group/Page/Group.hlp"}</legend>
        {if $parent_groups|@count > 0}
	    <table class="form-layout-compressed">
		<tr>
		    <td><label>{ts}Remove Parent?{/ts}</label></td>
		</tr>
		{foreach from=$parent_groups item=cgroup key=group_id}
		    {assign var="element_name" value="remove_parent_group_"|cat:$group_id}
		    <tr>
			<td>&nbsp;&nbsp;{$form.$element_name.html}&nbsp;{$form.$element_name.label}</td>
		    </tr>
		{/foreach}
	    </table>
	    <br />
        {/if}
        <table class="form-layout-compressed">
	    <tr>
	        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$form.parents.label}</td>
	        <td>{$form.parents.html|crmReplace:class:huge}</td>
	    </tr>
	</table>
    </fieldset>
    {if $form.organization}
	<fieldset>
	    <legend>{ts}Associated Organization{/ts} {help id="id-group-organization" file="CRM/Group/Page/Group.hlp"}</legend>
	        <table class="form-layout-compressed">
		    <tr>
		        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$form.organization.label}</td>
			<td>{$form.organization.html|crmReplace:class:huge}
			    <div id="organization_address" style="font-size:10px"></div>
			</td>
		    </tr>
		</table>
	</fieldset>
    {/if} 
	
    <div class="form-item">
        {$form.buttons.html}
    </div>
    {if $action neq 1}
	<div class="action-link">
	    <a href="{$crmURL}">&raquo; {ts}Show Group Members{/ts}</a>
	    {if $group.saved_search_id} 
	        <br />
		<a href="{crmURL p="civicrm/contact/search/advanced" q="reset=1&force=1&ssID=`$group.saved_search_id`"}">&raquo; {ts}Edit Smart Group Criteria{/ts}</a>
	    {/if}
	</div>
    {/if}
</fieldset>

{literal}
<script type="text/javascript">
{/literal}{if $organizationID}{literal}
    cj(document).ready( function() { 
	//group organzation default setting
	var dataUrl = "{/literal}{crmURL p='civicrm/ajax/search' h=0 q="org=1&id=$organizationID"}{literal}";
	cj.ajax({ 
	        url     : dataUrl,   
	        async   : false,
	        success : function(html){ 
	                    //fixme for showing address in div
	                    htmlText = html.split( '|' , 2);
	                    htmlDiv = htmlText[0].replace( /::/gi, ' ');
			    cj('#organization').val(htmlText[0]);
	                    cj('div#organization_address').html(htmlDiv);
	                  }
	});
    });
{/literal}{/if}{literal}

var dataUrl = "{/literal}{$groupOrgDataURL}{literal}";
cj('#organization').autocomplete( dataUrl, {
					    width : 250, selectFirst : false, matchContains: true  
					    }).result( function(event, data, formatted) {
                                                       cj( "#organization_id" ).val( data[1] );
                                                       htmlDiv = data[0].replace( /::/gi, ' ');
                                                       cj('div#organization_address').html(htmlDiv);
						      });
</script>
{/literal}