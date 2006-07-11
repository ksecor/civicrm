{* this template is used for viewing the domain information *}

<div class="form-item">
<fieldset><legend>{ts}Domain Information{/ts}</legend>
    <dl>
        <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt>{$form.contact_name.label}</dt><dd>{$form.contact_name.html}</dd>
        <dt>{$form.email_domain.label}</dt><dd>{$form.email_domain.html}</dd>
        {edit}
        <dt>&nbsp;</dt><dd class="description">(e.g. example.org)</dd>
        {/edit}
        <dt>{$form.email_return_path.label}</dt><dd>{$form.email_return_path.html}</dd>
        {edit}
            <dt>&nbsp;</dt>
            <dd class="description">
                {ts}Use this field to populate the RETURN-PATH mail header element with a fixed value. Enter a fully qualified email address which belongs to a valid SMTP account in your domain. If this field is left blank, the FROM email address will be used as the RETURN-PATH.{/ts}
            </dd>
        {/edit}
    </dl>
    </fieldset>
{include file="CRM/Contact/Form/Location.tpl"}
{if !($action eq 4)}
{$form.buttons.html}
{/if}
        {if ($action eq 4)}
        <div class="action-link">
    	<a href="{crmURL q="action=update&reset=1"}" id="editDomainInfo">&raquo; {ts}Edit Domain Information{/ts}</a>
        </div>
        {/if}
</div>

{if $emailDomain EQ true}
<script type="text/javascript">
hide('id_location_1_show');
hide('id_location_1_phone_2_show');
hide('id_location_1_email_2_show');
hide('id_location_1_im_2_show');
</script>
{/if}
