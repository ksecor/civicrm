{* Search criteria form elements *}
<fieldset>
    <div class="form-item">
    {if $rows}
        {if $context EQ 'smog'}
            <h3>{ts}Find Members within this Group{/ts}</h3>
        {/if}
    {else}
        {if $context EQ 'smog'}
            <h3>{ts}Find Members within this Group{/ts}</h3>
        {elseif $context EQ 'amtg'}
            <h3>{ts}Find Contacts to Add to this Group{/ts}</h3>
        {/if}
    {/if}
{literal}
   <script type="text/javascript">
        dojo.require("dojo.data.ItemFileWriteStore");
	dojo.require("civicrm.CheckableTree");
        dojo.require("dojo.parser");
        dojo.require("dijit.Dialog");

function displayGroupTree(){
    var myStore = new dojo.data.ItemFileWriteStore({url:'http://brahma/trunk/civicrm/ajax/groupTree'});
    var myModel = new dijit.tree.ForestStoreModel({
	    store: myStore,
	    query: {type:'rootGroup'},
	    rootId: 'allGroups',
	    rootLabel: 'All Groups',
	    childrenAttrs: ["children"]
	});
    var tree = new civicrm.CheckableTree({
	    model: myModel
	});
    
    //var dd = dijit.byId('id-groupPicker');
    //dd.containerNode.appendChild(tree.domNode);
    dojo.body().appendChild(tree.domNode);
    tree.startup();
};

</script>
{/literal}
    {strip}
	<table class="no-border">
        <tr>
            <td class="label">{$form.sort_name.label} {$form.sort_name.html}</td>
            <td class="label">{$form.contact_type.label} {$form.contact_type.html}</td>
            <td class="label">
                {if $context EQ 'smog'}
                    {$form.group_contact_status.label}<br />
                {else}
                    {$form.group.label} &nbsp;
                {/if}
                {if $context EQ 'smog'}
                    {$form.group_contact_status.html}
                {else}
                    {$form.group.html}
                {/if}
            <td class="label">{$form.tag.label} {$form.tag.html}</td>
            <td style="vertical-align: bottom;">
                {$form.buttons.html}
            </td>
        </tr>
 	<tr>
	<td class="tundra">
	    <a href="javascript:displayGroupTree()">All Groups</a>
            <!-a href="javascript:dijit.byId('id-groupPicker').show()">Pick your groups</a>
            <div dojoType="dijit.Dialog" id="id-groupPicker" refreshOnShow=false class="tundra" href="Group.tpl"> </div-->
	</td>
	</tr>           
        {*FIXME : uncomment following fields and place in form layout when subgroup functionality is implemented
        {if $context EQ 'smog'}
           <td>  
             {$form.subgroups.html}
             {$form.subgroups_dummy.html}
          </td>
        {/if}
        *}
    </table>
    {/strip}
    </div>
</fieldset>