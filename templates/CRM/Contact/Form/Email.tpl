{* This file provides the plugin for the email block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $lid Contains the current location id in evaluation, and assigned in the Location.tpl file *}
{* @var $width Contains the width setting for the first column in the table *} 

<fieldset>
	<!----------- Primary EMAIL BLOCK--------- -->
    <div class="form-item">
        {$form.$lid.email_1.label}
        {$form.$lid.email_1.html}
    </div>

	{* The emailt section provides the HTML for the email block *}
	{* The section loops as many times as indicated by the variable $emailloop to give as many phone blocks *}

	{* @var $emailloop Gives the number of phone loops to be displayed, assigned in the Location.tpl file*}
	{* @var $smarty.section.emailt.index Gives the current index on the section loop *}
	{* @var $emindex Gives the current index on the section loop *}
	{* @var $email Contains the name of the email text box *}
	{* @var $exem Contains the name of the email expansion link *}
	{* @var $hideem Contains the name of the email hide link *}

	{section name = emailt start = 2 loop = $emailloop}

	{assign var = "emindex" value = "`$smarty.section.emailt.index`"}
	{assign var = "email" value = "email_`$emindex`"}
	{assign var = "exem" value = "exem`$emindex`_`$index`"} 	
 	{assign var = "hideem" value = "hideem`$emindex`_`$index`"}

    <!-- Link to EXPAND additional email block.-->
    <div id="expand_email_{$index}_{$emindex}" class="comment">
        {$form.$exem.html}
	</div>

    <!-- Additional email block.-->
	<div id="email_{$index}_{$emindex}" class="form-item">
        {$form.$lid.$email.label}
        {$form.$lid.$email.html}

        <!-- Link to HIDE this email block.-->
        <div class="box">
            {$form.$hideem.html}
        </div>
	</div>
	{/section}
</fieldset>
