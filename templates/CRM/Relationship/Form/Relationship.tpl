{* this template is used for adding relationships  *}

    <form {$form.attributes}>
    <p>
    <fieldset><legend>{if $op eq 'add'}New{else}Edit{/if} Relationship(s)</legend>
    <div class="form-item">
       <b>{$displayName} is {$form.relation.html} of </b>
        <br/>
        {$form.buttons.html}
    </div>
    </fieldset>
    </p>
    </form>

{debug}
