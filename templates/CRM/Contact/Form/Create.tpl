{* added onload javascript for source contact*}
{if $newContactId }
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'select_contact' ).setValue( "{$newContactId}" );
       {rdelim} );
   </script>
{/if}

<div class="form-item">
<fieldset><legend>New Contact Pop-up Launch Page</legend>
    <table class="form-layout-compressed">
      <tr>
 	    <td>{$form.select_contact.label}</td>
        <td>
            <div  class="tundra" dojoType= "dojox.data.QueryReadStore" jsId="organizationStore" url="{$orgDataURL}" doClientPaging="false" >
            {$form.select_contact.html}
            </div>
            <!--button dojoType="dijit.form.Button" onclick="dijit.byId('id-contactCreate').show()" class="tundra">Create new contact</button-->
            <a href="javascript:dijit.byId('id-contactCreate').show()" class="button"><span>&raquo; Create new contact</span></a>
            <div dojoType="dijit.Dialog" id="id-contactCreate" refreshOnShow=false class="tundra" href="{crmURL p='civicrm/profile/create' q="gid=1&reset=1&snippet=1&context=dialog"}">
            </div>
        </td>
      </tr> 
    </table>
</fieldset>
</div>
