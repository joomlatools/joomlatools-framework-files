/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

if(!Files) var Files = {};

(function($){

    /**
     * Files.Tree is a wrapper for Koowa.Tree, which wraps jqTree
     * @type extend Koowa.Tree
     */
    Files.Tree = Koowa.Tree.extend({
        getDefaults: function(){

            var self = this,
                defaults = {
                    autoOpen: 0, //root.open = true on previous script
                    onSelectNode: function(){},
                    dataFilter: function(response){

                        var data = response.entities,
                            parse = function(item, parent) {
                                var path = (parent && parent.path) ? parent.path+'/' : '';
                                path += item.name;

                                //Parse attributes
                                //@TODO check if 'type' is necessary
                                item = $.extend(item, {
                                    id: path,
                                    path: path,
                                    url: '#'+path,
                                    type: 'folder'
                                });

                                if (item.children) {
                                    var children = [];
                                    Files.utils.each(item.children, function(child) {
                                        children.push(parse(child, item));
                                    });
                                    item.children = children;
                                }

                                return item;
                        }

                        if (response.meta.total) {
                            Files.utils.each(data, function(item, key) {
                                parse(item);
                            });
                        }

                        return self.parseData(data);
                    }
                };

            return $.extend(true, {}, this.supr(), defaults); // get the defaults from the parent and merge them
        },

        /**
         * Customized parseData method due to using json, in a already nested data format
         * @param json returned data
         * @returns data
         */
        parseData: function(list){
            return [{
                label: this.options.root.text,
                //href: '#',
                url: '#',
                children: list
            }];
        },
        fromUrl: function(url) {

            var self = this;
            this.tree('loadDataFromUrl', url, null, function(response){
                if (Files.app && Files.app.active) {
                    self.selectPath(Files.app.active);
                }
            });

        },
        selectPath: function(path) {
            var node = this.tree('getNodeById', path);

            if(window.console) console.log('selectNode', path, node);

            if(!node) {
                var tree = this.tree('getTree');
                node = tree.children.length ? tree.children[0] : null;
            }

            this.tree('selectNode', node);
        },
        /**
         * Append a node to the tree
         * Required properties are 'id' and 'label', other properties are optional.
         * If no parent specified then the node is appended to the current selected node.
         * Pass parent as null for adding the node to root
         *
         * This API is intended for adding user created nodes, don't use this API to add multiple items or to refresh
         * the tree with updated data from the server.
         * Use fromUrl instead, as it's performance optimized for that purpose.
         *
         * @param row
         * @param parent    optional    Node instance, pass 'null' to force the node to be added to the root
         */
        appendNode: function(row, parent){

            if(parent === undefined) parent = this.tree('getSelectedNode');
            if(parent === false)     parent = this.tree('getTree').children[0]; //Get the root node when nothing is selected

            var node, data = $.extend(true, {}, row, {
                path: row.id,
                url: '#'+row.id,
                type: 'folder'
            });

            /**
             * If there's siblings, make sure it's added in alphabetical order
             */
            if(parent && parent.children && parent.children.length) {
                var name = data.label.toLowerCase();
                if(parent.children[0].name.toLowerCase() > name) {
                    node = this.tree('addNodeBefore', data, parent.children[0]);
                } else if(parent.children[parent.children.length - 1].name.toLowerCase() < name) {
                    node = this.tree('appendNode', data, parent);
                } else {
                    var i = 0;
                    while(parent.children[i].name.toLowerCase() < name) {
                        i++;
                    }
                    node = this.tree('addNodeBefore', data, parent.children[i]);
                }
            } else {
                node = this.tree('appendNode', data, parent);
            }
            /**
             * @TODO please investigate:
             * It may be counter-productive to always navigate into newly created folders, investigate if
             * just selecting the folder in the grid is a better workflow as it allows creating multiple folders with
             * lesser clicking around.
             */
            this.tree('selectNode', node);

            return this;
        },

        attachHandlers: function(){

            this._attachHandlers(); // Attach needed events from Koowa.Tree._attachHandlers

            var options = this.options, self = this;

            this.element.bind({
                'tree.select': // The select event happens when a node is clicked
                    function(event) {
                        if(window.console) console.log('tree.select', arguments);

                        var element;
                        if(event.node) { // When event.node is null, it's actually a deselect event
                            element = $(event.node.element);

                            self.tree('openNode', event.node); // open the selected node, if not open already

                            //Fire custom select node handler
                            options.onSelectNode(event.node);
                        }
                        if(event.node && !event.node.hasOwnProperty('is_open') && event.node.getLevel() === 2) {
                            if(window.console) console.log('special scrollIntoView event that checks if it is a second level node');
                            self.scrollIntoView(event.node, self.element, 300);
                        }
                    },
                'tree.open': // Animate a scroll to the node being opened so child elements scroll into view
                    function(event) {
                        if(window.console) console.log('scrollIntoView when folder opens');
                        self.scrollIntoView(event.node, self.element, 300);
                    }
            });
            this.element.on('tree.select', function(){
                /**
                 * Sidebar.js will fire a resize event when it sets the height on load, we want our animated scroll
                 * to happen after that, but not on future resize events as it would confuse the user experience
                 */

                self.element.one('resize', function(){
                    if(self.tree('getSelectedNode')) {
                        if(window.console) console.log('scrollIntoView event on tree.select.resize');
                        self.scrollIntoView(self.tree('getSelectedNode'), self.element, 900);
                    }
                });
            });

        }
    });

}(window.jQuery));