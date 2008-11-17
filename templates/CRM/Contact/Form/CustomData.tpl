{* this template is used for building tabbed custom data *} 
{if $cdType }
    {include file="CRM/Custom/Form/CustomData.tpl"}
{else}
    <div id="customData"></div>
    <div class="html-adjust">{$form.buttons.html}</div>  

    {*include custom data js file*}
    {include file="CRM/common/customData.tpl"}
	{if $action eq 1}
		{literal}
			<script type="text/javascript">
				cj(document).ready(function() {
					buildCustomData( 'Contact' );
				});
			</script>
		{/literal}
	{else}
		{if $customValueCount }
			{literal}
			<script type="text/javascript">
				var customValueCount = {/literal}"{$customValueCount}"{literal}
				var groupID = {/literal}"{$groupID}"{literal}
				buildCustomData( 'Contact');
				for ( var i = 1; i < customValueCount; i++ ) {
					buildCustomData( 'Contact', null, null, i, groupID, true );
				}
			</script>
			{/literal}
		{/if}
	{/if}
{/if}

