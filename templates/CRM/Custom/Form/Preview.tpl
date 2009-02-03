{if $preview_type eq 'group'}
    {capture assign=infoMessage}{ts}Preview of the custom data group (fieldset) as it will be displayed within an edit form.{/ts}{/capture}
    {capture name=legend}
        {foreach from=$groupTree item=fieldName}
          {$fieldName.title}
        {/foreach}
    {/capture}
{else}
    {capture assign=infoMessage}{ts}Preview of this field as it will be displayed in an edit form.{/ts}{/capture}
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
      {if $element.is_view eq 0}{* fix for CRM-2699 *}
	{if $element.options_per_line }
        {*assign var="element_name" value=$element.custom_group_id|cat:_|cat:$field_id|cat:_|cat:$element.name*}
        {assign var="element_name" value=custom_$field_id}
        <dt>{$form.$element_name.label} </dt>
        <dd>
            {assign var="count" value="1"}
                <table class="form-layout-compressed">
                 <tr>
                   {* sort by fails for option per line. Added a variable to iterate through the element array*}
                   {assign var="index" value="1"}
                   {foreach name=outer key=key item=item from=$form.$element_name}
                        {if $index < 10}
                            {assign var="index" value=`$index+1`}
                        {else}
                          <td class="labels font-light">{$form.$element_name.$key.html}</td>
                              {if $count == $element.options_per_line}
                                {assign var="count" value="1"}
                           </tr>
                           <tr>
                            {else}
                                {assign var="count" value=`$count+1`}
                            {/if}
                         {/if}
                    {/foreach}  
                </tr>                  
            </table>
        </dd>
        {if $element.help_post}
            <dt>&nbsp;</dt><dd class="description">{$element.help_post}</dd>
        {/if}
	{else}
        {assign var="name" value=`$element.name`} 
        {*assign var="element_name" value=$group_id|cat:_|cat:$field_id|cat:_|cat:$element.name*}
        {assign var="element_name" value="custom_"|cat:$field_id}  
        <dt>{$form.$element_name.label}</dt>
	<dd class="html-adjust">{$form.$element_name.html}&nbsp;
	    {if $element.html_type eq 'Radio'}
		&nbsp;(&nbsp;<a href="#" title="unselect" onclick="unselectRadio('{$element_name}', '{$form.formName}'); return false;" >{ts}unselect{/ts}</a>&nbsp;) 
	    {/if}
	    {if $element.data_type eq 'Date'}
	        {if $element.skip_calendar NEQ true } 
                <span class="html-adjust">
                    {include file="CRM/common/calendar/desc.tpl" trigger="$element_name"}
		    {include file="CRM/common/calendar/body.tpl" dateVar=$element_name startDate=1905 endDate=2010 doTime=1  trigger="$element_name"}
		</span></dd>
	
	        {/if}
            {/if}
        		
        {if $element.help_post}
            <dt>&nbsp;</dt><dd class="description">{$element.help_post}</dd>
        {/if}
	{/if}
     {/if}
    {/foreach}
    </dl>
    {if $cd_edit.help_post}<br /><div class="messages help">{$cd_edit.help_post}</div>{/if}
    </fieldset>
{/foreach}
{/strip}

<dl>
  <dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</div>
