{if $trackingFields and ! empty($trackingFields)}
{literal}
<script type="text/javascript">
cj(
   function( ) {
{/literal}
    {foreach from=$trackingFields key=trackingFieldName item=dontCare}
       cj("#{$trackingFieldName}").parent().parent().hide( );
    {/foreach}
{literal}
  }
);
</script>
{/literal}
{/if}