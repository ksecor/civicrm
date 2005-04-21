{* {debug} *}
List of Saved Searches. <p>
<table>
  <tr><td>Name</td><td>Description</td><td>qill</td></tr>
  {foreach from=$rows item=row}
    <tr>
      <td>{$row.name}</td>
      <td>{$row.description}</td>
      <td>{$row.qill}</td>
    </tr>
  {/foreach}
</table>
