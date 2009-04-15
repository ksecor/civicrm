{literal}
<script type="text/javascript"> 
    cj(function() {
        cj("#fulltext_submit").click( function() {
            var text  = cj("#text").val();
            var table = cj("#fulltext_table").val();
            var url = {/literal}'{crmURL p="civicrm/contact/search/custom" h=0 q="csid=`$fullTextSearchID`&reset=1&force=1&text="}'{literal} + text;
            if ( table ) {
                url = url + '&table=' + table;
            }
            document.getElementById('id_fulltext_search').action = url;
        });
    });
</script>
{/literal}

<div class="block-crm">
    <form method="post" id="id_fulltext_search">
    <input type="text" name="text" id='text' value="" style="width: 10em;" />
	<br/><br/>
	<select class="form-select" id="fulltext_table" name="fulltext_table">
    	<option value="">All Tables</option>
    	<option value="Contact">Contacts</option>
    	<option value="Activity">Activities</option>
    	<option value="Case">Cases</option>
    </select><input type="submit" name="submit" id="fulltext_submit" value="{ts}Go{/ts}" class="form-submit"/>
    </form>
</div>
