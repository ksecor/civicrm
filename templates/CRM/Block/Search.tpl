<div class="block-crm">
{if $config->includeDojo}
<script type="text/javascript"> 
  dojo.require("dojox.data.QueryReadStore");
  dojo.require("dijit.form.ComboBox");
  dojo.require("dojo.parser");
</script>
{/if}
    <form action="{$postURL}" method="post">
    <div dojoType="dojox.data.QueryReadStore" jsId="searchStore" url="{$dataURL}" doClientPaging="false"></div>
    <div class="tundra">
        <input type="hidden" name="contact_type" value="" />
        {* Add the required Drupal form security token, if defined by Block.php *}
        {if $drupalFormToken}
            <input type="hidden" name="edit[token]" value="{$drupalFormToken}" />
        {/if}
        <input type="text" name="sort_name"  value="" dojoType="dijit.form.ComboBox" store="searchStore" mode="remote" searchAttr="name"  pageSize="10" />
        <br />
        <input type="submit" name="_qf_Basic_refresh" value="{ts}Search{/ts}" class="form-submit" />
        <br />
        <a href="{$advancedSearchURL}" title="{ts}Go to Advanced Search{/ts}">&raquo; {ts}Advanced Search{/ts}</a>
    </div>
    </form>
</div>
