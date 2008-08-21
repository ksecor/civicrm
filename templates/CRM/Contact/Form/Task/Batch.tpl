<div class="form-item">
<fieldset>
<div id="help">
    {ts}Update field values for each contact as needed. Click <strong>Update Contacts</strong> below to save all your changes. To set a field to the same value for ALL rows, enter that value for the first contact and then click the <strong>Copy icon</strong> (next to the column title).{/ts}
</div>
    <legend>{$profileTitle}</legend>
         <table>
            <tr class="columnheader">
             <th>{ts}Name{/ts}</th>
             {foreach from=$fields item=field key=fieldName}
                {if strpos( $field.name, '_date' ) !== false || 
                   (substr( $field.name, 0, 7 ) == 'custom_' && $field.data_type == 'Date') }
                  <th><img  src="{$config->resourceBase}i/copy.png" alt="{ts 1=$field.title}Click to copy %1 from row one to all rows.{/ts}" onclick="copyValuesDate('{$field.name}')" class="action-icon" title="{ts}Click here to copy the value in row one to ALL rows.{/ts}" />{$field.title}</th>
                {else}
                  <th><img  src="{$config->resourceBase}i/copy.png" alt="{ts 1=$field.title}Click to copy %1 from row one to all rows.{/ts}" onclick="copyValues('{$field.name}')" class="action-icon" title="{ts}Click here to copy the value in row one to ALL rows.{/ts}" />{$field.title}</th>             
                {/if}
             {/foreach}
            </tr>
            {foreach from=$componentIds item=cid}
             <tr class="{cycle values="odd-row,even-row"}">
              <td>{$sortName.$cid}</td> 
              {foreach from=$fields item=field key=fieldName}
                {assign var=n value=$field.name}
{if $field.options_per_line}
        <td class="compressed">
	    {assign var="count" value="1"}
        {strip}
        <table class="form-layout-compressed">
        <tr>
          {* sort by fails for option per line. Added a variable to iterate through the element array*}
          {assign var="index" value="1"}
          {foreach name=optionOuter key=optionKey item=optionItem from=$form.field.$cid.$n}
          {if $index < 10}
              {assign var="index" value=`$index+1`}
          {else}
              <td class="labels font-light">{$form.field.$cid.$n.$optionKey.html}</td>
              {if $count == $field.options_per_line}
                  </tr>
                   <tr>
                   {assign var="count" value="1"}
              {else}
          	       {assign var="count" value=`$count+1`}
              {/if}
          {/if}
          {/foreach}
        </tr>
        </table>
        {/strip}
        </td>
{else}
                <td class="compressed">{$form.field.$cid.$n.html}</td>
{/if}
              {/foreach}
             </tr>
            {/foreach}
           </tr>
         </table>
        <dl>
            <dt></dt><dd>{if $fields}{$form._qf_BatchUpdateProfile_refresh.html}{/if} &nbsp; {$form.buttons.html}</dd>
        </dl>
</fieldset>
</div>

{*include batch copy js js file*}
{include file="CRM/common/batchCopy.tpl"}

