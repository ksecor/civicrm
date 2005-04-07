{* this template is used for adding relationships  *}

    <form {$form.attributes}>
    <p>
    <fieldset><legend>{if $op eq 'add'}New{else}Edit{/if} Relationship(s)</legend>
    <div class="form-item">
       <b>{$displayName} is {$form.relationship_type_id.html} of </b>
    </div><hr>
    <div class="form-item">
      Use 'Search' to narrow down list of contacts. Then mark the contact(s) and click 'Create Relationship'	
    </div>
    <div class="form-item">
      Search: {$form.contact_type.html} <br><br>
              {$form.name.html} {$form.search.html} 
    </div>
        <br/>
    <fieldset>
     display search result..
    </fieldset>
    <div class="form-item">
	  {$form.start_date.label}{$form.start_date.html}<br>
	  {$form.end_date.label}{$form.end_date.html}      
    </div>
        {$form.buttons.html}
    </fieldset>
    </p>
    </form>
