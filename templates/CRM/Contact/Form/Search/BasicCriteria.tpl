{* Search criteria form elements *}
<fieldset>
    <div class="form-item">
    {if $rows}
        {if $context EQ 'smog'}
            <h3>{ts}Find Members within this Group{/ts}</h3>
        {/if}
    {else}
        {if $context EQ 'smog'}
            <h3>{ts}Find Members within this Group{/ts}</h3>
        {elseif $context EQ 'amtg'}
            <h3>{ts}Find Contacts to Add to this Group{/ts}</h3>
        {/if}
    {/if}

    {strip}
	<table class="no-border">
        <tr>
            <td class="label">{$form.sort_name.label} {$form.sort_name.html}</td>
            <td class="label">{$form.contact_type.label} {$form.contact_type.html}</td>
            <td class="label">
                {if $context EQ 'smog'}
                    {$form.group_contact_status.label}<br />
                {else}
                    {$form.group.label} &nbsp;
                {/if}
                {if $context EQ 'smog'}
                    {$form.group_contact_status.html}
                {else}
                    {$form.group.html}
                {/if}
            <td class="label">{$form.tag.label} {$form.tag.html}</td>
            <td style="vertical-align: bottom;">
                {$form.buttons.html}
            </td>
        </tr>

        {*FIXME : uncomment following fields and place in form layout when subgroup functionality is implemented
        {if $context EQ 'smog'}
           <td>  
             {$form.subgroups.html}
             {$form.subgroups_dummy.html}
          </td>
        {/if}
        *}
    </table>
    {/strip}
    </div>
</fieldset>