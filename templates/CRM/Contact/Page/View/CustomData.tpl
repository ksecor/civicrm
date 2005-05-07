{* template for custom data *}
<div id="name" class="data-group form-item">
    <p>
	<label>{$displayName}</label>
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


