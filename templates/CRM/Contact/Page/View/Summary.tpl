{* Contact Summary template for new tabbed interface. Replaces Basic.tpl *}
{if $action eq 2}
{include file="CRM/Contact/Form/Contact.tpl"}
{else}
<div id="mainTabContainer" >
    <ul>
        <li id="tab_summary"><a href="#summary" title="{ts}Summary{/ts}" >{ts}Summary{/ts}</a></li>
        {foreach from=$allTabs key=tabName item=tabValue}
            <li id="tab_{$tabValue.id}"><a href="{$tabValue.url}" title="{$tabValue.title}">{$tabValue.title}&nbsp;({$tabValue.count})</a></li>
        {/foreach}
    </ul>

    <div title="Summary" id="contact-summary" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
        {* View Contact Summary *}
        <div class="buttons ui-corner-all">
            <span id="actions">
                {if $permission EQ 'edit'}
                    <input type="button" class="edit button" accesskey="E" value="{ts}Edit{/ts}" name="edit_contact_info" onclick="window.location='{crmURL p='civicrm/contact/add' q="reset=1&action=update&cid=$contactId"}';"/>
                {/if}
                {* CRM-4418 *}
                {if call_user_func(array('CRM_Core_Permission','check'), 'delete contacts')}
                    <input type="button" class="delete button" value="{ts}Delete{/ts}" name="contact_delete" onclick="window.location='{crmURL p='civicrm/contact/view/delete' q="reset=1&delete=1&cid=$contactId"}';"/>
                {/if}
                {* Include links to enter Activities if session has 'edit' permission *}
                {if $permission EQ 'edit'}
                    {include file="CRM/Activity/Form/ActivityLinks.tpl"}
                {/if}
                &nbsp;&nbsp; <span class="label">Go to:</span>
                {if $dashboardURL }
                    <input type="button" onclick="window.location='{$dashboardURL}'" value="{ts}Dashboard{/ts}"/>
                {/if}
                {if $url }
                    <input type="button" onclick="window.location='{$url}'" value="{ts}User Record{/ts}"/>
                {/if}
            </span>

            <span id="icons">
                <a title="vCard record for this contact." href='{crmURL p='civicrm/contact/view/vcard' q="reset=1&cid=$contactId"}' title="{ts}vCard{/ts}"> <img src="{$config->resourceBase}i/vcard-icon.png" alt="vCard record for this contact." /></a>
                <a title="Printer-friendly view of this page." href='{crmURL p='civicrm/contact/view/print' q="reset=1&print=1&cid=$contactId"}' title="{ts}Print{/ts}"> <img src="{$config->resourceBase}i/print-icon.png" alt="Printer-friendly view of this page." /></a>
            </span>
        </div>    

        <div id="contactTopBar" class="ui-corner-all">
            <table>
                <tr>
                    {if $job_title}
                        <td class="label">{ts}Position{/ts}</td>
                        <td>{$job_title}</td>
                    {/if}
                    {if $current_employer_id}
                        <td class="label">{ts}Employer{/ts}</td>
                        <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$current_employer_id`"}" title="{ts}view current employer{/ts}">{$current_employer}</a></td>
                    {/if}
                    {if $legal_name}
                        <td class="label">{ts}Legal Name{/ts}</td>
                        <td>{$legal_name}</td>
                        {if $sic_code}
                            <td class="label">{ts}SIC Code{/ts}</td>
                            <td>{$sic_code}</td>
                        {/if}
                    {else}
                        <td class="label">{ts}Nickname{/ts}</td>
                        <td>{$nick_name}</td>
                    {/if}
                </tr>
                <tr>
                    <td class="label"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$contactId&selectedChild=tag"}" title="{ts}Edit Tags{/ts}">{ts}Tags{/ts}</a></td><td id="tags">{$contactTag}</td>
                    {if $source}
                        <td class="label">{ts}Source{/ts}</td><td>{$source}</td>
                    {/if}
                </tr>
            </table>
        </div>

        <div id="contact_details" class="ui-corner-all">
            <div id="contact_panel">
                <div id="contactCardLeft">
                    <table>
                        {foreach from=$phone item=item}
                            {if $item.phone}
                            <tr>
                                <td class="label">{$item.phone_type}</td>
                                <td><span {if $privacy.do_not_phone} class="do-not-phone" title={ts}"Privacy flag: Do Not Phone"{/ts} {/if}>{$item.phone}</span></td>
                            </tr>
                            {/if}
                        {/foreach}
                        {foreach from=$im item=item}
                            {if $item.name or $item.provider}
                            {if $item.name}<tr><td class="label">{$item.provider}</td><td>{$item.name}</td></tr>{/if}
                        {/if}
                        {/foreach}
                    </table>
                </div>
                <div id="contactCardRight">
                    <table>
                        {foreach from=$email item=item }
                            {if $item.email}
                            <tr>
                                <td class="label">{ts}Email{/ts}</td>
                                <td><span class={if $item.is_primary eq 1}"primary" {else if $privacy.do_not_email}"do-not-email" title="Privacy flag: Do Not Email"{/if}><a href="mailto:{$item.email}">{$item.email}</a>{if $item.is_bulkmail}&nbsp;(Bulk){/if}</span></td>
                            </tr>
                            {/if}
                        {/foreach}
                        {if $home_URL}
                        <tr>
                            <td class="label">{ts}Website{/ts}</td>
                            <td><a href="{$home_URL}" target="_blank">{$home_URL}</a></td>
                        </tr>
                        {/if}
                    </table>
                </div>
            </div>       
            <div id="contact_panel">
                <div class="spacer"> </div>
                {foreach from=$address item=add key=locationIndex}
                <div id="{cycle name=location values="contactCardLeft,contactCardRight"}">
                    <table>
                        <tr>
                            <td class="label">{$add.location_type}&nbsp;{ts}Address{/ts}</td>
                            <td>
                                {if $HouseholdName and $locationIndex eq 1}
                                    <strong>{ts}Household Address:{/ts}</strong><br />
                                    <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$mail_to_household_id`"}">{$HouseholdName}</a><br />
                                {/if}
                                {$add.display|nl2br}
                            </td>
                        </tr>
                    </table>
                </div>
                {/foreach}
                <div class="separator">&nbsp;</div>
            </div>
            <div class="spacer">&nbsp;</div>
            <div id="contact_panel">
                <div id="contactCardLeft">
                    <table>
                        <tr><td class="label">{ts}Privacy{/ts}</td>
                            <td><span class="font-red upper">
                                {foreach from=$privacy item=privacy key=index}
                                {if $privacy}{$privacy_values.$index}<br />{/if}
                                {/foreach}
                            </span></td>
                        </tr>
                        <tr>
                            <td class="label">{ts}Method{/ts}</td><td>{$preferred_communication_method_display}</td>
                        </tr>
                        <tr>
                            <td class="label">{ts}Email Format{/ts}</td><td>{$preferred_mail_format}</td>
                        </tr>
                    </table>
                </div>
                <div id="contactCardRight">
                    {if $contact_type eq 'Individual' AND $showDemographics}
                    <table>
                        <tr>
                            <td class="label">{ts}Gender{/ts}</td><td>{$gender_display}</td>
                        </tr>
                        <tr>
                            <td class="label">{ts}Date of Birth{/ts}</td><td>{$birth_date|crmDate}</td>
                        </tr>
                        <tr>
                            {if $is_deceased eq 1}
                                {if $deceased_date}<td class="label">{ts}Date Deceased{/ts}</td><td>{$deceased_date|crmDate}</td>{else}<td class="label" colspan=2><span class="font-red upper">{ts}Contact is Deceased{/ts}</span></td>{/if}
                            {else}
                                <td class="label">{ts}Age{/ts}</td>
                                <td>{if $age.y}{ts count=$age.y plural='%count years'}%count year{/ts}{elseif $age.m}{ts count=$age.m plural='%count months'}%count month{/ts}{/if} </td>
                            {/if}
                        </tr>
                    </table>
                    {/if}
                </div>
                <div class="separator">&nbsp;</div>
            </div>
            <div class="spacer">&nbsp;</div>
        </div>
        <div class="spacer">&nbsp;</div>
        <div id="customFields">
            <div id="contact_panel">
                <div id="contactCardLeft">
                    {include file="CRM/Contact/Page/View/CustomDataView.tpl" side='left'}
                </div>            
                <div id="contactCardRight">
                    {include file="CRM/Contact/Page/View/CustomDataView.tpl" side='right'}
                </div>
                <div class="separator">&nbsp;</div>
            </div>
            <div class="spacer">&nbsp;</div>
        </div>
        {*
            {foreach from=$loc.user_unique_id item=user_unique_id}
            {if $user_unique_id.user_unique_id}
            {if $user_unique_id.is_primary eq 1}<strong>{/if}
                {ts}User_Unique_Id:{/ts} {if $user_unique_id.user_unique_id}{$user_unique_id.user_unique_id}{/if}
                {if $user_unique_id.is_primary eq 1}</strong>{/if}
            </br>
            {/if}
            {/foreach}
            {foreach from=$loc.openid item=openid}
            {if $openid.openid}
            {ts}OpenID:{/ts} <a href="{$openid.openid}">{$openid.openid|mb_truncate:40}</a>

            {/if}
            {if $config->userFramework eq "Standalone" }
            {if $openid.allowed_to_login eq 1}		
            {ts}(Allowed to login){/ts}
            {/if}
            {/if} 	
            <br />
            {/foreach}
        </div>
        <div class="col2">
            {if $config->mapAPIKey AND $loc.address.geo_code_1 AND $loc.address.geo_code_2}
                <a href="{crmURL p='civicrm/contact/map' q="reset=1&cid=`$contactId`&lid=`$loc.location_type_id`"}" title="{ts}Map Primary Address{/ts}">{ts}Map this Address{/ts}</a><br />
            {/if}

        </div>
    </div>
    *}

    <script type="text/javascript"> 
        {if !$contactTag}cj("#tagLink").hide( );{/if}
        var selectedTab = 'summary';
        {if $selectedChild}selectedTab = "{$selectedChild}";{/if}    
        {literal}
        cj( function() {
            var tabIndex = cj('#tab_' + selectedTab).prevAll().length
            cj("#mainTabContainer").tabs( {selected: tabIndex} );        

            {/literal}
            var showBlocks = new Array({$showBlocks});
            var hideBlocks = new Array({$hideBlocks});
            {literal}
            // on_load_init_blocks( showBlocks, hideBlocks );
        });
        {/literal}
    </script>
    {/if}
