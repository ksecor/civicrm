{* this template is used for adding/editing tags  *}

<div id="name" class="data-group form-item">
 	<label>{$displayName}</label>
</div>
<h3>Tags(categories) for this contact:</h3>

<form {$form.attributes}>
  <div class="form-item">
    <fieldset>
      {foreach from=$category item="row" key = "id"}
         {$form.categoryList[$id].html} &nbsp;{$row} <br>
      {/foreach}
    </fieldset>
  </div>
 <div class="horizontal-position">
    <span class="two-col1">
       <span class="fields">{$form.buttons.html}</span>
    </span>
 </div>
	
</form>
<br><br>
