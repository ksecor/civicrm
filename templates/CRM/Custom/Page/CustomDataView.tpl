{* Custom Data view mode*}
{assign var="showEdit" value=1}
{foreach from=$viewCustomData item=customValues key=customGroupId}
{foreach from=$customValues item=cd_edit key=index}
    {if $showEdit and $editCustomData and $groupId}	
      <div class="action-link">
        <a href="{crmURL p="civicrm/contact/view/cd/edit" q="tableId=`$contactId`&groupId=`$groupId`&action=update&reset=1"}" class="button" style="margin-left: 6px;"><span>&raquo; {ts 1=$cd_edit.title}Edit %1{/ts}</span></a><br/><br/>
      </div>
      <span id="statusmessg" class="success-status">{ts}Selected Custom value has been deleted.{/ts}&nbsp; <a href="javascript:hide('statusmessg');">{ts}Hide{/ts}</a></span>
    {/if}
    {assign var="showEdit" value=0}
    
    <div id="{$cd_edit.name}_show_{$index}" class="section-hidden section-hidden-border">
    <a href="#" onclick="hide('{$cd_edit.name}_show_{$index}'); show('{$cd_edit.name}_{$index}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{$cd_edit.title}</label>{if $groupId and $index}&nbsp;( <a href="javascript:deleteCustomValue( {$index}, '{$cd_edit.name}_show_{$index}', {$customGroupId} );">{ts}Delete{/ts}</a> ){/if}<br />
    </div>

    <div id="{$cd_edit.name}_{$index}" class="section-shown form-item">
    <fieldset><legend><a href="#" onclick="hide('{$cd_edit.name}_{$index}'); show('{$cd_edit.name}_show_{$index}'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{$cd_edit.title}{if $groupId and $index}&nbsp;( <a href="javascript:deleteCustomValue( {$index}, '{$cd_edit.name}_{$index}', {$customGroupId} );">{ts}Delete{/ts}</a> ){/if}</legend>
    <dl>
    {foreach from=$cd_edit.fields item=element key=field_id}
        {if $element.options_per_line != 0}
            <dt>{$element.field_title}</dt>
            <dd class="html-adjust">
                    {* sort by fails for option per line. Added a variable to iterate through the element array*}
                    {foreach from=$element.field_value item=val}
                        {$val}<br/>
                    {/foreach}
            </dd>
        {else}
            <dt>{$element.field_title}</dt>
            {if $element.field_type == 'File'}
                {if $element.field_value.displayURL}
                    <dd class="html-adjust"><a href="javascript:popUp('{$element.field_value.displayURL}')" ><img src="{$element.field_value.displayURL}" height = "100" width="100"></a></dd>
                {else}
                    <dd class="html-adjust"><a href="{$element.field_value.fileURL}">{$element.field_value.fileName}</a></dd>
                {/if}
            {else}
                <dd class="html-adjust">{$element.field_value}</dd>
            {/if}
        {/if}
    {/foreach}
    </dl>
    </fieldset>
    </div>

	<script type="text/javascript">
	{if $cd_edit.collapse_display eq 0 }
		hide("{$cd_edit.name}_show_{$index}"); show("{$cd_edit.name}_{$index}");
	{else}
		show("{$cd_edit.name}_show_{$index}"); hide("{$cd_edit.name}_{$index}");
	{/if}
	</script>
{/foreach}
{/foreach}

{*currently delete is available only for tab custom data*}
{if $groupId}
<script type="text/javascript">
    {literal}
    cj( function ( ) {
        cj( '#statusmessg').hide( );
    });

    function deleteCustomValue( valueID, elementID, groupID ) {
        var postUrl = {/literal}"{crmURL p='civicrm/ajax/customvalue' h=0 }"{literal};
        cj.ajax({
          type: "POST",
          data:  "valueID=" + valueID + "&groupID=" + groupID,    
          url: postUrl,
          success: function(html){
              cj( '#' + elementID ).hide( );
              cj( '#statusmessg').show( ).fadeOut( 10000 );
          }
        });
    }
    {/literal}
</script>
{/if}

