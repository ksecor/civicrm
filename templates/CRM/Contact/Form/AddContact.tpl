{* added onload javascript for contact*}
<div dojoType="dojox.data.QueryReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra" doClientPaging="false"></div>
<span class="form-item">
   <span class="tundra">	
        {$form.$contactFieldName.$contactCount.html}
   </span>
    
    <span id="{$contactFieldName}_{$contactCount}_show">
       <a href="#" onclick="buildContact({$nextContactCount},'{$contactFieldName}');return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><a href="#" onclick="buildContact({$nextContactCount},'{$contactFieldName}');return false;">{ts}Add Contact{/ts}</a>
    </span>
    <span id="{$contactFieldName}_{$nextContactCount}"></span>
</span>
