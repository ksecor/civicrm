<html xmlns="http://www.w3.org/1999/xhtml" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">
<head>
  <title>{$pageTitle}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <base href="{crmURL p="" a=true}" /><!--[if IE]></base><![endif]-->
  <style type="text/css" media="screen, print">@import url({$config->userFrameworkResourceURL}css/print.css);</style>
</head>

<body>
<div id="crm-container">
<h1>{$pageTitle}</h1>
<div id="report-date">{$reportDate}</div>
<h2>{ts}Case Summary{/ts}</h2>
<table class="report-layout">
    <tr>
    	<th class="reports-header">{ts}Client{/ts}</th>
    	<th class="reports-header">{ts}Case Type{/ts}</th>
       	<th class="reports-header">{ts}Status{/ts}</th>
        <th class="reports-header">{ts}Start Date{/ts}</th>
    	<th class="reports-header">{ts}Case ID{/ts}</th>
    </tr>
    <tr>
        <td>{$case.clientName}</td>
        <td>{$case.caseType}</td>
        <td>{$case.status}</td>
        <td>{$case.start_date}</td>
        <td>{$caseId}</td> 
    </tr>
</table>
<h2>{ts}Case Roles{/ts}</h2>
<table class ="report-layout">
    <tr>
    	<th class="reports-header">{ts}Case Role{/ts}</th>
    	<th class="reports-header">{ts}Name{/ts}</th>
       	<th class="reports-header">{ts}Phone{/ts}</th>
        <th class="reports-header">{ts}Email{/ts}</th>
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
               <td>{ts}(not assigned){/ts}</td>
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
<br />

{if $otherRelationships}
    <table  class ="report-layout">
       	<tr>
    		<th class="reports-header">{ts}Client Relationship{/ts}</th>
    		<th class="reports-header">{ts}Name{/ts}</th>
    		<th class="reports-header">{ts}Phone{/ts}</th>
    		<th class="reports-header">{ts}Email{/ts}</th>
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
    <br />
{/if}

{if $globalRelationships}
    <table class ="report-layout">
       	<tr>
    	 	<th class="reports-header">{$globalGroupInfo.title}</th>
     	 	<th class="reports-header">{ts}Phone{/ts}</th>
    	 	<th class="reports-header">{ts}Email{/ts}</th>
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

<h2>{ts}Case Activities{/ts}</h2>
{foreach from=$activities item=activity key=key}
  <table  class ="report-layout">
       {foreach from=$activity item=field name=fieldloop}
           <tr>
             <th scope="row" class="label">{$field.label|escape}</th>
             {if $field.label eq 'Activity Type' or $field.label eq 'Status'}
                <td class="bold">{$field.value|escape}</th> 
             {else}
                <td>{$field.value|escape}</th> 
             {/if}
           </tr>
       {/foreach}
  </table>
  <br />
{/foreach}
</div>
</body>
</html>





