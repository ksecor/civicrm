<fieldset>
<legend>{ts}Saved Search{/ts}</legend>
 <div id="search-status">
    <ul>
        {foreach from=$qill item=criteria}
            <li>{$criteria}</li>
        {/foreach}
    </ul>
    <br />
 </div>
 <div class="form-item">
 <dl>
   <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
   <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
   <dt></dt><dl>{$form.buttons.html}</dl>
 </dl>
 </div>
</fieldset>
