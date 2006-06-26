<div id="help">
    {ts}Use this form to find member(s) by member name,email,membership type, status, source, signup /renew date,end date.{/ts}
</div>
<div class="form-item">
<fieldset><legend>{ts}Find Members{/ts}</legend>
    {strip} 
        <dl>
        <dt>{$form.member_name.label}</dt>
        <dd>
        <table class="form-layout">
        <tr>
        <td>{$form.member_name.html}</td><td class="label">{$form.buttons.html}</td>
        </tr>
        </table>
        </dd>
        {include file="CRM/Member/Form/Search/Common.tpl"}
    {/strip}
</fieldset>
</div> 
