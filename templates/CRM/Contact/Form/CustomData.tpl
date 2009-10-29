{* this template is used for building tabbed custom data *} 
{if $cdType }
    {include file="CRM/Custom/Form/CustomData.tpl"}
{else}
    <div id="customData"></div>
    <div class="html-adjust">{$form.buttons.html}</div>  

    {*include custom data js file*}
    {include file="CRM/common/customData.tpl"}

	{if $customValueCount }
		{literal}
		<script type="text/javascript">
			var customValueCount = {/literal}"{$customValueCount}"{literal}
			var groupID = {/literal}"{$groupID}"{literal}
			var contact_type = {/literal}"{$contact_type}"{literal};
			var contact_subtype = {/literal}"{$contact_subtype}"{literal};
			buildCustomData( contact_type, contact_subtype );
			for ( var i = 1; i < customValueCount; i++ ) {
				buildCustomData( contact_type, contact_subtype, null, i, groupID, true );
			}
		</script>
		{/literal}
	{/if}
{/if}
