{* {debug} *}
List of Saved Searches. <p>
<table>
  <tr><td>Name</td><td>Description</td></tr>
  {foreach from=$rows item=row}
    <tr>
      <td>{$row.name}</td>
      <td>{$row.description}</td>
    </tr>
  {/foreach}
</table>
