{* Content of Facebook-Tab *}
{if $user}
<br/>
<div class="form-item">
<table>
<tr>
    <td valign="top">
        {if $user.pic}<img src="{$user.pic}"/>{else}<img src="http://static.ak.facebook.com//pics/s_default.jpg" /> {/if}
    </td>
    <td valign="top" >
        <span class="label"> Name: </span><span>{$user.first_name}&nbsp;{$user.last_name}</span><br>
        <span class="label"> Status: </span><span>{$user.status.message}</span><br>
        <span class="label"> Location: </span><span>{$user.current_location.city}, {$user.current_location.country}</span><br>
    </td>
    <td valign="top">
    <ul>
      <li><a href="javascript:popUp('http://www.facebook.com/giftshop.php?to={$user.uid}')">Send Gift</a>
      <li><a href="javascript:popUp('http://www.facebook.com/poke.php?id={$user.uid}')">Poke</a>
    </ul>
    </td>
</tr>
</table>
</div>
<br/><br/>
<div class="form-item">
  <table class="form-layout-compressed">
    <tr>
    {foreach from=$userFriends key=id item=friend}
         <td valign="top">{if $friend.pic}<img src="{$friend.pic}" /> {else}<img src="http://static.ak.facebook.com//pics/s_default.jpg" /> {/if}<br/>
         {$friend.first_name}&nbsp;{$friend.last_name}<br/>{$friend.status.message}</td>
    {/foreach}    
    </tr>
  </table> 
</div>
{/if}