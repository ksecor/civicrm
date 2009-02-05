<table id="pricelabel" style="display:none" class="form-layout">
      <tr>  <td class="label"><label>Total Fee(s)</label> </td> <td id="pricevalue" class="view-value"></td> </tr>
</table>

{literal} 
<script type="text/javascript">
var totalfee=0;
var formName = '{/literal}{$form.formName}{literal}';
var scriptfee = eval("document."+formName+".scriptFee.value");
var scriptarray = eval("document."+formName+".scriptArray.value");
var symbol = '{/literal}{$currencySymbol}{literal}';
if ('{/literal}{$totalAmount}{literal}'!= '' ) {
  scriptfee   = parseFloat('{/literal}{$totalAmount}{literal}');
  scriptarray = ',{/literal}{$feeString}{literal}';
}
if(scriptfee){
  totalfee = parseFloat(scriptfee);
  document.getElementById('pricelabel').style.display = "block";
  document.getElementById('pricevalue').innerHTML = "<b>"+symbol+"</b> "+totalfee;
  scriptfee = parseFloat('0');
}
var price = new Array();
if(scriptarray){
  price = scriptarray.split(',');
}
function addPrice(priceVal, priceId) {
  var op  = document.getElementById(priceId).type;
  var ele = document.getElementById(priceId).name.substr(6);
  if (op == 'checkbox') {
    var chek = ele.split('[');
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
  var thousandMarker = ',';
  var seperator;
  if (priceset != 0) {
    //to handle monetary localization.
    seperator = priceset[1].charAt(priceset[1].length - 3);

    if (  seperator == ',' ) {
          thousandMarker = '.';  
    }

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

    seperator = selectvalue[1].charAt(selectvalue[1].length - 3);

    if (  seperator == ',' ) {
          thousandMarker = '.';  
    }
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
    var totalAmount  = (totalfee).formatMoney(2, seperator, thousandMarker);
    document.getElementById('pricevalue').innerHTML = "<b>"+symbol+"</b> "+totalAmount;
    scriptfee   = totalfee;
    scriptarray = price;
  } else{
    document.getElementById('pricelabel').style.display = "none";
  }
}

Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "," : d, 
    t = t == undefined ? "." : t, s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

</script>
{/literal}