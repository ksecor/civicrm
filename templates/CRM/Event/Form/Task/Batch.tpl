<div class="form-item">
<fieldset>
<div id="help">
    {ts}Update field values for each participation as needed. Click <strong>Update Event Participtions</strong> below to save all your changes. To set a field to the same value for ALL rows, enter that value for the first participation and then click the <strong>Copy icon</strong> (next to the column title).{/ts}
</div>
    <legend>{$profileTitle}</legend>
         <table>
            <tr class="columnheader">
             <th>{ts}Name{/ts}</th>
             <th>{ts}Event{/ts}</th>
             {foreach from=$fields item=field key=fieldName}
                {if strpos( $field.name, '_date' ) !== false}   
                  <th><img  src="{$config->resourceBase}i/copy.png" alt="{ts 1=$field.title}Click to copy %1 from row one to all rows.{/ts}" onclick="copyValuesDate('{$field.name}')" class="action-icon" title="{ts}Click here to copy the value in row one to ALL rows.{/ts}" />{$field.title}</th>
                {else}
                  <th><img  src="{$config->resourceBase}i/copy.png" alt="{ts 1=$field.title}Click to copy %1 from row one to all rows.{/ts}" onclick="copyValues('{$field.name}')" class="action-icon" title="{ts}Click here to copy the value in row one to ALL rows.{/ts}" />{$field.title}</th>
                {/if}
             {/foreach}
            </tr>
            {foreach from=$componentIds item=pid}
             <tr class="{cycle values="odd-row,even-row"}">
              <td>{$details.$pid.name}</td> 
              <td>{$details.$pid.title}</td>   
              {foreach from=$fields item=field key=fieldName}
                {assign var=n value=$field.name}
                <td class="compressed">{$form.field.$pid.$n.html}</td> 
              {/foreach}
             </tr>
            {/foreach}
           </tr>
         </table>
        <dl>
            <dt></dt><dd>{if $fields}{$form._qf_Batch_refresh.html}{/if} &nbsp; {$form.buttons.html}</dd>
        </dl>
</fieldset>
</div>

{*include batch copy js js file*}
{include file="CRM/common/batchCopy.tpl"}
