<div id="crm-quick-search" class="block-crm">
    <form action="{$postURL}" method="post">
    <div class="form-item">
        <select name="contact_type" size="1" class="form-select">
            <option>-all contacts-</option>
            <option>Individuals</option>
            <option>Organizations</option>
            <option>Households</option>
        </select>
    </div>
    <div class="form-item">
        <!-- <label for="quick_search">Name:</label> -->
        <input type="text" name="sort_name" class="form-text required" value="-full or partial name-" onFocus="clearFldVal(this);" />
        <br />
        <input type="submit" name="_qf_Search_refresh" value="Search" class="form-submit" />
        <br />
        <a href="{$advancedSearchURL}" title="Go to Advanced Search">&raquo; Advanced Search</a>
    </div>
    </form>
</div>
