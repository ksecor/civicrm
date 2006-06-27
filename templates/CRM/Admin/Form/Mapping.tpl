{* this template is used for adding/editing a tag (admin)  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Tag{/ts}{elseif $action eq 2}{ts}Edit Mapping{/ts}{else}{ts}Delete Mapping{/ts}{/if}</legend>
    {if $action eq 1 or $action eq 2 }
        <dl>
        <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt>{$form.mapping_type.label}</dt><dd>{$form.mapping_type.html}</dd>
        </dl>
    {else}
        <div class="status">
        <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/>
        {ts 1=$mappingType}WARNING : Are you sure you want to delete <b>%1</b> Mapping? This action can not be undone.{/ts}</div>
    {/if}
    <dl>
    <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
    <div class="spacer"></div>
</fieldset>
</div>
