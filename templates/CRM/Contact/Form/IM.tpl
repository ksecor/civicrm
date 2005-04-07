{* This file provides the plugin for the Instant Messenger block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var location.$index Contains the current location id, assigned in the Location.tpl file *}
{* @var $blockCount Contains the max number of IM field sets to offer. *} 

<fieldset>
	<!----------- Display Primary IM BLOCK ----------->	
    <div class="form-item">
        <span class="labels">
            {$form.location.$index.im.1.provider_id.label}
        </span>
        <span class="fields">
            {$form.location.$index.im.1.provider_id.html}
            {$form.location.$index.im.1.name.html}
            <div class="description font-italic">Select IM service provider, and enter screen-name / user id.</div>
        </span>
    </div>
    <!-- Spacer div contains floated elements -->
    <div class="spacer"></div>

    {section name = innerLoop start = 2 loop = $blockCount}
       {assign var=innerIndex value=$smarty.section.innerLoop.index}

    <!-- Link to EXPAND Additional IM block -->
       <div id="location[{$index}][im][{$innerIndex}][show]" class="show-section">
        {$form.location.$index.im.$innerIndex.show.html}
       </div>

    <!-- Display Additional IM block fields -->
    <div id="location[{$index}][im][{$innerIndex}]" class="form-item">
        <span class="labels">
            {$form.location.$index.im.$innerIndex.provider_id.label}
        </span>
        <span class="fields">
            {$form.location.$index.im.$innerIndex.provider_id.html}
            {$form.location.$index.im.$innerIndex.name.html}
        </span>
        <!-- Spacer div contains floated elements -->
        <div class="spacer"></div>

        <!-- Link to hide this IM block -->
       <div id="location[{$index}][im][{$innerIndex}][hide]" class="hide-section">
        {$form.location.$index.im.$innerIndex.hide.html}
       </div>

    </div>
    {/section}
</fieldset>
