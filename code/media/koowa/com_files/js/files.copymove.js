/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

if(!Files) var Files = {};

(function($) {

var CopyMoveDialog = Koowa.Class.extend({
    initialize: function(options) {
        this.supr();

        options = {
            view: $(options.view),
            tree: $(options.view).find('.tree-container'),
            button: $(options.button, options.view),
            open_button: $(options.open_button)
        };

        this.setOptions(options);
        this.attachEvents();
    },
    attachEvents: function() {
        var self = this;

        if (this.options.open_button) {
            this.options.open_button.click(function(event) {
                event.preventDefault();

                self.show();
            });
        }

        if (this.options.view.find('form')) {
            this.options.view.find('form').submit(function(event) {
                event.preventDefault();

                self.submit();
            });
        }
    },
    show: function() {
        var options = this.options,
            count = Object.getLength(this.getSelectedNodes());

        if (options.open_button.hasClass('unauthorized') || !count) {
            return;
        }

        var data = Files.app.tree.tree('toJson'),
            tree = new Koowa.Tree(options.view.find('.tree-container'));

        tree.tree('loadData', $.parseJSON(data));

        this.getSelectedNodes().each(function(node) {
            var tree_node = tree.tree('getNodeById', node.path);
            if (tree_node) {
                tree.tree('removeNode', tree_node);
            }
        });

        $.magnificPopup.open({
            items: {
                src: $(options.view),
                type: 'inline'
            }
        });
    },
    hide: function() {
        if (this.options.tree instanceof $) {
            this.options.tree.empty();
        }

        $.magnificPopup.close();
    },
    getSelectedNodes: function() {
        return Files.app.grid.nodes.filter(function(row) { return row.checked });
    },
    handleError: function(xhr) {
        var response = JSON.decode(xhr.responseText, true);

        this.hide();

        if (response && response.error) {
            alert(response.error);
        }
    }
});

Files.CopyDialog = CopyMoveDialog.extend({
    submit: function() {
        var self  = this,
            nodes = this.getSelectedNodes(),
            names = Object.values(nodes.map(function(node) { return node.name; })),
            destination = this.options.view.find('.tree-container').tree('getSelectedNode').path,
            url = Files.app.createRoute({view: 'nodes', folder: Files.app.getPath()});

        if (!names.length) {
            return;
        }

        $.ajax(url, {
            type: 'POST',
            data: {
                'name' : names, // names are passed in POST to circumvent 2k characters rule in URL
                'destination_folder': destination || '',
                '_action': 'copy',
                'csrf_token': Files.token
            }
        }).done(function(response) {
            // Add new nodes to the tree

            self.hide();
        }).fail($.proxy(this.handleError, this));
    }
});

Files.MoveDialog = CopyMoveDialog.extend({
    submit: function() {
        var self  = this,
            nodes = this.getSelectedNodes(),
            names = Object.values(nodes.map(function(node) { return node.name; })),
            destination = this.options.view.find('.tree-container').tree('getSelectedNode').path,
            url = Files.app.createRoute({view: 'nodes', folder: Files.app.getPath()});

        if (!names.length) {
            return;
        }

        $.ajax(url, {
            type: 'POST',
            data: {
                'name' : names, // names are passed in POST to circumvent 2k characters rule in URL
                'destination_folder': destination || '',
                '_action': 'move',
                'csrf_token': Files.token
            }
        }).done(function(response) {
            var tree = Files.app.tree;
            nodes.each(function(node) {
                if (node.element) {
                    node.element.dispose();
                }

                Files.app.grid.nodes.erase(node.path);

                var tree_node = tree.tree('getNodeById', node.path);
                if (tree_node) {
                    // Update properties
                    tree_node.path = (destination ? destination+'/' : '')+node.name;
                    tree_node.id = tree_node.path;
                    tree_node.url = '#'+tree_node.path;

                    // Move under new parent
                    var parent_node = destination ? tree.tree('getNodeById', destination) : tree.tree('getTree').children[0];
                    if (parent_node) {
                        tree.tree('moveNode', tree_node, parent_node, 'inside');
                    }
                }
            });

            self.hide();
        }).fail($.proxy(this.handleError, this));
    }
});

})(window.kQuery);