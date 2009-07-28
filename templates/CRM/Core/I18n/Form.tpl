<fieldset>
  <dl>
    {foreach from=$locales item=locale}
      {assign var='elem' value="$field $locale"|replace:' ':'_'}
      <dt>{$form.$elem.label}</dt><dd>{$form.$elem.html}</dd>
    {/foreach}
  </dl>
  {if $context == 'dialog'}
    <input type="submit" value="Save"/>
  {else}
    {$form.buttons.html}
  {/if}
</fieldset>
{$form.action}
{literal}
<script type="text/javascript">
var fieldName = "{/literal}{$field}{literal}";
var tsLocale = "{/literal}{$tsLocale}{literal}";
cj('#Form').submit(function() { 
      cj(this).ajaxSubmit({ 
                            beforeSubmit: function (formData, jqForm, options) {
                                                    var queryString = cj.param(formData); 
                                                    var postUrl     = cj('#Form').attr('action');
                                                    cj.ajax({
                                                             type   : "POST",
                                                             url    : postUrl,    
                                                             async  : false,
                                                             data   : queryString,
                                                             success: function( response ) {
																	  cj('#' + fieldName).val( cj('#' + fieldName +'_' + tsLocale ).val() );
                                                                      cj("#locale-dialog_"+fieldName).dialog("close");
                                                                     }
                                                    });
                                                return false; 
                                            }}); 
          return false; 
});
</script>
{/literal}