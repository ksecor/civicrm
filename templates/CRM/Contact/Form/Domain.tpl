{* this template is used for viewing the domain information *}

<div class="form-item">
<fieldset><legend>{ts}Domain Information{/ts}</legend>
    <dl>
        <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt>{$form.contact_name.label}</dt><dd>{$form.contact_name.html}</dd>
        <dt>{$form.email_domain.label}</dt><dd>{$form.email_domain.html}</dd>
        {edit}
            <dt>&nbsp;</dt>
            <dd class="description">
                {ts}Set this to the domain (e.g., <code>example.org</code>) that should be seen in your mailings' "action" email addressess (like <code>subscribe.*@example.org</code>). This domain (or, more properly, the machine that this domain's MX record points to) has to know how to handle incoming CiviMail emails (so it can process the "actions" like subscribe, optOut, etc.).{/ts}
            </dd>
        {/edit}
        <dt>{$form.email_return_path.label}</dt><dd>{$form.email_return_path.html}</dd>
        {edit}
            <dt>&nbsp;</dt>
            <dd class="description">
                {ts}Use this field to populate the <code>Return-Path</code> mail header element with a fixed value (e.g., <code>myuser@example.org</code>). Enter a fully qualified email address which belongs to a valid SMTP account in your domain. This address will not be seen by "typical" email clients. Consult with your SMTP provider what address to put in here so that the SMTP server accepts outgoing mail from CiviMail. If this field is left blank, the <code>From</code> email address will be used as the <code>Return-Path</code>.{/ts}
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
