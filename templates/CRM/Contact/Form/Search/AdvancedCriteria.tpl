{* Advanced Search Criteria Fieldset *}
<fieldset>
    <legend><span id="searchForm_hide"><a href="#" onclick="hide('searchForm','searchForm_hide'); show('searchForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a></span>
        {if $context EQ 'smog'}{ts}Find Members within this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {elseif $savedSearch}{ts 1=$savedSearch.name}%1 Smart Group Criteria{/ts}
        {else}{ts}Search Criteria{/ts}{/if}
    </legend>
    <div class="form-item">
    {strip}
	<table class="form-layout">
		<tr>
            <td class="font-size12pt">{$form.sort_name.label}</td>
            <td>{$form.sort_name.html}
                <div class="description font-italic">
                    {ts}Complete OR partial contact name OR email.{/ts}
                </div>
            </td>
            <td>
                {$form.uf_group_id.label} {$form.uf_group_id.html}
            </td>
            <td class="label">{$form.buttons.html}</td>       
        </tr>
		<tr>
            <td><label>{ts}Contact Type(s){/ts}</label><br />
                {$form.contact_type.html}
            </td>
            <td><label>{ts}Group(s){/ts}</label><br />
                <div class="listing-box">
                    {foreach from=$form.group item="group_val"}
                    <div class="{cycle values="odd-row,even-row"}">
                    {$group_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
            <td><label>{ts}Tag(s){/ts}</label><br />
                <div class="listing-box">
                    {foreach from=$form.tag item="tag_val"} 
                    <div class="{cycle values="odd-row,even-row"}">
                    {$tag_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
		</tr>
        <tr>
            <td><br />{$form.privacy.label}</td>
            <td><br />{$form.privacy.html}</td>
        </tr>
    </table>
    <fieldset><legend>{ts}Location{/ts}</legend>
    <table class="form-layout">
        <tr>
            <td class="label">{$form.street_address.label}</td>
            <td>{$form.street_address.html}</td>
            <td class="label">{$form.city.label}</td>
            <td>{$form.city.html}</td>
        </tr>
        <tr>
            <td class="label">{$form.state_province.label}</td>
            <td>{$form.state_province.html|crmReplace:class:big}</td>
            <td class="label">{$form.country.label}</td>
            <td>{$form.country.html|crmReplace:class:big}</td>
        </tr>
        <tr>
            <td class="label">{$form.postal_code.label}</td>
            <td>{$form.postal_code.html}&nbsp;&nbsp;<label>{ts}OR{/ts}</label></td> 
            <td class="label">{$form.postal_code_low.label}</td>
            <td>{$form.postal_code_low.html|crmReplace:class:six}
                {$form.postal_code_high.label}
                {$form.postal_code_high.html|crmReplace:class:six}
            </td>
        </tr>
		<tr>
            <td class="label">{$form.location_type.label}</td>
            <td>{$form.location_type.html} 
                <div class="description">
                    {ts}Location search uses the PRIMARY location for each contact by default. To search by specific location types (e.g. Home, Work...), check one or more boxes above.{/ts}
                </div> 
            </td>
            <td class="label">{$form.location_name.label}</td><td>{$form.location_name.html|crmReplace:class:medium}</td>
        </tr>
    </table>
    </fieldset>

    <fieldset><legend>{ts}Activity History{/ts}</legend>
    <table class="form-layout">
        <tr>
            <td class="label">
                {$form.activity_type.label}
            </td>
            <td>
                {$form.activity_type.html}
            </td>
        </tr>
        <tr>
            <td class="label">
                {$form.activity_date_low.label}
            </td>
            <td>
                 {$form.activity_date_low.html} &nbsp; {$form.activity_date_high.label} {$form.activity_date_high.html}
            </td>
        </tr>
    </table>
    </fieldset>

    {include file="CRM/Custom/Form/Search.tpl" showHideLinks=true}

    {if $validCiviContribute}
    <div id="contributeForm_show" class="data-group">
      <a href="#" onclick="hide('contributeForm_show'); show('contributeForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
      <label>{ts}Contributions{/ts}</label>
    </div>
    <div id="contributeForm">
    <fieldset><legend><a href="#" onclick="hide('contributeForm'); show('contributeForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Contributions{/ts}</legend>
    <table class="form-layout"> 
       {include file="CRM/Contribute/Form/Search/Common.tpl"}
    </table>
    </fieldset>
    </div>
    {/if}

    {if $validCiviMember}
    <div id="memberForm_show" class="data-group">
      <a href="#" onclick="hide('memberForm_show'); show('memberForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
      <label>{ts}Memberships{/ts}</label>
    </div>
    <div id="memberForm">
    <fieldset><legend><a href="#" onclick="hide('memberForm'); show('memberForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Memberships{/ts}</legend>
    <table class="form-layout">
       <tr>
         <td> 
          {include file="CRM/Member/Form/Search/Common.tpl"}
         </td> 
       </tr>
    </table>
    </fieldset>
    </div>
    {/if}

        
    {if $validQuest}
    <div id="questForm_show" class="data-group">
      <a href="#" onclick="hide('questForm_show'); show('questForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
      <label>{ts}Quest Student{/ts}</label>
    </div>
    <div id="questForm">
    <fieldset><legend><a href="#" onclick="hide('questForm'); show('questForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Quest Student{/ts}</legend>
    <table class="form-layout"> 
       {include file="CRM/Quest/Form/Search/Common.tpl"}
    </table>
    </fieldset>
    </div>
    {/if}
        
    <div id="relationship_show" class="data-group">
      <a href="#" onclick="hide('relationship_show'); show('relationship'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
      <label>{ts}Relationship{/ts}</label>
    </div>
    <div id="relationship">
    <fieldset><legend><a href="#" onclick="hide('relationship'); show('relationship_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Relationship{/ts}</legend>
    <table class="form-layout">
         <tr>
            <td class="label">
                {$form.relation_type_id.label}
            </td>
            <td>
                {$form.relation_type_id.html}
            </td>
            <td class="label">
                {$form.relation_target_name.label}
            </td>
            <td>
                {$form.relation_target_name.html|crmReplace:class:large}
            </td>    
        </tr>
      </table>         
    </fieldset>
    </div>

{if $showTask}
    <div id="task_show" class="data-group">
      <a href="#" onclick="hide('task_show'); show('task'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
      <label>{ts}Task{/ts}</label>
    </div>
    <div id="task">
    <fieldset><legend><a href="#" onclick="hide('task'); show('task_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Task{/ts}</legend>
    <table class="form-layout">
         <tr>
            <td class="label">
                {$form.task_id.label}
            </td>
            <td>
                {$form.task_id.html}
            </td>
            <td class="label">
                {$form.task_status_id.label}
            </td>
            <td>
                {$form.task_status_id.html}
            </td>    
        </tr>
      </table>         
    </fieldset>
    </div>
{/if}

    <table class="form-layout">
    <tr>
    <td></td>
    <td class="label">{$form.buttons.html}</td>
    </tr>
    </table>
    {/strip}
    </div>
</fieldset>

<script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );

{if $customShow} 
    var showBlocks = new Array({$customShow});
    var hideBlocks = new Array({$customHide});	
    on_load_init_blocks( showBlocks, hideBlocks );
{/if}    
</script>
