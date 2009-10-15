
    <dl>
    {if $priceSet.help_pre}
	<dt>&nbsp;</dt>
	<dd class="description">{$priceSet.help_pre}</dd>
    {/if}
    {foreach from=$priceSet.fields item=element key=field_id}
        {if ($element.html_type eq 'CheckBox' || $element.html_type == 'Radio') && $element.options_per_line}
            {assign var="element_name" value=price_$field_id}
            <dt style="margin-top: .5em;">{$form.$element_name.label}</dt>
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
    <div class="form-item">
	<dt></dt>
	<dd>{include file="CRM/Price/Form/Calculate.tpl"}</dd>
    </div> 
    {if $priceSet.help_post}
	<dt>&nbsp;</dt>
	<dd class="description">{$priceSet.help_post}</dd>
    {/if}
    </dl>
 
