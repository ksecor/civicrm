{* template for custom data *}

{if $action eq 2}
    <form {$form.attributes}>
    <p>
    <fieldset><legend>Edit Custom Data</legend>
    <div class="form-item">
        {$form.note.html}
        <br/>
        {$form.buttons.html}
    </div>
    </fieldset>
    </p>
    </form>
{/if}

<div id="name" class="data-group form-item">
    <p>
	<label>{$displayName}</label>
        <a href="{crmURL p='civicrm/contact/view/cd' q="cid=`$contactId`&action=update"}">Edit custom data</a>
    </p>
</div>

<div class="form-item">
{foreach from=$groupTree key=fieldset_name item=cd}
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