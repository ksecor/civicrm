	<table class="form-layout">
		<tr>
            <td class="font-size12pt">{$form.sort_name.label}</td>
            <td>{$form.sort_name.html}
                <div class="description font-italic">
                    {ts}Complete OR partial Contact Name.{/ts}
                </div>
                {$form.email.html}
                <div class="description font-italic">
                    {ts}Complete OR partial Email Address.{/ts}
                </div>
            </td>
            <td>
                {$form.uf_group_id.label} {$form.uf_group_id.html}
                <br /><br />
                <div class="form-item">
                    {if $form.uf_user}{$form.uf_user.label} {$form.uf_user.html}
                    &nbsp; <a href="#" title="unselect" onclick="unselectRadio('uf_user', 'Advanced'); return false;" >unselect</a>

                    <div class="description font-italic">
                        {ts 1=$config->userFramework}Does the contact have a %1 Account?{/ts}
                    </div>
                    {/if}
                </div>
            </td>
            <td class="label">{$form.buttons.html}</td>       
        </tr>
		<tr>
{if $form.contact_type}
            <td><label>{ts}Contact Type(s){/ts}</label><br />
                {$form.contact_type.html}
            </td>
{else}
            <td>&nbsp;</td>
{/if}
{if $form.group}
            {* Choose regular or 'tall' listing-box class for Group select box based on # of groups. *}
            {if $form.group|@count GT 8}
                {assign var="boxClass" value="listing-box-tall"}
            {else}
                {assign var="boxClass" value="listing-box"}
            {/if}
            <td><label>{ts}Group(s){/ts}</label>
                <div class="{$boxClass}">
                    {foreach from=$form.group item="group_val"}
                    <div class="{cycle values="odd-row,even-row"}">
                    {$group_val.html}
                    </div>
                    {/foreach}
                </div>
            </td>
{else}
            <td>&nbsp;</td>
{/if}

{if $form.tag}
            {* Choose regular or 'tall' listing-box class for Tag select box based on # of groups. *}
            <td colspan="2"><label>{ts}Tag(s){/ts}</label>
                <div id="Tag" class="listing-box">
                {if $form.tag|@count GT 8}
                   {include file="CRM/Tag/Form/Search.tpl"}
                {else}
                    {foreach from=$form.tag item="tag_val"} 
                      <div class="{cycle values="odd-row,even-row"}">
                      {$tag_val.html}
                      </div>
                    {/foreach}
                {/if}
                </div>
            </td>
{else}
            <td colspan="2">&nbsp;</td>
{/if}
	    </tr>
        <tr>
            <td>{$form.privacy.label}</td>
            <td colspan="3">{$form.privacy.html} {help id="id-privacy"}
            </td>
        </tr>
        <tr>
            <td>{$form.contact_source.label}</td>
            <td colspan="3">{$form.contact_source.html}</td>
        </tr>
    </table>
