{* Content of Facebook-Tab *}
{if $user}
<br/>
<div>
<table border=1>
<tr>
    <td rowspan="3" align="center">
        {if $user.pic}<img src="{$user.pic}" />
        {else}<img src="http://static.ak.facebook.com//pics/s_default.jpg" />
        {/if} 
        <br/>{$user.first_name}&nbsp;{$user.last_name}
    </td>
    <td width="50%" valign="middle">Name: {$user.first_name}&nbsp;{$user.last_name}</td><td rowspan="100%" align="center">
    <ul>
      <li><a href="javascript:popUp('http://www.facebook.com/giftshop.php?to={$user.uid}')">Send Gift</a>
      <li><a href="javascript:popUp('http://www.facebook.com/poke.php?id={$user.uid}')">Poke</a>
    </ul>
    </td>
</tr>
<tr>
    <td>Status: {$user.status.message}</td>
</tr>
<tr>
    <td>Location: {$user.current_location.city}, {$user.current_location.country}</td>
</tr>
</table>
</div>

<table>
<tr>
       {foreach from=$userFriends key=id item=friend}
            <td>{if $friend.pic}<img src="{$friend.pic}" />
                {else}<img src="http://static.ak.facebook.com//pics/s_default.jpg" />
                {/if}<br/>{$friend.first_name}&nbsp;{$friend.last_name}
            </td>
        {/foreach}    

</tr>
</table>

{/if}