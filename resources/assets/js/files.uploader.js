/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright    Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/nooku/nooku-files for the canonical source repository
 */

if (!Files) var Files = {};

(function($) {

//We only want to run this once
    var exposePlupload = function (uploader) {
        document.id('files-upload').addClass('uploader-files-queued').removeClass('uploader-files-empty');
        uploader.refresh();
        uploader.unbind('QueueChanged', exposePlupload);
        window.fireEvent('QueueChanged');
    };

    var addDragDrop = function(uploader) {
        var timer,
            cancel = function (e) {
                e.preventDefault();// required by FF + Safari
                e.stopPropagation();
                e.originalEvent.dataTransfer.dropEffect = 'copy'; // tells the browser what drop effect is allowed here
            },
            createDragoverHandler = function (container) {

                //Create hilite + label
                var focusring = $('<div class="dropzone-focusring"></div>'),
                    label = $('<div class="alert alert-success">' + Koowa.translate('Drop your files to upload to {folder}').replace('{folder}', Files.app.title) + '</div>');

                focusring.css({
                    display: 'none',
                    position: 'absolute',
                    backgroundColor: 'hsla(0, 0%, 100%, 0.75)',
                    top: 0,
                    left: 0,
                    bottom: 0,
                    right: 0,
                    zIndex: 65558,
                    borderStyle: 'solid',
                    borderWidth: '5px',
                    opacity: 0,
                    transition: 'opacity 300ms',
                    paddingTop: 10,
                    textAlign: 'center'
                });
                container.append(focusring);

                //To inherit styling
                $('#files-upload').append(label);
                ['border-radius', 'color', 'background', 'border'].forEach(function (prop) {
                    label.css(prop, label.css(prop));
                });
                label.css({
                    display: 'inline-block',
                    margin: '0 auto'
                });
                focusring.append(label);
                focusring.css('border-color', label.css('color')); //border-color too bright

                return function (e) {

                    e.preventDefault();// required by FF + Safari
                    e.originalEvent.dataTransfer.dropEffect = 'copy'; // tells the browser what drop effect is allowed here
                    if (focusring.css('display') == 'none') {
                        label.text(Koowa.translate('Drop your files to upload to {folder}').replace('{folder}', Files.app.title));
                        focusring.css('display', 'block');
                        setTimeout(function () {
                            focusring.css('opacity', 1);
                            if (!$('#files-upload').is(':visible')) {
                                $('#files-canvas').addClass('dropzone-droppable');
                            }
                        }, 1);

                    }
                    //container.addClass('dropzone-dragover'); //This breaks safaris drag and drop, still unknown why

                    //This is a failsafe measure
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        $('.dropzone-focusring').css('opacity', 0).css('display', 'none');
                        $('#files-canvas').removeClass('dropzone-droppable');
                    }, 300);
                };
            },
            createDragleaveHandler = function (/*container*/) {
                return function (e) {
                    //@TODO following code is too buggy, it fires multiple times causing a flickr, for now the focusring will only dissappear on drop
                    //container.removeClass('dropzone-dragover');
                    //$('.dropzone-focusring').css('opacity', 0).css('display', 'none');
                };
            },
            addSelectedFiles = function(native_files) {
                var file, i, files = [], id, fileNames = {};

                // Add the selected files to the file queue
                for (i = 0; i < native_files.length; i++) {
                    file = native_files[i];

                    // Safari on Windows will add first file from dragged set multiple times
                    // @see: https://bugs.webkit.org/show_bug.cgi?id=37957
                    if (fileNames[file.name]) {
                        continue;
                    }
                    fileNames[file.name] = true;

                    uploader.addFile(file);
                }
            },
        // Make the document body a dropzone
            files_canvas = $('#files-canvas'),
            body = $(document.body);

        document.id('files-upload').addClass('uploader-droppable');

        //Prevent file drops from duplicating due to double drop events
        $('#files-upload-multi_filelist').bind('drop', function (event) {
            event.stopPropagation();
            //@TODO implement the rest of the drop code from handler, to remove focusring
            $(document.body).removeClass('dropzone-dragover');
        });

        body.bind('dragover', createDragoverHandler(body)); //Using dragenter caused inconsistent behavior
        body.bind('dragleave', createDragleaveHandler(body));
        body.bind('dragenter', cancel);
        body.bind('drop', function (event) {
            event.preventDefault();
            files_canvas.removeClass('dragover');
            var dataTransfer = event.originalEvent.dataTransfer;

            // Add dropped files
            if (dataTransfer && dataTransfer.files && dataTransfer.files.length) {
                var copy = dataTransfer.files;

                if (!$('#files-upload').is(':visible')) {
                    //@TODO the click handler is written in mootools, so we use mootools here
                    document.getElement(Files.app.options.uploader_dialog.button).fireEvent('click', 'DOMEvent' in window ? new DOMEvent : new Event);
                }

                setTimeout(function() {
                    addSelectedFiles(copy);
                }, 300);
            }
        });
        body.bind('dragend', function () {
            $('.dropzone-focusring').css('opacity', 0).css('display', 'none');
        });

        uploader.bind('QueueChanged', exposePlupload);
    };

    var getUniqueName = function (name, fileExists) {
        // Get a unique file name by appending (1) (2) etc.
        var i = 1,
            extension = name.substr(name.lastIndexOf('.') + 1),
            base = name.substr(0, name.lastIndexOf('.'));

        while (true) {
            name = base + ' (' + i + ').' + extension;

            if (!fileExists(name)) {
                break;
            }

            i++;
        }

        return name;
    };

    Files.createUploader = function (options) {
        options = $.extend({}, {
            element: '#files-upload-multi',
            multi_selection: true,
            media_path: ''
        }, options);
        var element = $(options.element);

        if (element.length === 0) {
            return;
        }

        //This trick enables the flash runtime to work properly when the uploader is hidden
        var containershim = 'mushycode' + Math.floor((Math.random() * 10000000000) + 1);
        $('<div id="' + containershim + '" class="uploader-flash-container" />').appendTo($(document.body));

        var config  = {
            runtimes: 'html5,flash',
            container: containershim,
            browse_button: 'pickfiles',
            multi_selection: options.multi_selection,
            dragdrop: true,
            unique_names: false,
            rename: true,
            url: '/', // this is added on the go in BeforeUpload event
            flash_swf_url: options.media_path+'koowa/com_files/js/plupload/Moxie.swf',
            urlstream_upload: true, // required for flash
            multipart_params: {
                _action: 'add',
                csrf_token: Files.token
            },
            filters: {
                prevent_duplicates: true
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            preinit: {
                Error: function (up, args) {
                    if (args.code == plupload.INIT_ERROR) {
                        var message = Koowa.translate('{html5} or {flash} required for uploading files from your computer.', {
                            html5: '<a href="https://google.com/chrome" target="_blank">' + Koowa.translate('HTML5 enabled browser') + '</a>',
                            flash: '<a href="https://get.adobe.com/flashplayer/" target="_blank">' + Koowa.translate('Flash Player') + '<a/>'
                        });

                        // Plupload clears element's contents after the event so setTimeout is needed
                        setTimeout(function() {
                            element.append('<div class="alert alert-error warning">' + message + '</div>');
                        }, 100);
                    }
                }
            }
        };

        if (Files.app && Files.app.container) {
            if (Files.app.container.parameters.allowed_extensions) {
                var types = Files.app.state.get('types'),
                    extensions = Files.app.container.parameters.allowed_extensions;

                if (typeof types === 'object' && types.length === 1 && types[0] === 'image') {
                    extensions = $.grep(Files.app.container.parameters.allowed_extensions, function(extension) {
                        return $.inArray(extension, Files.FileTypes.map.image) !== -1;
                    });
                }

                config.filters.mime_types = [
                    {title: Koowa.translate('All Files'), extensions: extensions.join(',')}
                ]
            }

            /**
             * Enable chunking only for HTML5 runtime. See http://plupload.com/docs/Chunking for more info
             */
            config.preinit.Init = function(uploader) {
                var chunking = (uploader.runtime === 'html5' && uploader.features.chunks),
                    limit = Files.app.container.parameters.maximum_size,
                    server_limit = Files.app.container.server_upload_limit;

                if (!chunking) {
                    if (!limit || limit == 0 || (limit > server_limit)) {
                        limit = server_limit - 1048576;
                    }
                } else {
                    // Leave 1 mb for the rest of the POST data
                    var chunk_size = Math.max(1048576, server_limit - 1048576);

                    if (chunk_size > 33554432) {
                        chunk_size = 33554432; // use 512 mb chunks at the maximum
                    }

                    uploader.setOption('chunk_size', chunk_size);
                }

                if (limit > 0) {
                    uploader.setOption('max_file_size', limit);

                    var max_size = document.id('upload-max-size');
                    if (max_size) {
                        max_size.set('html', new Files.Filesize(limit).humanize());
                    }
                } else {
                    document.id('upload-max').setStyle('display', 'none');
                }
            };
        }

        SqueezeBox.addEvent('open', function () {
            //This is to make sure the flash upload button shim is positioned correctly after the modal is opened
            window.fireEvent('refresh');
        });

        window.addEvent('refresh', function () {
            uploader.refresh();
        });

        element.pluploadQueue(config);

        var uploader = element.pluploadQueue();

        // Overwrite checker
        var $start = $('.plupload_start', element),
            getNamesFromArray = function (array) {
                var results = [];
                $.each(array, function (i, entity) {
                    results.push(entity.attributes.name);
                });

                return results;
            },
            startUpload = function () {
                if (!$start.hasClass('plupload_disabled')) {
                    uploader.start();
                }
            },
            getConfirmationMessage = function (files) {
                var message = '';

                if (files.length === 1) {
                    message = Koowa.translate('A file with the same name already exists. Would you like to overwrite it?');
                } else if (files.length > 1) {
                    message = Koowa.translate('Following files already exist. Would you like to overwrite them? {names}', {
                        names: "\n" + files.join("\n")
                    });
                }

                return message;
            },
            makeUnique = function (file, similar) {
                var names = [];
                if (typeof similar.data === 'object' && similar.data.length) {
                    names = getNamesFromArray(similar.data);
                }
                $.each(uploader.files, function (i, f) {
                    if (f.id !== file.id) {
                        names.push(f.name);
                    }
                });

                file.name = getUniqueName(file.name, function (name) {
                    return $.inArray(name, names) !== -1;
                });

                $('#' + file.id).find('div.plupload_file_name span').text(file.name);
            },
            checkDuplicates = function (response) {
                uploader.settings.multipart_params.overwrite = 0;

                if (typeof response.data === 'object' && response.data.length) {
                    var existing = getNamesFromArray(response.data),
                        promises = [];

                    if (confirm(getConfirmationMessage(existing))) {
                        uploader.settings.multipart_params.overwrite = 1;

                        return startUpload();
                    }

                    $.each(uploader.files, function (i, file) {
                        if ($.inArray(file.name, existing) !== -1) {
                            promises.push($.ajax({
                                type: 'GET',
                                url: Files.app.createRoute({
                                    view: 'files', folder: Files.app.getPath(), limit: 100,
                                    search: file.name.substr(0, file.name.lastIndexOf('.')) + ' ('
                                })
                            }).done(function (response) {
                                return makeUnique(file, response)
                            }));
                        }
                    });

                    if (promises) {
                        $.when.apply(kQuery, promises).then(function () {
                            startUpload();
                        });
                    }
                }
                else {
                    startUpload();
                }
            };

        $start.click(function (e) {
            e.preventDefault();

            var names = [];
            $.each(uploader.files, function (i, file) {
                if (file.loaded == 0) {
                    names.push(file.name);
                }
            });

            if (names.length) {
                $.ajax({
                    url: Files.app.createRoute({view: 'files', limit: 100, folder: Files.app.getPath()}),
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        name: names
                    }
                }).done(checkDuplicates).fail(startUpload);
            }

        });

        // Do not allow more than 100 files to be uploaded at once
        uploader.bind('FilesAdded', function (uploader) {
            if (uploader.files.length > 100) {
                uploader.splice(0, uploader.files.length - 100);
            }
        });

        if (!options.multi_selection) {
            /**
             * Only leave the last file if there are more than one in the queue
             */
            var removeExcessFiles = function (uploader) {
                    var count = uploader.files.length;

                    if (count > 1) {
                        $.each(uploader.files, function (i, file) {
                            if (i !== count - 1) { // Find the last file
                                uploader.removeFile(file);
                            }
                        });
                    }

                    queue_locked = false;
                },
                queue_locked = false;

            uploader.bind('QueueChanged', function (uploader) {
                if (queue_locked) {
                    return;
                }

                queue_locked = true;
                removeExcessFiles(uploader);
            });
        }

        uploader.bind('ChunkUploaded', function(up, file, info) {
            var response = $.parseJSON(info.response);

            if (response.status === false)
            {
                file.status = plupload.FAILED;
                failed[file.id] = response.error;
                up.stop();
            }
        });

        var msie    = window.navigator.userAgent.indexOf('MSIE '),
            trident = window.navigator.userAgent.indexOf('Trident/'),
            is_ie   = (msie > 0 || trident > 0),
            hideDropZone = function() {
                document.id('files-upload')
                    .addClass('uploader-nodroppable')
                    .setStyle('position', '')
                    .addClass('uploader-files-queued')
                    .removeClass('uploader-files-empty');

                uploader.refresh();
            };

        if (!is_ie) {
            setTimeout(function() {
                if (uploader.features.dragdrop) {
                    addDragDrop(uploader);
                } else {
                    hideDropZone();
                }
            }, 1500);
        } else {
            hideDropZone();
        }

        uploader.bind('BeforeUpload', function (uploader, file) {
            // set directory in the request
            uploader.settings.url = Files.app.createRoute({
                view: 'file',
                plupload: 1,
                folder: Files.app.getPath()
            });
        });

        uploader.bind('UploadComplete', function (uploader) {
            $('li.plupload_delete a,div.plupload_buttons', element).show();

            uploader.disableBrowse(false);
            uploader.refresh();
        });

        // Keeps track of failed uploads and error messages so we can later display them in the queue
        var failed = {};
        uploader.bind('FileUploaded', function (uploader, file, response) {
            var json = JSON.decode(response.response, true) || {},
                row,
                item,
                path;

            if (json.status) {
                item = json.data.attributes;
                path = (item.folder ? item.folder+'/' : '') + item.name;

                if (typeof Files.app.grid.nodes[path] === 'undefined') {
                    var cls = Files[item.type.capitalize()];
                    row = new cls(item);
                    Files.app.grid.insert(row);
                } else {
                    row = Files.app.grid.nodes[path];

                    if (item.metadata) {
                        row.metadata = item.metadata;
                        row.size = new Files.Filesize(row.metadata.size);
                    }
                }

                if (row.type == 'image' && Files.app.grid.layout == 'icons') {
                    var image = row.element.getElement('img');
                    if (image) {
                        row.getThumbnail(function (response) {
                            if (response.item.thumbnail) {
                                image.set('src', response.item.thumbnail).addClass('loaded').removeClass('loading');
                                row.element.getElement('.files-node').addClass('loaded').removeClass('loading');
                            }
                        });

                        /* @TODO Test if this is necessary: This is for the thumb margins to recalculate */
                        window.fireEvent('resize');
                    }
                }
                Files.app.fireEvent('uploadFile', [row]);
            } else {
                failed[file.id] = json.error ? json.error : Koowa.translate('Unknown error');
            }
        });

        uploader.bind('StateChanged', function (uploader) {
            Object.each(failed, function (error, id) {
                icon = $('#' + id).attr('class', 'plupload_failed').find('a').css('display', 'block');
                if (error) {
                    icon.attr('title', error);
                }
            });

        });

        $$('.plupload_clear').addEvent('click', function (e) {
            e.stop();

            if (confirm(Koowa.translate('Are you sure you want to clear the upload queue? This cannot be undone!'))) {
                // need to work on a clone, otherwise iterator gets confused after elements are removed
                var files = uploader.files.slice(0);
                files.each(function (file) {
                    uploader.removeFile(file);
                });
            }
        });

        Files.app.uploader = uploader;

        /**
         * Switcher between uploaders
         */
        var toggleForm = function (type) {
            $$('.upload-form').setStyle('display', 'none');
            document.id('files-uploader-' + type).setStyle('display', 'block');

            // Plupload needs to be refreshed if it was hidden
            if (type == 'computer' && element.length) {
                if (!uploader.files.length && uploader.features.dragdrop) {
                    document.id('files-upload').removeClass('uploader-files-queued').addClass('uploader-files-empty');
                    if (document.id('files-upload-multi_browse')) {
                        uploader.bind('QueueChanged', exposePlupload);
                    }
                }
            }

            window.fireEvent('refresh');
        };

        $$('.upload-form-toggle').addEvent('click', function (e) {
            var hash = this.get('href').split('#')[1];
            $$('.upload-form-toggle').removeClass('active');
            e.preventDefault();
            this.addClass('active');

            toggleForm(hash);
        });

        /**
         * Remote file form
         */
        var form = document.id('remoteForm'), filename = document.id('remote-name'),
            submit = form.getElement('.remote-submit'), submit_default = submit.get('value'),
            setRemoteWrapMargin = function () {
                form.getElement('.remote-wrap').setStyle('margin-right', submit.measure(function () {
                    return this.getSize().x
                }));
            },
            input = document.id('remote-url'),
            current_url,
            validate = new Request.JSON({
                onRequest: function () {
                    if (current_url != this.options.url) {
                        submit.set('value', submit_default);
                        setRemoteWrapMargin();
                        current_url = this.options.url;
                    }
                },
                onSuccess: function (response) {
                    if (response.error) return this.fireEvent('failure', this.xhr);

                    var length = response['content-length'].toInt(10);
                    if (length && length < Files.app.container.parameters.maximum_size) {
                        var size = new Files.Filesize(length).humanize();
                        submit.addClass('btn-primary').set('value', submit_default + ' (' + size + ')');
                        setRemoteWrapMargin();
                    } else {
                        submit.removeClass('btn-primary');
                    }

                },
                onFailure: function (xhr) {
                    var response = JSON.decode(xhr.responseText, true);
                    if (response.code && parseInt(response.code / 100, 10) == 4) {
                        submit.removeClass('btn-primary');
                    }
                    else {
                        submit.addClass('btn-primary');
                    }
                }
            });

        var default_filename,
            validateInput = function () {
                var value = this.value.trim(), host = new URI(value).get('host');
                if (value && host && value.match(host)) {
                    submit.removeProperty('disabled');
                    return true;
                } else {
                    submit.setProperty('disabled', 'disabled');
                    return false;
                }
            },
            validateUrl = function () {
                if (validateInput.call(this)) {
                    if (Files.app.container.parameters.maximum_size) {
                        validate.setOptions({url: Files.app.createRoute({view: 'proxy', url: this.value})}).get();
                    }
                    else {
                        submit.addClass('btn-primary');
                    }

                    if (!filename.get('value') || filename.get('value') == default_filename) {
                        default_filename = new URI(this.value).get('file');
                        filename.set('value', default_filename);
                    }
                } else {
                    submit.set('value', submit_default).removeClass('btn-primary');
                    setRemoteWrapMargin();
                }
            };

        input.addEvent('focus', function () {
            this.set('placeholder', this.get('title')).removeClass('success');
        });
        input.addEvent('blur', validateUrl);


        input.addEvent('change', validateInput);
        if (window.addEventListener) {
            input.addEventListener('input', validateInput);
            input.addEventListener('paste', function () {
                // this.value isn't updated with the value yet, so we delay our callback until it is
                validateUrl.delay(0, this);
            });
        } else {
            input.addEvent('keyup', validateInput);
        }

        var request = new Request.JSON({
            url: Files.app.createRoute({view: 'file', folder: Files.app.getPath()}),
            data: {
                _action: 'add',
                csrf_token: Files.token,
                file: ''
            },
            onRequest: function () {
                submit.setProperty('disabled', 'disabled');
            },
            onSuccess: function (json) {
                if (this.status == 201 && json.status) {
                    var el = json.data.attributes;
                    var cls = Files[el.type.capitalize()];
                    var row = new cls(el);
                    Files.app.grid.insert(row);
                    if (row.type == 'image' && Files.app.grid.layout == 'icons') {
                        var image = row.element.getElement('img');
                        if (image) {
                            row.getThumbnail(function (response) {
                                if (response.item.thumbnail) {
                                    image.set('src', response.item.thumbnail).addClass('loaded').removeClass('loading');
                                    row.element.getElement('.files-node').addClass('loaded').removeClass('loading');
                                }
                            });
                            /* @TODO Test if this is necessary: This is for the thumb margins to recalculate */
                            window.fireEvent('resize');
                        }
                    }
                    Files.app.fireEvent('uploadFile', [row]);
                    submit.removeClass('btn-primary').set('value', submit_default);
                    setRemoteWrapMargin();
                    form.reset();
                    input.set('placeholder', Koowa.translate('Uploaded successfully!')).addClass('success');
                } else {
                    var error = json.error ? json.error : Koowa.translate('Unknown error');
                    alert(Koowa.translate('An error occurred: {error}', {error: error}));
                }
            },
            onFailure: function (xhr) {
                submit.removeProperty('disabled');

                var response = $.parseJSON(xhr.response);
                if (response && response.error) {
                    alert(Koowa.translate('An error occurred: {error}', {error: response.error}));
                } else {
                    alert(Koowa.translate('An error occurred with status code: {code}', {code: xhr.status}));
                }

            }
        });
        form.addEvent('submit', function (e) {
            e.stop();
            request.options.data.file = document.id('remote-url').get('value');
            request.options.url = Files.app.createRoute({
                view: 'file',
                folder: Files.app.getPath(),
                name: document.id('remote-name').get('value')
            });
            request.send();
        });

        //Width fix
        setRemoteWrapMargin();

        //Remove FLOC fix
        document.id('files-upload').getParent().setStyle('visibility', '');
    };

})(kQuery);