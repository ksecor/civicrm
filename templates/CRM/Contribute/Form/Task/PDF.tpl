{* Confirmation of contribution deletes  *}
<div class="messages status">
  <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        <p>{include file="CRM/Contribute/Form/Task.tpl"}</p>
    </dd>
  </dl>
</div>
<div id="help">
    {ts}A PDF file containing one receipt per page will be downloaded to your local computer when you click <strong>Download Receipt(s)</strong>.
    Your browser may display the file for you automatically, or you may need to open it for printing using any PDF reader (such as Adobe&reg; Reader).{/ts}
</div>
<div class="spacer"></div>
<div class="form-item">
 {$form.buttons.html}
</div>
