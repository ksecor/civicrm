{* Approved message *}
{if $returnContent eq 'Approved'}
============================
Your Personal Campaign Page
============================

Your personal campaign page has been approved and is now live. 

{if $isTellFriendEnabled}
Promote your fundraising page:
{$pcpTellFriendURL}

{/if}
View and update your page:
{$pcpInfoURL}

{if $pcpNotifyEmailAddress}
Questions? Send email to:
{$pcpNotifyEmailAddress}
{/if}

{* Rejected message *}
{else if $returnContent eq 'Rejected'}
============================
Your Personal Campaign Page
============================

Your personal campaign page has been reviewed. There were some issues with the content
which prevented us from approving the page. We are sorry for any inconvenience.

{if $pcpNotifyEmailAddress}
Please contact our site administrator for more information: 
{$pcpNotifyEmailAddress}
{/if}

{/if}