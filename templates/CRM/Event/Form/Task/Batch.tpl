<div class="form-item">
<fieldset>
<div id="help">
    {if $context EQ 'statusChange'} {* Update Participant Status task *}
        {ts}Update the status for each participant individually, OR change all statuses to:{/ts}
        {$form.status_change.html}  {help id="id-status_change"}
        {if $notifyingStatuses}
          <div class="status">
            {ts 1=$notifyingStatuses}Participants whose status is changed TO any of the following will be automatically notified via email: %1.{/ts}
          </div>
        {/if}
    {else}
        {ts}Update field values for each participant as needed. To set a field to the same value for ALL rows, enter that value for the first participation and then click the <strong>Copy icon</strong> (next to the column title).{/ts}
    {/if}
    <p>{ts}Click <strong>Update Participant(s)</strong> below to save all your changes.{/ts}</p>
</div>
    <legend>{$profileTitle}</legend>
         <table>
            <tr class="columnheader">
             <th>{ts}Name{/ts}</th>
             <th>{ts}Event{/ts}</th>
             {foreach from=$fields item=field key=fieldName}
                {if strpos( $field.name, '_date' ) !== false ||
                    (substr( $field.name, 0, 7 ) == 'custom_' && $field.data_type == 'Date')}   
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
