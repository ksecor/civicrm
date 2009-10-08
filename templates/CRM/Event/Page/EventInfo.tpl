{* this template is used for displaying event information *}

<div class="vevent">
    <div class="display-block">
        <table class="form-layout">
            {if $event.summary}
		<tr>
                    <td colspan="3" class="report">{$event.summary}</td>
                </tr>
            {/if}
            {if $event.description}
                <tr>
                    <td colspan="3" class="report">
                    <span class="summary">{$event.description}</span></td>
                </tr>
            {/if}
            <tr>
                <td><label>{ts}When{/ts}</label></td>
                <td colspan="2" width="90%">
                <abbr class="dtstart" title="{$event.event_start_date|crmDate}">
                {$event.event_start_date|crmDate}</abbr>
                {if $event.event_end_date}
                    &nbsp; {ts}through{/ts} &nbsp;
                    {* Only show end time if end date = start date *}
                    {if $event.event_end_date|date_format:"%Y%m%d" == $event.event_start_date|date_format:"%Y%m%d"}
                        <abbr class="dtend" title="{$event.event_end_date|crmDate:0:1}">
                        {$event.event_end_date|crmDate:0:1}
                        </abbr>        
                    {else}
                        <abbr class="dtend" title="{$event.event_end_date|crmDate}">
                        {$event.event_end_date|crmDate}
                        </abbr> 	
                    {/if}
                {/if}
                </td>
            </tr>
            {if $isShowLocation}
                {if $location.address.1}
                    <tr>
                        <td style="vertical-align:top;" height="auto"><label>{ts}Location{/ts}</label></td>
                        <td style="vertical-align:top;" height="auto">
                        {$location.address.1.display|nl2br}
                            </td>
                            {if ( $event.is_map && $config->mapAPIKey && ( is_numeric($location.address.1.geo_code_1)  || ( $config->mapGeoCoding && $location.address.1.city AND $location.address.1.state_province ) ) ) }
                                <td style="vertical-align:top;" rowspan=3 align="left">
                                {assign var=showDirectly value="1"}
                                {if $mapProvider eq 'Google'}
                                    {include file="CRM/Contact/Form/Task/Map/Google.tpl" fields=$showDirectly}
                                {elseif $mapProvider eq 'Yahoo'}
                                    {include file="CRM/Contact/Form/Task/Map/Yahoo.tpl"  fields=$showDirectly}
                                {/if}
                                <br/><a href="{$mapURL}" title="{ts}Show large map{/ts}">{ts}Show large map{/ts}</a>
                                </td>
                            {/if}
                    </tr> 
                {/if}
            {/if}{*End of isShowLocation condition*}  

            {if $location.phone.1.phone || $location.email.1.email}
                <tr>
                    <td><label>{ts}Contact{/ts}</label></td>
                    <td>
                        {* loop on any phones and emails for this event *}
                        {foreach from=$location.phone item=phone}
                            {if $phone.phone}
                                {if $phone.phone_type}{$phone.phone_type_display}{else}{ts}Phone{/ts}{/if}: 
                                    <span class="tel">{$phone.phone}</span> <br />
                                {/if}
                        {/foreach}

                        {foreach from=$location.email item=email}
                            {if $email.email}
                                {ts}Email:{/ts} <span class="email"><a href="mailto:{$email.email}">{$email.email}</a></span>
                            {/if}
                        {/foreach}
                    </td>
                </tr>
            {/if}
    
            {if $event.is_monetary eq 1 && $feeBlock.value}
                <tr>
                    <td style="vertical-align:top;"><label>{$event.fee_label}</label></td>
                    <td>
                        <table class="form-layout-compressed">
                            {foreach from=$feeBlock.value name=fees item=value}
                                {assign var=idx value=$smarty.foreach.fees.iteration}
                                <tr>
                                    <td>{$feeBlock.label.$idx}</td>
                                    <td>{$feeBlock.value.$idx|crmMoney}</td>
                                </tr>
                            {/foreach}
                        </table>
                    </td>
                </tr>
            {/if}
        </table>

        {include file="CRM/Custom/Page/CustomDataView.tpl"}
        
	{if $allowRegistration}
            <div class="action-link">
                <strong><a href="{$registerURL}" title="{$registerText}">&raquo; {$registerText}</a></strong>
            </div>
        {/if}
        { if $event.is_public }
            <br />{include file="CRM/Event/Page/iCalLinks.tpl"}
        {/if}
    </div>
</div>
