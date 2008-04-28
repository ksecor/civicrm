           {if $priceSet}
           <table class="form-layout">
             <tr>  
             <td class="label">{$form.amount.label}</td>
             <td><table class="form-layout-compressed">
              {foreach from=$priceSet.fields item=element key=field_id}
                 {if ($element.html_type eq 'CheckBox' || $element.html_type == 'Radio') && $element.options_per_line}
                    {assign var="element_name" value=price_$field_id}
                    {assign var="count" value="1"}
                    <tr><td class="label"> {$form.$element_name.label}</td>
                        <td class="view-value">
                        <table class="form-layout-compressed">
                        {foreach name=outer key=key item=item from=$form.$element_name}
                            <tr>	
                                {if is_numeric($key) }
                                    <td class="labels font-light"><td>{$form.$element_name.$key.html}</td>
                                    {if $count == $element.options_per_line}
                                        {assign var="count" value="1"}
                                        </tr>
                                        <tr>			
                                    {else}
                                        {assign var="count" value=`$count+1`}
                                    {/if}
                                {/if}
                            </tr>
                        {/foreach}
                        {if $element.help_post AND $action eq 1}
                            <tr><td></td><td class="description">{$element.help_post}</td></tr>
                        {/if}
                        </table>
                      </td>
                    </tr>
                  {else}	
                    {assign var="name" value=`$element.name`}
                    {assign var="element_name" value="price_"|cat:$field_id}
                    <tr><td class="label"> {$form.$element_name.label}</td>
                        <td class="view-value">{$form.$element_name.html}
                            {if $element.help_post AND $action eq 1}
                                <br /><span class="description">{$element.help_post}</span>
                            {/if}
                       </td>
                    </tr>
                  {/if}
               {/foreach}
              </table>
            </td>
           </tr>
          </table>
          {else} {* NOT Price Set *}
          <table class="form-layout">
            <tr>
	    <td class="label">{$form.amount.label}<span class="marker"> *</span></td><td>{$form.amount.html}
               {if $action EQ 1}
                <br /><span class="description">{ts}Event Fee Level (if applicable).{/ts}</span>
               {/if}
            </td>
            </tr>
          {/if}
