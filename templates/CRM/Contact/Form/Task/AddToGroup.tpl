<div class="form-item">
<fieldset>
    <legend>{ts}Add Contacts to a Group{/ts}</legend>
    <dl>
        <dt></dt><dd>{$form.group_option.html}</dd>
     </dl>
     <dl id="id_existing_group">
        <dt>{if $group.id}{ts}Group{/ts}{else}{$form.group_id.label}<span class="marker">*</span>{/if}</dt><dd>{$form.group_id.html}</dd>
     </dl>
     <dl id="id_new_group">
        <dt>{$form.title.label}<span class="marker">*</span></dt><dd>{$form.title.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
     </dl> 
     <dl>
      <dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd> 
     <dt></dt><dd>{$form.buttons.html}</dd>
       
    </dl>
</fieldset>
</div>

{include file="CRM/common/showHide.tpl"}

{literal} 

<script type="text/javascript">
showElements();
function showElements() {

if ( document.getElementsByName('group_option')[0].checked ) {
  show('id_existing_group');
  hide('id_new_group');
} else 
{
  show('id_new_group');
  hide('id_existing_group');  
}
}
</script>
{/literal} 