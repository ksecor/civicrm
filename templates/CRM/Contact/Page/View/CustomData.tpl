{* template for custom data *}

{if $action eq 2}
    <form {$form.attributes}>
    <div class="form-item">
    <p>
    <fieldset><legend>Edit Custom Data</legend>
    {foreach from=$groupTree2 item=arr key=fieldset_name}
        {foreach from=$arr item=element}
        {assign var="element_name" value=`$element.name`} 
        <dl>
        {$form.note.html}
        <dt>{$form.$element_name.label}</dt><dd>{$form.$element_name.html}</dd>
        </dl>
        {/foreach}
    {/foreach}
    <dl>
    <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>  
    </fieldset>
    </p>
    </div>
    </form>
{/if}

<div id="name" class="data-group form-item">
    <p>
	<label>{$displayName}</label>
        <a href="{crmURL p='civicrm/contact/view/cd' q="cid=`$contactId`&action=update"}">Edit custom data</a>
    </p>
</div>


<div class="form-item">
{foreach from=$groupTree1 key=fieldset_name item=cd}
<fieldset><legend>{$fieldset_name}</legend>
    {foreach from=$cd item=cd_value key=cd_name}
    <dl>
    <dt>{$cd_name}</dt>
    <dd>{if $cd_value}{$cd_value}{else}--{/if}</dd>
    </dl>
    {/foreach}
</fieldset>
{/foreach}
</div>
