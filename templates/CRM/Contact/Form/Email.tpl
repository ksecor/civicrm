{* This file provides the plugin for the email block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $lid Contains the current location id in evaluation, and assigned in the Location.tpl file *}
{* @var $width Contains the width setting for the first column in the table *} 

<fieldset>
	<!----------- Primary EMAIL BLOCK--------- -->
    <div class="form-item">
        <span class="labels">
            {$form.location.$index.email.1.email.label}
        </span>
        <span class="fields">
            {$form.location.$index.email.1.email.html}
        </span>
    </div>
    <!-- Spacer div contains floated elements -->
    <div class="spacer"></div>

    {section name = innerLoop start = 2 loop = $blockCount}
       {assign var=innerIndex value=$smarty.section.innerLoop.index}

       <!-- Link to EXPAND additional email block.-->
       <div id="location[{$index}][email][{$innerIndex}][show]" class="show-section">
        {$form.location.$index.email.$innerIndex.show.html}
       </div>

         <!-- Additional email block.-->
        <div id="location[{$index}][email][{$innerIndex}]" class="form-item">
            <span class="labels">
             {$form.location.$index.email.$innerIndex.email.label}
            </span>
            <span class="fields">
             {$form.location.$index.email.$innerIndex.email.html}
            </span>
            <!-- Spacer div contains floated elements -->
            <div class="spacer"></div>

            <!-- Link to HIDE this email block.-->
            <div id="location[{$index}][email][{$innerIndex}][hide]" class="hide-section">
             {$form.location.$index.email.$innerIndex.hide.html}
            </div>
        </div>
	{/section}
</fieldset>
