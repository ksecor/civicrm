<div id="crm-quick-search" class="block-crm">
    <frameset>
    <form action="crm/contact/search" method="post">
    <div class="form-item">
        <select name="contact_type" size="1" class="form-select">
            <option>-all contacts-</option>
            <option>Individuals</option>
            <option>Organizations</option>
            <option>Households</options>
        </select>
    </div>
    <div class="form-item">
        <!-- <label for="quick_search">Name:</label> -->
        <input type="text" name="sort_name" class="form-text required" value="-full or partial-" onFocus="clearFldVal(this);">
    </div>

    <div class="form-item">
        <input type="submit" value="search" class="form-submit">
    </div>
    </form>
    
    <div class="link">
        <a href="crm/contact/search" title="Go to Advanced Search">Advanced Search...</a>
    </div>
    </frameset>
</div>
