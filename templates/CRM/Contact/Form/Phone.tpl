{* This file provides the plugin for the phone block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var location.$index Contains the current location id, and assigned in the Location.tpl file *}
{* @var $blockCount Contains the max number of phone field sets to offer. *} 
 
<fieldset>
    <div class="form-item">
        <span class="labels">
            <label>{$form.location.$index.phone.1.phone.label}</label>
        </span>
        <span class="fields">
            {$form.location.$index.phone.1.phone_type.html}{$form.location.$index.phone.1.phone.html}
        </span>
    </div>
    <!-- Spacer div contains floated elements -->
    <div class="spacer"></div>

    {section name = innerLoop start = 2 loop = $blockCount}
       {assign var=innerIndex value=$smarty.section.innerLoop.index}

    <!-- Link to expand additional phone block.-->
    <div id="location[{$index}][phone][{$innerIndex}][show]" class="show-section">
        {$form.location.$index.phone.$innerIndex.show.html}
    </div>

    <!-- Additional phone block.-->
    <div id="location[{$index}][phone][{$innerIndex}]" class="form-item">
        <span class="labels">
            <label>{$form.location.$index.phone.$innerIndex.phone.label}</label>
        </span>
        <span class="fields">
            {$form.location.$index.phone.$innerIndex.phone_type.html}{$form.location.$index.phone.$innerIndex.phone.html}
        </span>
        <!-- Spacer div contains floated elements -->
        <div class="spacer"></div>

		<!-- Link to hide this phone block -->
       <div id="location[{$index}][phone][{$innerIndex}][hide]" class="hide-section">
        {$form.location.$index.phone.$innerIndex.hide.html}
       </div>

	 </div>

	{/section}
</fieldset>
