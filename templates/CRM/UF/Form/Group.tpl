{* add/update/view CiviCRM Profile *}       
<div class="form-item">   
    {if $action eq 8 or $action eq 64}
        <fieldset>
            {if $action eq 8}
                <legend>{ts}Delete CiviCRM Profile{/ts}</legend>
            {else}
                <legend>{ts}Disable CiviCRM Profile{/ts}</legend>
            {/if}
            <div class="messages status">
                <dl>
                    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
                    <dd>{$message}</dd>
                </dl>
            </div>
        </fieldset>
    {else}
        <fieldset>
            <legend>{ts}CiviCRM Profile{/ts}</legend>
                <dl class="html-adjust">
                    <dt>{$form.title.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_uf_group' field='title' id=$gid}{/if}</dt>
                    <dd>{$form.title.html}</dd>

                    <dt>{$form.uf_group_type.label} {help id='id-used_for' file="CRM/UF/Form/Group.hlp"}</dt>
                    <dd>{$form.uf_group_type.html}&nbsp;{$otherModuleString}</dd>

                    <dt>{$form.weight.label}{if $config->userFramework EQ 'Drupal'} {help id='id-profile_weight' file="CRM/UF/Form/Group.hlp"}{/if}</dt>
                    <dd>{$form.weight.html}</dd>

                    <dt>{$form.help_pre.label} {help id='id-help_pre' file="CRM/UF/Form/Group.hlp"}</dt>
                    <dd>{$form.help_pre.html}</dd>
                    
                    <dt>{$form.help_post.label} {help id='id-help_post' file="CRM/UF/Form/Group.hlp"}</dt>
                    <dd>{$form.help_post.html}</dd>
                </dl>
                <dl>
                    <div class="spacer"></div>	
                    <dt></dt><dd>{$form.is_active.html} {$form.is_active.label}</dd>
                </dl>
	</fieldset>
        {* adding advance setting tab *}
        {include file='CRM/UF/Form/AdvanceSetting.tpl'}        
    {/if}
    {if $action ne 4}
        <dl>
            <dt></dt>
            <dd><div id="crm-submit-buttons">{$form.buttons.html}</div></dd>

            <dt></dt>
            <dd></dd>
        </dl>
    {else}
        <div id="crm-done-button">
            <dt></dt>
            <dd>{$form.done.html}</dd>
        </div>
    {/if} {* $action ne view *}
</div>
  
{if $action eq 2 or $action eq 4 } {* Update or View*}
    <p></p>
    <div class="action-link">
	<a href="{crmURL p='civicrm/admin/uf/group/field' q="action=browse&reset=1&gid=$gid"}" class="button"><span>&raquo; {ts}View or Edit Fields for this Profile{/ts}</a></span>
    </div>
{/if}

{include file="CRM/common/showHide.tpl"}

{* include jscript to warn if unsaved form field changes *}
{include file="CRM/common/formNavigate.tpl"}

