<div class="form-item">
  <fieldset>
    <legend>
      {if $action eq 1}{ts}New Participant Status{/ts}{elseif $action eq 2}{ts}Edit Participant Status{/ts}{else}{ts}Delete Participant Status{/ts}{/if}
    </legend>

    {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>{ts}WARNING: Deleting this Participant Status will remove all of its settings.{/ts} {ts}Do you want to continue?{/ts}</dd>
        </dl>
      </div>
      <dl>
        <dt></dt><dd>{$form.buttons.html}</dd>
      </dl>
    {else}
      <table class="form-layout-compressed">
        <tr><td class="label">{$form.name.label}</td><td>{$form.name.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Name of this status type, for use in the code.{/ts}</td></tr>

        <tr><td class="label">{$form.label.label}</td><td>{$form.label.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Human-visible label of this status type.{/ts}</td></tr>

        <tr><td class="label">{$form.class.label}</td><td>{$form.class.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}The general class of this status.{/ts}</td></tr>

        <tr><td class="label">{$form.is_reserved.label}</td><td>{$form.is_reserved.html}</td></tr>
        <tr><td class="label">{$form.is_active.label}  </td><td>{$form.is_active.html}  </td></tr>
        <tr><td class="label">{$form.is_counted.label} </td><td>{$form.is_counted.html} </td></tr>

        <tr><td class="label">{$form.weight.label}</td><td>{$form.weight.html}</td></tr>

        <tr><td class="label">{$form.visibility.label}</td><td>{$form.visibility.html}</td></tr>

        <tr><td class="label">&nbsp;</td><td>{$form.buttons.html}</td></tr>
      </table>
    {/if}
    <div class="spacer"></div>
  </fieldset>
</div>
