{if $previewField }
{capture assign=infoMessage}{ts}Preview of the Civicrm profile field.{/ts}{/capture}
{else}
{capture assign=infoMessage}{ts}Preview of the Civicrm profile (fieldset).{/ts}{/capture}
{/if}
{include file="CRM/common/info.tpl"}
{if ! empty( $fields )}
{if $viewOnly }
<div id="crm-container"> {* wrap in crm-container div so crm styles are used *}
    {strip}
    {if $help_pre && $action neq 4}<div class="messages help">{$help_pre}</div>{/if}
    {assign var=zeroField value="Initial Non Existent Fieldset"}
    {assign var=fieldset  value=$zeroField}
    {foreach from=$fields item=field key=name}
    {if $field.groupTitle != $fieldset}
        {if $fieldset != $zeroField}
           </table>
           {if $groupHelpPost}
              <div class="messages help">{$groupHelpPost}</div>
           {/if}
           {if $mode ne 8}
              </fieldset>
           {/if}
        {/if}
        {if $mode ne 8}
            <fieldset><legend>{$field.groupTitle}</legend>
        {/if}
        {assign var=fieldset  value=`$field.groupTitle`}
        {assign var=groupHelpPost  value=`$field.groupHelpPost`}
        {if $field.groupHelpPre}
            <div class="messages help">{$field.groupHelpPre}</div>
        {/if}
        <table class="form-layout-compressed">
    {/if}
    {assign var=n value=$field.name}
    {if $field.options_per_line > 1}
	<tr>
        <td class="option-label">{$form.$n.label}</td>
        <td>
	    {assign var="count" value="1"}
        {strip}
        <table class="form-layout-compressed">
        <tr>
          {* sort by fails for option per line. Added a variable to iterate through the element array*}
          {assign var="index" value="1"}
          {foreach name=outer key=key item=item from=$form.$n}
          {if $index < 10}
              {assign var="index" value=`$index+1`}
          {else}
              <td class="labels font-light">{$form.$n.$key.html}</td>
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
    </tr>
	{else}
        <tr><td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
	{/if}
        {* Show explanatory text for field if not in 'view' mode *}
        {if $field.help_post && $action neq 4}
            <tr><td>&nbsp;</td><td class="description">{$field.help_post}</td></tr>
        {/if}
    {/foreach}
    </table>
    {if $field.groupHelpPost}
    <div class="messages help">{$field.groupHelpPost}</div>
    {/if}
    {/strip}
</div> {* end crm-container div *}
{else}
	{capture assign=infoMessage}{ts}This Civicrm profile field is view only.{/ts}{/capture}
	{include file="CRM/common/info.tpl"}
{/if}
{/if} {* fields array is not empty *}


<div class=" horizontal-center "> 
	{$form.buttons.html}
</div>
