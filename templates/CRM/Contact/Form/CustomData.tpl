{* this template is used for building tabbed custom data *} 
{if $cdType }
    {include file="CRM/Custom/Form/CustomData.tpl"}
{else}
    <div id="customData"></div>
    <div class="html-adjust">{$form.buttons.html}</div>  

    {*include custom data js file*}
    {include file="CRM/common/customData.tpl"}

	{if $customValueCount }
		<script type="text/javascript">
            {literal}
            var customValueCount = {/literal}"{$customValueCount}"{literal}
            var groupID = {/literal}"{$groupID}"{literal}
            var contact_type = {/literal}"{$contact_type}"{literal};
            buildCustomData( contact_type );
            for ( var i = 1; i < customValueCount; i++ ) {
                buildCustomData( contact_type, null, null, i, groupID, true );
            }

            function hideStatus( groupID ) {
                cj( '#statusmessg_' + groupID ).hide( );
            }

            function deleteCustomValue( valueID, elementID, groupID ) {
                var postUrl = {/literal}"{crmURL p='civicrm/ajax/customvalue' h=0 }"{literal};
                cj.ajax({
                  type: "POST",
                  data:  "valueID=" + valueID + "&groupID=" + groupID,    
                  url: postUrl,
                  success: function(html){
                      cj( '#' + elementID ).hide( );
                      cj( '#statusmessg_' + groupID ).show( );
                  }
                });
            }
    		{/literal}
		</script>
	{/if}
{/if}
