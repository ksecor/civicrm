{* This file provides the templating for the Location block *}
{* The phone, Email, Instant messenger and the Address blocks have been plugged in from external source files*}

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $pid Contains the index of the location block under the locationt loop *}

 {assign var = "pid" value = ""}


{* The locationt section displays the location block *}
{* The section loops as many times as indicated by the variable $locloop to give as many phone blocks *}

{* @var $locloop Gives the number of location loops to be displayed, assigned in the Location.tpl file*}
{* $index contains the current index of the locationt section *}
{* $smarty.section.locationt.index contains the current index of the locationt section *}
{* The section loops to display as many location blocks as contained in the $locloop variable *}
{* @var $lid Contains the current location id in evaluation *}
{* @var $width Contains the width setting for the first column in the table *} 
{* @var $exloc Contains the name of the location expansion link *}
{* @var $hideloc Contains the name of the location hide link *}

 {section name = locationt start = 1 loop = $locloop}
 {assign var = "index" value = "`$smarty.section.locationt.index`"}
 
 {if $locloop - 1  >= 1} 
 {assign var = "pid" value = "`$smarty.section.locationt.index`"}

 {/if}

 {assign var = "lid" value = "location`$pid`"}

 {assign var = "exloc" value = "exloc`$index`"} 
 {assign var = "hideloc" value = "hideloc`$index`"} 
 {assign var = "width" value = "200"}
 

 {if $pid > 1}
 <div id="expand_loc{$index}" class="comment">
    {$form.$exloc.html}
 </div>
 {/if}

<div id="location{$pid}">
	<fieldset>
    {if $pid > 1}
    <legend>Additional Location</legend>
    {else}
    <legend>Primary Location</legend>
    {/if}
    
    <div class="form-item">
        <!-- Location type drop-down (e.g. Home, Work...) -->
        {$form.$lid.location_type_id.html}
        
        {if $pid > 1}
            <!-- Checkbox for "make this the primary location" -->
            {$form.$lid.is_primary.html} Make this the primary location {*$form.$lid.is_primary.label*}
        {/if}
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

    {if $pid > 1 }
        <div class="box">
            {$form.$hideloc.html}
        </div>
    {/if}
    </fieldset>
</div> <!-- End of Location block div -->
{/section}

