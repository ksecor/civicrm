<div id="help">
    {capture assign=crmURL}{crmURL p='civicrm/admin/menu' q="action=add&reset=1"}{/capture}
    {ts 1=$crmURL}Create CiviCRM Menus. ( <a href='%1'>Add Menu</a> ){/ts}
</div>
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/Navigation.tpl"}
{else}
    {if $rows}
        {*
        <table cellpadding="0" cellspacing="0" border="0">
            <tr class="columnheader">
                <th>{ts}Menu{/ts}</th>
                <th>{ts}Enabled?{/ts}</th>
                <th>&nbsp;</th>
            </tr>
            {foreach from=$rows item=row}
            <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
    	        <td>{$row.label}</td>	
    	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
    	        <td>{$row.action|replace:'xx':$row.id}</td>
            </tr>
            {/foreach}
        </table>
        *}
        <div id="navigation-tree" class="navigation-tree" style="height:auto;"></div>
        {literal}
        <script type="text/javascript">
        cj(function () {
            cj("#navigation-tree").tree({
                data  : {
                    type  : "json",
                    async : true, 
                    url : {/literal}"{crmURL p='civicrm/ajax/menu' h=0 }"{literal},
                },
                rules : {
                    droppable : [ "tree-drop" ],
                    multiple : true,
                    deletable : "all",
                    draggable : "all"
                },
                callback : {
                    onmove      : function( node, reference, type ) {
                        var postURL = {/literal}"{crmURL p='civicrm/ajax/menutree' h=0 }"{literal};
                        cj.get( postURL + '&type=move&id=' + node.id + '&ref_id=' + (reference === -1 ? 0 : reference.id) + '&move_type=' + type, 
                            function (data) {
                			    if (!node.id) node.id = data;
                		    }
                		);                		                    
                    },
                    ondrop : function ( NODE,REF_NODE,TYPE,TREE_OBJ ) {
                         
                    }
                }
            });
        });

        </script>
        {/literal}

    {/if}
{/if}
