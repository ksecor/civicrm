{* this template is used for adding/editing group (name and description only)  *}
<div id="help">
    {if $action eq 2}
        {capture assign=crmURL}{crmURL p="civicrm/group/search" q="reset=1&force=1&context=smog&gid=`$group.id`"}{/capture}
        {ts 1=$crmURL}You can edit the Name and Description for this group here. Click <a href="%1">Show Group Members</a> to view, add or remove contacts in this group.{/ts}
    {else}
        {ts}Enter a unique name and a description for your new group here. Then click 'Continue' to find contacts to add to your new group.{/ts}
    {/if}
</div>
<div class="form-item">
<fieldset><legend>{ts}Group Settings{/ts}</legend>
    <dl>
        <dt>{$form.title.label}</dt>
            <dd>{$form.title.html}{if $group.saved_search_id}&nbsp;({ts}Smart Group{/ts})
            {/if}
            </dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt>{$form.group_type.label}</dt><dd>{$form.group_type.html}</dd>
        <dt>{$form.visibility.label}</dt><dd>{$form.visibility.html}</dd>
        <dt class="extra-long-fourty">&nbsp;</dt>
        <dd class="description">{ts}Select 'User and User Admin Only' if membership in this group is controlled by authorized CiviCRM users only. If you want to allow contacts to join and remove themselves from this group via the Registration and Account Profile forms, select 'Public User Pages'. If you also want to include group membership search and sharing in the Profile screens, select 'Public User Pages and Listings'.{/ts}</dd> 
    </dl>
    {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
</fieldset>
</div>
{if $action neq 1}
<div class="form-item">
<fieldset><legend>{ts}Child Groups{/ts}</legend>
    {if $child_groups|@count > 0}
    <dl>
        <dt>Remove?</dt><dd>Child Group</dd>
        {foreach from=$child_groups item=cgroup key=group_id}
            {assign var="element_name" value="remove_child_group_"|cat:$group_id}
            <dt>{$form.$element_name.html}</dt><dd>{$form.$element_name.label}</dd>
        {/foreach}
    </dl><br/><br/>
    {/if}
    <dl>
    <dt>{$form.add_child_group.label}</dt>
    <dd>{$form.add_child_group.html}</dd>
    </dl>
</fieldset>
</div>
{/if}

{$form.buttons.html}

{if $action neq 1}
<div class="action-link">
    <a href="{$crmURL}">&raquo; {ts}Show Group Members{/ts}</a>
    {if $group.saved_search_id} 
        <br /><a href="{crmURL p="civicrm/contact/search/advanced" q="reset=1&force=1&ssID=`$group.saved_search_id`"}">&raquo; {ts}Edit Smart Group Criteria{/ts}</a>
    {/if}
</div>
{/if}