<h3 class="head"> 
    <span class="ui-icon ui-icon-triangle-1-e"></span><a href="#">{ts}{$title}{/ts}</a>
</h3>
<div id="notesBlock" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
   <table class="form-layout-compressed">
     <tr class="last-row">
       <td>{$form.contact_source.label}<br />
           {$form.contact_source.html}
       </td>
       <td>{$form.external_identifier.label}<br />
           {$form.external_identifier.html}
       </td>
       {* FIXME *}
       <td><label for="internal_identifier">Internal Id</label><br />
        203</td>
     </tr>

     <tr>
       <td colspan=3>{$form.subject.label}<br  >
        {$form.subject.html}</td>
     </tr>
     <tr>
       <td colspan=3>{$form.note.label}<br />
        {$form.note.html}
       </td>
     </tr>
   </table>
</div>
