{* {debug} *}
List of Saved Searches. <p>
<table>
  <tr><td>Name</td><td>Description</td><td>Search Typexs</td></tr>
  {foreach from=$rows item=row}
    <tr>
      <td>{$row.name}</td>
      <td>{$row.description}</td>
      <td>{$row.search_type}</td>
    </tr>
  {/foreach}
</table>
