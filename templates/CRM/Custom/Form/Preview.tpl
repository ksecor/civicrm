{if $preview_type eq 'group'}
    {capture assign=infoMessage}{ts}Preview of the custom group (fieldset) as it will be displayed when editing a contact.{/ts}{/capture}
    {capture name=legend}{$groupTitle}{/capture}
{else}
    {capture assign=infoMessage}{ts}Preview of this field as it will be displayed when editing a contact.{/ts}{/capture}
    {capture name=legend}
        {foreach from=$groupTree item=val key=key}
            {foreach from=$val.fields item=name}
            {$name.label}
            {/foreach}
        {/foreach}
    {/capture}
{/if}
{include file="CRM/common/info.tpl"}
<div class="form-item">
{strip}

{foreach from=$groupTree item=cd_edit key=group_id}
{$cd_edit.title}

    <p>
    <fieldset><legend>{$smarty.capture.legend}</legend>
    {if $cd_edit.help_pre}<div class="message help">{$cd_edit.help_pre}</div><br />{/if}
    <dl>
    {foreach from=$cd_edit.fields item=element key=field_id}
        {assign var="name" value=`$element.name`} 
        {assign var="element_name" value=$group_id|cat:_|cat:$field_id|cat:_|cat:$element.name}
        <dt>{$form.$element_name.label}</dt><dd>&nbsp;{$form.$element_name.html}</dd>
        {if $element.help_post}
            <dt>&nbsp;</dt><dd class="description">{$element.help_post}</dd>
        {/if}
    {/foreach}
    </dl>
    </fieldset>
    </p>
{/foreach}
{/strip}

<dl>
  <dt></dt><dd>{$form.buttons.html}</dd>
</dl>  
</div>
