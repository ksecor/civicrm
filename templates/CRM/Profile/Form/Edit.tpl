{if ! empty( $fields )}

            You can add someone that is missing or add someone that wants to let
            others know they are OK.</span>

{assign var=count value=0}

    <table class="form-layout-compressed">
    {foreach from=$fields item=field key=name}
        {if $count EQ 1}
            <tr><td colspan=2>
            <p>
            <span class="title">Name of the person</span><br/>
            Please enter the person's name and information. This can be either a
            person that is missing, or a person that has been found.</td></tr>
        {/if}
        {if $count EQ 8}
            <tr><td colspan=2>
            <p>
            <span class="title">Status of the person</span><br/>
            Explain the status of the person and where they are currently located
            (if known) (e.g. Bill was last seen on the roof of his house and we
            don't know what has happened to him, Sondra is fine in the Astrodome.)</td></tr>
        {/if}
        {if $count EQ 10}
            <tr><td colspan=2>
            <p>
            <span class="title">Contact information</span><br/>
            Enter some information on how you can be reached. (e.g. If anyone
            finds Bill or knows his location, I can be reached on my cell  at XYZ
            or by email at ABC; If anyone is looking for me I can be reached
            through the Red Cross at XYX)</td></tr>
{/if}
        {if $count EQ 14}
            <tr><td colspan=2>
            <p>
            <span class="title">Volunteer Data Entry Information</span><br />
            If you are a volunteer entering data from other sources (media
            reports, other websites, message boards etc.), please explain the
            source of this entry (include a URL) and the date of the source (if
            it was posted on a message board on Wed Aug 31, put Wed, Aug 31).</td></tr>
        {/if}

        {assign var=n value=$field.name}
        <tr><td class="label">{$form.edit.$n.label}</td><td>{$form.edit.$n.html}</td></tr>
        {* Show explanatory text for field if not in 'view' mode *}
        {if $field.help_post && $action neq 4}
            <tr><td> </td><td
class="description">{ts}{$field.help_post}{/ts}</td></tr>
        {/if}
        {assign var=count value=$count+1}
    {/foreach}
    <tr><td></td><td>{$form.buttons.html}</td></tr>
    </table>
{/if} {* fields array is not empty *}
