{* Custom Data view mode*}
{strip}
{foreach from=$viewCustomData item=cd_edit key=group_id}
    <div id="{$cd_edit.name}_show" class="section-hidden section-hidden-border">
    <a href="#" onclick="hide('{$cd_edit.name}_show'); show('{$cd_edit.name}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}{$cd_edit.title}{/ts}</label><br />
    </div>

    <div id="{$cd_edit.name}" class="form-item">
    <fieldset><legend><a href="#" onclick="hide('{$cd_edit.name}'); show('{$cd_edit.name}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}{$cd_edit.title}{/ts}</legend>
    {if $cd_edit.help_pre}<div class="messages help">{$cd_edit.help_pre}</div>{/if}
    <dl>
    {foreach from=$cd_edit.fields item=element key=field_id}
	{if $element.options_per_line != 0}
    
        {assign var="element_name" value=$group_id|cat:_|cat:$field_id|cat:_|cat:$element.name}			
        <dt>{$element.element_title}</dt>
        <dd>
        {assign var="count" value="1"}
        {strip}
        <table class="form-layout-compressed">
        <tr>
            {* sort by fails for option per line. Added a variable to iterate through the element array*}
            {assign var="index" value="1"}
            {foreach name=outer key=key item=item from=$element.element_value}
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
        </tr>
        </table>
        {/strip}
        </dd>
	{else}
        {assign var="name" value=`$element.name`} 
        {assign var="element_name" value=$group_id|cat:_|cat:$field_id|cat:_|cat:$element.name}
        <dt>{$element.field_title}</dt><dd>&nbsp;{$element.field_value}</dd>
        {if $element.help_post}
            <dt>&nbsp;</dt><dd class="description">{$element.help_post}</dd>
        {/if}
	{/if}
    {/foreach}
    </dl>
    {if $cd_edit.help_post}<div class="messages help">{$cd_edit.help_post}</div>{/if}
    </fieldset>
    </div>

	<script type="text/javascript">
	{if $cd_edit.collapse_display eq 0 }
		hide("{$cd_edit.name}_show"); show("{$cd_edit.name}");
	{else}
		show("{$cd_edit.name}_show"); hide("{$cd_edit.name}");
	{/if}
	</script>
{/foreach}
{/strip}