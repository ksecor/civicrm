{* add/update/view CiviCRM Profile *}

<div class="form-item">
    <fieldset><legend>{if $action eq 8}{ts}Delete CiviCRM Profile{/ts}{else}{ts}CiviCRM Profile{/ts}{/if}</legend>
	{if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
          <dd>    
          {ts 1=$title}Delete %1 Profile?{/ts}
          </dd>
       </dl>
      </div>
    {else}
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    <dt>{$form.uf_group_type.label}</dt><dd>{$form.uf_group_type.html}</dd>
    <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Weight controls the order in which profiles are presented when there are more than one. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
    <dt>{$form.help_pre.label}</dt><dd>{$form.help_pre.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed at the beginning of the fieldset.{/ts}</dd>
    <dt>{$form.help_post.label}</dt><dd>{$form.help_post.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed at the end of the fieldset.{/ts}</dd>
    <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    
    </dl>
    {/if}

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
    </fieldset>
</div>
{if $action eq 2 or $action eq 4} {* Update or View*}
    <p></p>
    <div class="action-link">
    <a href="{crmURL p='civicrm/admin/uf/group/field' q="action=browse&reset=1&gid=$gid"}">&raquo;  {ts}View or Edit Fields for this Profile{/ts}</a>
    </div>
{/if}
