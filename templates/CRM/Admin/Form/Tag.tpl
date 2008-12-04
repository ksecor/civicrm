{* this template is used for adding/editing a tag (admin)  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Tag{/ts}{elseif $action eq 2}{ts}Edit Tag{/ts}{else}{ts}Delete Tag{/ts}{/if}</legend>
    {if $action eq 1 or $action eq 2 }
        <dl>
        <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
<dt>{$form.parent_id.label}</dt><dd>{$form.parent_id.html}</dd>
        </dl>
        {if $parent_tags|@count > 0}
        <table class="form-layout-compressed">
            <tr><td><label>{ts}Remove Parent?{/ts}</label></td></tr>
            {foreach from=$parent_tags item=ctag key=tag_id}
                {assign var="element_name" value="remove_parent_tag_"|cat:$tag_id}
                <tr><td>&nbsp;&nbsp;{$form.$element_name.html}&nbsp;{$form.$element_name.label}</td></tr>
            {/foreach}
        </table><br />
        {/if}
    {else}
        <div class="status">{ts 1=$delName}Are you sure you want to delete <b>%1</b> Tag?{/ts}</div>
    {/if}
    <dl>
    <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
    <div class="spacer"></div>
</fieldset>
</div>
