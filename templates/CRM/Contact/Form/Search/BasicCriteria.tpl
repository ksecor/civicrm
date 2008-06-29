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
      dojo.require("dojo.parser");
      dojo.require("dijit.Dialog");
      dojo.require("dojo.data.ItemFileWriteStore");
      dojo.require("civicrm.CheckableTree");
      function displayGroupTree(){
	  var dataUrl = {/literal}"{crmURL p='civicrm/ajax/groupTree' h=0 }"{literal};
	  var myStore = new dojo.data.ItemFileWriteStore({url: dataUrl});
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

	  var dd = dijit.byId('id-groupPicker');
	  dd.containerNode.appendChild(tree.domNode);
	  tree.startup();

      };

function setCheckBoxValues(){
    var tt = dijit.byId('civicrm_CheckableTree_0');
    console.log(tt.getCheckedValues());

    var groupId = document.getElementById('group');
    groupId.value = tt.getCheckedValues( );

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
{*$form.group.label*}{ts}in{/ts} &nbsp;
                {/if}
                {if $context EQ 'smog'}
                    {$form.group_contact_status.html}
                {else}

<a href="#" onclick="dijit.byId('id-groupPicker').show()">{ts}Select Group(s){/ts}</a>
<div class="tundra" dojoType="dijit.Dialog" id="id-groupPicker" title="Select Group(s)" execute="setCheckBoxValues();">
<a href="javascript:displayGroupTree()">All Groups</a><br/>
<button dojoType=dijit.form.Button type="submit">Done</button>
</div>
                    {*$form.group.html*}
                {/if}
            <td class="label">{$form.tag.label} {$form.tag.html}</td>
            <td style="vertical-align: bottom;">
                {$form.buttons.html}
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