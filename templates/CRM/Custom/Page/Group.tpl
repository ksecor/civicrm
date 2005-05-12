{if $action eq 1 or $action eq 2 or $action eq 4}
<form {$form.attributes}>
<div class="form-item">
    <fieldset>
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    {*<dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>*}
    <dt>{$form.extends.label}</dt><dd>{$form.extends.html}</dd>
    <dt>{$form.style.label}</dt><dd>{$form.style.html}</dd>
    {*<dt>{$form.help_pre.label}</dt><dd>{$form.help_pre.html}</dd>*}
    {*<dt>{$form.help_post.label}</dt><dd>{$form.help_post.tml}</dd>*}
    <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
    <dt>{$form.help_pre.label}</dt><dd>{$form.help_pre.html|crmReplace:class:huge}</dd>
    <dt>{$form.help_post.label}</dt><dd>{$form.help_post.html|crmReplace:class:huge}</dd>
    <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    {if $action ne 4}
        <div id="crm-submit-buttons">
        <dt></dt><dd>{$form.buttons.html}</dd>
        </div>
    {/if} {* $action ne view *}
    </dl>
    </fieldset>
</div>
</form>
{/if}

{if $rows}
<div id="notes">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
          <th>Group Title</th>
          <th>Description</th>
          <th>Status</th>
          <th>Used For</th>
          <th></th>
       </tr>
{foreach from=$rows item=row}
       <tr class="{cycle values="odd-row,even-row"}">
         <td>{$row.title}</td>
         <td>{$row.description}</td>
         <td>{$row.is_active}</td>
         <td>{$row.extends}</td>
         <td>{$row.action}</td>
       </tr>
{/foreach}
       </table>
       {/strip}
       {if $action eq 16 or $action eq 4}
        <br/>
       <div class="action-link">
         <a href="{crmURL p='civicrm/admin/custom/group' q="action=add"}">New Custom Data Group</a>
       </div>
       {/if}
    </div>
 </p>
</div>
{else}
   {if $action ne 1} {* When we are adding an item, we should not display this message *}
   <div class="message status">
   <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
     There are no custom data groups for this organization. You can <a href="{crmURL p='civicrm/admin/custom/group' q='action=add'}">add one</a>.
   </div>
   {/if}
{/if}
