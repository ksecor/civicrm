{literal}<link rel="stylesheet" type="text/css" href="{/literal}{$config->userFrameworkResourceURL}{literal}css/civicrm.css" />{/literal}
<div id="crm-container">
<table class ="chart">
    <tr>
    	<th>{ts}Client{/ts}</th>
    	<th>{ts}Case Type{/ts}</th>
       	<th>{ts}Status{/ts}</th>
        <th>{ts}Start Date{/ts}</th>
    	<th>{ts}Case ID{/ts}</th>
    </tr>
    <tr>
        <td>{$case.clientName}</td>
        <td>{$case.caseType}</td>
        <td>{$case.status}</td>
        <td>{$case.start_date}</td>
        <td>{$caseId}</td> 
    </tr>
</table>
<table class ="chart" >
    <tr>
    	<th>{ts}Case Role{/ts}</th>
    	<th>{ts}Name{/ts}</th>
       	<th>{ts}Phone{/ts}</th>
        <th>{ts}Email{/ts}</th>
    </tr>

    {foreach from=$caseRelationships item=row key=relId}
       <tr>
          <td>{$row.relation}</td>
          <td>{$row.name}</td>
          <td>{$row.phone}</td>
          <td>{$row.email}</td> 
       </tr>
    {/foreach}
    {foreach from=$caseRoles item=relName key=relTypeID}
         {if $relTypeID neq 'client'} 
           <tr>
               <td>{$relName}</td>
               <td>(not assigned)</td>
               <td></td>
               <td></td>
           </tr>
         {else}
           <tr>
               <td>{$relName.role}</td>
               <td>{$relName.sort_name}</td>
               <td>{$relName.phone}</td>
               <td>{$relName.email}</td>
           </tr> 
         {/if}
	{/foreach}
</table>

{if $otherRelationships}
    <table  class ="chart">
       	<trx>
    		<th>{ts}Client Relationship{/ts}</th>
    		<th>{ts}Name{/ts}</th>
    		<th>{ts}Phone{/ts}</th>
    		<th>{ts}Email{/ts}</th>
    	</tr>
        {foreach from=$otherRelationships item=row key=relId}
        <tr>
            <td>{$row.relation}</td>
            <td>{$row.name}</td>
            <td>{$row.phone}</td>
            <td>{$row.email}</td>
        </tr>
        {/foreach}
    </table>
{/if}

{if $globalRelationships}
    <table class ="chart">
       	<tr>
    	 	<th>{$globalGroupInfo.title}</th>
     	 	<th>{ts}Phone{/ts}</th>
    	 	<th>{ts}Email{/ts}</th>
    	</tr>
        {foreach from=$globalRelationships item=row key=relId}
        <tr>
            <td>{$row.sort_name}</td>
            <td>{$row.phone}</td>
            <td>{$row.email}</td>
        </tr>
	    {/foreach}
    </table>
{/if}

<dl><dt><label>{ts}Activities{/ts}</label></dt>
    <dd></dd>
</dl>
{foreach from=$activities item=activity key=key}
  <table  class ="chart">
       {foreach from=$activity item=field name=fieldloop}
         {if $field.label eq 'Activity Type' or $field.label eq 'Status'}
           <tr class="even-row">
         {else}
            <tr>
         {/if}
             <td class ="label">{$field.label|escape}</td>
             <td>{$field.value|escape}</td> 
         </tr>
       {/foreach}
  </table>
  <br />
{/foreach}
</div>






