{if $action eq 1 or $action eq 2 or $action eq 8}
    {include file="CRM/Admin/Form/Navigation.tpl"}
{else}
    <div id="help">
        <a href="{crmURL p="civicrm/admin/menu" q="action=add&reset=1"}" class="button" style="margin-left: 6px;"><span>&raquo; {ts}Add New Menu{/ts}</span></a>&nbsp;&nbsp;&nbsp;&nbsp;
        <span id="reset-menu" class="success-status" style="display:none">
        {capture assign=rebuildURL}{crmURL p='civicrm/admin/menu' q="reset=1"}{/capture}
        {ts 1=$rebuildURL}<a href='%1' title="Rebuild Navigation"><strong>Click here</strong></a> to see your changes in top navigation menu.{/ts}
        </span><br/><br/>
    </div>
    <div id="navigation-tree" class="navigation-tree" style="height:auto;"></div>
    <div class="spacer"></div>
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
                        id		: "edit",
                        label	: "Edit", 
                        icon	: "create.png",
                        visible	: function (node, treeObject) { if(node.length != 1) return false; return treeObject.check("renameable", node); }, 
                        action	: function (node, treeObject) { 
                                    var nid = cj(node).attr('id');
                                    var nodeID = nid.substr( 5 );
                                    var editURL = {/literal}"{crmURL p='civicrm/admin/menu' h=0 q='action=update&reset=1&id='}"{literal} + nodeID;
                                    location.href =  editURL;  
                                  } 
                    },
                    "separator",
                    { 
                        id		: "rename",
                        label	: "Rename", 
                        icon	: "rename.png",
                        visible	: function (node, treeObject) { if(node.length != 1) return false; return treeObject.check("renameable", node); }, 
                        action	: function (node, treeObject) { treeObject.rename(node); } 
                    },
                    "separator",
                    { 
                        id		: "delete",
                        label	: "Delete",
                        icon	: "remove.png",
                        visible	: function (node, treeObject) { var ok = true; $.each(node, function () { if(treeObject.check("deletable", this) == false) ok = false; return false; }); return ok; }, 
                        action	: function (node, treeObject) { $.each(node, function () { treeObject.remove(this); }); } 
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
