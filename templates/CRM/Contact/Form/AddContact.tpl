{* added onload javascript for contact*}
{if $newContactId }
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'select_contact' ).setValue( "{$newContactId}" );
       {rdelim} );
   </script>
{/if}

<span class="form-item">
   <span class="tundra">	
        {$form.$contactFieldName.$contactCount.html}
   </span>
    
    <span id="{$contactFieldName}_{$prevCount}_show">
       <a href="#" onclick="buildContact({$contactCount},'{$contactFieldName}');return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/>{ts}Add Contact{/ts}</a>
    </span>
    <span id="{$contactFieldName}_{$contactCount}"></span>
</span>
