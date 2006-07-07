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
            {$form.location.$index.email.1.email.html}
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
