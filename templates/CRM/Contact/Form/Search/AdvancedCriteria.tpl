{* Advanced Search Criteria Fieldset *}
<fieldset>
    <legend><span id="searchForm[hide]"><a href="#" onClick="hide('searchForm','searchForm[hide]'); show('searchForm[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a></span>
        {if $context EQ 'smog'}{ts}Find Members of this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {else}{ts}Search Criteria{/ts}{/if}
    </legend>
    <div class="form-item">
    {strip}
	<table class="form-layout">
		<tr>
            <td class="font-size12pt">{$form.sort_name.label}</td>
            <td>{$form.sort_name.html}
                <div class="description font-italic">
                  {ts}Complete OR partial contact name.{/ts}
                </div>
            </td>
            <td class="label">{$form.buttons.html}</td>       
        </tr>
		<tr>
            <td><label>{ts}Contact Type(s){/ts}</label><br />
                {$form.cb_contact_type.html}
            </td>
            <td><label>{ts}In Group(s){/ts}</label><br />
                <div class="listing-box">
                    {foreach from=$form.cb_group item="cb_group_val"}
                    <div class="{cycle values="odd-row,even-row"}">
                    {$cb_group_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
            <td><label>{ts}Tag(s){/ts}</label><br />
                <div class="listing-box">
                    {foreach from=$form.cb_tag item="cb_tag_val"} 
                    <div class="{cycle values="odd-row,even-row"}">
                    {$cb_tag_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
		</tr>
    </table>
    <fieldset><legend>{ts}Location{/ts}</legend>
    <table class="form-layout">
        <tr>
            <td class="label">{$form.street_name.label}</td>
            <td>{$form.street_name.html}</span>
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
        
            <td class="label">{$form.cb_location_type.label}</td>
            <td colspan="3">
                {$form.cb_location_type.html}
            </td>
        </tr>


        <tr>
            <td></td>
            <td colspan="2">
                {$form.cb_primary_location.html}<br />
                <span class="description font-italic">
                {ts}Ignore any contact locations not marked as primary for this search.{/ts}
                </span>
            </td>
            <td></td>
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
	        {assign var="name" value=`$element.name`} 
	        {assign var="element_name" value='customData_'|cat:$group_id|cat:_|cat:$field_id|cat:_|cat:$element.name}
	        <dt>{$form.$element_name.label}</dt><dd>&nbsp;{$form.$element_name.html}</dd>
	    {/foreach}
	    </dl>
	    </fieldset>
	    </p>
	    </div>
	{/foreach}

    </fieldset>
    {/strip}

    <div class="element-right">{$form.buttons.html}</div>
    <div>&nbsp;</div>
    <div class="spacer"></div>
    </div>

</fieldset>

<script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
</script>
