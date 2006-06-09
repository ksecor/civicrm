<div class="form-item"> 
<fieldset> 
  <legend>Dojo Test Page</legend>

<dl>
<dt>{$form.sort_name.label}</dt><dd>{$form.sort_name.html}</dd>
<dt>&nbsp;</dt><dd class="description">{ts}name goes back to the server and gets the next 5 items which match the prefix.{/ts}</dd>

<dt>{$form.state_province.label}</dt><dd>{$form.state_province.html}</dd>
<dt>&nbsp;</dt><dd class="description">{ts}state is a dynamic combo box which also affects the below country box. The values of the combo box are fetched from the server based on the user's locale etc{/ts}</dd>

<dt>{$form.country.label}</dt><dd>{$form.country.html}</dd>
<dt>&nbsp;</dt><dd class="description">{ts}country is either dependent on the above or can be independely filled.{/ts}</dd>

<dt>{$form.birth_date.label}</dt><dd>{$form.birth_date.html}</dd>
<dt>&nbsp;</dt><dd class="description">{ts}birth day is a simple date field{/ts}</dd>

<dt>{$form.scheduled_date_time.label}</dt><dd>{$form.scheduled_date_time.html}</dd>
<dt>&nbsp;</dt><dd class="description">{ts}scheduled date and time is a more complex datetime field{/ts}</dd>

<dt>{$form.group.label}</dt><dd>{$form.group.html}</dd>
<dt>&nbsp;</dt><dd class="description">{ts}we'd like to place this element somewhere in the below list (before/after). So a clean widget to do it would be awesome.{/ts}</dd>
</dl>

</fieldset>
</div>