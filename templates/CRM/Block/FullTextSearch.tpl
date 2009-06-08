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
    	<option value="">{ts}All{/ts}</option>
    	<option value="Contact">{ts}Contacts{/ts}</option>
    	<option value="Activity">{ts}Activities{/ts}</option>
{if call_user_func(array('CRM_Core_Permission','access'), 'CiviCase')}
    	<option value="Case">{ts}Cases{/ts}</option>
{/if}
{if call_user_func(array('CRM_Core_Permission','access'), 'CiviContribute')}
      	<option value="Contribution">{ts}Contributions{/ts}</option>
{/if}
{if call_user_func(array('CRM_Core_Permission','access'), 'CiviEvent')}
        <option value="Participant">{ts}Participants{/ts}</option>
{/if}
{if call_user_func(array('CRM_Core_Permission','access'), 'CiviMember')}
        <option value="Membership">{ts}Memberships{/ts}</option>
{/if}
    </select> {help id="id-fullText-block" file="CRM/Contact/Form/Search/Custom/FullText.hlp"}
    </form>
</div>
