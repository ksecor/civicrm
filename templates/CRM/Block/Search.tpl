<div class="block-crm">
    <form action="{$postURL}" method="post">
    <div class="form-item">
        <input type="hidden" name="contact_type" value="" />
        <input type="text" name="sort_name" class="form-text required" value="" />
        <br />
        <input type="submit" name="_qf_Search_refresh" value="{ts}Search{/ts}" class="form-submit" />
        <br />
        <a href="{$advancedSearchURL}" title="{ts}Go to Advanced Search{/ts}">&raquo; {ts}Advanced Search{/ts}</a>
    </div>
    </form>
</div>
