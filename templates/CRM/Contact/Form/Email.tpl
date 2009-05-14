{* This file provides the plugin for the email block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $lid Contains the current location id in evaluation, and assigned in the Location.tpl file *}
{* @var $width Contains the width setting for the first column in the table *} 

     {* ----------- Primary EMAIL BLOCK--------- *}
    <div class="form-item">
        <span class="labels">
            {$form.location.$index.email.1.email.label}
        </span>
        <span class="fields">
            <span>{$form.location.$index.email.1.email.html}</span>
             {if $hold neq 1}
	            <span>{$form.location.$index.email.1.on_hold.html}</span>
             
	            <span>&nbsp;{$form.location.$index.email.1.is_bulkmail.html}</span> &nbsp;&nbsp;{if $index EQ 1}{help id="id-bulkmail"}{/if}
            {/if}
            {* Link to add a field. *}
            <span id="id_location_{$index}_email_2_show" class="add-remove-link">
                {$form.location.$index.email.2.show.html}
            </span>
        </span>
    </div>
    {* Spacer div contains floated elements *}
    <div class="spacer"></div>

    {section name = innerLoop start = 2 loop = $blockCount}
       {assign var=innerIndex value=$smarty.section.innerLoop.index}

        {* Email block {$innerIndex}. *}
        <div id="id_location_{$index}_email_{$innerIndex}" class="form-item">
            <span class="labels">
             {$form.location.$index.email.$innerIndex.email.label}
            </span>
            <span class="fields">
              <span>{$form.location.$index.email.$innerIndex.email.html}</span>
              {if $hold neq 1}
        	      <span>{$form.location.$index.email.$innerIndex.on_hold.html}</span>
              {/if}
              <span>&nbsp;{$form.location.$index.email.$innerIndex.is_bulkmail.html}</span> 
              {* Link to hide this field *}
              <span id="id_location_{$index}_email_{$innerIndex}_hide" class="add-remove-link element-right">
              {$form.location.$index.email.$innerIndex.hide.html}
              </span>
              {* Link to add another field.*}
              {if $innerIndex LT $blockCount}
                {assign var=j value=$innerIndex+1}
                <span id="id_location_{$index}_email_{$j}_show" class="add-remove-link">
                    {$form.location.$index.email.$j.show.html}
                </span>
                {* changing the code as there should not be any <div> within <span>*} 
                {*<div id="location[{$index}][email][{$j}][show]" class="add-remove-link">
                    {$form.location.$index.email.$j.show.html}
                </div>*}
              {/if}
            </span>
            
            {* Spacer div contains floated elements *}
            <div class="spacer"></div>
        </div>
{/section}
{literal}
<script type="text/javascript">
function email_is_bulkmail_onclick(formname, emailID, maxLocs, locID) {

    var changedKey = 'location[' + locID + '][email][' + emailID + '][is_bulkmail]';

    if (document.forms[formname].elements[changedKey][1].checked) {
        if ( confirm('Do you want to use this email address for bulk mailing?') == true ) {
	    for (var i = 1; i <= 5; i++) {
		Key = 'location[' +  locID +'][email][' + i + '][is_bulkmail]';
		if ( i != emailID ) {
		    document.forms[formname].elements[Key][1].checked = false;
		} 
	    }
        } else {
            document.forms[formname].elements[changedKey][1].checked = false;
        }
    } 	
    
}
</script>
{/literal}
