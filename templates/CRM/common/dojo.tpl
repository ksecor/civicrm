{literal}
<script type="text/javascript">
  djConfig = {
	isDebug: true
  };
</script>
{/literal}
<script type="text/javascript" src="{$config->resourceBase}packages/dojo/dojo.js"></script>
{if $dojoIncludes}
<script type="text/javascript">
  {dojo}{$dojoIncludes}{/dojo}
</script>
{/if}