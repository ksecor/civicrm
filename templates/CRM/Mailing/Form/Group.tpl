{include file="CRM/WizardHeader.tpl}

<div class="form-item">
<fieldset>
  <legend>Select Mailing Recipients</legend>
  <dl>
<table>
{section name = groupLoop start = 1 loop = $groupCount+1 }
{assign var=index value=$smarty.section.groupLoop.index}
<tr><td>{$form.groupType.$index.html}</td><td>{$form.group.$index.html}</td></tr>
{/section}
{section name = mailingLoop start = 1 loop = $mailingCount}
{assign var=index value=$smarty.section.mailingLoop.index}
<tr><td>{$form.mailingType.$index.html}</td><td>{$form.mailing.$index.html}</td></tr>
{/section}
</table>
    <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
