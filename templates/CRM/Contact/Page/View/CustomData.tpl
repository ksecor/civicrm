{* template for custom data *}

{if $action eq 2}
    <form {$form.attributes}>
    <div class="form-item">
    <p>
    <fieldset><legend>Edit Custom Data</legend>
        <dl>
        {$form.note.html}
        <dt>{$form.registered_voter.label}</dt><dd>{$form.registered_voter.html}</dd>
        <dt>{$form.party_registration.label}</dt><dd>{$form.party_registration.html}</dd>
        <dt>{$form.date_last_voted.label}</dt><dd>{$form.date_last_voted.html}</dd>
        <dt>{$form.voting_precinct.label}</dt><dd>{$form.voting_precinct.html}</dd>
        <dt>{$form.school_college.label}</dt><dd>{$form.school_college.html}</dd>
        <dt>{$form.degree.label}</dt><dd>{$form.degree.html}</dd>
        <dt>{$form.marks.label}</dt><dd>{$form.marks.html}</dd>
        <dt>{$form.date_of_degree.label}</dt><dd>{$form.date_of_degree.html}</dd>
        <br/>
        <dt></dt><dd>{$form.buttons.html}</dd>
        </dl>
    </fieldset>
    </p>
    </div>
    </form>
{/if}

<div id="name" class="data-group form-item">
    <p>
	<label>{$displayName}</label>
        <a href="{crmURL p='civicrm/contact/view/cd' q="cid=`$contactId`&action=update"}">Edit custom data</a>
    </p>
</div>

<div class="form-item">
{foreach from=$groupTree1 key=fieldset_name item=cd}
<fieldset><legend>{$fieldset_name}</legend>
    {foreach from=$cd item=cd_value key=cd_name}
    <dl>
    <dt>{$cd_name}</dt>
    <dd>{if $cd_value}{$cd_value}{else}--{/if}</dd>
    </dl>
    {/foreach}
</fieldset>
{/foreach}
</div>
