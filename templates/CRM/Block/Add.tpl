<div id="crm-quick-create">
<form action="{$postURL}" method="post">

<div class="form-item">
    <div>
        <label for="qa_first_name">{ts}First Name:{/ts}</label>
    </div>
    <div>
        <input type="text" name="first_name" id="qa_first_name" class="form-text" maxlength="64" />
    </div>
</div>

<div class="form-item">
    <div>
        <label for="qa_last_name">{ts}Last Name:{/ts}</label>
    </div>
    <div>
        <input type="text" name="last_name" id="qa_last_name" class="form-text required" maxlength="64" />
    </div>
</div>

<div class="form-item">
    <div>
        <label for="qa_email">{ts}Email:{/ts}</label>
    </div>
    <div>
        <input type="text" name="email[1][email]" id="qa_email" class="form-text" maxlength="64" />
    </div>

    <input type="hidden" name="email[1][location_type_id]" value="{$primaryLocationType}" />
    <input type="hidden" name="email[1][is_primary]" value="1" />
    <input type="hidden" name="ct" value="Individual" />
</div>

<div class="form-item"><input type="submit" name="_qf_Contact_next" value="{ts}Save{/ts}" class="form-submit" /></div>

</form>
</div>
