{* template for custom data *}
{if $action eq 0 or $action eq 1 or $action eq 2 or $recordActivity}
    {include file="CRM/Contact/Form/CustomData.tpl" mainEdit=$mainEditForm}
{/if}

{strip}
    {if $action eq 16 or $action eq 4} {* Browse or View actions *}
            <div class="form-item">
              {if $editCustomData and $groupId}
                <div class="action-link">
                  &nbsp; <a href="{crmURL p="civicrm/contact/view/cd/edit" q="tableId=`$contactId`&groupId=`$groupId`&action=update&reset=1"}">&raquo; {ts 1=$groupTree.$groupId.title}Edit %1{/ts}</a>
                </div>
              {/if} 
		{include file="CRM/Custom/Page/CustomDataView.tpl"}            
 	      {if $editCustomData and $groupId}	
                <div class="action-link">
                  &nbsp; <a href="{crmURL p="civicrm/contact/view/cd/edit" q="tableId=`$contactId`&groupId=`$groupId`&action=update&reset=1"}">&raquo; {ts 1=$groupTree.$groupId.title}Edit %1{/ts}</a>
                </div>
              {/if}
            </div>
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
