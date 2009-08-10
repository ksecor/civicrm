<div id="help">
{ts}Click <strong>Merge</strong> to move data from the Duplicate Contact on the left into the Main Contact. In addition to the contact data (address, phone, email...), you may choose to move all or some of the related activity records (groups, contributions, memberships, etc.).{/ts} {help id="intro"}
</div>

<div class="action-link">
    	<a href="{crmURL q="reset=1&cid=$other_cid&oid=$main_cid"}">&raquo; {ts}Flip between original and duplicate contacts.{/ts}</a>
</div>
<table>
  <tr class="columnheader">
    <th>&nbsp;</th>
    <th><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$other_cid"}">{$other_name}</a> (duplicate)</th>
    <th>{ts}Mark All{/ts}<br />=={$form.toggleSelect.html} ==&gt;</th>
    <th><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$main_cid"}">{$main_name}</a></th>
  </tr>

  {foreach from=$rows item=row key=field}
     <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.title}</td>
        <td>{$row.other}</td>
        <td style='white-space: nowrap'>{if $form.$field}=={$form.$field.html}==&gt;{/if}</td>
        <td>
            {if $row.title|substr:0:5 == "Email"   OR 
                $row.title|substr:0:7 == "Address" OR 
                $row.title|substr:0:2 == "IM"      OR 
                $row.title|substr:0:6 == "OpenID"  OR 
                $row.title|substr:0:5 == "Phone"}

	        {assign var=position  value=$field|strrpos:'_'}
                {assign var=blockId   value=$field|substr:$position+1}
                {assign var=blockName value=$field|substr:14:$position-14}

                {$form.location.$blockName.$blockId.locTypeId.html}&nbsp;
                {if $blockName eq 'address'}
                <span id="main_{$blockName}_{$blockId}_overwrite">{if $row.main}(overwrite){else}(add){/if}</span>
                {/if} 

                {$form.location.$blockName.$blockId.operation.html}&nbsp;<br />
            {/if}
            <span id="main_{$blockName}_{$blockId}">{$row.main}</span>
        </td>
     </tr>
  {/foreach}

  {foreach from=$rel_tables item=params key=paramName}
    <tr class="{cycle values="even-row,odd-row"}">
      <th>{ts}Move related...{/ts}</th><td><a href="{$params.other_url}">{$params.title}</a></td><td style='white-space: nowrap'>=={$form.$paramName.html}==&gt;</td><td><a href="{$params.main_url}">{$params.title}</a></td>
    </tr>
  {/foreach}
</table>
<div class='form-item'>
  <!--<p>{$form.moveBelongings.html} {$form.moveBelongings.label}</p>-->
  <!--<p>{$form.deleteOther.html} {$form.deleteOther.label}</p>-->
</div>
<div class='form-item'>
    <p><strong>{ts}WARNING: The duplicate contact record WILL BE DELETED after the merge is complete.{/ts}</strong></strong><br />
    {$form.buttons.html}</p>
</div>


{literal}
<script type="text/javascript">

cj(document).ready(function(){ 
    cj('table td input.form-checkbox').each(function() {
       var ele = null;
       var element = cj(this).attr('id').split('_',3);

       switch ( element['1'] ) {
           case 'addressee':
                 var ele = '#' + element['0'] + '_' + element['1'];
                 break;

           case 'email':
           case 'postal':
                 var ele = '#' + element['0'] + '_' + element['1'] + '_' + element['2'];
                 break;
       }

       if( ele ) {
          cj(this).bind( 'click', function() {
 
              if( cj( this).attr( 'checked' ) ){
                  cj('input' + ele ).attr('checked', true );
                  cj('input' + ele + '_custom' ).attr('checked', true );
              } else {
                  cj('input' + ele ).attr('checked', false );
                  cj('input' + ele + '_custom' ).attr('checked', false );
              }
          });
       }
    });
});

function mergeAddress( element, blockId ) {
   var allAddress = {/literal}{$mainLocAddress}{literal};
   var address    = eval( "allAddress." + 'main_' + element.value );
   var label      = '(overwrite)';

   if ( !address ) { 
     address = '';
     label   = '(add)';
   }

   cj( "#main_address_" + blockId ).html( address );	
   cj( "#main_address_" + blockId +"_overwrite" ).html( label );
}

</script>
{/literal}
