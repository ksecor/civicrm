{if $showCMS }{*true if is_cms_user field is set *}
 <br><div>{$form.cms_create_account.html} {$form.cms_create_account.label}</div>
 <div class="messages help">{ts}If you would like to create an account on this site, fill in the registration details{/ts}</div>
 <div id="details">
  <table class="form-layout-compressed">
    <tr><td>{$form.cms_name.label}</td> <td>{$form.cms_name.html}</td></tr>
{if $form.cms_pass}
    <tr><td>{$form.cms_pass.label}</td> <td> {$form.cms_pass.html}</td></tr>        
    <tr><td>{$form.cms_confirm_pass.label}</td> <td> {$form.cms_confirm_pass.html}</td></tr>
{/if}
  </table>        
 </div>
{/if}

