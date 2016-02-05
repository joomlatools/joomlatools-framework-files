/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

if (!Files) Files = {};
Files.Attachments = {};

Files.Attachments.App = new Class({
    Extends: Files.App,
    Implements: [Events, Options],
    cookie: false,
    attachments: {},
    options: {
        pathway: false,
        persistent: false,
        types: ['file', 'image'],
        state: {
            defaults: {
                limit: 0,
                offset: 0
            }
        },
        grid: {
            spinner_container: 'files-spinner',
            preview:  'files-preview',
            cookie: false,
            layout: 'compact',
            batch_delete: false
        },
        attachments: {
            permissions: {
                attach: false,
                detach: false
            },
            grid: {
                spinner_container: 'attachments-spinner',
                preview: 'attachments-preview',
                cookie: false,
                layout: 'attachments',
                element: 'attachments-grid'
            }
        },
        history: {
            enabled: false
        },
        uploader_dialog: false,
        folder_dialog: false,
        copy_dialog: false,
        move_dialog: false
    },

    initialize: function(options) {

        this.parent(options);

        this.attachments.permissions = options.attachments.permissions;

        this.setAttachmentsGrid();

        this.preview = document.id(this.options.grid.preview);
        this.attachments.preview = document.id(this.options.attachments.grid.preview);

        var app = this;

        if (options.callbacks)
        {
            Array.each(options.callbacks, function(callback)
            {
                if (typeof window[callback] == 'function') {
                    window[callback](app);
                }
            });
        }
    },
    setPaginator: function() {
    },
    setAttachmentsGrid: function() {
        var opts = this.options.attachments.grid;
        var that = this;

        Object.append(opts, {
            'onClickAttachment': function (e) {
                var target = document.id(e.target),
                    node = target.getParent('.files-node-shadow') || target.getParent('.files-node');

                node.getParent().getChildren().removeClass('active');
                node.addClass('active');
                var row = node.retrieve('row');
                var copy = Object.append({}, row);
                copy.template = 'details_attachment';

                that.attachments.preview.empty();

                copy.render('attachments').inject(that.attachments.preview);

                if (copy.file.thumbnail) {
                    that.attachments.preview.getElement('img').set('src', copy.file.thumbnail.thumbnail).show();
                }
            },
            'onBeforeRenderObject': function(context) {
                var row = context.object;
                row.download_link = that.createRoute({view: 'file', format: 'html', name: row.name});
            },
            /*'afterInsertRows': function(context) {
             // Select the initial file for preview
             var url = app.getUrl();

             if (url.getData('file')) {
             var select = url.getData('file').replace(/\+/g, ' ');
             select = app.active ? app.active+'/'+select : select;
             var node = app.grid.nodes.get(select);

             if (node && node.element) {
             app.grid.fireEvent('clickAttachment', [{target: node.element.getElement('a')}]);
             }
             }
             }*/
        });

        this.attachments.grid = new Files.Attachments.Grid(this.options.attachments.grid.element, opts);

    },
    setGrid: function () {
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

Files.Attachments.Grid = new Class({
    Extends: Files.Grid,
    options: {
        url: null
    },
    initialize: function(container,options) {
        this.parent(container, options);

        this.url = options.url;

        if (this.url) {
            this.refresh();
        }
    },
    refresh: function() {
        var that = this;

        if (this.url)
        {
            that.reset(); // Flush current content.

            this.spin();

            new Request.JSON({
                url: this.url,
                method: 'get',
                onSuccess: function(response)
                {
                    Files.app.attachments.grid.insertRows(response.entities);
                    that.unspin();
                }
            }).send();
        }
    },
    insert: function(object, position) {
        this.fireEvent('beforeInsertNode', {object: object, position: position});

        if (!this.options.types || this.options.types.contains(object.type)) {
            this.renderObject(object, position);

            this.nodes.set(object.name, object);

            this.fireEvent('afterInsertNode', {node: object, position: position});
        }
    },
    attachEvents: function() {
        var that = this;
        this.container.addEvent('click:relay(.files-attachment a.navigate)', function(e) {
            e.stop();
            that.fireEvent('clickAttachment', arguments);
        });
    },
    /**
     * Insert multiple rows, possibly coming from a JSON request
     */
    insertRows: function(rows) {
        this.fireEvent('beforeInsertRows', {rows: rows});

        Object.each(rows, function(row) {
            var item = new Files.Attachment(row);
            this.insert(item, 'last');
        }.bind(this));

        if (this.options.icon_size) {
            this.setIconSize(this.options.icon_size);
        }

        this.fireEvent('afterInsertRows', {rows: rows});
    },
});

Files.Template.Attachments = new Class({
    initialize: function(html) {
        return new Element('div', {html: html}).getFirst();
    }
});

Files.Attachment = new Class({
    Extends: Files.Row,

    type: 'attachment',
    template: 'attachment',
    initialize: function(object, options) {
        this.setOptions(options);

        Object.each(object, function(value, key) {
            this[key] = value;
        }.bind(this));

        if (typeof this.name !== 'string') {
            this.name = '';
        }

        this.identifier = this.name;

        this.size = new Files.Filesize(this.file.metadata.size);
        this.filetype = Files.getFileType(this.file.metadata.extension);

        if (Files.app) {
            this.baseurl = Files.app.baseurl;
        }
    },
    getAttachedDate: function(formatted) {
        if (this.created_on_timestamp) {
            var date = new Date();
            date.setTime(this.created_on_timestamp*1000);
            if (formatted) {
                return date.getDate()+' '+Koowa.Date.getMonthName(date.getMonth()+1, true)+' '+date.getFullYear();
            } else {
                return date;
            }
        }

        return null;
    }
});