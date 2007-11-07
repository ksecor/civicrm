{* add/update/view CiviCRM Profile *}       

<div class="form-item">  
 <fieldset>
  {if $action eq 8 or $action eq 64}
    {if $action eq 8}
      <legend>{ts}Delete CiviCRM Profile{/ts}</legend>
    {else}
      <legend>{ts}Disable CiviCRM Profile{/ts}</legend>
    {/if}
    <div class="messages status">
    <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>    
    {$message}
    </dd>
    </dl>
    </div>
   {else}
    <legend>{ts}CiviCRM Profile{/ts}</legend>
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    <dt>{$form.uf_group_type.label}</dt><dd>{$form.uf_group_type.html}&nbsp;{$otherModuleString}</dd>
    <dt>&nbsp;</dt><dd class="description">
    <table class="form-layout-compressed">
    <tr><td>{ts}Profiles can be explicitly linked to a module page.{/ts}
    <ul class="left-alignment">
   {if $config->userFramework EQ 'Drupal'}
    <li>{ts}Check <strong>User Registration</strong> if you want this Profile to be included in the New Account registration form.{/ts}</li>
    <li>{ts}Check <strong>View/Edit User Account</strong> to include it in the view and edit screens for existing user accounts.{/ts}</li>
   {/if}
    <li>{ts}Check <strong>Profile</strong> if you want it to use it for customized listings and view screens for the civicrm/profile path.{/ts}</li>
    <li>{ts}Check <strong>Search Results</strong> to use this profile to display an alternate set of results columns for CiviCRM Basic and Advanced Search.{/ts}</li>
    </ul>
    </td></tr></table></dd>
    <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Weight controls the order in which profiles are presented when there are more than one. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
    <dt>{$form.help_pre.label}</dt><dd>{$form.help_pre.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed at the beginning of the form.{/ts}</dd>
    <dt>{$form.help_post.label}</dt><dd>{$form.help_post.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed at the end of the form.{/ts}</dd>
    <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
    </dl>
    
    <div id="id-advanced-show" class="section-hidden section-hidden-border" style="clear: both;">
        <a href="#" onclick="hide('id-advanced-show'); show('id-advanced'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Advanced Settings{/ts}</label><br />
    </div>

    <div id="id-advanced" class="section-shown">
    <fieldset>
    <legend><a href="#" onclick="hide('id-advanced'); show('id-advanced-show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Advanced Settings{/ts}</legend>
    <dl>
    <dt>{$form.group.label}</dt><dd>{$form.group.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a group if you are using this profile for search and listings, AND you want to limit the listings to members of a specific group.{/ts}</dd>
    <dt>{$form.add_contact_to_group.label}</dt><dd>{$form.add_contact_to_group.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a group if you are using this profile for adding new contacts, AND you want the new contacts to be automatically assigned to a group.{/ts}</dd>
    <dt>{$form.notify.label}</dt><dd>{$form.notify.html}</dd>
    <dt class="extra-long-fourty">&nbsp;</dt><dd class="description">{ts}If you want member(s) of your organization to receive a notification email whenever this Profile form is used to enter or update contact information, enter one or more email addresses here. Multiple email addresses should be separated by a comma (e.g. jane@example.org, paula@example.org). The first email address listed will be used as the FROM address in the notifications.{/ts}</dd>
    <dt></dt><dd>{$form.collapse_display.html} {$form.collapse_display.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Check this box if you want only the profile title to be displayed when the page is initially loaded (form fields are hidden until the user expands the form by clicking on the "plus" icon).{/ts}</dd>
    <dt>{$form.post_URL.label}</dt><dd>{$form.post_URL.html}</dd>
    <dt class="extra-long-fourty">&nbsp;</dt><dd class="description">{ts}If you are using this profile as a contact signup or edit form, and want to redirect the user to a static URL after they've submitted the form - enter the complete URL here. If this field is left blank, the built-in Profile form will be redisplayed with a generic status message - 'Your contact information has been saved.'{/ts}</dd>
    <dt>{$form.cancel_URL.label}</dt><dd>{$form.cancel_URL.html}</dd>  
    <dt>&nbsp;</dt><dd class="description">{ts}If you are using this profile as a contact signup or edit form, and want to redirect the user to a static URL if they click the Cancel button - enter the complete URL here. If this field is left blank, the built-in Profile form will be redisplayed.{/ts}</dd>
    <dt></dt><dd>{$form.add_captcha.html} {$form.add_captcha.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}When CAPTCHA is included in an add / edit profile form, users are required to read an image with letters and numbers and enter the value in a field. This helps prevent abuse by automated scripts.{/ts}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}<strong>Do not enable this feature for stand-alone profile forms. CAPTCHA requires dynamic page generation. Submitting a stand-alone form with CAPTCHA included will always result in a CAPTCHA validation error.</strong>{/ts}</dd>
    {if $config->userFramework EQ 'Drupal'} 
        <dt>&nbsp;</dt><dd class="description">{ts}<strong>CAPTCHA is also not available when a profile is used inside the User Registration and My Account screens.</strong>{/ts}</dd>
    {/if}
    {if $config->userFramework == 'Drupal' AND $config->userFrameworkVersion >=5.1} {* Create CMS user only available for Drupal installs. *}
        <dt></dt><dd>{$form.is_cms_user.html} {$form.is_cms_user.label}</dd>
        <dt class="extra-long-fourty">&nbsp;</dt><dd class="description">{ts}If you are using this profile as a contact signup form OR using it in an online contribution page, anonymous users will be given the option to create a Drupal User Account as part of completing the form. This feature requires the 'Email Verification' option to be checked (Drupal User Settings). In addition, you must include a Primary Email Address field in the profile.{/ts}</dd>
    {/if}
    <dt></dt><dd>{$form.is_update_dupe.html} {$form.is_update_dupe.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}If enabled, the contact record is updated if a matching existing record is found. Note that if there are multiple matches, the first match found is updated.{/ts}</dd>
    <dt></dt><dd>{$form.is_map.html} {$form.is_map.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}If enabled, a Map link is included on the profile listings rows and detail screens for any contacts whose records include sufficient location data for your mapping provider.{/ts}</dd>
    <dt></dt><dd>{$form.is_edit_link.html} {$form.is_edit_link.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Check this box if you want to include a link in the listings to Edit profile fields. Only users with permission to edit the contact will see this link.{/ts}</dd>
    <dt></dt><dd>{$form.is_uf_link.html} {$form.is_uf_link.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts 1=$config->userFramework}Check this box if you want to include a links in the listings to view contacts' %1 user account information (e.g. their 'My Account' page). This link will only be included for contacts who have a user account on your website.{/ts}</dd>

    </dl>
    </div> 
    {/if}
   
    <dl>
    {if $action ne 4}
        <dt></dt>
        <dd>
        <div id="crm-submit-buttons">{$form.buttons.html}</div>
        </dd>
    {else}
        <div id="crm-done-button">
        <dt></dt><dd>{$form.done.html}</dd>
        </div>
    {/if} {* $action ne view *}
    </dl>		
    </fieldset>
</div>

{if $action eq 2 or $action eq 4 } {* Update or View*}
    <p></p>
    <div class="action-link">
    <a href="{crmURL p='civicrm/admin/uf/group/field' q="action=browse&reset=1&gid=$gid"}">&raquo;  {ts}View or Edit Fields for this Profile{/ts}</a>
    </div>
{/if}

{include file="CRM/common/showHide.tpl"}
