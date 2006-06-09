<fieldset>
<legend>{ts}Smart Group{/ts}</legend>
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
   <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
   <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
   <dt></dt><dl>{$form.buttons.html}</dl>
 </dl>
 </div>
</fieldset>
