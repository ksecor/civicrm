{if $tabHeader and count($tabHeader) gt 1}
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
        cj("#mainTabContainer").tabs( {
            selected: tabIndex,
            select: function(event, ui) {
                if ( !global_formNavigate ) {
                    var message = '{/literal}{ts}Confirm\n\nAre you sure you want to navigate away from this tab?\n\nYou have unsaved changes.\n\nPress OK to continue, or Cancel to stay on the current tab.{/ts}{literal}';
                    if ( !confirm( message ) ) {
                        return false;
                    } else {
                        global_formNavigate = true;
                    }
                }
                return true;
            }
        });        
    });
{/literal}
</script>
