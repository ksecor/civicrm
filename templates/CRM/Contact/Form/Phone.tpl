{* This file provides the plugin for the phone block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $lid Contains the current location id in evaluation, and assigned in the Location.tpl file *}
{* @var $width Contains the width setting for the first column in the table *} 
 
<fieldset>
    <div class="form-item">
        <label>{$form.location.$index.phone.1.phone.label}</label>
        {$form.location.$index.phone.1.phone_type_id.html}{$form.location.$index.phone.1.phone.html}
    </div>

    {section name = innerLoop start = 2 loop = 4}
       {assign var=innerIndex value=$smarty.section.innerLoop.index}

    <!-- Link to expand additional phone block.-->
       <div id="location[{$index}][phone][{$innerIndex}][show]" class="comment">
        {$form.location.$index.phone.$innerIndex.show.html}
       </div>

    <!-- Additional phone block.-->
    <div id="phone_{$index}_{$phindex}" class="form-item">
        <label>{$form.location.$index.phone.$innerIndex.phone.label}</label>
        {$form.location.$index.phone.$innerIndex.phone_type_id.html}{$form.location.$index.phone.$innerIndex.phone.html}

		<!-- Link to hide this phone block -->
       <div id="location[{$index}][phone][{$innerIndex}][hide]" class="box">
        {$form.location.$index.phone.$innerIndex.hide.html}
       </div>

	 </div>

	{/section}
</fieldset>
