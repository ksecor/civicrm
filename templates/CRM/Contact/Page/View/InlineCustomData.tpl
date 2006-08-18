{* template for custom data *}
    {if $action eq 1 or $action eq 2}
        {include file="CRM/Contact/Form/InlineCustomData.tpl"}
    {/if}

    {strip}
    {if $action eq 16 or $action eq 4} {* Browse or View actions *}
        {if $groupTree}
            <div class="form-item">
                {foreach from=$groupTree item=cd key=group_id}
                <div id="{$cd.name}_show" class="data-group">
                <a href="#" onclick="hide('{$cd.name}_show'); show('{$cd.name}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}{$cd.title}{/ts}</label><br />
                </div>


                <div id="{$cd.name}">
                <fieldset><legend><a href="#" onclick="hide('{$cd.name}'); show('{$cd.name}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}{$cd.title}{/ts}</legend>
                    <dl>
                    {foreach from=$cd.fields item=cd_value key=field_id}
			        {if $cd_value.options_per_line != 0 }
			            {assign var="element_name" value="custom_"|cat:$field_id}			
			            <dt>{$cd_value.label} </dt>
			            <dd class="html-adjust">
                        {if $viewForm.$element_name}
                            {assign var="count" value="1"}
                            {assign var="no" value="1"}
                            {strip}
                            <table class="form-layout-compressed">
                            <tr> 
                            {section name=rowLoop start=1 loop=$viewForm.$element_name}
                            {assign var=index value=$smarty.section.rowLoop.index}
                            {if $viewForm.$element_name.$index.html != "" } 
                                {if $no != '1'} {/if}
                                <td>{$viewForm.$element_name.$index.html}</td>
                                {assign var="no" value=`$no+1`}
                                {if $count == $cd_value.options_per_line}
                                    </tr> 
                                    <tr>
                                    {assign var="count" value="1"}
                                {else}
                                    {assign var="count" value=`$count+1`}
                                {/if} 
                            {else}
                            <td></td>
                            {/if}
                            {/section}
                            </tr>
                            </table>
                            {/strip}
                        {else}
                            &nbsp;
                        {/if}
                        </dd>
		            {else}
                        {assign var="name" value=`$cd_value.name`} 
                        {assign var="element_name" value="custom_"|cat:$field_id}
                         <dt>{$cd_value.label}</dt>
                         {if $groupTree.$group_id.fields.$field_id.data_type == 'File'}
                            {if $viewForm.$element_name.html}
                             {if $groupTree.$group_id.fields.$field_id.customValue.displayURL }
                              {* <dd class="html-adjust"><a href="{$groupTree.$group_id.fields.$field_id.customValue.fileURL}"><img src="{$groupTree.$group_id.fields.$field_id.customValue.displayURL}" height = "100" width="100"></a></dd> *}
                               <dd class="html-adjust"><a href="javascript:popUp('{$groupTree.$group_id.fields.$field_id.customValue.displayURL}')" ><img src="{$groupTree.$group_id.fields.$field_id.customValue.displayURL}" height = "100" width="100"></a></dd>
                             {else}
                               <dd class="html-adjust"><a href="{$groupTree.$group_id.fields.$field_id.customValue.fileURL}">{$groupTree.$group_id.fields.$field_id.customValue.fileName}</a></dd>
                             {/if}
                            {/if}
                         {else}
                         <dd class="html-adjust">{$viewForm.$element_name.html}&nbsp;</dd>
                         {/if}
                    {/if}
                    {/foreach}
                    </dl>
                <div class="spacer"></div>
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
{literal}



function popUp(URL) {
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=420,left = 202,top = 184');");
}
{/literal}
</script>
