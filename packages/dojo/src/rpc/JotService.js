/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.rpc.JotService");
dojo.require("dojo.rpc.RpcService");
dojo.require("dojo.rpc.JsonService");
dojo.require("dojo.json");
dojo.rpc.JotService=function(){
this.serviceUrl="/_/jsonrpc";
};
dojo.inherits(dojo.rpc.JotService,dojo.rpc.JsonService);
dojo.lang.extend(dojo.rpc.JotService,{bind:function(_1,_2,_3,_4){
dojo.io.bind({url:_4||this.serviceUrl,content:{json:this.createRequest(_1,_2)},method:"POST",mimetype:"text/json",load:this.resultCallback(_3),error:this.errorCallback(_3),preventCache:true});
},createRequest:function(_5,_6){
var _7={"params":_6,"method":_5,"id":this.lastSubmissionId++};
return dojo.json.serialize(_7);
}});
