{* test template for testing ajax *}
<script type="text/javascript" src="{crmURL p='civicrm/contact/StateCountryServer' q='set=1'}"></script>
<script type="text/javascript" src="{$config->resourceBase}js/Test.js"></script>

<form id="autoCompleteForm" name="autoCompleteForm">
Enter a State: <input type="text" id="state" name="state" value="" onkeyup="getWord(this,event);" autocomplete="off" onblur="getWord(this,event);">
<!-- Note the autocomplete="off": without it you get errors like;
"Permission denied to get property XULElement.selectedIndex..."
-->
state id: <input type="text" id="state_id" name="state_id" value="" READONLY>
<br>
Country :<input type="text" id="country" name ="country" READONLY>
country id: <input type="text" name="country_id" id="country_id" value="" READONLY>
