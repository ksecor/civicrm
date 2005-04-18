{if $op eq 'add' or $op eq 'edit'}
   {include file="CRM/LocationType/Form/LocationType.tpl"}	
{/if}

<div id="ltype">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
	<th>Name</th>
	<th>Description</th>
	<th></th>
       </tr>
       {foreach from=$locationTypes item=lType}
         <tr class="{cycle values="odd-row,even-row"}">
	    <td> {$lType.name}
	    </td>	
            <td>
                {$lType.description|truncate:80:"...":true}
                {* Include '(more)' link to view entire note if it has been truncated *}
                {*assign var="descSize" value=$lType.description|count_characters:true}
                {if $descSize GT 80}
		  <a href="{crmURL p='admin/contact/locType' q="ltid=`$lType.id`&op=view"}">(more)</a>
                {/if*}
            </td>
            <td><a href="{crmURL p='admin/contact/locType' q="ltid=`$lType.id`&op=edit"}">Edit</a> |
                <a href="{crmURL p='admin/contact/locType' q="ltid=`$lType.id`&op=dact&st=`$lType.is_active`"}" onclick ="return confirm('Are you sure you want to {if $lType.is_active eq 0 } enable {else} disable {/if} this location type?');">
               {if $lType.is_active eq 0 } Activate {else} Deactivate {/if}</a>
            </td>	
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $op eq 'browse' or $op eq 'dact'}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL p='admin/contact/locType' q="op=add"}">New Location Type</a>
       </div>
       {/if}
    </div>
 </p>
</div>
