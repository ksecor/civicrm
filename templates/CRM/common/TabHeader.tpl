{if $tabHeader and count($tabHeader) gt 1}
<div id="mainTabContainer">
<ul class="wizard-bar">
{foreach from=$tabHeader key=tabName item=tabValue}
  <li id="tab_{$tabName}">
  {if $tabValue.link and $tabValue.active}
     <a href="{$tabValue.link}" title="{$tabValue.title}">{$tabValue.title}</a>
  {else}
     {$tabValue.title}
  {/if}
  {if ! $tabValue.valid}
    &nbsp; ({ts}disabled{/ts})
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
