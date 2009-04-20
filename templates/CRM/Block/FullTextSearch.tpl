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
    <div style="margin-bottom: 8px;">
    <input type="text" name="text" id='text' value="" style="width: 10em;" />&nbsp;<input type="submit" name="submit" id="fulltext_submit" value="{ts}Go{/ts}" class="form-submit"/>
	</div>
	<select class="form-select" id="fulltext_table" name="fulltext_table">
    	<option value="">All</option>
    	<option value="Contact">Contacts</option>
    	<option value="Activity">Activities</option>
    	<option value="Case">Cases</option>
      	<option value="Contribution">Contributions</option>
        <option value="Participant">Participants</option>
        <option value="Membership">Memberships</option>
    </select> {help id="id-fullText-block" file="CRM/Contact/Form/Search/Custom/FullText.hlp"}
    </form>
</div>
