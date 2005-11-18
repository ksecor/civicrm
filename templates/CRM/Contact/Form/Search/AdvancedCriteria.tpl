{* Advanced Search Criteria Fieldset *}
<fieldset>
    <legend><span id="searchForm[hide]"><a href="#" onClick="hide('searchForm','searchForm[hide]'); show('searchForm[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a></span>
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
    </table>
    <fieldset><legend>{ts}Location{/ts}</legend>
    <table class="form-layout">
        <tr>
            <td class="label">{$form.street_address.label}</td>
            <td>{$form.street_address.html}</span>
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
            <td class="label">{$form.postal_code_low.label}</span>
            <td>{$form.postal_code_low.html|crmReplace:class:six}
                {$form.postal_code_high.label}
                {$form.postal_code_high.html|crmReplace:class:six}
            </td>
        </tr>
		<tr>
            <td class="label">{$form.location_type.label}</td>
            <td colspan="3">
                {$form.location_type.html}
                <div class="description">
                    {ts}Location search uses the PRIMARY location for each contact by default. To search by specific location types (e.g. Home, Work...), check one or more boxes above.{/ts}
                </div>
            </td>
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
                {$form.activity_from_date.label}
            </td>
            <td>
                 {$form.activity_from_date.html} &nbsp; {$form.activity_to_date.label} {$form.activity_to_date.html}
            </td>
        </tr>
    </table>
    </fieldset>
    {if $groupTree}
    <fieldset><legend>{ts}Custom Data{/ts}</legend>
	{foreach from=$groupTree item=cd_edit key=group_id}

	    <div id="{$cd_edit.title}[show]" class="data-group">
	    <a href="#" onClick="hide('{$cd_edit.title}[show]'); show('{$cd_edit.title}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"></a><label>{ts}{$cd_edit.title}{/ts}</label><br />
	    </div>

	    <div id="{$cd_edit.title}">
	    <p>
	    <fieldset><legend><a href="#" onClick="hide('{$cd_edit.title}'); show('{$cd_edit.title}[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a>{ts}{$cd_edit.title}{/ts}</legend>
	    <dl>
	    {foreach from=$cd_edit.fields item=element key=field_id}
        {assign var="element_name" value='custom_'|cat:$field_id}
        {if $element.options_per_line != 0}
            <dt>{$form.$element_name.label}</dt>
            <dd>
            {assign var="count" value="1"}
            {strip}
            <table class="form-layout-compressed">
            <tr>
                {* sort by fails for option per line. Added a variable to iterate through the element array*}
                {assign var="index" value="1"}
                {foreach name=outer key=key item=item from=$form.$element_name}
                {if $index < 10}
                    {assign var="index" value=`$index+1`}
                {else}
                    <td class="labels font-light">{$form.$element_name.$key.html}</td>
                        {if $count == $element.options_per_line}
                        </tr>
                        <tr>
                        {assign var="count" value="1"}
                        {else}
                        {assign var="count" value=`$count+1`}
                        {/if}
                {/if}
                {/foreach}
            </tr>
            <tr>
                <td> 
                {if $element.html_type eq 'Radio'}
                &nbsp; <a href="#" title="unselect" onclick="unselectRadio('{$element_name}', '{$form.formName}'); return false;" >unselect</a>
                {/if}
                </td>
            </tr>
            </table>
            {/strip}
            </dd>
        	{else}
            {assign var="type" value=`$element.html_type`}
	        {assign var="element_name" value='custom_'|cat:$field_id}
  	        <dt>{$form.$element_name.label}</dt><dd>&nbsp;{$form.$element_name.html}
                  {if $element.html_type eq 'Radio'}
&nbsp; <a href="#" title="unselect" onclick="unselectRadio('{$element_name}', '{$form.formName}'); return false;" >unselect</a>
                  {/if}
                </dd>
	{/if}
	    {/foreach}
	    </dl>
	    </fieldset>
	    </p>
	    </div>
	{/foreach}

    </fieldset>
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
