{if ! empty( $fields )}
<div id="crm-container"> {* wrap in crm-container div so crm styles are used *}

{include file="CRM/common/form_body.tpl"}

    {strip}
    {if $help_pre && $action neq 4}<div class="messages help">{$help_pre}</div><br />{/if}
    {assign var=zeroField value="Initial Non Existent Fieldset"}
    {assign var=fieldset  value=$zeroField}
    {foreach from=$fields item=field key=name}
    {if $field.groupTitle != $fieldset}
        {if $fieldset != $zeroField}
           </table>
           </fieldset>
        {/if}
        <fieldset><legend>{$field.groupTitle}</legend>
        {assign var=fieldset  value=`$field.groupTitle`}
        <table class="form-layout-compressed">
    {/if}
    {assign var=n value=$field.name}
    {if $field.options_per_line > 1}
	<tr>
        <td>{$form.$n.label} </td>
        <td>
	    {assign var="count" value="1"}
	<table class="form-layout">
        <tr>
          {* sort by fails for option per line. Added a variable to iterate through the element array*}
          {assign var="index" value="1"}
          {foreach name=outer key=key item=item from=$form.$element_name}
          {if $index < 10}
              {assign var="index" value=`$index+1`}
          {else}
              <td class="label font-light">{$form.$element_name.$key.html}</td>
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
	</td>
        </tr>
	{else}
        <tr><td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
        {* Show explanatory text for field if not in 'view' mode *}
        {if $field.help_post && $action neq 4}
            <tr><td>&nbsp;</td><td class="description">{$field.help_post}</td></tr>
        {/if}
	{/if}
    {/foreach}
    </table>
    </fieldset>
    {if $help_post && $action neq 4}<br /><div class="messages help">{$help_post}</div>{/if}
    {/strip}
</div> {* end crm-container div *}
{/if} {* fields array is not empty *}
