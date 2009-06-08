{if $config->languageLimit|@count >= 2 and $translatePermission }
<a href="javascript:" onClick="loadDialog('{crmURL p='civicrm/i18n' q="reset=1&table=$table&field=$field&id=$id&snippet=1&context=dialog" h=0}', '{$field}');"><img src="{$config->resourceBase}i/langs.png" /></a><div id="locale-dialog_{$field}" style="display:none"></div>

{literal}
<script type="text/javascript">
function loadDialog( url, fieldName ) {
 cj.ajax({
         url: url,
         success: function( content ) {
             cj("#locale-dialog_" +fieldName ).show( ).html( content ).dialog({
             		modal       : true,
			width       : 290,
			height      : 290,
			resizable   : true,
			bgiframe    : true,
			overlay     : { opacity: 0.5, background: "black" },
			beforeclose : function(event, ui) {
			               cj(this).dialog("destroy");
   			              }
             });
         }
      });
}
</script>
{/literal}
{/if}
