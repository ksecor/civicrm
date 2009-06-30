{if $tabHeader and count($tabHeader) gt 1}
{if $doneUrl}
   <table class="form-layout"><tr><td align="right"><input type="button" name='cancel' value="{ts}Done{/ts}" onclick="location.href='{$doneUrl}';"/></td></tr></table>
{/if}
<div id="mainTabContainer">
<ul>
{foreach from=$tabHeader key=tabName item=tabValue}
   <li id="tab_{$tabName}">
   {if $tabValue.link and $tabValue.active}
      <a href="{$tabValue.link}" title="{$tabValue.title}">{$tabValue.title}{if !$tabValue.valid}&nbsp;({ts}disabled{/ts}){/if}</a>
   {else}
      {$tabValue.title}{if !$tabValue.valid}&nbsp;({ts}disabled{/ts}){/if}
   {/if}
   </li>
{/foreach}
</ul>
</div>
{/if}


<script type="text/javascript"> 
   var selectedTab = 'EventInfo';
   {if $selectedTab}selectedTab = "{$selectedTab}";{/if}    
{literal}
    cj( function() {
        var tabIndex = cj('#tab_' + selectedTab).prevAll().length
        cj("#mainTabContainer").tabs( {selected: tabIndex} );        
    });
{/literal}
</script>
