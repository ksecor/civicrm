<div class='spacer'></div>
<fieldset>
<legend>{ts}Smart Group{/ts}</legend>
{if $qill[0]}
<div id="search-status">
    <ul>
        {foreach from=$qill item=criteria}
          <li>{$criteria}</li>
        {/foreach}
    </ul>
    <br />
</div>
{/if}
 <div class="form-item">
 <dl>
   <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
   <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
   <dt></dt><dd>{$form.buttons.html}</dd>
 </dl>
 </div>
</fieldset>
