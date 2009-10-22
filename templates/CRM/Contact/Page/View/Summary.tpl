{* Contact Summary template for new tabbed interface. Replaces Basic.tpl *}
{if $action eq 2}
    {include file="CRM/Contact/Form/Contact.tpl"}
{else}
    <div id="mainTabContainer" >
        <ul>
            <li id="tab_summary"><a href="#contact-summary" title="{ts}Summary{/ts}" >{ts}Summary{/ts}</a></li>
            {foreach from=$allTabs key=tabName item=tabValue}
            <li id="tab_{$tabValue.id}"><a href="{$tabValue.url}" title="{$tabValue.title}">{$tabValue.title}&nbsp;({$tabValue.count})</a></li>
            {/foreach}
        </ul>

        <div title="Summary" id="contact-summary" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
            {if $hookContentPlacement neq 3}
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
                        {if $groupOrganizationUrl}
                            <input type="button" onclick="window.location='{$groupOrganizationUrl}'" value="{ts}Associated Multi-Org Group{/ts}"/>   
                        {/if}
                    </span> 
                    <span id="icons">
                        <a title="{ts}vCard record for this contact.{/ts}" href='{crmURL p='civicrm/contact/view/vcard' q="reset=1&cid=$contactId"}'> <img src="{$config->resourceBase}i/vcard-icon.png" alt="vCard record for this contact." /></a>
                        <a title="{ts}Printer-friendly view of this page.{/ts}" href='{crmURL p='civicrm/contact/view/print' q="reset=1&print=1&cid=$contactId"}'"> <img src="{$config->resourceBase}i/print-icon.png" alt="Printer-friendly view of this page." /></a>
                    </span>
                </div><!-- .buttons -->
                
                {if $hookContent and $hookContentPlacement eq 2}
                    {include file="CRM/Contact/Page/View/SummaryHook.tpl"}
                {/if}
                
                {if $current_employer_id OR $job_title OR $legal_name OR $sic_code OR $nick_name OR $contactTag OR $source}
                <div id="contactTopBar" class="ui-corner-all">
                    <table>
                        {if $current_employer_id OR $job_title OR $legal_name OR $sic_code OR $nick_name}
                        <tr>
                            {if $current_employer_id}
                            <td class="label">{ts}Employer{/ts}</td>
                            <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$current_employer_id`"}" title="{ts}view current employer{/ts}">{$current_employer}</a></td>
                            {/if}
                            {if $job_title}
                            <td class="label">{ts}Position{/ts}</td>
                            <td>{$job_title}</td>
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
                        {/if}
                        {if $contactTag OR $source}
                        <tr>
                            <td class="label" id="tagLink"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$contactId&selectedChild=tag"}" title="{ts}Edit Tags{/ts}">{ts}Tags{/ts}</a></td><td id="tags">{$contactTag}</td>
                            {if $source}
                            <td class="label">{ts}Source{/ts}</td><td>{$source}</td>
                            {/if}
                        </tr>
                        {/if}
                    </table>

                    <div class="clear"></div>
                </div><!-- #contactTopBar -->
                {/if}

                <div class="contact_details ui-corner-all">
                    <div class="contact_panel">
                        <div class="contactCardLeft">
                            <table>
                                {foreach from=$email item=item }
                                    {if $item.email}
                                    <tr>
                                        <td class="label">{$item.location_type}&nbsp;{ts}Email{/ts}</td>
                                        <td><span class={if $privacy.do_not_email}"do-not-email" title="{ts}Privacy flag: Do Not Email{/ts}" {elseif $item.on_hold}"email-hold" title="{ts}Email on hold - generally due to bouncing.{/ts}" {elseif $item.is_primary eq 1}"primary"{/if}><a href="mailto:{$item.email}">{$item.email}</a>{if $item.on_hold}&nbsp;({ts}On Hold{/ts}){/if}{if $item.is_bulkmail}&nbsp;({ts}Bulk{/ts}){/if}</span></td>
                                    </tr>
                                    {/if}
                                {/foreach}
                                {if $home_URL}
                                <tr>
                                    <td class="label">{ts}Website{/ts}</td>
                                    <td><a href="{$home_URL}" target="_blank">{$home_URL}</a></td>
                                </tr>
                                {/if}
                                {if $user_unique_id}
                                    <tr>
                                        <td class="label">{ts}Unique Id{/ts}</td>
                                        <td>{$user_unique_id}</td>
                                    </tr>
                                {/if}
                            </table>
                        </div><!-- #contactCardLeft -->

                        <div class="contactCardRight">
                            {if $phone OR $im OR $openid}
                                <table>
                                    {foreach from=$phone item=item}
                                        {if $item.phone}
                                        <tr>
                                            <td class="label">{$item.location_type}&nbsp;{$item.phone_type}</td>
                                            <td {if $item.is_primary eq 1}class="primary"{/if}><span {if $privacy.do_not_phone} class="do-not-phone" title={ts}"Privacy flag: Do Not Phone"{/ts} {/if}>{$item.phone}</span></td>
                                        </tr>
                                        {/if}
                                    {/foreach}
                                    {foreach from=$im item=item}
                                        {if $item.name or $item.provider}
                                        {if $item.name}<tr><td class="label">{$item.provider}&nbsp;({$item.location_type})</td><td {if $item.is_primary eq 1}class="primary"{/if}>{$item.name}</td></tr>{/if}
                                        {/if}
                                    {/foreach}
                                    {foreach from=$openid item=item}
                                        {if $item.openid}
                                            <tr>
                                                <td class="label">{$item.location_type}&nbsp;{ts}OpenID{/ts}</td>
                                                <td {if $item.is_primary eq 1}class="primary"{/if}><a href="{$item.openid}">{$item.openid|mb_truncate:40}</a>
                                                    {if $config->userFramework eq "Standalone" AND $item.allowed_to_login eq 1}
                                                        <br/> <span style="font-size:9px;">{ts}(Allowed to login){/ts}</span>
                                                    {/if}
                                                </td>
                                            </tr>
                                        {/if}
                                    {/foreach}
                                </table>
    						{/if}
                        </div><!-- #contactCardRight -->

                        <div class="clear"></div>
                    </div><!-- #contact_panel -->
					{if $address}
                    <div class="separator"></div>

                    <div class="contact_panel">
                        {foreach from=$address item=add key=locationIndex}
                        <div class="{cycle name=location values="contactCardLeft,contactCardRight"}">
                            <table>
                                <tr>
                                    <td class="label">{$add.location_type}&nbsp;{ts}Address{/ts}
                                        {if $config->mapAPIKey AND $add.geo_code_1 AND $add.geo_code_2}
                                            <br /><a href="{crmURL p='civicrm/contact/map' q="reset=1&cid=`$contactId`&lid=`$add.location_type_id`"}" title="{ts}Map {$add.location_type} Address{/ts}"><span class="geotag">{ts}Map{/ts}</span></a>
                                        {/if}</td>
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

                        <div class="clear"></div>
                    </div>

                    <div class="separator"></div>
					{/if}
                    <div class="contact_panel">
                        <div class="contactCardLeft">
                            <table>
                                <tr><td class="label">{ts}Privacy{/ts}</td>
                                    <td><span class="font-red upper">
                                        {foreach from=$privacy item=privacy key=index}
                                            {if $privacy}{$privacy_values.$index}<br />{/if}
                                        {/foreach}
					{if $is_opt_out}{ts}No Bulk Emails (User Opt Out){/ts}{/if}
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="label">{ts}Preferred Method(s){/ts}</td><td>{$preferred_communication_method_display}</td>
                                </tr>
                                <tr>
                                    <td class="label">{ts}Email Format{/ts}</td><td>{$preferred_mail_format}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="contactCardRight">
                            {if $contact_type eq 'Individual' AND $showDemographics}
                            <table>
                                <tr>
                                    <td class="label">{ts}Gender{/ts}</td><td>{$gender_display}</td>
                                </tr>
                                <tr>
                                    <td class="label">{ts}Date of birth{/ts}</td><td>
                                    {if $birthDateViewFormat}	 
                                        {$birth_date|crmDate:$birthDateViewFormat}
                                    {else}
										{$birth_date|crmDate}</td>
                                    {/if} 
                                </tr>
                                <tr>
                                    {if $is_deceased eq 1}
                                        {if $deceased_date}<td class="label">{ts}Date Deceased{/ts}</td>
                                           <td>
											{if $birthDateViewFormat}          
												{$deceased_date|crmDate:$birthDateViewFormat}
											{else}
												{$deceased_date|crmDate}
											{/if}
                                           </td>
                                        {else}<td class="label" colspan=2><span class="font-red upper">{ts}Contact is Deceased{/ts}</span></td>
                                        {/if}
                                    {else}
                                    <td class="label">{ts}Age{/ts}</td>
                                    <td>{if $age.y}{ts count=$age.y plural='%count years'}%count year{/ts}{elseif $age.m}{ts count=$age.m plural='%count months'}%count month{/ts}{/if} </td>
                                    {/if}
                                </tr>
                            </table>
                            {/if}
                        </div><!-- #contactCardRight -->
						
						<div class="clear"></div>
                        <div class="separator"></div>
						
						<div class="contactCardLeft">
						{if $contact_type neq 'Organization'}
						 <table>
							<tr>
								<td class="label">{ts}Email Greeting{/ts}{if $email_greeting_custom}<br/><span style="font-size:8px;">({ts}Customized{/ts})</span>{/if}</td>
								<td>{$email_greeting_display}</td>
							</tr>
							<tr>
								<td class="label">{ts}Postal Greeting{/ts}{if $postal_greeting_custom}<br/><span style="font-size:8px;">({ts}Customized{/ts})</span>{/if}</td>
								<td>{$postal_greeting_display}</td>
							</tr>
						 </table>
						 {/if}
						</div>
						<div class="contactCardRight">
						 <table>
							<tr>
								<td class="label">{ts}Addressee{/ts}{if $addressee_custom}<br/><span style="font-size:8px;">({ts}Customized{/ts})</span>{/if}</td>
								<td>{$addressee_display}</td>
							</tr>
						 </table>
						</div>
						
                        <div class="clear"></div>
                    </div>
                </div><!--contact_details-->

                <div id="customFields">
                    <div class="contact_panel">
                        <div class="contactCardLeft">
                            {include file="CRM/Contact/Page/View/CustomDataView.tpl" side='1'}
                        </div><!--contactCardLeft-->

                        <div class="contactCardRight">
                            {include file="CRM/Contact/Page/View/CustomDataView.tpl" side='0'}
                        </div>

                        <div class="clear"></div>
                    </div>
                </div>
                
                {if $hookContent and $hookContentPlacement eq 1}
                    {include file="CRM/Contact/Page/View/SummaryHook.tpl"}
                {/if}
            {else}
                {include file="CRM/Contact/Page/View/SummaryHook.tpl"}
            {/if}
        </div>

    </div>

    <script type="text/javascript"> 
    var selectedTab = 'summary';
    {if $selectedChild}selectedTab = "{$selectedChild}";{/if}    
	{literal}
	cj( function() {
        var tabIndex = cj('#tab_' + selectedTab).prevAll().length
        cj("#mainTabContainer").tabs( {selected: tabIndex} );        
    });
    {/literal}
    </script>
{/if}
