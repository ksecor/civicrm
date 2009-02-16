{* Custom Data form*}
{foreach from=$groupTree item=cd_edit key=group_id}
<div id="{$cd_edit.name}_show_{$cgCount}" class="section-hidden section-hidden-border">
	<a href="#" onclick="hide('{$cd_edit.name}_show_{$cgCount}'); show('{$cd_edit.name}_{$cgCount}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}{$cd_edit.title}{/ts}</label><br />
</div>

<div id="{$cd_edit.name}_{$cgCount}" class="form-item">
	<fieldset><legend><a href="#" onclick="hide('{$cd_edit.name}_{$cgCount}'); show('{$cd_edit.name}_show_{$cgCount}'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}{$cd_edit.title}{/ts}</legend>

		{if $cd_edit.help_pre}<div class="messages help">{$cd_edit.help_pre}</div>{/if}
		<dl>
			{foreach from=$cd_edit.fields item=element key=field_id}
			{assign var="element_name" value=$element.element_name}
			{if $element.is_view eq 0}{* fix for CRM-3510 *}
                        {if $element.help_pre}
                            <dt></dt><dd class="html-adjust description">{$element.help_pre}</dd>
			{/if}
			{if $element.options_per_line != 0 }

			<dt>{$form.$element_name.label}</dt>
			<dd class="html-adjust">
				{assign var="count" value="1"}
				<table class="form-layout-compressed" style="margin-top: -0.5em;">
					<tr>
						{* sort by fails for option per line. Added a variable to iterate through the element array*}
						{assign var="index" value="1"}
						{foreach name=outer key=key item=item from=$form.$element_name}
						{if $index < 10}
						{assign var="index" value=`$index+1`}
						{else}
						<td class="labels font-light">{$form.$element_name.$key.html}</td>
						{if $count == $element.options_per_line}
					</tr>
					<tr>
						{assign var="count" value="1"}
						{else}
						{assign var="count" value=`$count+1`}
						{/if}
						{/if}
						{/foreach}
                        {if $element.html_type eq 'Radio'}
                        <td>			            
	                      &nbsp;&nbsp;(&nbsp;<a href="#" title="unselect" onclick="unselectRadio('{$element_name}', '{$form.formName}'); return false;" >{ts}unselect{/ts}</a>&nbsp;) 
                        </td>
				        {/if}
					</tr>
                </table>
			</dd>
			{if $element.help_post}
			<dt></dt><dd class="html-adjust description">{$element.help_post}</dd>
			{/if}
			{else}
			<dt>{$form.$element_name.label}</dt>
			<dd class="html-adjust">{$form.$element_name.html}
				{if $element.html_type eq 'Radio'}
				&nbsp;&nbsp;(&nbsp;<a href="#" title="unselect" onclick="unselectRadio('{$element_name}', '{$form.formName}'); return false;" >{ts}unselect{/ts}</a>&nbsp;) 
				{/if}
				{if $element.data_type eq 'File'}
                    {if $element.element_value.data}
                    <span class="html-adjust"><br />
                        &nbsp;{ts}Attached File{/ts}: &nbsp;
                        {if $element.element_value.displayURL }
                        <a href="javascript:popUp('{$element.element_value.displayURL}')" ><img src="{$element.element_value.displayURL}" height = "100" width="100"></a>
                        {else}
                        <a href="{$element.element_value.fileURL}">{$element.element_value.fileName}</a>
                        {/if}
                        {if $element.element_value.deleteURL }
                        <br />
                        {$element.element_value.deleteURL}
                        {/if}	
                    </span>  
                    {/if} 
				{/if}
				{if $element.data_type eq 'Date' && $element.skip_calendar NEQ true } 
                    {if $element.skip_ampm NEQ true }
                    {include file="CRM/common/calendar/desc.tpl" trigger=trigger_$element_name doTime=1}
                    {include file="CRM/common/calendar/body.tpl" dateVar=$element_name startDate=$currentYear-$element.start_date_years endDate=$currentYear+$element.end_date_years doTime=1 trigger=trigger_$element_name}
                    {else}
                    {include file="CRM/common/calendar/desc.tpl" trigger=trigger_$element_name}
                    {include file="CRM/common/calendar/body.tpl" dateVar=$element_name startDate=$currentYear-$element.start_date_years endDate=$currentYear+$element.end_date_years doTime=1 trigger=trigger_$element_name ampm=1}
                    {/if} 
				{/if}
			</dd>                
			{if $element.help_post}
			<dt>&nbsp;</dt><dd class="html-adjust description">{$element.help_post}</dd>
			{/if}
			{/if}
			{/if}
			{/foreach}
		</dl>
		<div class="spacer"></div>
		{if $cd_edit.help_post}<div class="messages help">{$cd_edit.help_post}</div>{/if}

	</fieldset>
    {if $cd_edit.is_multiple and ( ( $cd_edit.max_multiple eq '' )  or ( $cd_edit.max_multiple > 0 and $cd_edit.max_multiple >= $cgCount ) ) }
        <div id="add-more-link-{$cgCount}"><a href="javascript:buildCustomData('{$cd_edit.extends}','{$cd_edit.extends_entity_column_id}', '{$cd_edit.extends_entity_column_value}', {$cgCount}, {$group_id}, true );">{ts 1=$cd_edit.title}Add another %1 record{/ts}</a></div>	
    {/if}
</div>
<div id="custom_group_{$group_id}_{$cgCount}"></div>

<script type="text/javascript">
{if $cd_edit.collapse_display eq 0 }
	hide("{$cd_edit.name}_show_{$cgCount}"); show("{$cd_edit.name}_{$cgCount}");
{else}
	show("{$cd_edit.name}_show_{$cgCount}"); hide("{$cd_edit.name}_{$cgCount}");
{/if}
</script>
{/foreach}


