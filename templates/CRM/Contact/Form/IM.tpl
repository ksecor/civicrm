{* This file provides the plugin for the Instant Messenger block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var location.$index Contains the current location id, assigned in the Location.tpl file *}
{* @var $blockCount Contains the max number of im field sets to offer. *} 

    {* ----------- Display Primary im BLOCK ---------- *}	
    <div class="form-item">
        <span class="labels">
            {$form.location.$index.im.1.provider_id.label}
        </span>
        <span class="fields">
            {$form.location.$index.im.1.provider_id.html}
            {$form.location.$index.im.1.name.html}
            {*<div class="description font-italic">{ts}Select im service provider, and enter screen-name / user id.{/ts}</div>*}
            <br class="spacer"/>
            <span class="description font-italic">{ts}Select im service provider, and enter screen-name / user id.{/ts}</span>
            {* -- Link to add a field.-- *}
            <span id="id_location_{$index}_im_2_show" class="add-remove-link">
                {$form.location.$index.im.2.show.html}
            </span>
        </span>
    </div>
    {* -- Spacer div contains floated elements -- *}
    <div class="spacer"></div>

    {section name = innerLoop start = 2 loop = $blockCount}
       {assign var=innerIndex value=$smarty.section.innerLoop.index}

     {* --  im block $innerIndex -- *}
    <div id="id_location_{$index}_im_{$innerIndex}" class="form-item">
        <span class="labels">
            {$form.location.$index.im.$innerIndex.provider_id.label}
        </span>
        <span class="fields">
            <span>{$form.location.$index.im.$innerIndex.provider_id.html}</span>
            <span>{$form.location.$index.im.$innerIndex.name.html}</span>
            {* Link to hide this field *}
            <span id="id_location_{$index}_im_{$innerIndex}_hide" class="add-remove-link element-right">
            {$form.location.$index.im.$innerIndex.hide.html}
            </span>
            {* Link to add another field. *}
            {if $innerIndex LT $blockCount}
            {assign var=j value=$innerIndex+1}
            <span id="id_location_{$index}_im_{$j}_show" class="add-remove-link">
                {$form.location.$index.im.$j.show.html}
            </span>
            {* changing the code as there should not be any <div> within <span>*} 
            {*<div id="location[{$index}][im][{$j}][show]" class="add-remove-link">
                {$form.location.$index.im.$j.show.html}
            </div>*}
            {/if}
        </span>
		
        {* -- Spacer div contains floated elements *}
        <div class="spacer"></div>

    </div>
    {/section}
