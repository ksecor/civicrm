{if $action eq 1 or $action eq 2}
   {include file="CRM/Admin/Form/LocationType.tpl"}	
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
       {foreach from=$rows item=lType}
         <tr class="{cycle values="odd-row,even-row"}">
	    <td> {$lType.name}
	    </td>	
            <td>
                {$lType.description|truncate:80:"...":true}
                {* Include '(more)' link to view entire note if it has been truncated *}
                {*assign var="descSize" value=$lType.description|count_characters:true}
                {if $descSize GT 80}
		  <a href="{crmURL p='admin/contact/locType' q="ltid=`$lType.id`&action=view"}">(more)</a>
                {/if*}
            </td>
	    <td>{$lType.action}</td>
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $action ne 1 and $action ne 2}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL p='admin/contact/locationType' q="action=add&reset=1"}">New Location Type</a>
       </div>
       {/if}
    </div>
 </p>
</div>
