{if $form.address.$blockId.name}
  <tr>
      <td colspan="2">
        {$form.address.$blockId.name.label}<br />
        {$form.address.$blockId.name.html}<br />
        <span class="description font-italic">{ts}Name of this address block like "My House, Work Place,.." which can be used in address book {/ts}</span>
      </td>
  </tr>
{/if}