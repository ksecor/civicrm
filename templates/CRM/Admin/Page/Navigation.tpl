{if $action eq 1 or $action eq 2 or $action eq 8}
    {include file="CRM/Admin/Form/Navigation.tpl"}
{else}
    <div id="help">
        {ts}Customize the CiviCRM navigation menu bar for your users here.{/ts} {help id="id-navigation"}
    </div>
    <div class="spacer"></div>
    <div id="new-menu-item">
        <a href="{crmURL p="civicrm/admin/menu" q="action=add&reset=1"}" class="button" style="margin-left: 6px;"><span>&raquo; {ts}New Menu Item{/ts}</span></a>&nbsp;&nbsp;&nbsp;&nbsp;
        <span id="reset-menu" class="success-status" style="display:none">
        {capture assign=rebuildURL}{crmURL p='civicrm/admin/menu' q="reset=1"}{/capture}
        {ts 1=$rebuildURL}<a href='%1' title="Reload page"><strong>Click here</strong></a> to reload the page and see your changes in the menu bar above.{/ts}
        </span><br/><br/>
    </div>
    <div class="spacer"></div>
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
            ui : {
                context	: 
                [ 
                    { 
                        id		: "rename",
                        label	: "Rename", 
                        icon	: "rename.png",
                        visible	: function (NODE, TREE_OBJ) { if(NODE.length != 1) return false; return TREE_OBJ.check("renameable", NODE); }, 
                        action	: function (NODE, TREE_OBJ) { TREE_OBJ.rename(NODE); } 
                    },
                    "separator",
                    { 
                        id		: "delete",
                        label	: "Delete",
                        icon	: "remove.png",
                        visible	: function (NODE, TREE_OBJ) { var ok = true; $.each(NODE, function () { if(TREE_OBJ.check("deletable", this) == false) ok = false; return false; }); return ok; }, 
                        action	: function (NODE, TREE_OBJ) { $.each(NODE, function () { TREE_OBJ.remove(this); }); } 
                    }
                ]
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
