{* Content of Facebook-Tab *}
<br/>
<div>
<table border=1>
<tr>
    <td rowspan="3" align="center"><img src="{$user.pic}"></img></td>
    <td width="50%" valign="middle">Name: {$user.first_name}&nbsp;{$user.last_name}</td><td rowspan="100%">friends photos</td>
</tr>
<tr>
    <td>Status: {$user.status.message}</td>
</tr>
<tr>
    <td>Location: {$user.current_location.city}, {$user.current_location.country}</td>
</tr>
<tr>
    <td>Send Gift</td><td>user-info-4</td>
</tr>
</table>
</div>