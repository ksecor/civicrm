{* this template is used for adding/editing a group  *}
<div class="message status">
  <dl>
  <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
  <dd>
    Are you sure you want to delete the group {$name}? This group currently has {$count} members
    in it.
  </dd>
  </dl>
</div>

<form {$form.attributes}>
<div class="form-item">
 <dl>
   <dt></dt><dd>{$form.buttons.html}</dd>
 </dl>
</div>
</form>
