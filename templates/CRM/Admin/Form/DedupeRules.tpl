<div class="form-item">
  <h2>{ts 1=$contact_type}Matching Rules for %1 Contacts{/ts}</h2>
    <div id="help">
        {ts}Configure up to five fields to evaluate when searching for 'suspected' duplicate contact records.{/ts} {help id="id-rules"}
    </div>
<fieldset>
  <dl>
    <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
    <dt>{$form.level.label}</dt><dd>{$form.level.html}</dd>
    <table style="width: auto;">
      <tr class="columnheader"><th>{ts}Field{/ts}</th><th>{ts}Length{/ts}</th><th>{ts}Weight{/ts}</th></tr>
         {section name=count loop=5}
         {capture assign=where}where_{$smarty.section.count.index}{/capture}
         {capture assign=length}length_{$smarty.section.count.index}{/capture}
         {capture assign=weight}weight_{$smarty.section.count.index}{/capture}
      <tr class="{cycle values="odd-row,even-row"}"><td>{$form.$where.html}</td><td>{$form.$length.html}</td><td>{$form.$weight.html}</td></tr>
    {/section}
    <tr class="columnheader"><th colspan="2" style="text-align: right;">{$form.threshold.label}</th><td>{$form.threshold.html}</td></tr>
  </table>
  <p>{$form.buttons.html}</p>
  </dl>
</fieldset>
</div>
