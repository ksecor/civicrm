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
                        {assign var="name" value=`$cd_value.name`} 
                        {assign var="element_name" value="custom_"|cat:$field_id}
                        <dt>{$cd_value.label}</dt>
                        <dd>{$viewForm.$element_name.html}&nbsp;</dd> 
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

