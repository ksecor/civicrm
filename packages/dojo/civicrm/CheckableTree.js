if(!dojo._hasResource["civicrm.CheckableTree"]){ //_hasResource checks added by build. Do not use _hasResource directly in your code.
dojo._hasResource["civicrm.CheckableTree"] = true;

dojo.provide("civicrm.CheckableTree");

dojo.require("dijit.Tree");

dojo.declare(
	     'civicrm.CheckableTree',
	     dijit.Tree, {
    
		 checkedFiles: [],
    
		 initCheckedFiles: function(){
		     var store = this.model.store;
		     var items = store._arrayOfAllItems;
		     for (i = 0;i < items.length; i++){
			 if (store.getValue(items[i], 'type') == 'file' && store.getValue(items[i], 'checked')){
			     this.checkedFiles[this.checkedFiles.length] = items[i];
			 }
		     }
		 },
    
		 onClick: function(item, treeNode){
		     this.recursiveCheckItem(item, !this.model.store.getValue(item, 'checked'));
	       
		     //check or uncheck ancestors if neccessary
		     if (this.model.store.getValue(item, 'checked')){
			 var node = treeNode.getParent();
			 var break_flag = false;
			 while (node != null && node.item != this.model.root && !this.model.store.getValue(node.item, 'checked') && !break_flag){
			     children = node.item.children;
			     for (var c=0;c<children.length;c++){
				 if (!this.model.store.getValue(children[c], 'checked')){
				     //do nothing
				     break_flag = true;
				     break;
				 }
			     }
			     if (!break_flag){
				 node.item.checked = [true];
			     }
			     node = node.getParent();
			 }
				
		     } else {
			 //uncheck all parents
			 var node = treeNode.getParent();
			 while (node != null && node.item != this.model.root && this.model.store.getValue(node.item, 'checked')){
			     node.item.checked = [false];
			     node = node.getParent();
			 }
		     }
		     this._refreshTreeDisplay(treeNode);
		 },

		 recursiveCheckItem: function(item, checked){
		     if (this.model.store.getValue(item, 'type') == 'file'){
			 if (checked) 
			     this.checkedFiles[this.checkedFiles.length] = item;
			 else
			     this.checkedFiles.pop(item);
		     }
		     item.checked = [checked];
		     if (item.type == 'category'){
			 for (var i=0;i<item.children.length;i++){
			     this.recursiveCheckItem(item.children[i], checked);
			 }
		     }
		 },

		 _refreshTreeDisplay: function(treeNode){
		     //refreshes only the descendants and ancestors of a node, not the entire tree 
		     // (to save time when only part has been modified)
						 
		     this._refreshSubTree(treeNode);
						 
		     var node = treeNode.getParent();
		     while (node != null && node.item != this.model.root){
			 this._refreshNodeIcon(node);
			 node = node.getParent();
		     }
		 },

		 _refreshSubTree: function(treeNode){
		     this._refreshNodeIcon( treeNode );
		     var chld = treeNode.getChildren();
		     for( var i = 0; i < chld.length; i++ ){
			 this._refreshSubTree( chld[ i ] );
		     }
		 },

		 _refreshNodeIcon: function(treeNode){
		     if (treeNode.item == this.model.root) return;
		     if (!this.model.store.getValue(treeNode.item, 'checked')){
			 dojo.removeClass( treeNode.iconNode, "dijitCheckBoxChecked" );
		     } else {
			 dojo.addClass( treeNode.iconNode, "dijitCheckBoxChecked" );
		     }
		 },

		 getIconClass: function(item){
		     if (item == this.model.root) return null;
		     if( this.model.store.getValue(item, 'checked')) return "dijitCheckBox dijitCheckBoxChecked";
		     else return "dijitCheckBox";
		 },

		 getLabelClass: function(item){
		     if (item == this.model.root) return null;
		     var time = this.model.store.getValue(item, 'updated_at');
		     var now = time - 100; // K.upload.now_timestamp;
		     if (now - time <= 3600) return 'ageHour';
		     if (now - time <= 43200) return 'ageDay'; // (last 12 hours)
		     if (now - time <= 604800) return 'ageWeek';
		     return 'ageOld';
		 },

		 getLabel: function(item){
		     if (item == this.model.root) return null;
		     if (this.model.store.getValue(item, 'type') == 'category')
			 return this.model.store.getValue(item, 'name');
		     return this.model.store.getValue(item, 'name');
		 }
	     });

}