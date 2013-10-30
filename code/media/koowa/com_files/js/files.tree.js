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
        options: {
            onSelectNode: function (){},
            onAdopt: function (){},
            adopt: null,
            root: {
                open: true
            }
        },

        getDefaults: function(){

            var self = this,
                defaults = {
                    autoOpen: 0, //root.open = true on previous script
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
                //id: -1, //negative 1 used as 0 doesn't work with this.selectNode
                href: '#',
                children: list
            }];
        },

        /**
         * We need to duplicate this because in the latest Mootree noClick argument is removed.
         */
        select: function(node, noClick) {
            if (!noClick) {
                this.onClick(node); node.onClick(); // fire click events
            }
            if (this.selected === node) return; // already selected
            if (this.selected) {
                // deselect previously selected node:
                this.selected.select(false);
                this.onSelect(this.selected, false);
            }
            // select new node:
            this.selected = node;
            node.select(true);
            this.onSelect(node, true);

            while (true) {
                if (!node.parent || node.parent.id == null) {
                    break;
                }
                node.parent.toggle(false, true);

                node = node.parent;
            }
        },
        adopt: function(id, parentNode) {
            this.parent(id, parentNode);

            this.onAdopt(id, parentNode);
        },
        fromUrl: function(url) {

            var self = this;
            this.tree('loadDataFromUrl', url, null, function(response){
                if (Files.app && Files.app.active) {
                    self.selectPath(Files.app.active);
                }
                //@TODO may not be necessary
                //self.onAdopt(self.options.div, self.root);
            });

        },
        selectPath: function(path) {
            var node = path !== undefined ? this.tree('getNodeById', path) : this.tree('getTree');

            //Calling a private API in order to make the Root node selectable
            this.element.data('simple_widget_tree')._selectNode(node, true);
            //this.tree('selectNode', path !== undefined ? this.tree('getNodeById', path) : this.tree('getTree'));
            return;
            if (path !== undefined) {
                var node = this.get(path);
                if (node) {
                    this.select(node, true);
                }
                else {
                    this.select(this.root, true);
                }
            }
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