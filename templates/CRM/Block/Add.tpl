<div id="crm-quick-create">
<form action="{$postURL}" method="post">

<div class="form-item">
    <label for="firstname">{ts}First Name:{/ts}</label></br>
    <input type="text" name="first_name" class="form-text" maxlength="64" />
</div>

<div class="form-item">
    <label for="lastname">{ts}Last Name:{/ts}</label><br/>
    <input type="text" name="last_name" class="form-text required" maxlength="64" />
</div>

<div class="form-item">
    <label for="email">{ts}Email:{/ts}</label><br/>
    <input type="text" name="location[1][email][1][email]" class="form-text" maxlength="64" />
</div>

<input type="hidden" name="location[1][location_type_id]" value="1" />
<input type="hidden" name="location[1][is_primary]"       value="1" />
<input type="hidden" name="c_type"                        value="Individual" />

<div class="form-item"><input type="submit" name="_qf_Edit_next" value="{ts}Save{/ts}" class="form-submit" /></div>

</form>
</div>
