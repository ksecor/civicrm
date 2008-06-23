 <tr>
     <td class="font-size12pt" colspan="2">{$form.pledge_name.label}&nbsp;&nbsp;
    {if $pledge_name_value}
    <script type="text/javascript">
	dojo.addOnLoad( function( ) {ldelim}
        dijit.byId( 'pledge_name' ).setValue( "{$pledge_name_value}")
        {rdelim} );
    </script>
    {/if}
 <div dojoType="dojox.data.QueryReadStore" jsId="pledgeNameStore" url="{$dataURLPledgeName}" class="tundra">
{$form.pledge_name.html}
     </td>       
 </tr>
 <tr> 
     <td>{$form.pledge_is_active.html}&nbsp;{$form.pledge_is_active.label}</td> 
     <td>{$form.signer_pledge_done.html}&nbsp;{$form.signer_pledge_done.label}</td> 
 </tr>
   