{* These jscript calls carryover the field help from the corresponding custom data field. HOWEVER
they are currently causing sporadic failures in insert and delete - so commenting out for now. dgg *}
{* <script type="text/javascript" src="{crmURL p='civicrm/server/uf' q="set=1&path=civicrm/server/uf"}"></script> 
<script type="text/javascript" src="{$config->resourceBase}js/UF.js"></script> *}

<fieldset><legend>{if $action eq 8}{ts}Delete CiviCRM Profile Field{/ts}{else}{ts}CiviCRM Profile Field{/ts}{/if}</legend>
{if $action ne 8} {* do not display stuff for delete function *}
    <div id="crm-submit-buttons" class="form-item"> 
    <dl> 
    {if $action ne 4} 
        <dt>&nbsp;</dt><dd>&nbsp;{$form.buttons.html}</dd> 
    {else} 
        <dt>&nbsp;</dt><dd>&nbsp;{$form.done.html}</dd> 
    {/if} {* $action ne view *} 
    </dl> 
    </div> 
{/if} {* action ne delete *}
    
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
        <dt> </dt><dd class="description">&nbsp;{ts}Select the CiviCRM field you want to include in this Profile.{/ts}</dd>
        {/if}
        <dt>{$form.label.label}</dt><dd>&nbsp;{$form.label.html}</dd>       
        <dt>{$form.is_required.label}</dt><dd>&nbsp;{$form.is_required.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Are users required to complete this field?{/ts}</dd>
        {/if}
        <dt>{$form.is_view.label}</dt><dd>&nbsp;{$form.is_view.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}If checked, users can view but not edit this field.{/ts}</dd>
        {/if}
        <dt>{$form.visibility.label}</dt><dd>&nbsp;{$form.visibility.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Is this field hidden from other users ('User and User Admin Only'), or is it visible to others ('Public User Pages')? Select 'Public User Pages and Listings' to make the field searchable (in the Profile Search form). When visibility is 'Public User Pages and Listings', users can also click the field value when viewing a contact in order to locate other contacts with the same value(s) (i.e. other contacts who live in Poland).{/ts}</dd>
        {/if}
        <dt>{$form.in_selector.label}</dt><dd>&nbsp;{$form.in_selector.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Is this field visible in the selector table displayed in profile searches? This setting applies only to fields with 'Public User Pages and Listings' visibility.{/ts}</dd>
        {/if}
        <dt>{$form.weight.label}</dt><dd>&nbsp;{$form.weight.html}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Weight controls the order in which fields are displayed within a profile. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</dd>
        {/if}
        <dt>{$form.help_post.label}</dt><dd>&nbsp;{$form.help_post.html|crmReplace:class:huge}</dd>
        {if $action neq 4}
        <dt>&nbsp;</dt><dd class="description">&nbsp;{ts}Explanatory text displayed to users for this field.{/ts}</dd>
        {/if}
        <dt>{$form.is_active.label}</dt><dd>&nbsp;{$form.is_active.html}</dd>
	<dt>{$form.is_searchable.label}</dt><dd>&nbsp;{$form.is_searchable.html}</dd>
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
