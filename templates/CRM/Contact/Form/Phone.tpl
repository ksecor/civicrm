{* This file provides the plugin for the phone block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var location.$index Contains the current location id, and assigned in the Location.tpl file *}
{* @var $blockCount Contains the max number of phone field sets to offer. *} 
 
    <div class="form-item">
        <span class="labels">
            <label>{$form.location.$index.phone.1.phone.label}</label>
        </span>
        <span class="fields">
            {$form.location.$index.phone.1.phone_type.html}{$form.location.$index.phone.1.phone.html}
            <!-- Link to add a field.-->
            <span id="location[{$index}][phone][2][show]" class="add-remove-link">
                {$form.location.$index.phone.2.show.html}
            </span>
        </span>

    </div>
    <!-- Spacer div contains floated elements -->
    <div class="spacer"></div>

    {section name = innerLoop start = 2 loop = $blockCount}
       {assign var=innerIndex value=$smarty.section.innerLoop.index}

    <!-- Phone block {$innerIndex}.-->
    <div id="location[{$index}][phone][{$innerIndex}]" class="form-item">
        <span class="labels">
            <label>{$form.location.$index.phone.$innerIndex.phone.label}</label>
        </span>
        <span class="fields">
            {$form.location.$index.phone.$innerIndex.phone_type.html}{$form.location.$index.phone.$innerIndex.phone.html}
            <!-- Link to add another field.-->
            {if $innerIndex LT $blockCount}
            {assign var=j value=$innerIndex+1}
            <div id="location[{$index}][phone][{$j}][show]" class="add-remove-link">
                {$form.location.$index.phone.$j.show.html}
            </div>
            {/if}
        </span>
		<!-- Link to hide this field -->
        <span id="location[{$index}][phone][{$innerIndex}][hide]" class="add-remove-link element-right">
            {$form.location.$index.phone.$innerIndex.hide.html}
        </span>
        <!-- Spacer div contains floated elements -->
        <div class="spacer"></div>
	 </div>

	{/section}
