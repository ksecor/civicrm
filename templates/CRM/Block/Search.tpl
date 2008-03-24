<div class="block-crm">
{if $config->includeDojo}
<script type="text/javascript"> 
  dojo.require("dojox.data.QueryReadStore");
  dojo.require("dijit.form.ComboBox");
  dojo.require("dojo.parser");
</script>
{literal}
<script type="text/javascript"> 
function getValue(buttonName)
{
   
    var element  = document.getElementById('id_search_block');
    var element1 = dijit.byId( 'id_sort_name' );
    var value    = element1.getValue();

    if ( value && (buttonName.name == '_qf_Edit_next_view') ) {
        element.action = "{/literal}{$config->userFrameworkBaseURL}{literal}civicrm/contact/view?reset=1&cid=" +  element1;
    } else {
        var element2             = dojo.byId( 'id_sort_name' );
        element1.valueNode.value = element2.value;
        buttonName.name          = '_qf_Basic_refresh';
    }
}

</script>
{/literal}
{/if}
    <form action="{$postURL}" name="search_block" id="id_search_block" method="post">
    <div dojoType="dojox.data.QueryReadStore" jsId="searchStore" url="{$dataURL}" doClientPaging="false"></div>
    <div class="tundra">
        <input type="hidden" name="contact_type" value="" />
        <input type="text" name="sort_name" id="id_sort_name" value="" dojoType="civicrm.FilteringSelect" store="searchStore" mode="remote" searchAttr="name"  pageSize="10" />
        <br />
         <input type="submit" name="_qf_Edit_next_view" value="{ts}View Contact{/ts}" class="form-submit" onclick="getValue(this)"/>
        <input type="submit" name="_qf_Basic_refresh" value="{ts}Search{/ts}" class="form-submit" onclick="getValue(this)"/>
       
        <br />
        <a href="{$advancedSearchURL}" title="{ts}Go to Advanced Search{/ts}">&raquo; {ts}Advanced Search{/ts}</a>
    </div>
    </form>
</div>
