{* this template is used for viewing the domain information *}

<div class="form-item">
<fieldset><legend>{ts}Domain Information{/ts}</legend>
    <dl>
        <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
        <dt>{$form.description.label}</dt><dd>{$form.description.html}</dd>
        <dt>{$form.contact_name.label}</dt><dd>{$form.contact_name.html}</dd>
        <dt>{$form.email_domain.label}</dt><dd>{$form.email_domain.html}</dd>
        <dt>{$form.email_return_path.label}</dt><dd>{$form.email_return_path.html}</dd>
        <dt>&nbsp;</dt>
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

