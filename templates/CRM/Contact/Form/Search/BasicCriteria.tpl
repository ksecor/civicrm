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

{ if $config->groupTree }
{literal}
<script type="text/javascript">
dojo.require("dojo.parser");
dojo.require("dijit.Dialog");
dojo.require("dojo.data.ItemFileWriteStore");
dojo.require("civicrm.CheckableTree");

function displayGroupTree( ) {
    // do not recreate if tree is already created
    if ( dijit.byId('civicrm_CheckableTree') ) {
	return;
    }

    var dataUrl = {/literal}"{crmURL p='civicrm/ajax/groupTree' h=0 }"{literal};
    var myStore = new dojo.data.ItemFileWriteStore({url: dataUrl});
    var myModel = new dijit.tree.ForestStoreModel({
	    store: myStore,
	    query: {type:'rootGroup'},
	    rootId: 'allGroups',
	    rootLabel: null,
	    childrenAttrs: ["children"]
	});
    var tree = new civicrm.CheckableTree({
	    model: myModel,
	    id: 'civicrm_CheckableTree'
	});
    
    var dd = dijit.byId('id-groupPicker');

    var button1 = new dijit.form.Button({label: "Done", type: "submit"});                                                                   
    dd.containerNode.appendChild(button1.domNode);      
    
    dd.containerNode.appendChild(tree.domNode);

    var button2 = new dijit.form.Button({label: "Done", type: "submit"});                                                                   
    dd.containerNode.appendChild(button2.domNode);      

    tree.startup();
    
};

function setCheckBoxValues( reload ) {
    var grp  = document.getElementById('id-group-names');
    if ( !reload ) {
	var tt        = dijit.byId('civicrm_CheckableTree');
	var groupId   = document.getElementById('group');
	groupId.value = tt.getCheckedIds( );
	grp.innerHTML = tt.getCheckedNames( );
    } else {
	grp.innerHTML = {/literal}"{$groupNames}"{literal};
    }
};

dojo.addOnLoad( function( ) {
    setCheckBoxValues( true );
});
</script>
{/literal}
{/if}

    {strip}
	<table class="no-border">
        <tr>
            <td class="label">{$form.sort_name.label} {$form.sort_name.html}</td>
            <td class="label">{$form.contact_type.label} {$form.contact_type.html}</td>
            <td class="label">
                {if $context EQ 'smog'}
                    {$form.group_contact_status.label}<br />
                {else}
                    {ts}in{/ts} &nbsp;
                {/if}
                {if $context EQ 'smog'}
                    {$form.group_contact_status.html}
                {else}
                    { if $config->groupTree }
                        <a href="#" onclick="dijit.byId('id-groupPicker').show(); displayGroupTree( );">{ts}Select Group(s){/ts}</a>
                        <div class="tundra" style="background-color: #f4eeee;" dojoType="dijit.Dialog" id="id-groupPicker" title="Select Group(s)" execute="setCheckBoxValues();">
                        </div><br />
                        <span id="id-group-names"></span>
                    {else}
                        {$form.group.html}
                    {/if}
                {/if}
            <td class="label">{$form.tag.label} {$form.tag.html}</td>
            <td style="vertical-align: bottom;">
                {$form.buttons.html}
            </td>
        </tr>
    </table>
    {/strip}
    </div>
</fieldset>