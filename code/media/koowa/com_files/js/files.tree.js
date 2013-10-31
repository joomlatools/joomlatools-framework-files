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
                    onAfterInitialize: function(){
                        this.onAdopt = this.options.onAdopt;

                        //this.parent(this.options, this.options.root);

                        if (this.options.adopt) {
                            this.adopt(this.options.adopt);
                        }

                        if (this.options.title) {
                            this.setTitle(this.options.title);
                        }
                    },
                    dataFilter: function(response){

                        var data = response.entities,
                            parse = function(item, parent) {
                                var path = (parent && parent.path) ? parent.path+'/' : '',
                                    level = (parent && parent.level >= 0) ? parent.level + 1 : 0;
                                path += item.name;

                                //Parse attributes
                                item = $.extend(item, {
                                    text: item.name,
                                    id: path,
                                    path: path,
                                    url: '#'+item.path,
                                    type: 'folder',
                                    level: level
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

        setTitle: function(title) {
            if (!this.title_element) {
                this.title_element = new Element('h3').inject(document.id(this.options.div), 'top');
            }
            this.title = title;
            this.title_element.set('text', title);
        },

        /**
         * Customized parseData method due to using json, in a already nested data format
         * @param json returned data
         * @returns data
         */
        parseData: function(list){
            return [{
                label: this.options.root.text,
                href: '#',
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
         * Previous code:
         *
            Files.app.tree.selected.insert({
                text: row.name,
                id: row.path,
                data: {
                    path: row.path,
                    url: '#'+row.path,
                    type: 'folder'
                }
            });
            Files.app.tree.selected.toggle(false, true);
         *
         * @param data
         */
        appendNode: function(data){
            $.extend(data, {foo: 'bar'});

            console.log(data);

            return this;
        },

        attachHandlers: function(){

            this._attachHandlers(); // Attach needed events from Koowa.Tree._attachHandlers

            var options = this.options, self = this;

            this.element.bind({
                'tree.select': // The select event happens when a node is clicked
                    function(event) {
                        var element;
                        if(event.node) { // When event.node is null, it's actually a deselect event
                            element = $(event.node.element);

                            self.tree('openNode', event.node); // open the selected node, if not open already

                            //Fire custom select node handler
                            options.onSelectNode(event.node);
                        }
                    },
                'tree.open': // Animate a scroll to the node being opened so child elements scroll into view
                    function(event) {
                        var node = event.node,
                            element = $(node.element),
                            viewport = self.element.height(),
                            offsetTop = element[0].offsetTop,
                            height = element.height(),
                            scrollTo = Math.min(offsetTop, (offsetTop - viewport) + height);

                        if(scrollTo > self.element.scrollTop()){ //Only scroll if there's overflow
                            self.element.animate({scrollTop: scrollTo }, 300);
                        }
                    }
            });
            this.element.on('tree.select', function(){
                /**
                 * Sidebar.js will fire a resize event when it sets the height on load, we want our animated scroll
                 * to happen after that, but not on future resize events as it would confuse the user experience
                 */

                self.element.one('resize', function(){
                    if(self.tree('getSelectedNode')) {
                        var node = self.tree('getSelectedNode'),
                            element = $(node.element),
                            viewport = self.element.height(),
                            offsetTop = element[0].offsetTop,
                            height = element.height(),
                            scrollTo = Math.min(offsetTop, (offsetTop - viewport) + height);

                        if(scrollTo > self.element.scrollTop()){ //Only scroll if there's overflow
                            self.element.stop().animate({scrollTop: scrollTo }, 900);
                        }
                    }
                });
            });

        }
    });

}(window.jQuery));