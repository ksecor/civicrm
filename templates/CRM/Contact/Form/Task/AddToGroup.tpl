<div class="form-item">
<fieldset>
    <legend>{ts}Add Contacts to a Group{/ts}</legend>
<table class="view-layout">
    {if $group.id}
       <tr><td class="label">{ts}Group{/ts}</td><td>{$form.group_id.html}</td></tr>
    {else}
        <tr><td>{$form.group_option.html}</td></tr>
        <tr id="id_existing_group">
            <td>
                <table class="view-layout">
                <tr><td class="label">{$form.group_id.label}<span class="marker">*</span></td><td>{$form.group_id.html}</td></tr>
                </table>
            </td>
        </tr>
        <tr id="id_new_group" class="html-adjust">
            <td>
                <table class="view-layout">
                <tr><td class="label">{$form.title.label}<span class="marker">*</span></td><td>{$form.title.html}</td><tr>
                <tr><td class="label">{$form.description.label}</td><td>{$form.description.html}</td></tr>
                {if $form.group_type}
                    <tr><td class="label">{$form.group_type.label}</td><td>{$form.group_type.html}</td></tr>
                {/if}
                </table>
            </td>
        </tr>
    {/if}
</table>
<table class="form-layout">
        <tr><td>{include file="CRM/Contact/Form/Task.tpl"}</td></tr>
        <tr><td>{$form.buttons.html}</td></tr>       
</table>
</fieldset>
</div>

{include file="CRM/common/showHide.tpl"}

{if !$group.id}
{literal}
<script type="text/javascript">
showElements();
function showElements() {
    if ( document.getElementsByName('group_option')[0].checked ) {
      cj('#id_existing_group').show();
      cj('#id_new_group').hide();
    } else {
      cj('#id_new_group').show();
      cj('#id_existing_group').hide();  
    }
}
</script>
{/literal} 
{/if}