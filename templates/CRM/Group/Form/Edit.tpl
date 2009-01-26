{* this template is used for adding/editing group (name and description only)  *}
<fieldset><legend>{ts}Group Settings{/ts}</legend>
    <div id="help">
    {if $action eq 2}
        {capture assign=crmURL}{crmURL p="civicrm/group/search" q="reset=1&force=1&context=smog&gid=`$group.id`"}{/capture}
        {ts 1=$crmURL}You can edit the Name and Description for this group here. Click <a href='%1'>Show Group Members</a> to view, add or remove contacts in this group.{/ts}
    {else}
        {ts}Enter a unique name and a description for your new group here. Then click 'Continue' to find contacts to add to your new group.{/ts}
    {/if}
    </div>
    <table class="form-layout">
        <tr><td class="label">{$form.title.label}</td>
            <td>{$form.title.html}
                {if $group.saved_search_id}&nbsp;({ts}Smart Group{/ts}){/if}
            </td>
        </tr>
        <tr><td class="label">{$form.description.label}</td><td>{$form.description.html}<br />
            <span class="description">{ts}Group description is displayed when groups are listed in Profiles and Mailing List Subscribe forms.{/ts}</span>
            </td>
        </tr>

    {if $form.group_type}
        <tr><td class="label">{$form.group_type.label}</td><td>{$form.group_type.html} {help id="id-group-type"}</td></tr>
    {/if}
    
        <tr><td class="label">{$form.visibility.label}</td><td>{$form.visibility.html} {help id="id-group-visibility"}</td></tr>
		<tr><td colspan=2>{include file="CRM/Custom/Form/CustomData.tpl"}</td></tr> 
    </table>

    <fieldset><legend>{ts}Parent Groups{/ts} {help id="id-group-parent"}</legend>
        {if $parent_groups|@count > 0}
        <table class="form-layout-compressed">
            <tr><td><label>{ts}Remove Parent?{/ts}</label></td></tr>
            {foreach from=$parent_groups item=cgroup key=group_id}
                {assign var="element_name" value="remove_parent_group_"|cat:$group_id}
                <tr><td>&nbsp;&nbsp;{$form.$element_name.html}&nbsp;{$form.$element_name.label}</td></tr>
            {/foreach}
        </table><br />
        {/if}
        <table class="form-layout-compressed">
        <tr>
            <td class="label">{$form.add_parent_group.label}</td>
            <td>{$form.add_parent_group.html}</td>
        </tr>
        </table>
    </fieldset>
    <div class="form-item">
        {$form.buttons.html}
    </div>
    {if $action neq 1}
    <div class="action-link">
        <a href="{$crmURL}">&raquo; {ts}Show Group Members{/ts}</a>
        {if $group.saved_search_id} 
            <br /><a href="{crmURL p="civicrm/contact/search/advanced" q="reset=1&force=1&ssID=`$group.saved_search_id`"}">&raquo; {ts}Edit Smart Group Criteria{/ts}</a>
        {/if}
    </div>
    {/if}
</fieldset>


