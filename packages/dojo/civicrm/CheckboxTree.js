dojo.provide("civicrm.CheckboxTreeNode");

dojo.require("dijit.Tree");
dojo.require("dijit.form.CheckBox");

dojo.declare(
"civicrm.CheckboxTreeNode",
[dijit._TreeNode],
{    
    templateString:"<div class=\"dijitTreeNode\" waiRole=\"presentation\"\n\t><div dojoAttachPoint=\"rowNode\" waiRole=\"presentation\"\n\t\t><span dojoAttachPoint=\"expandoNode\" class=\"dijitTreeExpando\" waiRole=\"presentation\"\n\t\t></span\n\t\t><span dojoAttachPoint=\"expandoNodeText\" class=\"dijitExpandoText\" waiRole=\"presentation\"\n\t\t></span\n\t\t><div dojoAttachPoint=\"contentNode\" class=\"dijitTreeContent\" waiRole=\"presentation\">\n</span><div dojoAttachPoint=\"iconNode\" class=\"dijitInline dijitTreeIcon\" waiRole=\"presentation\"></div>\n<span dojoType=\"dijit.form.CheckBox\"></span>\n\t\t<span dojoAttachPoint=\"labelNode\" class=\"dijitTreeLabel\" wairole=\"treeitem\" tabindex=\"-1\" waiState=\"selected-false\" dojoAttachEvent=\"onfocus:_onNodeFocus\"></span></div\n\t></div>\n</div>\n",
    widgetsInTemplate: true,
        
    setChildItems: function(/* Object[] */ items){
        // summary:
        //		Sets the child items of this node, removing/adding nodes
        //		from current children to match specified items[] array.

        var tree = this.tree,
        model = tree.model;

        // Orphan all my existing children.
        // If items contains some of the same items as before then we will reattach them.
        // Don't call this.removeChild() because that will collapse the tree etc.
        this.getChildren().forEach(function(child){
            dijit._Container.prototype.removeChild.call(this, child);
        }, this);

        this.state = "LOADED";

        if(items && items.length > 0){
            this.isExpandable = true;
            if(!this.containerNode){ // maybe this node was unfolderized and still has container
                this.containerNode = this.tree.containerNodeTemplate.cloneNode(true);
                this.domNode.appendChild(this.containerNode);
            }

            // Create _TreeNode widget for each specified tree node, unless one already
            // exists and isn't being used (presumably it's from a DnD move and was recently
            // released
            dojo.forEach(items, function(item){
                          
                var id = model.getIdentity(item),
                existingNode = tree._itemNodeMap[id],
                node = 
                    ( existingNode && !existingNode.getParent() ) ?
                    existingNode :
                    new civicrm.CheckboxTreeNode({
                    item: item,
                    tree: tree,
                    isExpandable: model.mayHaveChildren(item),
                    label: tree.getLabel(item)
                });
                          
                this.addChild(node);
                // note: this won't work if there are two nodes for one item (multi-parented items); will be fixed later
                tree._itemNodeMap[id] = node;
                if(this.tree.persist){
                    if(tree._openedItemIds[id]){
                        tree._expandNode(node);
                    }
                }
            }, this);

            // note that updateLayout() needs to be called on each child after
            // _all_ the children exist
            dojo.forEach(this.getChildren(), function(child, idx){
                child._updateLayout();
            });
        }else{
            this.isExpandable=false;
        }

        if(this._setExpando){
            // change expando to/from dot or + icon, as appropriate
            this._setExpando(false);
        }

        // On initial tree show, put focus on either the root node of the tree,
        // or the first child, if the root node is hidden
        if(!this.parent){
            var fc = this.tree.showRoot ? this : this.getChildren()[0],
            tabnode = fc ? fc.labelNode : this.domNode;
            tabnode.setAttribute("tabIndex", "0");
        }

        // create animations for showing/hiding the children (if children exist)
        if(this.containerNode && !this._wipeIn){
            this._wipeIn = dojo.fx.wipeIn({node: this.containerNode, duration: 150});
            this._wipeOut = dojo.fx.wipeOut({node: this.containerNode, duration: 150});
        }
    },


    // return the dijit.Checkbox inside the tree node
    getNodeCheckbox: function(){
        return this._supportingWidgets[0] ;
    },
      
    setNodeCheckboxValue: function(value){

        this.getNodeCheckbox().setAttribute('checked',value);
    },

    postCreate: function(){
        // set label, escaping special characters
        this.setLabelNode(this.label);

        // set expand icon for leaf
        this._setExpando();

        // set icon and label class based on item
        this._updateItemClasses(this.item);

        if(this.isExpandable){
            dijit.setWaiState(this.labelNode, "expanded", this.isExpanded);
        }

        // preload
        // get value from the store (JSON) of the property "checked" and set the checkbox
        //this.setNodeCheckboxValue(this.tree.model.store.getValue(this.item,"checked")) ;
	
        // connect onChange of the checkbox to alter the model of the tree
        dojo.connect(this.getNodeCheckbox(),'onChange',this,
                     function(){this.tree.model.store.setValue(this.item,"checked",(this.getNodeCheckbox().getValue() == false) ? false:true);}) ;
	
    },
          
    getCheckedNodesList: function(nodeArray){

        if ( this.getNodeCheckbox().isChecked() )
            nodeArray.push(this.item.label) ;
            
        this.getChildren().forEach(getCheckedNodesList(nodeArray), this);            
    }            
       
});

dojo.provide("civicrm.CheckboxTree");

dojo.declare(
"civicrm.CheckboxTree",
[dijit.Tree],
{
    
    _load: function(){

        // summary: initial load of the tree
        // load root node (possibly hidden) and it's children
        this.model.getRoot(
        dojo.hitch(this, function(item){
            var rn = this.rootNode = new civicrm.CheckboxTreeNode({
                item: item,
                tree: this,
                isExpandable: true,
                label: this.label || this.getLabel(item)
            });
            if(!this.showRoot){
                rn.rowNode.style.display="none";
            }
            this.domNode.appendChild(rn.domNode);
            this._itemNodeMap[this.model.getIdentity(item)] = rn;

            rn._updateLayout();		// sets "dijitTreeIsRoot" CSS classname

            // load top level children
            this._expandNode(rn);
        }),
        function(err){
            console.error(this, ": error loading root: ", err);
        }
    );
    },
	
    _onItemChange: function(/*Item*/ item){

        //summary: set data event on an item in the store
        var model = this.model,
        identity = model.getIdentity(item),
        node = this._itemNodeMap[identity];

        if(node){
            
            var newValue = this.model.store.getValue(item,"checked") ;
            
            node.setLabelNode(this.getLabel(item));
            // ridondante se checko con il mouse ma necessario
            // nel caso in cui il check sia propagato ai figli tramite modifica del model
            // (anche nel caso di modifiche esterne al model...)
            node.setNodeCheckboxValue(newValue);
            node._updateItemClasses(item);
	}

    }

});


dojo.provide("civicrm.tree.CheckboxTreeStoreModel");

dojo.declare(
"civicrm.tree.CheckboxTreeStoreModel",
[dijit.tree.ForestStoreModel],
{
    	onChange: function(/*dojo.data.Item*/ item){
            
            var currStore = this.store ;
            var newValue = currStore.getValue(item,"checked") ;

            // if a node gets checked we propagate the "event" down to the children
            // erase this if you don't need to propagate the event (simple check)
            this.getChildren(item,function(children){      
                dojo.forEach(children,function(child){
                    currStore.setValue(child,"checked",newValue) ;
                });
            }) ;            
	}
});