/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

if (!Files) Files = {};
Files.Compact = {};

Files.Compact.App = new Class({
	Extends: Files.App,
	Implements: [Events, Options],

	options: {
		types: ['file', 'image'],
		editor: null,
		preview: 'files-preview',
		grid: {
			cookie: false,
			layout: 'compact',
			batch_delete: false
		},
		history: {
			enabled: false
		}
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
        Files.utils.append(opts, {
			'onClickImage': function(e) {
				var target = document.id(e.target),
				    node = target.getParent('.files-node-shadow') || target.getParent('.files-node');

				node.getParent().getChildren().removeClass('active');
				node.addClass('active');
				var row = node.retrieve('row');
				var copy = Files.utils.append({}, row);
				copy.template = 'details_image';

				that.preview.empty();

				copy.render('compact').inject(that.preview);

				that.preview.getElement('img').set('src', copy.image);
			},
			'onClickFile': function(e) {
				var target = document.id(e.target),
			   		node = target.getParent('.files-node-shadow') || target.getParent('.files-node');

				node.getParent().getChildren().removeClass('active');
				node.addClass('active');
				var row = node.retrieve('row');
				var copy = Files.utils.append({}, row);
				copy.template = 'details_file';

				that.preview.empty();

				copy.render('compact').inject(that.preview);
			}
		});
		this.grid = new Files.Grid(this.options.grid.element, opts);
	}
});
