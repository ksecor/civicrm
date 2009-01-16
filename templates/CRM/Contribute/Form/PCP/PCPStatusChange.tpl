{* Approved message *}
{if $pcpStatus eq 'Approved'}
============================
{ts}Your Personal Campaign Page{/ts}

============================

{ts}Your personal campaign page has been approved and is now live.{/ts} 

{if $isTellFriendEnabled}
{ts}Promote your fundraising page{/ts}:
{$pcpTellFriendURL}

{/if}
{ts}View and update your page{/ts}:
{$pcpInfoURL}

{if $pcpNotifyEmailAddress}
{ts}Questions? Send email to{/ts}:
{$pcpNotifyEmailAddress}
{/if}

{* Rejected message *}
{else if $pcpStatus eq 'Rejected'}
============================
{ts}Your Personal Campaign Page{/ts}

============================

{ts}Your personal campaign page has been reviewed. There were some issues with the content
which prevented us from approving the page. We are sorry for any inconvenience.{/ts}

{if $pcpNotifyEmailAddress}
{ts}Please contact our site administrator for more information{/ts}: 
{$pcpNotifyEmailAddress}
{/if}

{/if}