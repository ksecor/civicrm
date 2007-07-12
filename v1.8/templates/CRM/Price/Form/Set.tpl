{* add/update/view price set *}

<div class="form-item">
    <fieldset><legend>{ts}Price Set{/ts}</legend>
    <div id="help">
        <p>
        {ts}Use this form to setup the title and group-level help of each set of Price fields.{/ts}
        </p>
    </div>
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}The name of this Price Set{/ts}</dd>
    <dt>{$form.help_pre.label}</dt><dd>{$form.help_pre.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed at the beginning of this group of fields.{/ts}</dd>
    <dt>{$form.help_post.label}</dt><dd>{$form.help_post.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed below this group of fields.{/ts}</dd>
    <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    {if $action ne 4}
        <dt></dt>
        <dd>
        <div id="crm-submit-buttons">{$form.buttons.html}</div>
        </dd>
    {else}
        <dt></dt>
        <dd>
        <div id="crm-done-button">{$form.done.html}</div>
        </dd>
    {/if} {* $action ne view *}
    </dl>
    </fieldset>
</div>
{if $action eq 2 or $action eq 4} {* Update or View*}
    <p></p>
    <div class="action-link">
    <a href="{crmURL p='civicrm/admin/price/field' q="action=browse&reset=1&gid=$gid"}">&raquo;  {ts}Fields for this Set{/ts}</a>
    </div>
{/if}
