{* add/update/view CiviCRM Profile *}

<div class="form-item">
    <fieldset><legend>{if $action eq 8}{ts}Delete CiviCRM Profile{/ts}{else}{ts}CiviCRM Profile{/ts}{/if}</legend>
	{if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts 1=$title}Delete %1 Profile?{/ts}
          </dd>
       </dl>
      </div>
    {else}
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    {* Hide 'Used For' property if userFramework NEQ Drupal and no non-user module link exists for this profile *}
    {if $config->userFramework EQ 'Drupal' OR $otherModuleString}
        <dt>{$form.uf_group_type.label}</dt><dd>{if $config->userFramework EQ 'Drupal'}{$form.uf_group_type.html}&nbsp;{/if}{$otherModuleString}</dd>
        <dt>&nbsp;</dt><dd class="description">
        {capture assign=siteRoot}&lt;{ts}site root{/ts}&gt;{/capture}
        <table class="form-layout-compressed">
        <tr><td>{ts 1="$siteRoot/civicrm/profile?reset=1&amp;gid=3"}Profiles can be explicitly linked to a module page. Any Profile form/listings page can also be linked directly by adding its ID to the civicrm/profile path. (Example: <em>%1</em>)
        {if $config->userFramework EQ 'Drupal'}
        <ul class="left-alignment">
          <li>{ts}Check <strong>User Registration</strong> if you want this Profile to be included in the New Account registration form.{/ts}</li>
          <li>{ts}Check <strong>View/Edit User Account</strong> to include it in the view and edit screens for existing user accounts.{/ts}</li>
          <li>{ts}Check <strong>Profile</strong> if you want it included in the default contact listing and view screens for the civicrm/profile path.{/ts}</li>
          <li>{ts}Check <strong>Search Listings</strong> to use this profile as an alternate set of columns for CiviCRM search results.{/ts}</li>
        </ul>
        {/if}{/ts}
        </td></tr></table></dd>
    {/if}
    <dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Weight controls the order in which profiles are presented when there are more than one. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
    <dt>{$form.group.label}</dt><dd>{$form.group.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a group if you are using this profile for search and listings, AND you want to limit the listings to members of a specific group.{/ts}</dd>
    <dt>{$form.add_contact_to_group.label}</dt><dd>{$form.add_contact_to_group.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a group if you are using this profile for adding new contacts, AND you want the new contacts to be automatically assigned to a group.{/ts}</dd>
    <dt>{$form.help_pre.label}</dt><dd>{$form.help_pre.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed at the beginning of the form.{/ts}</dd>
    <dt>{$form.help_post.label}</dt><dd>{$form.help_post.html|crmReplace:class:huge}&nbsp;</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Explanatory text displayed at the end of the form.{/ts}</dd>
    <dt>{$form.post_URL.label}</dt><dd>{$form.post_URL.html}</dd>  
    <dt>&nbsp;</dt><dd class="description">{ts}If you are using this profile as a contact signup or edit form, and want to redirect the user to a static URL after they've submitted the form - enter the complete URL here. If this field is left blank, the built-in Profile form will be redisplayed with a generic status message - 'Your contact information has been saved.'{/ts}</dd>
    <dt>{$form.cancel_URL.label}</dt><dd>{$form.cancel_URL.html}</dd>  
    <dt>&nbsp;</dt><dd class="description">{ts}If you are using this profile as a contact signup or edit form, and want to redirect the user to a static URL if they click the Cancel button - enter the complete URL here. If this field is left blank, the built-in Profile form will be redisplayed.{/ts}</dd>
    <dt></dt><dd>{$form.add_captcha.html} {$form.add_captcha.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}When CAPTCHA is included in an add / edit profile form, users are required to read an image with letters and numbers and enter the value in a field. This helps prevent abuse by automated scripts.{/ts}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}<strong>Do not enable this feature for stand-alone profile forms. CAPTCHA requires dynamic page generation. Submitting a stand-alone form with CAPTCHA included will always result in a CAPTCHA validation error.</strong>{/ts}</dd>
    <dt></dt><dd>{$form.is_map.html} {$form.is_map.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}If enabled, a Map link is included on the profile listings rows and detail screens for any contacts whose records include sufficient location data for your mapping provider.{/ts}</dd>
    <dt></dt><dd>{$form.collapse_display.html} {$form.collapse_display.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Check this box if you want only the profile title to be displayed when the page is initially loaded (form fields are hidden until the user expands the form by clicking on the "plus" icon).{/ts}</dd>
    <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
        
    
    </dl>
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

