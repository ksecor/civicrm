{* This file provides the plugin for the phone block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $lid Contains the current location id in evaluation, and assigned in the Location.tpl file *}
{* @var $width Contains the width setting for the first column in the table *} 
 
<fieldset>
    <!------------ Primary (1st) PHONE BLOCK ------------->
    <div class="form-item">
        <label>{$form.$lid.phone_1.label}</label>
        {$form.$lid.phone_type_1.html}{$form.$lid.phone_1.html}
    </div>

	{* The phonet section provides the HTML for the phone block *}
	{* The section loops as many times as indicated by the variable $phoneloop to give as many phone blocks *}

	{* @var $phoneloop Gives the number of phone loops to be displayed, assigned in the Location.tpl file*}
	{* @var $smarty.section.phonet.index Gives the current index on the section loop *}
	{* @var $phindex Gives the current index on the section loop *}
	{* @var $phone Contains the name of the phone text box *}
	{* @var $phone_type Contains the name of the phone select box *}
	{* @var $exph Contains the name of the phone expansion link *}
	{* @var $hideph Contains the name of the phone hide link *}


	{section name = phonet start = 2 loop = $phoneloop}

	{assign var = "phindex" value = "`$smarty.section.phonet.index`"}
	{assign var = "phone" value = "phone_`$phindex`"}
	{assign var = "phone_type" value = "phone_type_`$phindex`"}
	{assign var = "exph" value = "exph`$phindex`_`$index`"} 	
 	{assign var = "hideph" value = "hideph`$phindex`_`$index`"}

    <!-- Link to expand additional phone block.-->
    <div id="expand_phone_{$index}_{$phindex}" class="comment">
        {$form.$exph.html}
    </div>

    <!-- Additional phone block.-->
    <div id="phone_{$index}_{$phindex}" class="form-item">
        <label>{$form.$lid.$phone.label}</label>
        {$form.$lid.$phone_type.html}{$form.$lid.$phone.html}

		<!-- Link to hide this phone block -->
        <div class="box">
            {$form.$hideph.html}
        </div>
	 </div>

	{/section}
</fieldset>
