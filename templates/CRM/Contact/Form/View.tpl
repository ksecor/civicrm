{* View an existing Contact (used for all contact types) *}
{* All available data values are loaded in to smarty variables. Sets of values (like location, email, phone) are loaded into arrays *}

{* Including the javascript source code from Common.js files *}
<script type="text/javascript" src="{$config->httpBase}js/Common.js"></script>

<div id="name" class="data-group form-item">
    <p>
    {if $contact_type eq 'Individual'}
        <label>{$prefix} {$display_name} {$suffix}</label> &nbsp; &nbsp; {$job_title}
    {elseif $contact_type eq 'Organization'}
        <label>{$display_name}</label>
    {elseif $contact_type eq 'Household'}
        <label>{$display_name}</label>
    {/if}
    <span class="horizontal-position"><a href="#">Major Donor</a>, <a href="#">Volunteer</a></span>
    <span class="element-right">
        <input type="button" name="edit_contact" value="Edit Contact" onClick="location.href='{$config->httpBase}contact/edit/{$contact_id}';">
    </span>
    </p>
</div>

{* Display populated Locations. Primary location expanded by default. *}
{foreach from=$location item=loc key=locationIndex}

 <div id="location[{$locationIndex}]" class="data-group form-item">
  <a href="#">(-)</a> <label>{$loc.location_type} Home</label>
  {if $locationIndex = 1}(primary contact location){/if}<br/ >

  <div class="col1">
    {$loc.address.street_address}<br />
    {$loc.address.supplemental_address_1}<br />
    {$loc.address.city}, {$loc.address.state_province} {$loc.address.postal_code}<br />
    {$loc.address.country}
  </div>
  
  <div class="col2">
   {foreach from=$loc.phone item=phone key=phoneKey}
     {if $phone.is_primary eq 1}<strong>{/if}
     {$phone.phone} ({$phone.phone_type}) 
     {if $phone.is_primary eq 1}</strong>{/if}
     <br />
   {/foreach}

   {foreach from=$loc.email item=email key=emailKey}
     {if $email.is_primary eq 1}<strong>{/if}
     {$email.email}
     {if $email.is_primary eq 1}</strong>{/if}
     <br />
   {/foreach}
  </div>
  <div class="spacer"></div>
 </div>

{/foreach}

<div id="commPrefs" class="data-group form-item">
  <a href="#">(-)</a> <label>Communications Preferences</label><br />
  <div class="col1">
    <label>Privacy:</label>
    {foreach from=$privacy item=privacy_val key=privacy_label}
      {if $privacy_val eq 1}{$privacy_label|replace:"_":" "|upper} &nbsp; {/if}
    {/foreach}
  </div>
  <div class="col2">
    <label>Prefers:</label> {$preferred_communication_method}
  </div>
  <div class="spacer"></div>
</div>

 <div id="demographics" class="data-group form-item">
  <a href="#">(-)</a> <label>Demographics</label><br />
  <div class="col1">
    <label>Gender:</label> {$gender}<br />
    <label>Is Deceased:</label> {$is_deceased}
  </div>
  <div class="col2">
    <label>Date of Birth:</label> {$birth_date}
  </div>
  <div class="spacer"></div>
</div>

<div id="relationships" class="data-group form-item">
  <a href="#">(-)</a> <label>Relationships</label>
  <span class="horizontal-position">
   (no active Relationships for this contact)
  </span>
  <span class="element-right">
   <a href="">Create Relationship...</a>
  </span>
  <br />
  <div class="spacer"></div>
</div>

<div id="groups" class="data-group form-item">
  <a href="#">(-)</a> <label>Groups</label><br />
</div>

<div id="notes">
 <p>
 <fieldset><legend>Contact Notes</legend>
    <div class="form-item">
    (listing of most recent notes will go here)
    </div>
 </fieldset>
 </p>
</div>

<div id="edit-link" class="form-item">
  <span class="element-right">
   <input type="button" name="edit_contact" value="Edit Contact" onClick="location.href='{$config->httpBase}contact/edit/{$contact_id}';">
  </span>
</div> 

