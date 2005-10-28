<script type="text/javascript" src="{crmURL p='civicrm/server/uf' q="set=1&path=civicrm/server/uf"}"></script>
<script type="text/javascript" src="{$config->resourceBase}js/UF.js"></script>

<fieldset><legend>{if $action eq 8}{ts}Delete CiviCRM Profile Field{/ts}{else}{ts}CiviCRM Profile Field{/ts}{/if}</legend>
    <div id="crm-submit-buttons" class="form-item"> 
    <dl> 
    {if $action ne 4} 
        <dt>&nbsp;</dt><dd>&nbsp;{$form.buttons.html}</dd> 
    {else} 
        <dt>&nbsp;</dt><dd>&nbsp;{$form.done.html}</dd> 
    {/if} {* $action ne view *} 
    </dl> 
    </div> 
    
    <div class="form-item">
    {if $action eq 8}
      	<div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
          <dd>    
            {ts}WARNING: Deleting this profile field will remove it from Profile forms and listings.{/ts} {ts}Do you want to continue?{/ts}
          </dd>
       </dl>
      </div>
    {else}
        <dl>
        <dt>{$form.field_name.label}</dt><dd>&nbsp;{$form.field_name.html}</dd>
        {if $action neq 4}
        <dt> </dt><dd class="description">&nbsp;{ts}Select the CiviCRM field you want to share (expose) on the User Account screens.{/ts}</dd>
        {/if}
        <dt>{$form.is_required.label}</dt><dd>&nbsp;{$form.is_required.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Are users required to complete this field?{/ts}</dd>
        {/if}
        <dt>{$form.is_view.label}</dt><dd>&nbsp;{$form.is_view.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}If checked, users can view but not edit this field for their account.{/ts}</dd>
        {/if}
        <dt>{$form.is_registration.label}</dt><dd>&nbsp;{$form.is_registration.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Do you want to include this field in the new account registration form?{/ts}</dd>
        {/if}
        <dt>{$form.visibility.label}</dt><dd>&nbsp;{$form.visibility.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Is this field visible only to User Administrators and the user themself, or is it visible to others (Public User Pages)? Select 'Public User Pages and Listings' to make the field searchable (in the Profile Search form), and to include links to find other contacts with common value(s) in the account profile screen.{/ts}</dd>
        {/if}
        <dt>{$form.in_selector.label}</dt><dd>&nbsp;{$form.in_selector.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Is this field visible in the selector table displayed in profile searches?{/ts}</dd>
        {/if}
        <dt>{$form.weight.label}</dt><dd>&nbsp;{$form.weight.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Weight controls the order in which fields are displayed in a group. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
        {/if}
        {* User Listings are not supported for v1.1 
        <dt>{$form.listings_title.label}</dt><dd>{$form.listings_title.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">{ts}When this field is used to aggregate a user listings page, what is the title of that page?{/ts}</dd>
        {/if}
        *}
        <dt>{$form.is_match.label}</dt><dd>&nbsp;{$form.is_match.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Is this field part of the data set used to identify potential duplicate contact records?{/ts}</dd>
        {/if}
        <dt>{$form.help_post.label}</dt><dd>&nbsp;{$form.help_post.html|crmReplace:class:huge}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Explanatory text displayed to users for this field. All fields marked as 'Key to Contacts' will be combined when evaluating a match.{/ts}</dd>
        {/if}
        <dt>{$form.is_active.label}</dt><dd>&nbsp;{$form.is_active.html}</dd>
        </dl>
    </div>
    
    <div id="crm-submit-buttons" class="form-item">
    <dl>
    {/if}
    {if $action ne 4}
        <dt>&nbsp;</dt><dd>&nbsp;{$form.buttons.html}</dd>
    {else}
        <dt>&nbsp;</dt><dd>&nbsp;{$form.done.html}</dd>
    {/if} {* $action ne view *}
    </dl>
    </div>

</fieldset>

 {$initHideBoxes}
