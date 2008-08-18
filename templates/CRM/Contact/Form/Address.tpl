{* This file provides the plugin for the Address block in the Location block *}

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var location.$index Contains the current location id, and assigned in the Location.tpl file *}

<fieldset><legend>{if $legend}{$legend}{else}{ts}Address{/ts}{/if}</legend>
{if $introText}
    <div class="description">{$introText}</div>
{/if}
{foreach item=addressElement from=$addressSequence}
    <div id="id_location_{$index}_address_{$addressElement}">
        {include file=CRM/Contact/Form/Address/$addressElement.tpl}
    </div>
{/foreach}

{include file=CRM/Contact/Form/Address/geo_code.tpl}

<!-- Spacer div forces fieldset to contain floated elements -->
<div class="spacer"></div>
</fieldset>

