<table id="pricelabel" style="display:none" class="form-layout">
      <tr>  <td class="label"><label>Total Fee(s)</label> </td> <td id="pricevalue" class="view-value"></td> </tr>
</table>

{literal} 
<script type="text/javascript">
var totalfee=0;
var thousandMarker = '{/literal}{$config->monetaryThousandSeparator}{literal}';
var seperator      = '{/literal}{$config->monetaryDecimalPoint}{literal}';
var formName    = '{/literal}{$form.formName}{literal}';
var scriptfee   = eval("document."+formName+".scriptFee.value");
var scriptarray = eval("document."+formName+".scriptArray.value");
var symbol      = '{/literal}{$currencySymbol}{literal}';
var defaultFee      = parseFloat('{/literal}{$defaultFee}{literal}');
var defaultSelected = '{/literal}{$defaultSelected}{literal}';
if ('{/literal}{$totalAmount}{literal}'!= '' ) {
  scriptfee   = parseFloat('{/literal}{$totalAmount}{literal}');
  scriptarray = ',{/literal}{$feeString}{literal}';
}
if(scriptfee){
  totalfee = parseFloat(scriptfee);
  var totalEventFee = formatMoney( totalfee, 2, seperator, thousandMarker );
  document.getElementById('pricelabel').style.display = "block";
  document.getElementById('pricevalue').innerHTML = "<b>"+symbol+"</b> "+totalEventFee;
  scriptfee = parseFloat('0');
}
if ( defaultFee && ! totalfee ) {
  totalfee = defaultFee;
  scriptarray = defaultSelected;
  document.getElementById('pricelabel').style.display = "block";
  var totalEventFee  = formatMoney( totalfee, 2, seperator, thousandMarker);
  document.getElementById('pricevalue').innerHTML = "<b>"+symbol+"</b> "+totalEventFee;
}
var price = new Array();
if(scriptarray){
  price = scriptarray.split(',');
}
function addPrice(priceVal, priceId) {
  var op  = document.getElementById(priceId).type;
  var ele = document.getElementById(priceId).name.substr(6);
  if (op == 'checkbox') {
    var chek = ele.split('\[');
    ele = chek[0];
  }
  if(!price[ele]) {
    price[ele] = parseFloat('0');
  }
  var addprice = 0;
  var priceset = 0;
  if(op != 'select-one') {
    priceset = priceVal.split(symbol);
  }
  var Actualprice= "";
  var priceArray ;
  
  if (priceset != 0) {
    //to handle monetary localization.
    priceArray    = priceset[1].split(thousandMarker);
 
    for( i=0 ;i<priceArray.length ; i++ ){
      Actualprice =Actualprice+priceArray[i]; 
    }
    var addprice = parseFloat(Actualprice);
  }
  switch(op)
    {
    case 'checkbox':
      if(document.getElementById(priceId).checked) {
	totalfee   += addprice;
	price[ele] += addprice;
      }else{
	totalfee   -= addprice;
	price[ele] -= addprice;
      }
      break;    
      
    case 'radio':
      totalfee = parseFloat(totalfee) + addprice - parseFloat(price[ele]);
      price[ele] = addprice;
      break;
      
    case 'text':
      var textval = parseFloat(document.getElementById(priceId).value);
      var curval = textval * addprice;
      if(textval>=0){
	totalfee = parseFloat(totalfee) + curval - parseFloat(price[ele]);
	price[ele] = curval;
      }else {
	totalfee = parseFloat(totalfee) - parseFloat(price[ele]);	
	price[ele] = parseFloat('0');
      }

      break;
      
    case 'select-one':
      var index = parseInt(document.getElementById(priceId).selectedIndex);
      var myarray = ['','{/literal}{$selectarray}{literal}'];
      if(index>0) {
	var selectvalue = myarray[index].split(symbol);
   
	priceArray = selectvalue[1].split(thousandMarker);
	for( i=0 ;i<priceArray.length ; i++ ){
	  Actualprice =Actualprice+priceArray[i]; 
	}
	totalfee = parseFloat(totalfee) + parseFloat(Actualprice) - parseFloat(price[ele]);
	price[ele] = parseFloat(Actualprice);
      }else {
	totalfee = parseFloat(totalfee) - parseFloat(price[ele]);
	price[ele] = parseFloat('0');
      }	
      break;
      
    }//End of swtich loop
  
  if( totalfee>0 ){
    document.getElementById('pricelabel').style.display = "block";
    var totalEventFee  = formatMoney( totalfee, 2, seperator, thousandMarker);
    document.getElementById('pricevalue').innerHTML = "<b>"+symbol+"</b> "+totalEventFee;
    scriptfee   = totalfee;
    scriptarray = price;
  } else{
    document.getElementById('pricelabel').style.display = "none";
  }
}

function formatMoney (amount, c, d, t){
	var n = amount, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "," : d, 
    t = t == undefined ? "." : t, s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

</script>
{/literal}