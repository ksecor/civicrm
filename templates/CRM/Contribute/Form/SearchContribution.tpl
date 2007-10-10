<div class="form-item">
<fieldset><legend>{ts}Find Contribution Pages{/ts}</legend>
<table class="form-layout">
    <tr>
        <td>{$form.title.label}</td>
        <td>{$form.title.html}
            <div class="description font-italic">
                {ts}Complete OR partial Contribution Title.{/ts}
            </div>
        </td>
        
        <td><label>{ts}Contribution Type{/ts}</td>
        <td>
                <div class="listing-box">
                    {foreach from=$form.contribution_type_id item="contribution_val"}
                    <div class="{cycle values="odd-row,even-row"}">
                         {$contribution_val.html}
                      </div>
                    {/foreach}
                </div>
        </td>
    
    </tr>
    <tr><td>&nbsp</td></tr>
 </table>
        <div class="right">{$form.buttons.html}</div> 
</fieldset>
</div>