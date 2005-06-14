<div class="form-item">
    <dl>
{foreach from=$fields item=field key=name}
<dt>{$form.$name.label}</dt><dd>{$form.$name.html}</dd>
{/foreach}
    </dl>
</div>
