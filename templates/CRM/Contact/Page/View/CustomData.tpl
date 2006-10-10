{* template for custom data *}
{if $action eq 1 or $action eq 2}
        {include file="CRM/Contact/Form/CustomData.tpl" mainEdit=$mainEditForm}
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
			               <dt>{$form.$element_name.label} </dt>
			               <dd class="html-adjust">
                           {if $form.$element_name}
                              {assign var="count" value="1"}
                              {assign var="no" value="1"}
                              {strip}
                              <table class="form-layout-compressed">
                                <tr>
                                {* sort by fails for option per line. Added a variable to iterate through the element array*}
                                {assign var="index" value="1"}
                                {foreach name=outer key=key item=item from=$form.$element_name}
                                   {if $index < 10}
                                      {assign var="index" value=`$index+1`}
                                   {else}
                                      <td class="labels font-light">{$form.$element_name.$key.html}</td>
                                      {if $count == $cd_value.options_per_line}{*4*}
                                         </tr>
                                         <tr>
                                         {assign var="count" value="1"}
                                      {else}
                                         {assign var="count" value=`$count+1`}
                                      {/if}
                                   {/if}
                                {/foreach}
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
                            {if $form.$element_name.html}
                             <dd class="html-adjust">
                             {if $groupTree.$group_id.fields.$field_id.customValue.displayURL }
                               {*<a href="{$groupTree.$group_id.fields.$field_id.customValue.fileURL}"><img src="{$groupTree.$group_id.fields.$field_id.customValue.displayURL}" height = "100" width="100"></a>*}
                               <a href="javascript:popUp('{$groupTree.$group_id.fields.$field_id.customValue.displayURL}')" ><img src="{$groupTree.$group_id.fields.$field_id.customValue.displayURL}" height = "100" width="100"></a>
                             {else}
                               <a href="{$groupTree.$group_id.fields.$field_id.customValue.fileURL}">{$groupTree.$group_id.fields.$field_id.customValue.fileName}</a>
                             {/if}
                            </dd>        
                         {* <dd class="html-adjust"><a href="{$groupTree.$group_id.fields.$field_id.customValue.fileURL}">{$groupTree.$group_id.fields.$field_id.customValue.fileName}</a></dd>*}
                            {/if}
                         {else}
                           <dd class="html-adjust">{$form.$element_name.html}&nbsp;</dd>
                         {/if}
                        {/if}
                    {/foreach}
                    </dl>
                    <div class="spacer"></div>
                </fieldset>
                </div>
                {/foreach}
                {if $editCustomData}
                    <div class="action-link">
                    {if $groupId}
                    <a href="{crmURL p="civicrm/contact/view/cd" q="cid=`$contactId`&gid=`$groupId`&action=update&reset=1"}">&raquo; {ts 1=$groupTree.$groupId.title}Edit %1{/ts}</a>
                    {/if}
                    </div>
		        {/if}
            </div>
        {/if}    
    {/if}
    {/strip}
 
{if $mainEditForm}
<script type="text/javascript"> 
    var showBlocks1 = new Array({$showBlocks1}); 
    var hideBlocks1 = new Array({$hideBlocks1}); 
 
    on_load_init_blocks( showBlocks1, hideBlocks1 ); 
</script>
{else}
<script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

    {* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
  </script>
{/if}

<script type="text/javascript">
{literal}
    function popUp(URL) {
    day = new Date();
    id = day.getTime();
    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=420,left = 202,top = 184');");
    }   
{/literal}
</script>
