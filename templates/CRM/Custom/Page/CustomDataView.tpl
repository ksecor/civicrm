{* Custom Data view mode*}
{foreach from=$viewCustomData item=customValues}
{foreach from=$customValues item=cd_edit key=index}
    <div id="{$cd_edit.name}_show_{$index}" class="section-hidden section-hidden-border">
    <a href="#" onclick="hide('{$cd_edit.name}_show_{$index}'); show('{$cd_edit.name}_{$index}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}{$cd_edit.title}{/ts}</label><br />
    </div>
{debug}
    <div id="{$cd_edit.name}_{$index}" class="form-item">
    <fieldset><legend><a href="#" onclick="hide('{$cd_edit.name}_{$index}'); show('{$cd_edit.name}_show_{$index}'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}{$cd_edit.title}{/ts}</legend>
    {if $cd_edit.help_pre}<div class="messages help">{$cd_edit.help_pre}</div>{/if}
    <dl>
    {foreach from=$cd_edit.fields item=element key=field_id}
        {if $element.options_per_line != 0}
            <dt>{$element.field_title}</dt>
            <dd>
                <table class="form-layout-compressed">
                    {* sort by fails for option per line. Added a variable to iterate through the element array*}
                    {foreach from=$element.field_value item=val}
                        <tr><td class="labels font-light">{$val}</td><tr>
                    {/foreach}
                    </tr>
                </table>
            </dd>
        {else}
            <dt>{$element.field_title}</dt>
            {if $element.field_type == 'File'}
                {if $element.field_value.displayURL}
                    <dd>&nbsp;<a href="javascript:popUp('{$element.field_value.displayURL}')" ><img src="{$element.field_value.displayURL}" height = "100" width="100"></a></dd>
                {else}
                    <dd>&nbsp;<a href="{$element.field_value.fileURL}">{$element.field_value.fileName}</a></dd>
                {/if}
            {else}
                <dd>&nbsp;{$element.field_value}</dd>
            {/if}
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
		hide("{$cd_edit.name}_show_{$index}"); show("{$cd_edit.name}_{$index}");
	{else}
		show("{$cd_edit.name}_show_{$index}"); hide("{$cd_edit.name}_{$index}");
	{/if}
	</script>
{/foreach}
{/foreach}
