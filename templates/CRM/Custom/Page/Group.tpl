{if $action eq 1 or $action eq 2 or $action eq 4}
<form {$form.attributes}>
<div class="form-item">
    <fieldset>
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    {*<dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>*}
    <dt>{$form.extends.label}</dt><dd>{$form.extends.html}</dd>
    <dt>{$form.style.label}</dt><dd>{$form.style.html}</dd>
    <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
    <dt>{$form.help_pre.label}</dt><dd>{$form.help_pre.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>{$form.help_post.label}</dt><dd>{$form.help_post.html|crmReplace:class:huge}&nbsp;</dd>
    <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    {if $action ne 4}
        <div id="crm-submit-buttons">
        <dt></dt><dd>{$form.buttons.html}</dd>
        </div>
    {else}
        <div id="crm-done-button">
        <dt></dt><dd>{$form.done.html}</dd>
        </div>
    {/if} {* $action ne view *}
    </dl>
    </fieldset>
</div>
</form>
{else}
    <div id="help">{ts}Viewing Custom Groups{/ts}</div>
{/if}

{if $rows}
<div id="notes">
<p>
    <div class="form-item">
    {strip}
    <table>
    <tr class="columnheader">
        <th>{ts}Group Title{/ts}</th>
        <th>{ts}Description{/ts}</th>
        <th>{ts}Is Active?{/ts}</th>
        <th>{ts}Used For{/ts}</th>
        <th></th>
    </tr>
    {foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
        <td>{$row.title}</td>
        <td>{$row.description}</td>
        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
        <td>{$row.extends}</td>
        <td>{$row.action}</td>
    </tr>
    {/foreach}
    </table>
    
    {if NOT ($action eq 1 or $action eq 2) }
    <p>
    <div class="action-link">
    <a href="{crmURL p='civicrm/admin/custom/group' q="action=add&reset=1"}">&raquo;  {ts}New Custom Data Group{/ts}</a>
    </div>
    </p>
    {/if}

    {/strip}
    </div>
</p>
</div>
{else}
   {if $action ne 1} {* When we are adding an item, we should not display this message *}
   <div class="message status">
   <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
     {ts}There are no custom data groups for this organization. You can {/ts}<a href="{crmURL p='civicrm/admin/custom/group' q='action=add&reset=1'}">{ts}add one{/ts}</a>.
   </div>
   {/if}
{/if}
