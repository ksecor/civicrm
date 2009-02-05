<div class="form-item">
<fieldset>
    <legend>{ts}Add Contacts to a Group{/ts}</legend>
    {if $group.id}
    <dl>
        <dt>{ts}Group{/ts}</dt><dd>{$form.group_id.html}</dd>
    {else}
    <dl>
        <dt></dt><dd>{$form.group_option.html}</dd>
    </dl>
    <dl id="id_existing_group">
        <dt>{$form.group_id.label}<span class="marker">*</span></dt><dd>{$form.group_id.html}</dd>
    </dl>
    <dl id="id_new_group">
        <dt>{$form.title.label}<span class="marker">*</span></dt><dd>{$form.title.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
{if $form.group_type}
     <dt>{$form.group_type.label}</dt><dd>{$form.group_type.html}</dd>
{/if}
    </dl> 
    <dl>
    {/if}
        <dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd> 
        <dt></dt><dd>{$form.buttons.html}</dd>
       
    </dl>
</fieldset>
</div>

{include file="CRM/common/showHide.tpl"}

{if !$group.id}
{literal}
<script type="text/javascript">
showElements();
function showElements() {
    if ( document.getElementsByName('group_option')[0].checked ) {
      show('id_existing_group');
      hide('id_new_group');
    } else {
      show('id_new_group');
      hide('id_existing_group');  
    }
}
</script>
{/literal} 
{/if}