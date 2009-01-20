<div class="form-item">
 <fieldset><legend>{ts}Find Items{/ts}</legend>
  <table class="form-layout">
    <tr>
        <td class="label">{$form.title.label}</td>
        <td>{$form.title.html|crmReplace:class:twenty}
             <div class="description font-italic">
                    {ts}Complete OR partial Item name.{/ts}
             </div>
        </td>
        <td class="right" rowspan="2">&nbsp;{$form.buttons.html}</td>  
    </tr>
  </table>
</fieldset>
</div>
