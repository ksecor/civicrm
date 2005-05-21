<div class="form-item">
<p>
<fieldset><legend>Edit Custom Data</legend>
{strip}
{foreach from=$groupTree item=cd_edit key=group_id}
    <fieldset><legend>{$cd_edit.title}</legend>
    {if $cd_edit.help_pre}<div class="message help">{$cd_edit.help_pre}</div>{/if}
    {foreach from=$cd_edit.fields item=element key=field_id}
        {assign var="name" value=`$element.name`} 
        {assign var="element_name" value=$group_id|cat:_|cat:$field_id|cat:_|cat:$element.name}
        <dl>
            <dt>{$form.$element_name.label}</dt><dd>{$form.$element_name.html}{if $element.help_post}<span class="description">{$element.help_post}</span>{/if}</dd>
        </dl>
    {/foreach}
    </fieldset>
{/foreach}
{/strip}

<dl>
  <dt></dt><dd>{$form.buttons.html}</dd>
</dl>  

</fieldset>
</p>
</div>
