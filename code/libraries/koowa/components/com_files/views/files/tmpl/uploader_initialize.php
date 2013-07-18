<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= @helper('com://admin/extman.template.helper.behavior.jquery'); ?>
<script src="media://koowa/com_files/plupload/plupload.core.html5.flash.queue.js" />

<script>
jQuery.noConflict();

window.addEvent('domready', function() {
    var element = jQuery('#files-upload-multi'), browse_label = Files._('Choose File');

    plupload.addI18n({'Add files': browse_label});

    //This trick enables the flash runtime to work properly when the uploader is hidden
    var containershim = 'mushycode'+ Math.floor((Math.random()*10000000000)+1);
    jQuery('<div id="'+containershim+'" class="uploader-flash-container" />').appendTo(jQuery(document.body));

    element.pluploadQueue({
        runtimes: 'html5,flash',
        container: containershim,
        browse_button: 'pickfiles',
        dragdrop: true,
        rename: true,
        url: '', // this is added on the go in BeforeUpload event
        flash_swf_url: 'media://koowa/com_files/plupload/plupload.flash.swf',
        urlstream_upload: true, // required for flash
        multipart_params: {
            action: 'add',
            _token: Files.token
        },
        headers: {
            'X-Requested-With': 'xmlhttprequest'
        },
        preinit: {
            Init: function(){
                if(SqueezeBox.isOpen) {
                    var heightfix = document.id('files-upload').measure(function(){return this.getSize().y;});
                    if(SqueezeBox.size.y != heightfix) SqueezeBox.fx.win.set({height: heightfix});
                }
            },
            Error: function(up, args){
                if(args.code == plupload.INIT_ERROR) {

                    element.append('<span class="warning">'+Files._('<a href="https://google.com/chrome" target="_blank">HTML5 enabled browser</a> or <a href="https://get.adobe.com/flashplayer/" target="_blank">Adobe Flash Player<a/> required for uploading files from your computer.')+'</span>');

                }
            }
        }
    });
    jQuery('#'+containershim).css({'position': '', 'z-index': 1});
    SqueezeBox.addEvent('open', function(){
        window.fireEvent('refresh');
    });

    var uploader = element.pluploadQueue(),
    //We only want to run this once
        exposePlupload = function(uploader) {
            document.id('files-upload').addClass('uploader-files-queued').removeClass('uploader-files-empty');
            if(document.id('files-upload-multi_browse')) {
                document.id('files-upload-multi_browse').set('text', 'Add files');
            }
            uploader.refresh();
            if(SqueezeBox.isOpen) SqueezeBox.resize({y: document.id('files-upload').measure(function(){return this.getSize().y;})}, true);
            uploader.unbind('QueueChanged', exposePlupload);
            //@TODO investigate better event name convention
            window.fireEvent('QueueChanged');
        };

    window.addEvent('refresh', function(){
        uploader.refresh();
    });

    if(uploader.features.dragdrop) {
        document.id('files-upload').addClass('uploader-droppable');

        var timer, cancel= function(e) {
            e.preventDefault();// required by FF + Safari
            e.stopPropagation();
            e.originalEvent.dataTransfer.dropEffect = 'copy'; // tells the browser what drop effect is allowed here
        }, createDragoverHandler = function(container){

            //Create hilite + label
            var focusring = jQuery('<div class="dropzone-focusring"></div>'),
                label = jQuery('<div class="alert alert-success">'+Files._('Drop your file(s) to upload to {{folder}}').replace('{{folder}}', Files.app.title)+'</div>');

            focusring.css({
                display: 'none',
                position: 'absolute',
                backgroundColor: 'hsla(0, 0%, 100%, 0.75)',
                top: 0,
                left: 0,
                bottom: 0,
                right: 0,
                zIndex: 65555,
                borderStyle: 'solid',
                borderWidth: '5px',
                opacity: 0,
                transition: 'opacity 300ms',
                paddingTop: 10,
                textAlign: 'center'
            });
            container.append(focusring);

            //To inherit styling
            jQuery('#files-upload').append(label);
            ['border-radius', 'color', 'background', 'border'].forEach(function(prop){
                label.css(prop, label.css(prop));
            });
            label.css({
                display: 'inline-block',
                margin: '0 auto'
            });
            focusring.append(label);
            focusring.css('border-color', label.css('color')); //border-color too bright

            return function(e){

                e.preventDefault();// required by FF + Safari
                e.originalEvent.dataTransfer.dropEffect = 'copy'; // tells the browser what drop effect is allowed here
                if(focusring.css('display') == 'none') {
                    label.text(Files._('Drop your file(s) to upload to \'{{folder}}\'').replace('{{folder}}', Files.app.title));
                    focusring.css('display', 'block');
                    setTimeout(function(){
                        focusring.css('opacity', 1);
                        if(!jQuery('#files-upload').is(':visible')) {
                            jQuery('#files-canvas').addClass('dropzone-droppable');
                        }
                    }, 1);

                }
                //container.addClass('dropzone-dragover'); //This breaks safaris drag and drop, still unknown why

                //This is a failsafe measure
                clearTimeout(timer);
                timer = setTimeout(function(){
                    jQuery('.dropzone-focusring').css('opacity', 0).css('display', 'none');
                    jQuery('#files-canvas').removeClass('dropzone-droppable');
                }, 300);
            };
        }, createDragleaveHandler = function(container){
            return function(e){
                //@TODO following code is too buggy, it fires multiple times causing a flickr, for now the focusring will only dissappear on drop
                //container.removeClass('dropzone-dragover');
                //jQuery('.dropzone-focusring').css('opacity', 0).css('display', 'none');
            };
        }

        function addSelectedFiles(native_files) {
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

                // Store away gears blob internally
                id = plupload.guid();
                plupload.html5files[id] = file;

                // Expose id, name and size
                files.push(new plupload.File(id, file.fileName || file.name, file.fileSize || file.size)); // fileName / fileSize depricated
            }

            // Trigger FilesAdded event if we added any
            if (files.length) {
                uploader.trigger("FilesAdded", files);
            }
        }

        //Prevent file drops from duplicating due to double drop events
        jQuery('#files-upload-multi_filelist').bind('drop', function(event){
            event.stopPropagation();
            //@TODO implement the rest of the drop code from handler, to remove focusring
            jQuery(document.body).removeClass('dropzone-dragover');
        });

        // Make the document body a dropzone
        var files_canvas = jQuery('#files-canvas'), body = jQuery(document.body);
        body.bind('dragover', createDragoverHandler(body)); //Using dragenter caused inconsistent behavior
        body.bind('dragleave', createDragleaveHandler(body));
        body.bind('dragenter', cancel);
        body.bind('drop', function(event){
            event.preventDefault();
            files_canvas.removeClass('dragover');
            var dataTransfer = event.originalEvent.dataTransfer;

            // Add dropped files
            if (dataTransfer && dataTransfer.files && dataTransfer.files.length) {
                addSelectedFiles(dataTransfer.files);

                if(!jQuery('#files-upload').is(':visible')) {
                    //@TODO the click handler is written in mootools, so we use mootools here
                    document.id('files-show-uploader').fireEvent('click', 'DOMEvent' in window ? new DOMEvent : new Event);
                }
            }
        });
        body.bind('dragend', function(event){
            jQuery('.dropzone-focusring').css('opacity', 0).css('display', 'none');
        });

    } else {
        document.id('files-upload').addClass('uploader-nodroppable');
    }

    if(uploader.features.dragdrop) {
        uploader.bind('QueueChanged', exposePlupload);
    } else {
        document.id('files-upload').setStyle('position', '').addClass('uploader-files-queued').removeClass('uploader-files-empty');
        if(document.id('files-upload-multi_browse')) {
            document.id('files-upload-multi_browse').set('text', 'Add files');
        }
        uploader.refresh();
    }

    uploader.bind('BeforeUpload', function(uploader, file) {
        // set directory in the request
        uploader.settings.url = Files.app.createRoute({
            view: 'file',
            plupload: 1,
            folder: Files.app.getPath()
        });
    });

    uploader.bind('UploadComplete', function(uploader) {
        jQuery('li.plupload_delete a,div.plupload_buttons', element).show();
        uploader.refresh();
    });

    // Keeps track of failed uploads and error messages so we can later display them in the queue
    var failed = {};
    uploader.bind('FileUploaded', function(uploader, file, response) {
        var json = JSON.decode(response.response, true) || {};
        if (json.status) {
            var item = json.item;
            var cls = Files[item.type.capitalize()];
            var row = new cls(item);
            Files.app.grid.insert(row);
            if (row.type == 'image' && Files.app.grid.layout == 'icons') {
                var image = row.element.getElement('img');
                if (image) {
                    row.getThumbnail(function(response) {
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
            var error = json.error ? json.error : 'Unknown error';

            failed[file.id] = error;
        }
    });

    uploader.bind('StateChanged', function(uploader) {
        Object.each(failed, function(error, id) {
            icon = jQuery('#' + id).attr('class', 'plupload_failed').find('a').css('display', 'block');
            if (error) {
                icon.attr('title', error);
            }
        });

    });

    $$('.plupload_clear').addEvent('click', function(e) {
        e.stop();

        if(confirm(<?= json_encode(@text('Are you sure you want to clear the upload queue? This cannot be undone!')) ?>)) {
            // need to work on a clone, otherwise iterator gets confused after elements are removed
            var files = uploader.files.slice(0);
            files.each(function(file) {
                uploader.removeFile(file);
            });
        }
    });

    if (Files.app && Files.app.container) {
        if (Files.app.container.parameters.allowed_extensions) {
            uploader.settings.filters = [
                {title: Files._('All Files'), extensions: Files.app.container.parameters.allowed_extensions.join(',')}
            ];
        }

        if (Files.app.container.parameters.maximum_size) {
            uploader.settings.max_file_size = Files.app.container.parameters.maximum_size;
            var max_size = document.id('upload-max-size');
            if (max_size) {
                max_size.set('html', new Files.Filesize(Files.app.container.parameters.maximum_size).humanize());
            }
        }
    }

    Files.app.uploader = uploader;

    /**
     * Switcher between uploaders
     */
    var toggleForm = function(type) {
        $$('.upload-form').setStyle('display', 'none');
        document.id('files-uploader-'+type).setStyle('display', 'block');

        // Plupload needs to be refreshed if it was hidden
        if (type == 'computer') {
            var uploader = jQuery('#files-upload-multi').pluploadQueue();
            if(!uploader.files.length && !uploader.features.dragdrop) {
                document.id('files-upload').removeClass('uploader-files-queued').addClass('uploader-files-empty');
                if(document.id('files-upload-multi_browse')) {
                    document.id('files-upload-multi_browse').set('text', browse_label);
                    uploader.bind('QueueChanged', exposePlupload);
                }
            }
        } else {
            document.id('remote-url').focus();
        }
        SqueezeBox.fx.win.set({height: document.id('files-upload').measure(function(){return this.getSize().y;})});
        window.fireEvent('refresh');
    };

    $$('.upload-form-toggle').addEvent('click', function(e) {
        var hash = this.get('href').split('#')[1];
        $$('.upload-form-toggle').removeClass('active');
        e.preventDefault();
        this.addClass('active');

        toggleForm(hash);
    });
});

/**
 * Remote file form
 */
window.addEvent('domready', function() {
    var form = document.id('remoteForm'), filename = document.id('remote-name'),
        submit = form.getElement('.remote-submit'), submit_default = submit.get('value'),
        setRemoteWrapMargin = function(){
            form.getElement('.remote-wrap').setStyle('margin-right', submit.measure(function(){return this.getSize().x}));
        },
        input = document.id('remote-url'),
        current_url,
        validate = new Request.JSON({
            onRequest: function(){
                if(current_url != this.options.url) {
                    submit.set('value', submit_default);
                    setRemoteWrapMargin();
                    current_url = this.options.url;
                }
            },
            onSuccess: function(response){
                if(response.error) return this.fireEvent('failure', this.xhr);

                var length = response['content-length'].toInt(10);
                if(length && length < Files.app.container.parameters.maximum_size) {
                    var size = new Files.Filesize(length).humanize();
                    submit.addClass('btn-primary').set('value', submit_default+' ('+size+')');
                    setRemoteWrapMargin();
                } else {
                    submit.removeClass('btn-primary');
                }

            },
            onFailure: function(xhr){
                var response = JSON.decode(xhr.responseText, true);
                if (response.code && parseInt(response.code/100, 10) == 4) {
                    submit.removeClass('btn-primary');
                }
                else {
                    submit.addClass('btn-primary');
                }
            }
        });

    var default_filename,
        validateInput = function(){
            var value = this.value.trim(), host = new URI(value).get('host');
            if(value && host && value.match(host)) {
                submit.removeProperty('disabled');
                return true;
            } else {
                submit.setProperty('disabled', 'disabled');
                return false;
            }
        },
        validateUrl = function() {
            if (validateInput.call(this)) {
                if (Files.app.container.parameters.maximum_size) {
                    validate.setOptions({url: Files.app.createRoute({view: 'proxy', url: this.value})}).get();
                }
                else {
                    submit.addClass('btn-primary');
                }

                if(!filename.get('value') || filename.get('value') == default_filename) {
                    default_filename = new URI(this.value).get('file');
                    filename.set('value', default_filename);
                }
            } else {
                submit.set('value', submit_default).removeClass('btn-primary');
                setRemoteWrapMargin();
            }
        };
    input.addEvent('focus', function(){
        this.set('placeholder', this.get('title')).removeClass('success');
    });
    input.addEvent('blur', validateUrl);


    input.addEvent('change', validateInput);
    if(window.addEventListener) {
        input.addEventListener('input', validateInput);
        input.addEventListener('paste', function(){
            // this.value isn't updated with the value yet, so we delay our callback until it is
            validateUrl.delay(0, this);
        });
    } else {
        input.addEvent('keyup', validateInput);
    }

    var request = new Request.JSON({
        url: Files.app.createRoute({view: 'file', folder: Files.app.getPath()}),
        data: {
            action: 'add',
            _token: Files.token,
            file: ''
        },
        onRequest: function(){
            submit.setProperty('disabled', 'disabled');
        },
        onSuccess: function(json) {
            if (this.status == 201 && json.status) {
                var el = json.item;
                var cls = Files[el.type.capitalize()];
                var row = new cls(el);
                Files.app.grid.insert(row);
                if (row.type == 'image' && Files.app.grid.layout == 'icons') {
                    var image = row.element.getElement('img');
                    if (image) {
                        row.getThumbnail(function(response) {
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
                input.set('placeholder', Files._('Uploaded successfully!')).addClass('success');
            } else {
                var error = json.error ? json.error : Files._('Unknown error');
                alert(Files._('An error occurred: ') + error);
            }
        },
        onFailure: function(xhr) {
            submit.removeProperty('disabled');
            alert(Files._('An error occurred with status code: ')+xhr.status);
        }
    });
    form.addEvent('submit', function(e) {
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
});
</script>