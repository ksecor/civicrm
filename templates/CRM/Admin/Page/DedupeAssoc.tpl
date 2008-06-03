    {if $rows}
          <div class="form-item">
            {strip}
              <table dojoType="SortableTable" widgetId="dupeTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">

            	<thead>
                <tr class="columnheader">
                  <th field="c1Name" dataType="html">{ts}Contact1 Name{/ts}</th>
                  <th field="c2Name" dataType="html">{ts}Contact2 Name{/ts}</th>
                  <th field="email"  dataType="String">{ts}Email{/ts}</th>
                  <th dataType="html">{ts}Merge{/ts}</th>
                </tr>
                </thead>

            	<tbody>
                {foreach from=$rows item=row}
                  <tr class="{cycle values="odd-row,even-row"} {$row.class}">
                    <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.c1`"}">{$row.c1_name}</a></td>	
                    <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.c2`"}">{$row.c2_name}</a></td>	
                    <td>{$row.c1_email}</td>	
                    <td><a href="{crmURL p='civicrm/contact/merge' q="reset=1&cid=`$row.c1`&oid=`$row.c2`"}">{ts}Merge{/ts}</a></td>
                  </tr>
                {/foreach}
            	</tbody>

              </table>
            {/strip}
          </div>
    {/if}

