{* This file provides the plugin for the im block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var location.$index Contains the current location id in evaluation, and assigned in the Location.tpl file *}
{* @var $width Contains the width setting for the first column in the table *} 

<fieldset>
	<!----------- Display Primary IM BLOCK ----------->	
    <div class="form-item">
        {$form.location.$index.im.service_id.1.label}
        {$form.location.$index.im.service_id.1.html}
        {$form.location.$index.im.screenname.1.html}
        <div class="description">Select IM service and enter screen-name / user id.</div>
    </div>

    {section name = innerLoop start = 2 loop = 4}
       {assign var=innerIndex value=$smarty.section.innerLoop.index}

    <!-- Link to EXPAND Additional IM block -->
       <div id="location[{$index}][im][{$innerIndex}][show]" class="comment">
        {$form.location.$index.im.$innerIndex.show.html}
       </div>

    <!-- Display Additional IM block fields -->
    <div id="IM.{$index}_{$imindex}" class="form-item">
        {$form.location.$index.im.$innerIndex.service_id.label}
        {$form.location.$index.im.$innerIndex.service_id.html}{$form.location.$index.im.$innerIndex.screenname.html}

        <!-- Link to hide this IM block -->
       <div id="location[{$index}][im][{$innerIndex}][hide]" class="box">
        {$form.location.$index.im.$innerIndex.hide.html}
       </div>

    </div>
    {/section}
</fieldset>
