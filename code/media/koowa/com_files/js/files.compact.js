/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

if (!Files) Files = {};
Files.Compact = {};

Files.Compact.App = new Class({
	Extends: Files.App,
	Implements: [Events, Options],
    cookie: false,
	options: {
        pathway: false,
        persistent: false,
		types: ['file', 'image'],
		editor: null,
		preview: 'files-preview',
        state: {
            defaults: {
                limit: 0,
                offset: 0
            }
        },
		grid: {
			cookie: false,
			layout: 'compact',
			batch_delete: false
		},
		history: {
			enabled: false
		},
        uploader_dialog: false,
        folder_dialog: false
	},

	initialize: function(options) {
		this.parent(options);

		this.editor = this.options.editor;
		this.preview = document.id(this.options.preview);
	},
	setPaginator: function() {
	},
	setGrid: function() {
		var opts = this.options.grid;
		var that = this;
        Object.append(opts, {
			'onClickImage': function(e) {
				var target = document.id(e.target),
				    node = target.getParent('.files-node-shadow') || target.getParent('.files-node');

				node.getParent().getChildren().removeClass('active');
				node.addClass('active');
				var row = node.retrieve('row');
				var copy = Object.append({}, row);
				copy.template = 'details_image';

				that.preview.empty();

				copy.render('compact').inject(that.preview);

				that.preview.getElement('img').set('src', copy.image).show();
			},
			'onClickFile': function(e) {
				var target = document.id(e.target),
			   		node = target.getParent('.files-node-shadow') || target.getParent('.files-node');

				node.getParent().getChildren().removeClass('active');
				node.addClass('active');
				var row = node.retrieve('row');
				var copy = Object.append({}, row);
				copy.template = 'details_file';

				that.preview.empty();

				copy.render('compact').inject(that.preview);
			}
		});
		this.grid = new Files.Grid(this.options.grid.element, opts);
	}
});
