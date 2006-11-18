{if $action eq 1 or $action eq 2}
   {include file="CRM/Mailing/Form/Component.tpl"}
{/if}

<div id="ltype">
 <p></p>
    <div class="form-item">
       {strip}
     <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
      <thead>
       <tr class="columnheader">
        <th field="Name" dataType="String">{ts}Name{/ts}</th>
        <th field="Type" dataType="String">{ts}Type{/ts}</th>
        <th field="Subject" dataType="String">{ts}Subject{/ts}</th>
        <th field="Body Text" dataType="String">{ts}Body Text{/ts}</th>
        <th field="Body HTML" dataType="String">{ts}Body HTML{/ts}</th>
        <th field="Default" dataType="String">{ts}Default?{/ts}</th>
        <th field="Enabled" dataType="String">{ts}Enabled?{/ts}</th>
        <th dataType="html"></th>
       </tr>
      </thead>
      <tbody>  
       {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
           <td>{$row.name}</td>	
           <td>{$row.component_type}</td>
           <td>{$row.subject}</td>
           <td>{$row.body_text}</td>
           <td>{$row.body_html|escape}</td>
           <td>{if $row.is_default eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	   <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
           <td>{$row.action}</td>
        </tr>
       {/foreach}
       </tbody>  
       </table>
       {/strip}

       {if $action ne 1 and $action ne 2}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL q="action=add&reset=1"}">&raquo; {ts}New Mailing Component{/ts}</a>
       </div>
       {/if}
    </div>
</div>
