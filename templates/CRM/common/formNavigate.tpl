{* Include this file in any form where we want to alert user if they've added or change form data, and then navigate away. *}
{literal}
<script type="text/javascript">
     cj( function( ) {
         cj("#{/literal}{$form.formName}{literal}").FormNavigate("{/literal}{ts}You have unsaved changes.{/ts}{literal}"); 
     });
</script>
{/literal}

