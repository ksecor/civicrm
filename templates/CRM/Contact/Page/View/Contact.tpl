<div id="name" class="data-group form-item">
    <span class="float-right">
        <input type="button" name="edit_contact" value="Edit Contact" onClick="location.href='{$config->httpBase}contact/edit?cid={$contact_id}';">
    </span>
    <p>
    {if $contact_type eq 'Individual'}
        <label>{$prefix} {$display_name} {$suffix}</label> &nbsp; &nbsp; {$job_title}
        <span class="horizontal-position"><a href="#">Major Donor</a>, <a href="#">Volunteer</a></span>
    {elseif $contact_type eq 'Organization'}
        <label>{$sort_name}</label>
        <span class="horizontal-position"><a href="#">For-profit</a>, <a href="#">Funder</a></span>
    {elseif $contact_type eq 'Household'}
        <label>{$sort_name}</label>
    {/if}
    </p>
</div>


{* Display populated Locations. Primary location expanded by default. *}
{foreach from=$location item=loc key=locationIndex}

 <div id="location[{$locationIndex}][show]" class="data-group form-item">
  <a href="#" onClick="hide('location[{$locationIndex}][show]'); show('location[{$locationIndex}]'); return false;">(+)</a> <label>{$loc.location_type}</label><br />
 </div>

 <div id="location[{$locationIndex}]" class="data-group form-item">
  <a href="#" onClick="hide('location[{$locationIndex}]'); show('location[{$locationIndex}][show]'); return false;">(-)</a> <label>{$loc.location_type}</label>
  {if $locationIndex eq 1}(primary contact location){/if}<br/ >

  <div class="col1">
    {$loc.address.street_address}<br />
    {if $loc.address.supplemental_address_1}{$loc.address.supplemental_address_1}<br />{/if}
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

 <div id="commPrefs[show]" class="data-group form-item">
  <a href="#" onClick="hide('commPrefs[show]'); show('commPrefs'); return false;">(+)</a> <label>Communications Preferences</label><br />
 </div>

<div id="commPrefs" class="data-group form-item">
  <a href="#" onClick="hide('commPrefs'); show('commPrefs[show]'); return false;">(-)</a> <label>Communications Preferences</label><br />
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

 {if $contact_type eq 'Individual'}
 <div id="demographics[show]" class="data-group form-item">
  <a href="#" onClick="hide('demographics[show]'); show('demographics'); return false;">(+)</a> <label>Demographics</label><br />
 </div>

 <div id="demographics" class="data-group form-item">
  <a href="#" onClick="hide('demographics'); show('demographics[show]'); return false;">(-)</a> <label>Demographics</label><br />
  <div class="col1">
    <label>Gender:</label> {$gender.gender}<br />
    {if $is_deceased eq 1}
        <label>Contact is Deceased</label>
    {/if}
  </div>
  <div class="col2">
    <label>Date of Birth:</label> {$birth_date}
  </div>
  <div class="spacer"></div>
  </div>
 {/if}

<div id="relationships[show]" class="data-group form-item">
  <a href="#" onClick="hide('relationships[show]'); show('relationships'); return false;">(+)</a> <label>Relationship</label><br />
</div>
<div id="relationships">
 <p>
 <fieldset><legend><a href="#" onClick="hide('relationships'); show('relationships[show]'); return false;">(-)</a>Relationship</legend>
    <div class="form-item">
       <table>
	<tr><td>Relationship</td><td>Contact</td><td>Email</td><td>Phone</td><td>City</td><td>State/Prov</td><td>&nbsp;</td></tr>
       {foreach from=$relationship item=rel}
         <tr>
            <td>
                {$rel.relation|truncate:80:"...":true}
                {* Include '(more)' link to view entire note if it has been truncated *}
                {*assign var="noteSize" value=$note.note|count_characters:true}
                {if $noteSize GT 80*}
                    <!--a href="{$config->httpBase}contact/view/rel&rid={$rel.id}&op=view">(more)</a-->
                {*/if*}
            </td>
	    <td>{$rel.name}</td>
	    <td>{$rel.email}</td>
	    <td>{$rel.phone}</td>
	    <td>{$rel.city}</td>
	    <td>{$rel.state}</td>
            <td><a href="{$config->httpBase}contact/view/rel&rid={$rel.id}&op=edit">Edit</a></td> 
         </tr>  
       {/foreach}
       </table>
       <br />
       <div class="action-link">
       <a href="{$config->httpBase}contact/view/rel&op=add">Create Relationship</a>
        {if $relationshipsCount gt 3 }
         | <a href="{$config->httpBase}contact/view/rel&op=browse">Browse all Relationships...</a>
        {/if}
        </div>
    </div>
 </fieldset>
 </p>
</div>

<div id="groups" class="data-group form-item">
  <span class="float-right">
   <a href="#">Add this contact to one or more groups...</a>
  </span>
  <a href="#"><label>Groups</label></a>
  <span class="horizontal-position">
   (this contact does not belong to any groups)
  </span>
  <br />
  <div class="spacer"></div>
</div>

<div id="notes[show]" class="data-group form-item">
  <a href="#" onClick="hide('notes[show]'); show('notes'); return false;">(+)</a> <label>Contact Notes</label><br />
</div>

<div id="notes">
 <p>
 <fieldset><legend><a href="#" onClick="hide('notes'); show('notes[show]'); return false;">(-)</a> Contact Notes</legend>
    <div class="form-item">
       <table>
       {foreach from=$note item=note}
         <tr>
            <td>
                {$note.note|truncate:80:"...":true}
                {* Include '(more)' link to view entire note if it has been truncated *}
                {assign var="noteSize" value=$note.note|count_characters:true}
                {if $noteSize GT 80}
                    <a href="{$config->httpBase}contact/view/note&nid={$note.id}&op=view">(more)</a>
                {/if}
            </td>
            <td>{$note.modified_date|date_format:"%B %e, %Y"}</td>
            <td><a href="{$config->httpBase}contact/view/note&nid={$note.id}&op=edit">Edit</a></td> 
         </tr>  
       {/foreach}
       </table>
       <br />
       <div class="action-link">
       <a href="{$config->httpBase}contact/view/note&op=add">New Note</a>
        {if $notesCount gt 3 }
         | <a href="{$config->httpBase}contact/view/note&op=browse">Browse all notes...</a>
        {/if}
        </div>
    </div>
 </fieldset>
 </p>
</div>

<div id="edit-link" class="form-item">
  <span class="float-right">
   <input type="button" name="edit_contact" value="Edit Contact" onClick="location.href='{$config->httpBase}contact/edit&cid={$contact_id}';">
  </span>
</div> 

 <script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
 </script>

