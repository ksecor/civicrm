{* add/update/view CiviCRM Profile *}

<div class="form-item">
    <fieldset><legend>{ts}CiviCRM Profile{/ts}</legend>
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    {if $action ne 4}
        <dt></dt>
        <dd>
        <div id="crm-submit-buttons">{$form.buttons.html}</div>
        </dd>
    {else}
        <div id="crm-done-button">
        <dt></dt><dd>{$form.done.html}</dd>
        </div>
    {/if} {* $action ne view *}
    </dl>
    </fieldset>
</div>
{if $action eq 2 or $action eq 4} {* Update or View*}
    <p></p>
    <div class="action-link">
    <a href="{crmURL p='civicrm/admin/uf/group/field' q="action=browse&amp;reset=1&amp;gid=$gid"}">&raquo;  {ts}View or Edit Fields for this Profile{/ts}</a>
    </div>
{/if}
