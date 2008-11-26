<div class ="form-item">
  <dl>
    <dt>{$form.group.label}</dt><dd>{$form.group.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a group if you are using this profile for search and listings, AND you want to limit the listings to members of a specific group.{/ts}</dd>
    <dt>{$form.add_contact_to_group.label}</dt><dd>{$form.add_contact_to_group.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a group if you are using this profile for adding new contacts, AND you want the new contacts to be automatically assigned to a group.{/ts}</dd>
    <dt>{$form.notify.label}</dt><dd>{$form.notify.html}</dd>
    <dt class="extra-long-fourty">&nbsp;</dt><dd class="description">{ts}If you want member(s) of your organization to receive a notification email whenever this Profile form is used to enter or update contact information, enter one or more email addresses here. Multiple email addresses should be separated by a comma (e.g. jane@example.org, paula@example.org). The first email address listed will be used as the FROM address in the notifications.{/ts}</dd>
    <dt>{$form.post_URL.label}</dt><dd>{$form.post_URL.html}</dd>
    <dt class="extra-long-fourty">&nbsp;</dt><dd class="description">{ts}If you are using this profile as a contact signup or edit form, and want to redirect the user to a static URL after they've submitted the form - enter the complete URL here. If this field is left blank, the built-in Profile form will be redisplayed with a generic status message - 'Your contact information has been saved.'{/ts}</dd>
    <dt>{$form.cancel_URL.label}</dt><dd>{$form.cancel_URL.html}</dd>  
    <dt>&nbsp;</dt><dd class="description">{ts}If you are using this profile as a contact signup or edit form, and want to redirect the user to a static URL if they click the Cancel button - enter the complete URL here. If this field is left blank, the built-in Profile form will be redisplayed.{/ts}</dd>
    <dt></dt><dd>{$form.add_captcha.html} {$form.add_captcha.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}When CAPTCHA is included in an add / edit profile form, users are required to read an image with letters and numbers and enter the value in a field. This helps prevent abuse by automated scripts.{/ts}</dd>
    <dt>&nbsp;</dt><dd class="description"><strong>{ts}Do not enable this feature for stand-alone profile forms. CAPTCHA requires dynamic page generation. Submitting a stand-alone form with CAPTCHA included will always result in a CAPTCHA validation error.{/ts}</strong></dd>
    {if $config->userFramework EQ 'Drupal'} 
        <dt>&nbsp;</dt><dd class="description"><strong>{ts}CAPTCHA is also not available when a profile is used inside the User Registration and My Account screens.{/ts}</strong></dd>
    {/if}
    {if ($config->userFramework == 'Drupal') OR ($config->userFramework == 'Joomla') } {* Create CMS user only available for Drupal/Joomla installs. *}
        <dt>{$form.is_cms_user.label}</dt><dd>{$form.is_cms_user.html}</dd>		
        <dt class="extra-long-fourty">&nbsp;</dt><dd class="description">{ts}If you are using this profile as a contact signup form OR using it in an online contribution page, anonymous users will be given the option to create a {$config->userFramework} User Account as part of completing the form. {if $config->userFramework EQ 'Drupal'}This feature requires the 'Email Verification' option to be checked (Drupal User Settings). {/if}In addition, you must include a Primary Email Address field in the profile.{/ts}</dd>
    {/if}
    <dt></dt><dd>{$form.is_update_dupe.html} {$form.is_update_dupe.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}If enabled, the contact record is updated if a matching existing record is found. Note that if there are multiple matches, the first match found is updated.{/ts}</dd>
    <dt></dt><dd>{$form.is_map.html} {$form.is_map.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}If enabled, a Map link is included on the profile listings rows and detail screens for any contacts whose records include sufficient location data for your mapping provider.{/ts}</dd>
    <dt></dt><dd>{$form.is_edit_link.html} {$form.is_edit_link.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Check this box if you want to include a link in the listings to Edit profile fields. Only users with permission to edit the contact will see this link.{/ts}</dd>
    <dt></dt><dd>{$form.is_uf_link.html} {$form.is_uf_link.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts 1=$config->userFramework}Check this box if you want to include a link in the listings to view contacts' %1 user account information (e.g. their 'My Account' page). This link will only be included for contacts who have a user account on your website.{/ts}</dd>
  </dl>
</div>