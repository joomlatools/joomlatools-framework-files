/**
 * @version     $Id$
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

if(!Files) var Files = {};

Files.blank_image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAMAAAAoyzS7AAAABGdBTUEAALGPC/xhBQAAAAd0SU1FB9MICA0xMTLhM9QAAAADUExURf///6fEG8gAAAABdFJOUwBA5thmAAAACXBIWXMAAAsSAAALEgHS3X78AAAACklEQVQIHWNgAAAAAgABz8g15QAAAABJRU5ErkJggg==';

Files.App = new Class({
	Implements: [Events, Options],

	_tmpl_cache: {},
	active: null,
	title: '',
	cookie: null,
	options: {
        cookie: {
            path: '/'
        },
		persistent: true,
		thumbnails: true,
		types: null,
		container: null,
		active: null,
		title: 'files-title',
		pathway: {
			element: 'files-pathway'
		},
		state: {
			defaults: {}
		},
		tree: {
			enabled: true,
			div: 'files-tree',
			theme: ''
		},
		grid: {
			element: 'files-grid',
			batch_delete: '#files-batch-delete',
			icon_size: 150
		},
		paginator: {
			element: 'files-paginator'
		},
		history: {
			enabled: true
		},
		router: {
			defaults: {
				option: 'com_files',
				view: 'files',
				format: 'json'
			}
		},
		initial_response: null,

		onAfterSetGrid: function(){
		    window.addEvent('resize', function(){
		        this.setDimensions(true);
		    }.bind(this));
		    this.grid.addEvent('onAfterRenew', function(){
		        this.setDimensions(true);
		    }.bind(this));
		    this.addEvent('onUploadFile', function(){
		        this.setDimensions(true);
		    }.bind(this));
		},
		onAfterNavigate: function(path) {
			if (path !== undefined) {
				this.setTitle(this.folder.name || this.container.title);
                jQuery('#upload-files-to, .upload-files-to').text(this.container.title+(path ? '/'+path : ''));
	        }
		}
	},

	initialize: function(options) {
		this.setOptions(options);

		if (this.options.persistent && this.options.container) {
			var container = typeof this.options.container === 'string' ? this.options.container : this.options.container.slug;
			this.cookie = 'com.files.container.'+container;
		}

		if(this.options.pathway) {
            this.setPathway();
        }
		this.setState();
		this.setHistory();
		this.setGrid();
		this.setPaginator();

		var url = this.getUrl();
		if (url.getData('container') && !this.options.container) {
			this.options.container = url.getData('container');
		}
		
		if (url.getData('folder')) {
			this.options.active = url.getData('folder');
		}

		if (this.options.title) {
			this.options.title = document.id(this.options.title);
		}
		
		if (this.options.thumbnails) {
			this.addEvent('afterSelect', function(resp) {
				this.setThumbnails();
			});
		}

		if (this.options.container) {
			this.setContainer(this.options.container);
		}

	},
	setState: function() {
		this.fireEvent('beforeSetState');

		if (this.cookie) {
            var state = Cookie.read(this.cookie+'.state'),
                obj   = JSON.decode(state, true);

            if (obj) {
                if (!this.getUrl().getData('folder')) {
                    this.options.active = obj.folder;
                }

                delete obj.folder;

                this.options.state.defaults = Files.utils.merge(this.options.state.defaults, obj);

            }

        }

		var opts = this.options.state;
		this.state = new Files.State(opts);

		this.fireEvent('afterSetState');
	},
	setHistory: function() {
		this.fireEvent('beforeSetHistory');

		if (this.options.history.enabled) {
			var that = this;
			this.history = History;
			window.addEvent('popstate', function(e) {
				if (e) { e.stop(); }

				var state = History.getState(),
					old_state = that.state.getData(),
					new_state = state.data,
					state_changed = false;

				Files.utils.each(old_state, function(value, key) {
					if (state_changed === true) {
						return;
					}
					if (new_state && new_state[key] && value !== new_state[key]) {
						state_changed = true;
					}
				});

				if (that.container && (state_changed || that.active !== state.data.folder)) {
					var set_state = Files.utils.append({}, state.data);
					['option', 'view', 'layout', 'folder', 'container'].each(function(key) {
						delete set_state[key];
					});
					that.state.set(set_state);
					that.navigate(state.data.folder, 'stateless');
				}
			});
			this.addEvent('afterNavigate', function(path, type) {
				if (type !== 'stateless' && that.history) {
					var obj = {
						folder: that.active,
						container: that.container ? that.container.slug : null
					};
					obj = Files.utils.append(obj, that.state.getData());
					var method = type === 'initial' ? 'replaceState' : 'pushState';
					var url = that.getUrl().setData(obj, true).set('fragment', '').toString()
					that.history[method](obj, null, url);
				}
			});
		}

		this.fireEvent('afterSetHistory');
	},
	/**
	 * type can be 'stateless' for no state or 'initial' to use replaceState
	 * response can be set if you want to set the results without an AJAX request.
	 */
	navigate: function(path, type, revalidate_cache, response) {
		this.fireEvent('beforeNavigate', [path, type]);
		if (path !== undefined) {
			if (this.active) {
				// Reset offset if we are changing folders
				this.state.set('offset', 0);
			}
			this.active = path == '/' ? '' : path;
		}

		this.grid.reset();

		var parts = this.active.split('/'),
			name = parts[parts.length ? parts.length-1 : 0],
			folder = parts.slice(0, parts.length-1).join('/'),
			that = this
			url_builder = function(url) {
				if (revalidate_cache) {
					url['revalidate_cache'] = 1;
				}
				return this.createRoute(url);
			}.bind(this),
			success = function(resp) {
				if (resp.status !== false) {
                    Files.utils.each(resp.items, function(item) {
						if (!item.baseurl) {
							item.baseurl = that.baseurl;
						}
					});
					that.response = resp;
					that.grid.insertRows(resp.items);

					that.fireEvent('afterSelect', resp);
				} else {
					alert(resp.error);
				}

			};

		this.folder = new Files.Folder({'folder': folder, 'name': name});
		
		if (response) {
			success(response);
		} else {
			this.folder.getChildren(success, null, this.state.getData(), url_builder);
		}

        if (this.cookie) {
            var data = jQuery.extend(true, {}, this.state.data);
            data.folder = this.active;
            Cookie.write(this.cookie+'.state', JSON.encode(data), this.options.cookie);
        }

		this.fireEvent('afterNavigate', [path, type]);
	},

	setContainer: function(container) {
		var setter = function(item) {
			this.fireEvent('beforeSetContainer', {container: item});

			this.container = item;
			this.baseurl = Files.sitebase + '/' + item.relative_path;

			this.active = '';

			if (this.uploader) {
				if (this.container.parameters.allowed_extensions) {
					this.uploader.settings.filters = [
					     {title: Files._('All Files'), extensions: this.container.parameters.allowed_extensions.join(',')}
	    			];
				}
				
				if (this.container.parameters.maximum_size) {
					this.uploader.settings.max_file_size = this.container.parameters.maximum_size;
					var max_size = document.id('upload-max-size');
					if (max_size) {
						max_size.set('html', new Files.Filesize(this.container.parameters.maximum_size).humanize());
					}
				}
			}

			if (this.container.parameters.thumbnails !== true) {
				this.options.thumbnails = false;
			} else {
				this.state.set('thumbnails', true);
			}

			if (this.options.types !== null) {
				this.options.grid.types = this.options.types;
				this.state.set('types', this.options.types);
			}

			this.fireEvent('afterSetContainer', {container: item});

			this.setTree();

			this.active = this.options.active || '';
			this.options.active = '';
			 
			if (typeof this.options.initial_response === 'string') {
				this.options.initial_response = JSON.decode(this.options.initial_response);
			}

			this.navigate(this.active, 'initial', false, this.options.initial_response);
		}.bind(this);

		if (typeof container === 'string') {
			new Request.JSON({
				url: this.createRoute({view: 'container', slug: container, container: false}),
				method: 'get',
				onSuccess: function(response) {
					setter(response.item);
				}.bind(this)
			}).send();
		} else {
			setter(container);
		}
	},
	setPaginator: function() {
		this.fireEvent('beforeSetPaginator');

		var opts = this.options.paginator,
			state = this.state;

        Files.utils.append(opts, {
			'state' : state,
			'onClickPage': function(el) {
				this.state.set('limit', el.get('data-limit'));
				this.state.set('offset', el.get('data-offset'));

				this.navigate();
			}.bind(this),
			'onChangeLimit': function(limit) {
				this.state.set('limit', limit);

                // Recalculate offset
                var total = Files.app.paginator.values.total,
                    offset = Files.app.paginator.values.offset;

                if (total) {
                    var page_count = Math.ceil(total/limit);
                    offset = (page_count-1)*limit;
                }

				this.state.set('offset', offset);

				this.navigate();
			}.bind(this)
		});
		this.paginator = new Files.Paginator(opts.element, opts);


		var that = this;
		that.addEvent('afterSelect', function(response) {
			that.paginator.setData({
				limit: response.limit,
				offset: response.offset,
				total: response.total
			});
			that.paginator.setValues();
		});

		this.fireEvent('afterSetPaginator');
	},
	setGrid: function() {
		this.fireEvent('beforeSetGrid');

		var that = this,
		    opts = this.options.grid,
			key = this.cookie+'.grid.layout';

		if (this.cookie && Cookie.read(key)) {
			opts.layout = Cookie.read(key);
		}

        Files.utils.append(opts, {
			'onClickFolder': function(e) {
				var target = document.id(e.target),
				    node = target.getParent('.files-node-shadow') || target.getParent('.files-node'),
					path = node.retrieve('row').path;
				if (path) {
					this.navigate(path);
				}
			}.bind(this),
			'onClickImage': function(e) {
				var target = document.id(e.target),
				    node = target.getParent('.files-node-shadow') || target.getParent('.files-node'),
                    row = node.retrieve('row'),
                    img = that.createRoute({view: 'file', format: 'raw', name: row.name, folder: row.folder});

				if (img) {
					SqueezeBox.open(img, {handler: 'image'});
				}
			},
			'onClickFile': function(e) {
				var target = document.id(e.target),
				    node = target.getParent('.files-node-shadow') || target.getParent('.files-node'),
					row = node.retrieve('row'),
					copy = Files.utils.append({}, row),
					trash = new Element('div', {style: 'display: none'}).inject(document.body);

				copy.template = 'file_preview';
				var template = copy.render().inject(trash), size = template.measure(function(){return this.getDimensions();});

				SqueezeBox.open(template, {
					handler: 'adopt',
					size: {x: size.x, y: size.y}
				});
				trash.dispose();
			},
			'onAfterSetLayout': function(context) {
				if (key) {
					Cookie.write(key, context.layout, this.options.cookie);
				}
			}.bind(this)
		});
		this.grid = new Files.Grid(this.options.grid.element, opts);

		this.fireEvent('afterSetGrid');
	},
	setTree: function() {
		this.fireEvent('beforeSetTree');

		if (this.options.tree.enabled) {
			var opts = this.options.tree,
				that = this;
            Files.utils.append(opts, {
				onClick: function(node) {
					if (node.id || node.data.url) {
						that.navigate(node && node.id ? node.id : '');
					}
				},
				root: {
					text: this.container.title,
					data: {
						url: '#'
					}
				}
			});
			this.tree = new Files.Tree(opts);
			this.tree.fromUrl(this.createRoute({view: 'folders', 'tree': '1', 'limit': '0'}));

			this.addEvent('afterNavigate', function(path) {
				that.tree.selectPath(path);
			});

			if (this.grid) {
				this.grid.addEvent('afterDeleteNode', function(context) {
					var node = context.node;
					if (node.type == 'folder') {
						var item = that.tree.get(node.path);
						if (item) {
							item.remove();
						}
					}
				});
			}
		}

		this.fireEvent('afterSetTree');
	},
	getUrl: function() {
		return new URI(window.location.href);
	},
	getPath: function() {
		return this.active;
	},
	setThumbnails: function() {
		this.setDimensions(true);
		var nodes = this.grid.nodes,
			that = this;
		if (nodes.getLength()) {
			nodes.each(function(node) {
				if (node.filetype !== 'image') {
					return;
				}
				var name = node.name;

				var img = node.element.getElement('img.image-thumbnail');
				if (img) {
					img.addEvent('load', function(){
					    this.addClass('loaded');
					});
					img.set('src', node.thumbnail ? node.thumbnail : Files.blank_image);
					
					(node.element.getElement('.files-node') || node.element).addClass('loaded').removeClass('loading');

					if(window.sessionStorage) {
					    sessionStorage[node.image.toString()] = img.get('src');
					}
				}
			});
		}

	},
	setDimensions: function(force){

	    if(!this._cached_grid_width) this._cached_grid_width = 0;

        //Only fire if the cache have changed
        if(this._cached_grid_width != this.grid.root.element.getSize().x || force) {
            var width = this.grid.root.element.getSize().x,
                factor = width/(this.grid.options.icon_size.toInt()+40),
                limit = Math.min(Math.floor(factor), this.grid.nodes.getLength()),
                resize = width / limit,
                thumbs = [[]],
                labels = [[]],
                index = 0,
                pointer = 0;

            this.grid.root.element.getElements('.files-node-shadow').each(function(element, i, elements){
                element.setStyle('width', (100/limit)+'%');
            }, this);

            this._cached_grid_width = this.grid.root.element.getSize().x;
        }
    },
    setPathway: function() {
    	this.fireEvent('beforeSetPathway');

        var pathway = new Files.Pathway(this.options.pathway);
        this.addEvent('afterSetTitle', pathway.setPath.bind(pathway, this));

		this.fireEvent('afterSetPathway');
	},
	setTitle: function(title) {
		this.fireEvent('beforeSetTitle', {title: title});

		this.title = title;

		if (this.options.title) {
			this.options.title.set('html', title);
		}

		this.fireEvent('afterSetTitle', {title: title});
	},
	createRoute: function(query) {
		query = Files.utils.merge(this.options.router.defaults, query || {});

		if (query.container !== false && !query.container && this.container) {
			query.container = this.container.slug;
		} else {
			delete query.container;
		}

		if (query.format == 'html') {
			delete query.format;
		}

		return '?'+new Hash(query).filter(function(value, key) {
			return typeof value !== 'function';
		}).toQueryString();
	}
});