<div id="crm-quick-create">
<form action="crm/contact/add" method="post">

<div class="form-item">
    <label for="firstname">First Name:</label></br>
    <input type="text" name="first_name" class="form-text" maxlength="64" />
</div>

<div class="form-item">
    <label for="lastname">Last Name:</label><br/>
    <input type="text" name="last_name" class="form-text required" maxlength="64" />
</div>

<div class="form-item">
    <label for="email">Email:</label><br/>
    <input type="text" name="location[1][email][1][email]" class="form-text" maxlength="64" />
</div>

<input type="hidden" name="location[1][location_type_id]" value="1" />
<input type="hidden" name="location[1][is_primary]"       value="1" />

<div class="form-item"><input type="submit" name="_qf_Contact_next" value="Save" class="form-submit" /></div>

</form>
</div>