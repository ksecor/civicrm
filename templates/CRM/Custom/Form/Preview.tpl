{if $preview_type eq 'group'}
    {capture assign=infoMessage}{ts}Preview of the custom group (fieldset) as it will be displayed when editing a contact.{/ts}{/capture}
    {capture name=legend}
        {foreach from=$groupTree item=name}
        {$name.title}
        {/foreach}
    {/capture}
{else}
    {capture assign=infoMessage}{ts}Preview of this field as it will be displayed when editing a contact.{/ts}{/capture}
{/if}
{include file="CRM/common/info.tpl"}
<div class="form-item">
{strip}

{foreach from=$groupTree item=cd_edit key=group_id}
    <p></p>
    <fieldset>{if $preview_type eq 'group'}<legend>{$smarty.capture.legend}</legend>{/if}
    {if $cd_edit.help_pre}<div class="messages help">{$cd_edit.help_pre}</div><br />{/if}
    <dl>
    {foreach from=$cd_edit.fields item=element key=field_id}
	{if $element.options_per_line}
	{assign var="element_name" value=$group_id|cat:_|cat:$field_id|cat:_|cat:$element.name}
	<dt>{$element.label} </dt>
	<dd>
		{assign var="count" value="1"}
	        <table class="form-layout">
	            {section name=rowLoop start=1 loop=$form.$element_name}
	            {assign var=index value=$smarty.section.rowLoop.index}
	            {if $form.$element_name.$index.html != "" }
		            {if $smarty.section.rowLoop.first}
		            <tr>
	                    {/if} 
			         <td>{$form.$element_name.$index.html}</td>
                            {if $count == $element.options_per_line}
				</tr>
	                        <tr>
	                        {assign var="count" value="1"}
			    {else}
			        {assign var="count" value=`$count+1`}
		            {/if}
                    
			    {if $smarty.section.rowLoop.last}
				</tr>
			    {/if}
		     {/if}
		     {/section}
		</table>
	</dd>
	{else}
        {assign var="name" value=`$element.name`} 
        {assign var="element_name" value=$group_id|cat:_|cat:$field_id|cat:_|cat:$element.name}
        <dt>{$form.$element_name.label}</dt><dd>&nbsp;{$form.$element_name.html}</dd>
        {if $element.help_post}
            <dt>&nbsp;</dt><dd class="description">{$element.help_post}</dd>
        {/if}
	{/if}
    {/foreach}
    </dl>
    </fieldset>
{/foreach}
{/strip}

<dl>
  <dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</div>
