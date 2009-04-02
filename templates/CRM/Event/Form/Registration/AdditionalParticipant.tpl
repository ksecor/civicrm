{if $skipCount}
    <h3>Skipped Participant(s): {$skipCount}</h3>
{/if}
{if $action & 1024}
    {include file="CRM/Event/Form/Registration/PreviewHeader.tpl"}
{/if}
{capture assign='reqMark'}<span class="marker"  title="{ts}This field is required.{/ts}">*</span>{/capture}

<div class="form-item">
{if $priceSet}
    <fieldset id="priceset"><legend>{$event.fee_label}</legend>
    <dl>
    {foreach from=$priceSet.fields item=element key=field_id}
        {if ($element.html_type eq 'CheckBox' || $element.html_type == 'Radio') && $element.options_per_line}
            {assign var="element_name" value=price_$field_id}
            <dt>{$form.$element_name.label}</dt>
            <dd>
            {assign var="count" value="1"}
            <table class="form-layout-compressed">
                <tr>
                    {foreach name=outer key=key item=item from=$form.$element_name}
                        {if is_numeric($key) }
                            <td class="labels font-light">{$form.$element_name.$key.html}</td>
                            {if $count == $element.options_per_line}
                                {assign var="count" value="1"}
                                </tr>
                                <tr>
                            {else}
                                {assign var="count" value=`$count+1`}
                            {/if}
                        {/if}
                    {/foreach}
                </tr>
            </table>
            </dd>
        {else}
            {assign var="name" value=`$element.name`}
            {assign var="element_name" value="price_"|cat:$field_id}
            <dt>{$form.$element_name.label}</dt>
            <dd>&nbsp;{$form.$element_name.html}</dd>
        {/if}
        {if $element.help_post}
            <dt>&nbsp;</dt>
            <dd class="description">{$element.help_post}</dd>
        {/if}
    {/foreach}
    </dl>
    <div class="form-item">
        <dt></dt>
        <dd>{include file="CRM/Event/Form/CalculatePriceset.tpl"}</dd>
    </div> 
    </fieldset>
{else}
    {if $paidEvent}
        <table class="form-layout-compressed">
            <tr>
                <td class="label nowrap">{$event.fee_label} <span class="marker">*</span></td>
                <td>&nbsp;</td>
                <td>{$form.amount.html}</td>
            </tr>
        </table>
    {/if}
{/if}

{assign var=n value=email-$bltID}
<table class="form-layout-compressed">
    <tr>
        <td class="label nowrap">{$form.$n.label}</td><td>{$form.$n.html}</td>
    </tr>
</table>

{include file="CRM/UF/Form/Block.tpl" fields=$customPre} 
{include file="CRM/UF/Form/Block.tpl" fields=$customPost} 

<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
</div>
