<script type="text/javascript" src="/js/ORG.js"></script>

{$form.javascript}

<form {$form.attributes}>

	{if $form.hidden}
	{$form.hidden}{/if}

	{if count($form.errors) gt 0}
	<table width="100%" cellpadding="1" cellspacing="0" border="0" bgcolor="#ff9900"><tr><td>
	<table width="100%" cellpadding="10" cellspacing="0" border="0" bgcolor="#FFFFCC"><tr><td align="center">
	<span class="error" style="font-size: 13px;">Please correct the errors below.</span>
	</td></tr></table>
	</td></tr></table>
	</p>
	{/if}


<div id="core">
<fieldset><legend>Organization</legend>
  <div class="form-item">
    <label>{$form.organization_name.label}</label>
    {$form.organization_name.html}
  </div>
  <div class="form-item">
      <label>{$form.legal_name.label}</label>
	{$form.legal_name.html}
  </div>
   <div class="form-item">
     <label>{$form.nick_name.label}</label>
        {$form.nick_name.html}
   </div>
   <div class="form-item">
	<label>{$form.primary_contact_id.label}</label>
	 {$form.primary_contact_id.html}
   </div>
    <div class="form-item">
        <label>{$form.sic_code.label}</label>
	{$form.sic_code.html}
   </div>

</fieldset>

{* Plugging the Communication preferences block *} 
 {include file="CRM/Contact/Form/Contact/Comm_prefs.tpl"}


{* location block *}

 {include file="CRM/Contact/Form/Location.tpl" locloop = 2 phoneloop = 4 emailloop = 4 imloop = 4} 

{******************************** ENDIND THE DIV SECTION **************************************}

</div> <!-- end 'core' section of contact form -->


<div id = "buttons" class="form-submit">
	{$form.buttons.html}
</div>

 {$form.my_script.label}
</form>
	
<script type="text/javascript">
on_load_execute(frm.name);
</script>


{if count($form.errors) gt 0}
<script type="text/javascript">
on_error_execute(frm.name);
</script>
{/if}

