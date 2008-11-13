<div class="form-item">
<fieldset><legend>{ts}Find Contribution Pages{/ts}</legend>
<table class="form-layout">
    <tr>
        <td class="label">{$form.title.label}</td>
        <td>{$form.title.html}
            <div class="description font-italic">
                {ts}Complete OR partial Contribution Page title.{/ts}
            </div>
        </td>
        
        <td class="label">{ts}Contribution Type{/ts}</td>
        <td>
                <div class="listing-box">
                    {foreach from=$form.contribution_type_id item="contribution_val"}
                    <div class="{cycle values="odd-row,even-row"}">
                         {$contribution_val.html}
                      </div>
                    {/foreach}
                </div>
        </td>
        <td class="right">&nbsp;{$form.buttons.html}</td>  
    </tr>
 </table>
</fieldset>
</div>