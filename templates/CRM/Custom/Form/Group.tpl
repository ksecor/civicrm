{* add/update/view custom data group *}

<div class="form-item">
    <fieldset><legend>{ts}Custom Data Group{/ts}</legend>
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    <dt>{$form.extends.label}</dt><dd>{$form.extends.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select the type of record that this group of custom data is available for.{/ts}</dd>
    <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Weight controls the order in which custom data groups are presented when there are more than one. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
    <dt>{$form.style.label}</dt><dd>{$form.style.html}</dd>
    <dt>{$form.collapse_display.label}</dt><dd>{$form.collapse_display.html}</dd>
    <dt>{$form.help_pre.label}</dt><dd>{$form.help_pre.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed at the beginning of the group fieldset.{/ts}</dd>
    <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    {if $action ne 4}
        <div id="crm-submit-buttons">
        <dt></dt><dd>{$form.buttons.html}</dd>
        </div>
    {else}
        <div id="crm-done-button">
        <dt></dt><dd>{$form.done.html}</dd>
        </div>
    {/if} {* $action ne view *}
    </dl>
    </fieldset>
</div>
{if $action eq 2 or $action eq 4} {* Update or View*}
    <p>
    <div class="action-link">
    <a href="{crmURL p='civicrm/admin/custom/group/field' q="action=browse&reset=1&gid=$gid"}">&raquo;  {ts}Custom Fields for this Group{/ts}</a>
    </div>
    </p>
{/if}