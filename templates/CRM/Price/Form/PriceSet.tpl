<div id="priceset_{$priceSetId}">
  <fieldset>
  <legend>{ts}Contribution{/ts}</legend>
    <table class="form-layout">
      {if $priceSet.help_pre}
      <tr>
	 <td colspan=2><span class="description">{$priceSet.help_pre}</span></td>
      </tr>
      {/if}
      
      {foreach from=$priceSet.fields item=element key=field_id}

        {if ($element.html_type eq 'CheckBox' || $element.html_type == 'Radio') && $element.options_per_line}
          {assign var="element_name" value=price_$field_id}
	  <tr>
	      <td class="label">{$form.$element_name.label}</td>
	      <td>
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
	      </td>
	  </tr>

          {if $element.help_post}
          <tr><td></td>
              <td class="description">{$element.help_post}</td>
          </tr>
          {/if}

	{else}

          {assign var="name" value=`$element.name`}
          {assign var="element_name" value="price_"|cat:$field_id}

	  <tr>
	      <td class="label">{$form.$element_name.label}</td>
	      <td>{$form.$element_name.html}
	          {if $element.help_post}<br /><span class="description">{$element.help_post}</span>{/if}
	      </td>
	  </tr>

        {/if}

      {/foreach}
      {if $priceSet.help_post}
      <tr>
	 <td colspan=2><span class="description">{$priceSet.help_post}</span></td>
      </tr>
      {/if}
    </table>
    {include file="CRM/Price/Form/Calculate.tpl"} 
  </fieldset>
</div>
