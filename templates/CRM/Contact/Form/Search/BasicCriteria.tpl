{* Search criteria form elements *}
{* Future ajax auto-complete scripting ...
<script type="text/javascript" src="{crmURL p='civicrm/server/search' q="set=1&path=civicrm/server/search"}"></script>
<script type="text/javascript" src="{$config->resourceBase}js/Search.js"></script>
*}
<fieldset>
    <legend>{if $context EQ 'smog'}<span id="searchForm_hide"><a href="#" onclick="hide('searchForm','searchForm_hide'); show('searchForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a></span>{/if}
        {if $context EQ 'smog'}{ts}Find Members within this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {else}{ts}Search Criteria{/ts}{/if}
    </legend>
 <div class="form-item">
    {strip}
	<table class="form-layout">
		<tr>
            <td class="font-size12pt">{$form.contact_type.label}</td><td>{$form.contact_type.html}</td>
            <td class="label">
                {if $context EQ 'smog'}
                    {$form.group_contact_status.label}<br/>{ts 1=$form.group.html}(for %1){/ts}
                {else}
                    {$form.group.label}
                {/if}
            </td>
            <td>
                {if $context EQ 'smog'}
                    {$form.group_contact_status.html}
                {else}
                    {$form.group.html}
                {/if}
            </td>
            <td class="label">{$form.tag.label}</td><td>{$form.tag.html}</td>
        </tr>
        <tr>
            <td class="label">{$form.sort_name.label}</td>
            <td colspan={if $context EQ 'smog'}"7"{else}"5"{/if}>{$form.sort_name.html}</td>
        </tr>
        <tr><td>&nbsp;</td>
            <td colspan={if $context EQ 'smog'}"6"{else}"4" class="report"{/if}>
                <div class="font-italic">
                {ts}Complete OR partial contact name OR email. To search by first AND last name, enter 'lastname, firstname'. Example: 'Doe, Jane'.{/ts}
                </div></td>
            <td class="label">{$form.buttons.html}</td>
        </tr>
        <tr>
            <td class="label" colspan={if $context EQ 'smog'}"8"{else}"6"{/if}>
                {if $context EQ 'smog'}
                     <a href="{crmURL p='civicrm/group/search/advanced' q="gid=`$group.id`&reset=1&force=1"}">&raquo; {ts}Advanced Search{/ts}</a>
                {elseif $context EQ 'amtg'}
                     <a href="{crmURL p='civicrm/contact/search/advanced' q="context=amtg&amtgID=`$group.id`&reset=1&force=1"}">&raquo; {ts}Advanced Search{/ts}</a>
                {else}
                     <a href="{crmURL p='civicrm/contact/search/advanced'}">&raquo; {ts}Advanced Search{/ts}</a>
                {/if}
            </td>
        </tr>
    </table>
    {/strip}

 </div>
</fieldset>
