    <!-- Quick Search box for rms pages-->
    <div class="block" id="crm-quick-search">
        <div class="rmsMenuTitle">Contact Search</div>
        <form name="quick_search_form" action="list_contacts.html" method="post">
        <input type="text" class="form-text" name="contact_search_string" size="20" value="-full or partial name-" onClick="contact_search_string.value = '';"/><br/>
        <input type="submit" name="contact_search" value="search" class="form-submit"><br/>
        <a href="">&raquo; Advanced Search</a>
    </div><br/>
    
    <!-- Shortcut Menu box for rms pages-->
    <div class="block" id="crm-shortcuts">
        <div class="rmsMenuTitle">Shortcuts</div>
        <ul compact="compact">
        <li><a href="list_contacts.php">List Contacts</a>
        <li><a href="add_contacts.php">Add Contact</a>
        <li><a href="crm_groups.php">Manage Groups
        <li><a href="import_contacts.php">Import Contacts</a>
        </ul>
    </div><br/>
    
    <!-- Quick Create Contact box -->
    <div class="block" id="crm-quick-create">
        <div class="rmsMenuTitle">Add Contact</div>
        <form name="rms_quick_create_form" action="list_contacts.html" method="post">
        <label>First Name:</label><br/>
        <input type="text" class="form-text" name="contact_first_name" size="15" value="" /><br/>
        <label>Last Name:</label><br/>
        <input type="text" class="form-text" name="contact_last_name" size="15" value="" /><br/>
        <label>Phone:</label><br/>
        <input type="text" class="form-text" name="contact_phone" size="15" value="" /><br/>
        <label>Email:</label><br/>
        <input type="text" class="form-text" name="contact_email" size="15" value="" /><br/>
        <input type="submit" name="contact_create" value="save" class="form-submit"><br/>
    </div><br/>
