{if $form.location.$index.address.geo_code_1}
{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
<div class="form-item">
    <span class="labels">
    {$form.location.$index.address.geo_code_1.label},
    {$form.location.$index.address.geo_code_2.label}
    </span>
    <span class="fields">
        {$form.location.$index.address.geo_code_1.html},
        {$form.location.$index.address.geo_code_2.html}
        <br class="spacer"/>
        <span class="description font-italic">
            {ts 1="http://wiki.civicrm.org/confluence//x/Ois" 2=$docURLTitle}Latitude and longitude may be automatically populated by enabling a Mapping Provider (<a href='%1' target='_blank' title='%2'>read more...</a>).{/ts}
        </span>
    </span>
</div>
{/if}
