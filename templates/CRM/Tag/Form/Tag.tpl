{* this template is used for adding/editing tags  *}

<script type="text/javascript" src="{$config->resourceBase}packages/jquery/jquery.js"></script>
<script type="text/javascript" src="{$config->resourceBase}js/rest.js"></script>
<script type="text/javascript" src="{$config->resourceBase}packages/jquery/plugins/jquery.treeview.min.js"></script>
<style>
.hit {ldelim}padding-left:10px;{rdelim}
.tree li {ldelim}padding-left:10px;{rdelim}
#Tag .tree .expandable .hit {ldelim}background:url('{$config->resourceBase}/i/expandable.gif') no-repeat left 3px;cursor:pointer{rdelim}
#Tag .tree .collapsable .hit {ldelim}background:url('{$config->resourceBase}/i/collapsable.gif') no-repeat left 3px;cursor:pointer{rdelim}
#Tag #tagtree .highlighted {ldelim}background-color:lightgrey;{rdelim}
#restmsg {ldelim}position:absolute;top:20px;left:200px;z-index:10000;padding:3px 15px;{rdelim}
#restmsg.msgok {ldelim}display:block;background:yellow;{rdelim}
#restmsg.msgnok {ldelim}display:block;background:red;color:white;{rdelim}
</style>
<script type="text/javascript">
civicrm_ajaxURL="{crmURL p='civicrm/ajax/rest' h=0}";
contactID={$contactId};
{literal}



//for some unknown reason, the document.ready isn't called after the load of the tab, so it's called by "hand"
initTagTree();
jQuery(document).ready(function(){initTagTree()});
function initTagTree() {
    //unobsctructive elements are there to provide the function to those not having javascript, no need for the others
    $(".unobstructive").hide();
    $("#tagtree").treeview({
        animated: "fast",
        collapsed: true,
        unique: true
    });
    $("#tagtree ul input:checked").each (function(){
        $(this).parents("li").children(".hit").addClass('highlighted');
    });
    $("#tagtree input").change(function(){
        tagid = this.id.replace("check_", "");

        //get current tags from Summary and convert to array
        var tagLabels = cj.trim( cj("#tags").text( ) );
        
        if ( tagLabels ) {
            var tagsArray = tagLabels.split(',');
        } else{
            var tagsArray = new Array();
        }
        
        //get current tag label
        var currentTagLabel = cj("#tagLabel_" + tagid ).text( );
        if (this.checked) {
            civiREST ('entity_tag','add',{contact_id:contactID,tag_id:tagid});
            // add check to tab label array
            tagsArray.push( currentTagLabel );
        } else {
            civiREST ('entity_tag','remove',{contact_id:contactID,tag_id:tagid});
            // build array of tag labels
            tagsArray = cj.map(tagsArray, function (a) { 
                 if ( cj.trim( a ) != currentTagLabel ) {
                     return cj.trim( a );
                 }
             });
        }

        //update summary tab 
        tagLabels = tagsArray.join(', ');
        cj("#tags").html( tagLabels );
    });
};
{/literal}
</script>
<div id="restmsg"></div>
<div id="Tag" class="view-content">
<fieldset><legend>{ts}Tags{/ts}</legend>
    <p>
    {if $action eq 16}
        {if $permission EQ 'edit'}
            {capture assign=crmURL}{crmURL p='civicrm/contact/view/tag' q='action=update'}{/capture}
            <span class="unobstructive">{ts 1=$displayName 2=$crmURL}Current tags for <strong>%1</strong> are highlighted. You can add or remove tags from <a href='%2'>Edit Tags</a>.{/ts}</span>
        {else}
            {ts}Current tags are highlighted.{/ts}
        {/if}
    {else}
        {ts}Mark or unmark the checkboxes, <span class="unobstructive">and click 'Update Tags' to modify tags.<span>{/ts}
    {/if}
    </p>
    <ul id="tagtree" class="tree">
        {foreach from=$tree item="node" key="id"}
        <li id="tag_{$id}">
            {if ! $node.children}<input name="tagList[{$id}]" id="check_{$id}" type="checkbox" {if $tagged[$id]}checked="checked"{/if}/>{/if}
            {if $node.children}<input name="tagList[{$id}]" id="check_{$id}" type="checkbox" {if $tagged[$id]}checked="checked"{/if}/>{/if}
            <span {if $node.children}class="hit"{/if} id="tagLabel_{$id}">{$node.name}</span>
            {if $node.children}
            <ul>
                {foreach from=$node.children item="subnode" key="subid"}
                    <li id="tag_{$subid}">
                        <input id="check_{$subid}" name="tagList[{$subid}]" type="checkbox" {if $tagged[$subid]}checked="checked"{/if}/>
                        <span {if $subnode.children}class="hit"{/if} id="tagLabel_{$subid}">{$subnode.name}</span>
                        {if $subnode.children}
                        <ul>
                            {foreach from=$subnode.children item="subsubnode" key="subsubid"}
                                <li id="tag_{$subsubid}">
                                    <input id="check_{$subsubid}" name="tagList[{$subsubid}]" type="checkbox" {if $tagged[$subsubid]}checked="checked"{/if}/>
                                    <span id="tagLabel_{$subsubid}">{$subsubnode.name}</span>
                                </li>
                            {/foreach} 
                        </ul>
                        {/if}
                    </li>	 
                {/foreach} 
            </ul>
            {/if}
        </li>	 
        {/foreach} 
    </ul>
   
      {*foreach from=$tag item="row" key="id"}

        <div class="form-item" id="rowidtag_{$id}">
         {$form.tagList[$id].html} &nbsp;<label for="tag_{$id}">{$row}</label>
        </div>

      {/foreach*}

    {* Show Edit Tags link if in View mode *}
    {if $permission EQ 'edit' AND $action eq 16}
        </fieldset>
        <div class="action-link unobstructive">
          <a accesskey="N" href="{crmURL p='civicrm/contact/view/tag' q='action=update'}" class="button"><span>&raquo; {ts}Edit Tags{/ts}</span></a>
        </div>
    {else}
       <div class="form-item unobstructive">{$form.buttons.html}</div>
       </fieldset>
    {/if}
</div>

{if $action eq 1 or $action eq 2 }
 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";	
    on_load_init_check(fname);
 </script>
{/if}
