<div id="help">
    <a href="{crmURL p="civicrm/admin/menu" q="action=add&reset=1"}" class="button" style="margin-left: 6px;"><span>&raquo; {ts}Add New Menu{/ts}</span></a>&nbsp;&nbsp;&nbsp;&nbsp;
    <span id="reset-menu" class="success-status" style="display:none">
        {capture assign=rebuildURL}{crmURL p='civicrm/admin/menu/rebuild' q="reset=1"}{/capture}
        {ts 1=$rebuildURL}The changes made to navigation will not be reflected in top navigation bar until you <a href='%1' title="Rebuild Navigation"><strong>click here</strong></a>.{/ts}
    </span><br/><br/>
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
                    onmove  : function( node, reference, type ) {
                        var postURL = {/literal}"{crmURL p='civicrm/ajax/menutree' h=0 }"{literal};
                        cj.get( postURL + '&type=move&id=' + node.id + '&ref_id=' + (reference === -1 ? 0 : reference.id) + '&move_type=' + type, 
                            function (data) {
                			    cj("#reset-menu").show( );
                		    }
                		);                		                    
                    },
                    onrename : function( node ) {
                        var postURL = {/literal}"{crmURL p='civicrm/ajax/menutree' h=0 }"{literal};
                        cj.get( postURL + '&type=rename&id=' + node.id + '&data=' + cj( node ).children("a:visible").text(), 
                            function (data) {
                			    cj("#reset-menu").show( );
                		    }
                		);
        			},
        			beforedelete : function( node ) {
        				return confirm("Are you sure you want to delete?");
        			},
        			ondelete : function ( node ) {
                        var postURL = {/literal}"{crmURL p='civicrm/ajax/menutree' h=0 }"{literal};
                        cj.get( postURL + '&type=delete&id=' + node.id, 
                            function (data) {
                			    cj("#reset-menu").show( );
                		    }
                		);
        			}
                }
            });
        });

        </script>
        {/literal}

    {/if}
{/if}
