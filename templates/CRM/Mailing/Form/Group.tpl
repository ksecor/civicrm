{if $groupCount == 0 and $mailingCount == 0}
  <div class="status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>
        {ts}To send a mailing, you must have a valid group of recipients - either at least one group that's a Mailing List or at least one previous mailing.{/ts}
      </dd>
    </dl>
  </div>
{else}
{include file="CRM/common/WizardHeader.tpl"}

<div class="form-item">
<fieldset>
  <dl>
    <dt>{$form.name.label}</dt><dd>{$form.name.html} {help id="mailing-name"}</dd>
    {if $context EQ 'search'}
    <dt>{$form.baseGroup.label}</dt><dd>{$form.baseGroup.html}</dd>
    {/if}	
  </dl>
</fieldset>

 <div id="id-additional-show" class="section-hidden section-hidden-border" style="clear: both;">
        <a href="#" onclick="hide('id-additional-show'); show('id-additional'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{if $context EQ 'search'}{ts}Additional Mailing Recipients{/ts}{else}{ts}Mailing Recipients{/ts}{/if}</label><br />
 </div>

 <div id="id-additional" class="form-item">
  <fieldset>
  <legend><a href="#" onclick="hide('id-additional'); show('id-additional-show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{if $context EQ 'search'}{ts}Additional Mailing Recipients{/ts}{else}{ts}Mailing Recipients{/ts}{/if}</legend>
  {strip}

  <table>
  {if $groupCount > 0}
    <tr><th class="label">{$form.includeGroups.label} {help id="include-groups"}</th></tr>
    <tr><td>{$form.includeGroups.html}</td></tr>
    <tr><th class="label">{$form.excludeGroups.label} {help id="exclude-groups"}</th></tr>
    <tr><td>{$form.excludeGroups.html}</td></tr>
  {/if}
  {if $mailingCount > 0}
  <tr><th class="label">{$form.includeMailings.label} {help id="include-mailings"}</th></tr>
  <tr><td>{$form.includeMailings.html}</td></tr>
  <tr><th class="label">{$form.excludeMailings.label} {help id="exclude-mailings"}</th></tr>
  <tr><td>{$form.excludeMailings.html}</td></tr>
  {/if}
  </table>

  <table>
    <tr>
       <td>{$form.search_id.label}</td>
       <td>{$form.search_id.html}</td>
    </tr>
    <tr>
       <td>{$form.search_args.label}</td>
       <td>{$form.search_args.html}</td>
    </tr>
    <tr>
       <td>{$form.group_id.label}</td>
       <td>{$form.group_id.html}</td>
    </tr>
  </table>    
  {/strip}
  </fieldset>
 </div>

 <dl>
 <dt></dt><dd>{$form.buttons.html}</dd>
 </dl>
</div>
{include file="CRM/common/showHide.tpl"}
{/if}
