{literal}
//Highlight the required field during import
paramsArray = new Array();

//build the an array of highlighted elements
{/literal}
{foreach from=$highlightedFields item=paramName}	    
    paramsArray["{$paramName}"] = "1";	    
{/foreach}
{literal}	             

//get select object of first element
selObj = document.getElementById("mapper\[0\]\[0\]");   

for ( i = 0; i < selObj.options.length; i++ ) {
    //check value is exist in array
    if (selObj.options[i].value in paramsArray) {
        //change background Color of all element whose ids start with mapper and end with [0];
        cj('select[id^="mapper"][id$="[0]"]').each( function( ) {
            cj(this.options[i]).append(' *').css({"color":"#FF0000"});
            });
    }
}
{/literal}