{* This file provides the templating for the Location block *}
{* The phone, Email, Instant messenger and the Address blocks have been plugged in from external source files*}

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $locationCount contains the max number of locations to be displayed, assigned in the Location.php file*}
{* @var $index contains the current index of the location section *}

 {section name = locationLoop start = 1 loop = $locationCount}
 {assign var=index value=$smarty.section.locationLoop.index}

{if $index > 1}
 <div id="location[{$index}][show]" class="show-section">
    {$form.location.$index.show.html}
 </div>
{/if}

<div id="location[{$index}]">
	<fieldset>
    {if $index > 1}
    <legend>Additional Location</legend>
    {else}
    <legend>Primary Location</legend>
    {/if}
    
    <div class="form-item">
        <!-- Location type drop-down (e.g. Home, Work...) -->
        {$form.location.$index.location_type_id.html}
        
        <!-- Checkbox for "make this the primary location" -->
        {$form.location.$index.is_primary.html}

    </div>

    <div>
        {* Display the phone block(s) *}
        {include file="CRM/Contact/Form/Phone.tpl"}
    </div>

    <div>
        {* Display the email block(s) *}
        {include file="CRM/Contact/Form/Email.tpl"}
    </div>

    <div>
        {* Display the instant messenger block(s) *}
        {include file="CRM/Contact/Form/IM.tpl"}
    </div>
 
    <div>
        {* Plugging the address block *}
        {include file="CRM/Contact/Form/Address.tpl"} 
    <div>

  {if $index != 1}
  <div id="location[{$index}][hide]" class="hide-section">
    {$form.location.$index.hide.html}
  </div>
  {/if}

   </fieldset>
</div> <!-- End of Location block div -->
{/section}

