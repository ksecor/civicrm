{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/common/WizardHeader.tpl"}
{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
<div id="help">
    {if $action eq 0}
        <p>{ts}This is the first step in creating a new online Contribution Page. You can create one or more different Contribution Pages for different purposes, audiences, campaigns, etc. Each page can have it's own introductory message, pre-configured contribution amounts, custom data collection fields, etc.{/ts}</p>
        <p>{ts}In this step, you will configure the page title, contribution type (donation, campaign contribution, etc.), goal amount, and introductory message. You will be able to go back and modify all aspects of this page at any time after completing the setup wizard.{/ts}</p>
    {else}
        {ts}Use this form to edit the page title, contribution type (e.g. donation, campaign contribution, etc.), goal amount, introduction, and status (active/inactive) for this online contribution page.{/ts}
    {/if}
</div>
 
<div class="form-item">
    <fieldset><legend>{ts}Title and Settings{/ts}</legend>
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}This title will be displayed at the top of the page.{/ts}</dd>
    <dt>{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}</dd>
    <dt class="extra-long-twenty">&nbsp;</dt><dd class="description">{ts}Select the corresponding contribution type for contributions made using this page (e.g. donation, membership fee, etc.). You can add or modify available types using the <strong>Contribution Type</strong> option from the CiviCRM Administrator Control Panel.{/ts}</dd>
    <dt>&nbsp;</dt><dd>{$form.is_for_organization.html} {$form.is_for_organization.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}If you want to allow the contribution/signup to be done by individuals on behalf of an organization.{/ts}</dd>
    <span id="for_org_text">
    <dt>{$form.for_organization.label}</dt><dd>{$form.for_organization.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Text displayed next to the checkbox for the 'contribute/signup on behalf of an organization' option on the contribution form.{/ts}</dd>
    </span>
    <dt>{$form.intro_text.label}</dt><dd class="editor">{$form.intro_text.html}</dd>
    <dt class="extra-long-twenty">&nbsp;</dt><dd class="description">{ts}Enter content for the introductory message. This will be displayed below the page title. You may include HTML formatting tags. You can also include images, as long as they are already uploaded to a server - reference them using complete URLs.{/ts}</dd>
    <dt>{$form.footer_text.label}</dt><dd class="editor">{$form.footer_text.html}</dd>
    <dt class="extra-long-twenty">&nbsp;</dt><dd class="description">{ts}If you want content displayed at the bottom of the contribution page, enter it here. You may include HTML formatting tags. You can also include images, as long as they are already uploaded to a server - reference them using complete URLs.{/ts}</dd>
    <dt>{$form.goal_amount.label}</dt><dd>{$form.goal_amount.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Enter an optional goal amount for this contribution page (e.g. for this 'campaign'). If you enable a contribution widget for this page, the widget will track progress against this goal. Otherwise, the goal will display as 'no limit'.{/ts}</dd>
    <dt>{$form.start_date.label}</dt><dd>{$form.start_date.html}</dd>
    <dt>{$form.end_date.label}</dt><dd>{$form.end_date.html}</dd>
    
    <dt>&nbsp;</dt><dd>{$form.honor_block_is_active.html} {$form.honor_block_is_active.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}If you want to allow contributors to specify a person whom they are honoring with their gift, check this box. An optional Honoree section will be included in the form. Honoree information is automatically saved and linked with the contribution record.{/ts}</dd>
    </dl>
    <div id="honor">
    <dl>
    <dt>{$form.honor_block_title.label}</dt><dd>{$form.honor_block_title.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Title for the Honoree section (e.g. &quot;Honoree Information&quot;).{/ts}</dd>
    <dt>{$form.honor_block_text.label}</dt><dd>{$form.honor_block_text.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Optional explanatory text for the Honoree section (displayed above the Honoree fields).{/ts}</dd>
    </dl>
    </div>
    <dl>
    <dt>&nbsp;</dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    {if $id}
    <dt>&nbsp;</dt><dd class="description">
        {if $config->userFramework EQ 'Drupal'}
            {ts}When your page is active, you can link people to the page by copying and pasting the following URL:{/ts}<br />
            <strong>{crmURL p='civicrm/contribute/transact' q="reset=1&id=`$id`"}</strong></dd>
        {elseif $config->userFramework EQ 'Joomla'}
            {ts 1=$id}When your page is active, create front-end links to the contribution page using the Menu Manager. Select <strong>Online Contribution</strong> and enter <strong>%1</strong> for the Contribution id.{/ts}
        {/if}
    {/if}
    </dl>
    {if $action ne 4}
        <div id="crm-submit-buttons">
            <dl><dt></dt><dd>{$form.buttons.html}</dd></dl>  
        </div>
    {else}
        <div id="crm-done-button">
            <dl><dt></dt><dd>{$form.done.html}</dd></dl>  
        </div>
    {/if}
    </fieldset>
</div>

{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_for_organization"
    trigger_value       = 1
    target_element_id   ="for_org_text" 
    target_element_type ="block"
    field_type          ="radio"
    invert              = 0
}

<script type="text/javascript">
 showHonor();
 {literal}
     function showHonor() {
        var checkbox = document.getElementsByName("honor_block_is_active");
        if (checkbox[0].checked) {
            document.getElementById("honor").style.display = "block";
        } else {
            document.getElementById("honor").style.display = "none";
        }  
     } 
 {/literal} 
</script>
