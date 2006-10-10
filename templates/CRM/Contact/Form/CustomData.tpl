
{strip}
{foreach from=$groupTree item=cd_edit key=group_id}
    <div id="{$cd_edit.name}_show" class="data-group">
    <a href="#" onclick="hide('{$cd_edit.name}_show'); show('{$cd_edit.name}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}{$cd_edit.title}{/ts}</label><br />
    </div>

    <div id="{$cd_edit.name}" class="form-item">
    <fieldset><legend><a href="#" onclick="hide('{$cd_edit.name}'); show('{$cd_edit.name}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}{$cd_edit.title}{/ts}</legend>
    {if $cd_edit.help_pre}<div class="messages help">{$cd_edit.help_pre}</div>{/if}
    <dl>
    {foreach from=$cd_edit.fields item=element key=field_id}
	{if $element.options_per_line != 0 }
        {assign var="element_name" value="custom_"|cat:$field_id}			
        <dt>{$form.$element_name.label}</dt>
        <dd class="html-adjust">
        {assign var="count" value="1"}
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
                    {if $count == $element.options_per_line}
                    </tr>
                    <tr>
                    {assign var="count" value="1"}
                    {else}
                    {assign var="count" value=`$count+1`}
                    {/if}
                {/if}
            {/foreach}
        <td></td>
        </tr>
        </table>
        {/strip}
        </dd>
        {if $element.help_post}
            <dt></dt><dd class="html-adjust description">{$element.help_post}</dd>
        {/if}
	 {else}
              {assign var="name" value=`$element.name`} 
              {assign var="element_name" value="custom_"|cat:$field_id}			
              <dt>{$form.$element_name.label}</dt>
              <dd class="html-adjust">
              <span>{$form.$element_name.html}</span>
              {if $element.data_type eq 'File'}
                {if $element.customValue.data}
                <span class="html-adjust">
                   &nbsp;Attached File : &nbsp
                   {if $groupTree.$group_id.fields.$field_id.customValue.displayURL }
                      <a href="javascript:popUp('{$groupTree.$group_id.fields.$field_id.customValue.displayURL}')" ><img src="{$groupTree.$group_id.fields.$field_id.customValue.displayURL}" height = "100" width="100"></a>
                      {*<a href="{$groupTree.$group_id.fields.$field_id.customValue.fileURL}"><img src="{$groupTree.$group_id.fields.$field_id.customValue.displayURL}" height = "100" width="100"></a>*}
                   {else}
                      <a href="{$groupTree.$group_id.fields.$field_id.customValue.fileURL}">{$groupTree.$group_id.fields.$field_id.customValue.fileName}</a>
                   {/if}
                </span>  
               {/if} 
              {/if}
              {if $element.data_type eq 'Date'}
	          {if $element.skip_calendar NEQ true } 
              <span>
               
		      {include file="CRM/common/calendar/desc.tpl" trigger=trigger_customdata_$field_id}
		      {include file="CRM/common/calendar/body.tpl" dateVar=$element_name startDate=$currentYear-$element.start_date_years endDate=$currentYear+$element.end_date_years trigger=trigger_customdata_$field_id} 
		      </span>
	          {/if}
              {/if}
              </dd>                
        	{if $element.help_post}
            	<dt>&nbsp;</dt><dd class="html-adjust description">{$element.help_post}</dd>
        	{/if}
	{/if}
    {/foreach}
    </dl>
    <div class="spacer"></div>
    {if $cd_edit.help_post}<div class="messages help">{$cd_edit.help_post}</div>{/if}
    </fieldset>
    </div>
{/foreach}
{/strip}

{if ! $mainEditForm}
<dl>
  <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>
</dl>  
{/if}

<script type="text/javascript"> 
    var showBlocks = new Array({$showBlocks1}); 
    var hideBlocks = new Array({$hideBlocks1}); 
 
{* hide and display the appropriate blocks as directed by the php code *} 
    on_load_init_blocks( showBlocks, hideBlocks ); 


{literal}


function popUp(URL) {
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=420,left = 202,top = 184');");
}
{/literal}

 </script> 
 
