{* template for custom data *}
    {if $action eq 1 or $action eq 2}
        {include file="CRM/Contact/Form/InlineCustomData.tpl"}
    {/if}

    {strip}
    {if $action eq 16 or $action eq 4} {* Browse or View actions *}
        {if $groupTree}
            <div class="form-item">
            
                {foreach from=$groupTree item=cd key=group_id}

                <div id="{$cd.title}[show]" class="data-group">
                <a href="#" onclick="hide('{$cd.title}[show]'); show('{$cd.title}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}{$cd.title}{/ts}</label><br />
                </div>


                <div id="{$cd.title}">
                <fieldset><legend><a href="#" onclick="hide('{$cd.title}'); show('{$cd.title}[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}{$cd.title}{/ts}</legend>
                    <dl>
                    {foreach from=$cd.fields item=cd_value key=field_id}
			        {if $cd_value.options_per_line != 0 }
			            {assign var="element_name" value=$group_id|cat:_|cat:$field_id|cat:_|cat:$cd_value.name}			
			            <dt>{$cd_value.label} </dt>
			            <dd>
			            {assign var="count" value="1"}
                        {strip}
			            <table class="form-layout-compressed">
                        <tr>
			            {section name=rowLoop start=1 loop=$viewForm.$element_name}
			            {assign var=index value=$smarty.section.rowLoop.index}
			            {if $viewForm.$element_name.$index.html != "" }
				            <td class="labels font-light">{$viewForm.$element_name.$index.html}</td>
                            {if $count == $cd_value.options_per_line}
				   	        </tr>
                            <tr>
					        {assign var="count" value="1"}
			                {else}
					        {assign var="count" value=`$count+1`}
		                    {/if}
		                {/if}
			            {/section}
			            </tr>
                        </table>
                        {/strip}
			            </dd>
		            {else}
                        {assign var="name" value=`$cd_value.name`} 
                        {assign var="element_name value=$group_id|cat:_|cat:$field_id|cat:_|cat:$cd_value.name}
                        <dt>{$cd_value.label}</dt>
                        <dd>&nbsp;{$viewForm.$element_name.html}</dd>
		            {/if}
                    {/foreach}
                    </dl>
                </fieldset>
                </div>
                {/foreach}             
            </div>
        {/if}    
    {/if}
    {/strip}
<script type="text/javascript">
    var showBlocks1 = new Array({$showBlocks1});
    var hideBlocks1 = new Array({$hideBlocks1});

    on_load_init_blocks( showBlocks1, hideBlocks1 );
</script>

