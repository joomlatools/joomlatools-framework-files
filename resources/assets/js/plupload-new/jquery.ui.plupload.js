/**
 * jquery.ui.plupload.js
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 *
 * Depends:
 *	jquery.ui.widget.js
 *
 */

 /* global jQuery:true */

/**
jQuery UI based implementation of the Plupload API - multi-runtime file uploading API.

To use the widget you must include _jQuery_ and _jQuery UI_ `ui.widget`.

In general the widget is designed the way that you do not usually need to do anything to it after you instantiate it. 
But! You still can intervenue, to some extent, in case you need to. Although, due to the fact that widget is based on 
_jQuery UI_ widget factory, there are some specifics. See examples below for more details.

@example
	<!-- Instantiating: -->
	<div id="uploader">
		<p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
	</div>

	<script>
		$('#uploader').plupload({
			url : '../upload.php',
			filters : [
				{title : "Image files", extensions : "jpg,gif,png"}
			],
			rename: true,
			flash_swf_url : '../../js/Moxie.swf',
			silverlight_xap_url : '../../js/Moxie.xap',
		});
	</script>

@example
	// Invoking methods:
	$('#uploader').plupload(options);

	// Display welcome message in the notification area
	$('#uploader').plupload('notify', 'info', "This might be obvious, but you need to click 'Add Files' to add some files.");

@example
	// Subscribing to the events...
	// ... on initialization:
	$('#uploader').plupload({ 
		...
		viewchanged: function(event, args) {
			// stuff ...
		}
	});
	// ... or after initialization
	$('#uploader').on("viewchanged", function(event, args) {
		// stuff ...
	});

@class UI.Plupload
@constructor
@param {Object} settings For detailed information about each option check documentation.
	@param {String} settings.url URL of the server-side upload handler.
	@param {Number|String} [settings.chunk_size=0] Chunk size in bytes to slice the file into. Shorcuts with b, kb, mb, gb, tb suffixes also supported. `e.g. 204800 or "204800b" or "200kb"`. By default - disabled.
	@param {String} [settings.file_data_name="file"] Name for the file field in Multipart formated message.
	@param {Object} [settings.filters={}] Set of file type filters.
		@param {Array} [settings.filters.mime_types=[]] List of file types to accept, each one defined by title and list of extensions. `e.g. {title : "Image files", extensions : "jpg,jpeg,gif,png"}`. Dispatches `plupload.FILE_EXTENSION_ERROR`
		@param {String|Number} [settings.filters.max_file_size=0] Maximum file size that the user can pick, in bytes. Optionally supports b, kb, mb, gb, tb suffixes. `e.g. "10mb" or "1gb"`. By default - not set. Dispatches `plupload.FILE_SIZE_ERROR`.
		@param {Boolean} [settings.filters.prevent_duplicates=false] Do not let duplicates into the queue. Dispatches `plupload.FILE_DUPLICATE_ERROR`.
		@param {Number} [settings.filters.max_file_count=0] Limit the number of files that can reside in the queue at the same time (default is 0 - no limit).
	@param {String} [settings.flash_swf_url] URL of the Flash swf.
	@param {Object} [settings.headers] Custom headers to send with the upload. Hash of name/value pairs.
	@param {Number|String} [settings.max_file_size] Maximum file size that the user can pick, in bytes. Optionally supports b, kb, mb, gb, tb suffixes. `e.g. "10mb" or "1gb"`. By default - not set. Dispatches `plupload.FILE_SIZE_ERROR`.
	@param {Number} [settings.max_retries=0] How many times to retry the chunk or file, before triggering Error event.
	@param {Boolean} [settings.multipart=true] Whether to send file and additional parameters as Multipart formated message.
	@param {Object} [settings.multipart_params] Hash of key/value pairs to send with every file upload.
	@param {Boolean} [settings.multi_selection=true] Enable ability to select multiple files at once in file dialog.
	@param {Boolean} [settings.prevent_duplicates=false] Do not let duplicates into the queue. Dispatches `plupload.FILE_DUPLICATE_ERROR`.
	@param {String|Object} [settings.required_features] Either comma-separated list or hash of required features that chosen runtime should absolutely possess.
	@param {Object} [settings.resize] Enable resizng of images on client-side. Applies to `image/jpeg` and `image/png` only. `e.g. {width : 200, height : 200, quality : 90, crop: true}`
		@param {Number} [settings.resize.width] If image is bigger, it will be resized.
		@param {Number} [settings.resize.height] If image is bigger, it will be resized.
		@param {Number} [settings.resize.quality=90] Compression quality for jpegs (1-100).
		@param {Boolean} [settings.resize.crop=false] Whether to crop images to exact dimensions. By default they will be resized proportionally.
	@param {String} [settings.runtimes="html5,flash,silverlight,html4"] Comma separated list of runtimes, that Plupload will try in turn, moving to the next if previous fails.
	@param {String} [settings.silverlight_xap_url] URL of the Silverlight xap.
	@param {Boolean} [settings.unique_names=false] If true will generate unique filenames for uploaded files.

	@param {Boolean} [settings.autostart=false] Whether to auto start uploading right after file selection.
	@param {Boolean} [settings.dragdrop=true] Enable ability to add file to the queue by drag'n'dropping them from the desktop.
	@param {Boolean} [settings.rename=false] Enable ability to rename files in the queue.
	@param {Object} [settings.buttons] Control the visibility of functional buttons. 
		@param {Boolean} [settings.buttons.browse=true] Display browse button.
		@param {Boolean} [settings.buttons.start=true] Display start button.
		@param {Boolean} [settings.buttons.stop=true] Display stop button. 
	@param {Object} [settings.views] Control various views of the file queue.
		@param {Boolean} [settings.views.list=true] Enable list view.
		@param {Boolean} [settings.views.thumbs=false] Enable thumbs view.
		@param {String} [settings.views.default='list'] Default view.
		@param {Boolean} [settings.views.remember=true] Whether to remember the current view (requires jQuery Cookie plugin).
	@param {Boolean} [settings.multiple_queues=true] Re-activate the widget after each upload procedure.
*/
;(function(window, document, plupload, o, $) {

/**
Dispatched when the widget is initialized and ready.

@event ready
@param {plupload.Uploader} uploader Uploader instance sending the event.
*/

/**
Dispatched when file dialog is closed.

@event selected
@param {plupload.Uploader} uploader Uploader instance sending the event.
@param {Array} files Array of selected files represented by plupload.File objects
*/

/**
Dispatched when file dialog is closed.

@event removed
@param {plupload.Uploader} uploader Uploader instance sending the event.
@param {Array} files Array of removed files represented by plupload.File objects
*/

/**
Dispatched when upload is started.

@event started
@param {plupload.Uploader} uploader Uploader instance sending the event.
*/

/**
Dispatched when upload is stopped.

@event stopped
@param {plupload.Uploader} uploader Uploader instance sending the event.
*/

/**
Dispatched during the upload process.

@event progress
@param {plupload.Uploader} uploader Uploader instance sending the event.
@param {plupload.File} file File that is being uploaded (includes loaded and percent properties among others).
	@param {Number} size Total file size in bytes.
	@param {Number} loaded Number of bytes uploaded of the files total size.
	@param {Number} percent Number of percentage uploaded of the file.
*/

/**
Dispatched when file is uploaded.

@event uploaded
@param {plupload.Uploader} uploader Uploader instance sending the event.
@param {plupload.File} file File that was uploaded.
	@param {Enum} status Status constant matching the plupload states QUEUED, UPLOADING, FAILED, DONE.
*/

/**
Dispatched when upload of the whole queue is complete.

@event complete
@param {plupload.Uploader} uploader Uploader instance sending the event.
@param {Array} files Array of uploaded files represented by plupload.File objects
*/

/**
Dispatched when the view is changed, e.g. from `list` to `thumbs` or vice versa.

@event viewchanged
@param {plupload.Uploader} uploader Uploader instance sending the event.
@param {String} type Current view type.
*/

/**
Dispatched when error of some kind is detected.

@event error
@param {plupload.Uploader} uploader Uploader instance sending the event.
@param {String} error Error message.
@param {plupload.File} file File that was uploaded.
	@param {Enum} status Status constant matching the plupload states QUEUED, UPLOADING, FAILED, DONE.
*/

var uploaders = {};	
	
function _(str) {
	return plupload.translate(str) || str;
}


$.widget("koowa.koowaUploader", {

	widgetEventPrefix: '',
	
	contents_bak: '',
		
	options: {
		filters: {
			prevent_duplicates: true
		},
		
		// widget specific
		buttons: {
			browse: true,
			start: true,
			stop: true	
		},
		
		views: {
			list: false,
			thumbs: true,
			active: 'thumbs',
			remember: true // requires: https://github.com/carhartl/jquery-cookie, otherwise disabled even if set to true
		},

		thumb_width: 100,
		thumb_height: 60,

		headers: {
			'X-Requested-With': 'XMLHttpRequest'
		},

		runtimes: 'html5',
		chunking: false,

		multi_selection: true,
		multiple_queues: true, // re-use widget by default
		dragdrop : true, 
		autostart: false,
		rename: true,

		preinit: {}
	},
	
	FILE_COUNT_ERROR: -9001,
	
	_create: function() {
		var self = this,
			id = this.element.attr('id');
		if (!id) {
			id = plupload.guid();
			this.element.attr('id', id);
		}
		this.id = id;
				
		// backup the elements initial state
		this.contents_bak = this.element.html();
		this.element.id = this.element.attr('id');

		this.content = $('.plupload_content', this.element);

		this.templates = {
			'file-single':   $('.js-file-single-template').text(),
			'file-multiple': $('.js-file-multiple-template').text(),
			'upload-pending': $('.js-upload-pending-template').text(),
			'upload-uploading': $('.js-upload-uploading-template').text(),
			'upload-finished': $('.js-upload-finished-template').text(),
			'upload-empty-single': $('.js-upload-empty-single-template').text(),
			'upload-empty-multiple': $('.js-upload-empty-multiple-template').text()
		};

		var suffix = this.options.multi_selection ? 'multiple' : 'single';

		$('.js-content', this.element).html(this._renderTemplate(this.templates['upload-empty-'+suffix]));

		// file template
		this.file_template = this.templates['file-'+suffix];

		// list of files
		this.filelist = $(suffix == 'single' ? '.js-content' : '.js-filelist-multiple', this.element)
			.attr({
				id: id + '_filelist',
				unselectable: 'on'
			});

		// buttons
		this.browse_button = $('.plupload_add', this.element).attr('id', id + '_browse').addClass('disabled');
		this.stop_button = $('.plupload_stop', this.element).attr('id', id + '_stop');
		this.start_button = $('.plupload_start', this.element).attr('id', id + '_start')
			.hide();

		this.clear_button = $('.js-clear-queue', this.element).click(function(event) {
			event.preventDefault();

			self.clearQueue();
		});

		this.element.on('click', '.js-open-info', function() {
			$('.k-upload__details-button__view').toggle(0);
			$('.k-upload__details-button__close').toggle(0);
			$('.k-upload').toggleClass('has-open-info');
		}).on('click', '.js-remove-file', function() {
			$(this).closest('tr').children('td').slideUp('fast');
		}).on('click', '.js-trigger-upload', function(e) {
			e.preventDefault();

			self.start_button.trigger('click');
		});

		this.browse_button.data('caption-original', this.browse_button.text());

		// progressbar
		this.progressbar = $('.bar', this.element);

		// error message
		this.message = $('.k-upload__body-message', this.element);
		this.message.find('button').click(function() {
			self.element.removeClass('has-error');
		});
		
		// counter
		this.counter = $('.plupload_count', this.element)
			.attr({
				id: id + '_count',
				name: id + '_count'
			});

		if (this.options.filters.extensions) {
			var extensions = this.options.filters.extensions;

			this.options.filters.mime_types = [{
				title: Koowa.translate('All Files'),
				extensions: typeof extensions === 'string' ? extensions : extensions.join(',')
			}];
		}

		if (this.options.chunking) {
			this.options.preinit.Init = this._setChunking;
		}

		// initialize uploader instance
		this._initUploader();

		this.view_mode = 'thumbs';

		this._setMaxCount(this.options.multi_selection ? 100 : 1);
	},

	_setChunking: function(uploader) {
		var limit = uploader.settings.maximum_size,
			server_limit = uploader.settings.server_limit;

		// Allow chunking
		if (uploader.runtime === 'html5' && uploader.features.chunks) {

			// Leave 1 mb for the rest of the POST data
			var chunk_size = Math.max(1048576, server_limit - 1048576);

			if (chunk_size > 33554432) {
				chunk_size = 33554432; // use 32 mb chunks at the maximum
			}

			uploader.setOption('chunk_size', chunk_size);
		}
		else {
			if (!limit || limit == 0 || (limit > server_limit)) {
				limit = server_limit - 1048576;
			}
		}

		if (limit > 0) {
			uploader.setOption('max_file_size', limit);
		}
	},

	_setMaxCount: function(count) {
		var queue_lock = false;

		this.uploader.bind('QueueChanged', function(uploader) {
			if (queue_lock) {
				return;
			}

			queue_lock = true;
			uploader.splice(0, uploader.files.length-count);
			queue_lock = false;
		});
	},

	_initUploader: function() {
		var self = this
		, id = this.id
		, uploader
		, options = {
			container: id,
			browse_button: id + '_browse'
		}
		;

		if (self.options.dragdrop) {
			options.drop_element = this.id;
		}

		this.filelist.on('click', function(e) {
			if ($(e.target).hasClass('js-remove-file')) {
				self.removeFile($(e.target).closest('.js-uploader-file').attr('id'));
				e.preventDefault();

				if (!self.uploader.files.length) {
					self.element.removeClass('has-open-info');
				}
			}
		});

		uploader = this.uploader = uploaders[id] = new plupload.Uploader($.extend(this.options, options));

		// retrieve full normalized set of options
		this.options = uploader.getOption();

		if (self.options.views.thumbs) {
			//uploader.settings.required_features.display_media = true;
		}

		// for backward compatibility
		if (self.options.max_file_count) {
			plupload.extend(uploader.getOption('filters'), {
				max_file_count: self.options.max_file_count
			});
		}

		plupload.addFileFilter('max_file_count', function(maxCount, file, cb) {
			if (maxCount <= this.files.length - (this.total.uploaded + this.total.failed)) {
				self.disable();
				
				this.trigger('Error', {
					code : self.FILE_COUNT_ERROR,
					message : _("File count error."),
					file : file
				});
				cb(false);
			} else {
				cb(true);
			}
		});


		uploader.bind('Error', function(up, err) {			
			var message, details = "";

			message = '<strong>' + err.message + '</strong>';
				
			switch (err.code) {
				case plupload.FILE_EXTENSION_ERROR:
					details = o.sprintf(_("File: %s"), err.file.name);
					break;
				
				case plupload.FILE_SIZE_ERROR:
					details = o.sprintf(_("File: %s, size: %d, max file size: %d"), err.file.name,  plupload.formatSize(err.file.size), plupload.formatSize(plupload.parseSize(up.getOption('filters').max_file_size)));
					break;

				case plupload.FILE_DUPLICATE_ERROR:
					details = o.sprintf(_("%s already present in the queue."), err.file.name);
					break;
					
				case self.FILE_COUNT_ERROR:
					details = o.sprintf(_("Upload element accepts only %d file(s) at a time. Extra files were stripped."), up.getOption('filters').max_file_count || 0);
					break;
				
				case plupload.IMAGE_FORMAT_ERROR :
					details = _("Image format either wrong or not supported.");
					break;	
				
				case plupload.IMAGE_MEMORY_ERROR :
					details = _("Runtime ran out of available memory.");
					break;
				
				/* // This needs a review
				case plupload.IMAGE_DIMENSIONS_ERROR :
					details = o.sprintf(_('Resoultion out of boundaries! <b>%s</b> runtime supports images only up to %wx%hpx.'), up.runtime, up.features.maxWidth, up.features.maxHeight);
					break;	*/
											
				case plupload.HTTP_ERROR:
					details = _("Upload URL might be wrong or doesn't exist.");
					break;
			}

			message += " <br /><i>" + details + "</i>";

			self._trigger('error', null, { up: up, error: err } );

			// do not show UI if no runtime can be initialized
			if (err.code === plupload.INIT_ERROR) {
				setTimeout(function() {
					self.destroy();
				}, 1);
			} else {
				self.notify('error', message);
			}
		});

		
		uploader.bind('PostInit', function(up) {
			self.element.addClass('is-initialized');

			// all buttons are optional, so they can be disabled and hidden
			if (!self.options.buttons.browse) {
				self._setButtonStatus(self.browse_button, 'disable');
				self.browse_button.hide();
				up.disableBrowse(true);
			} else {
				self._setButtonStatus(self.browse_button, 'enable');
			}
			
			if (!self.options.buttons.start) {
				self._setButtonStatus(self.start_button, 'disable');
				self.start_button.hide();
			} 
			
			if (!self.options.buttons.stop) {
				self._setButtonStatus(self.stop_button, 'disable');
				self.stop_button.hide();
			}
				
			if (!self.options.unique_names && self.options.rename) {
				self._enableRenaming();	
			}

			if (self.options.dragdrop && up.features.dragdrop) {
				var addHoverClass = function() {
					self.element.addClass("has-drag-hover");
				}, removeHoverClass = function() {
					self.element.removeClass("has-drag-hover");
				};

				self.element.on('drop', removeHoverClass);
				self.element.on('dragend', removeHoverClass);
				self.element.on('dragleave', removeHoverClass);

				self.element.on('dragenter', addHoverClass);
				self.element.on('dragover', addHoverClass);

				self.element.addClass('has-dragdrop-support');
			}
			
			self.start_button.click(function(e) {
				e.preventDefault();

				if (!self.start_button.hasClass('disabled')) {
					self.start();
				}
			});

			self.stop_button.click(function(e) {
				self.stop();
				e.preventDefault();
			});

			self._trigger('ready', null, { up: up });
		});
		
		// uploader internal events must run first 
		uploader.init();

		uploader.bind('FileFiltered', function(up, file) {
			self._addFiles(file);
		});

		
		uploader.bind('FilesAdded', function(up, files) {
			self._trigger('selected', null, { up: up, files: files } );

			self._trigger('updatelist', null, { filelist: self.filelist });
			
			if (self.options.autostart) {
				// set a little delay to make sure that QueueChanged triggered by the core has time to complete
				setTimeout(function() {
					self.start();
				}, 10);
			}
		});
		
		uploader.bind('FilesRemoved', function(up, files) {

			$.each(files, function(i, file) {
				$('#' + file.id).toggle("highlight", function() {
					$(this).remove();
				});
			});

			self._trigger('updatelist', null, { filelist: self.filelist });
			self._trigger('removed', null, { up: up, files: files } );
		});
		
		uploader.bind('QueueChanged', function() {
			self._handleState();
		});

		uploader.bind('StateChanged', function(up) {
			self._handleState();
			if (plupload.STARTED === up.state) {
				self._trigger('started', null, { up: self.uploader });
			} else if (plupload.STOPPED === up.state) {
				self._trigger('stopped', null, { up: self.uploader });
			}
		});

		var _handleUploadErrors = function(uploader, file, result) {
			var response = $.parseJSON(result.response);

			if (response.status === false)
			{
				var error = response.error ? response.error : Koowa.translate('Unknown error');
				self.notify('error', error);

				self.progressbar.removeClass('bar-success').addClass('bar-danger');

				file.status = plupload.FAILED;
				file.error_message = error;

				uploader.stop();
			}
		};

		uploader.bind('ChunkUploaded', _handleUploadErrors);

		uploader.bind('UploadFile', function(up, file) {
			self._handleFileStatus(file);
		});
		
		uploader.bind('FileUploaded', function(up, file, result) {
			_handleUploadErrors(up, file, result);
			self._handleFileStatus(file);
			self._trigger('uploaded', null, { up: up, file: file, result: result } );
		});
		
		uploader.bind('UploadProgress', function(up, file) {
			self._handleFileStatus(file);
			self._updateTotalProgress();
			self._trigger('progress', null, { up: up, file: file } );
		});
		
		uploader.bind('UploadComplete', function(up, files) {
			if (!self.progressbar.hasClass('bar-danger')) {
				self.progressbar.addClass('bar-success');
			}

			self._addFormFields();		
			self._trigger('complete', null, { up: up, files: files } );
		});
	},

	
	_setOption: function(key, value) {
		var self = this;

		if (key == 'buttons' && typeof(value) == 'object') {	
			value = $.extend(self.options.buttons, value);
			
			if (!value.browse) {
				self.browse_button.hide();
				self._setButtonStatus(self.browse_button, 'disable');
				self.uploader.disableBrowse(true);
			} else {
				self._setButtonStatus(self.browse_button, 'enable');
				self.browse_button.show();
				self.uploader.disableBrowse(false);
			}
			
			if (!value.start) {
				self._setButtonStatus(self.start_button, 'disable');
				self.start_button.hide();
			} else {
				self._setButtonStatus(self.start_button, 'enable');
				if (!self.options.autostart) {
					self.start_button.show();
				}
			}
			
			if (!value.stop) {
				self._setButtonStatus(self.stop_button, 'disable');
				self.stop_button.hide();
			} else {
				self._setButtonStatus(self.start_button, 'enable');
				if (!self.options.autostart) {
					self.start_button.show();
				}
			}
		}
		
		self.uploader.setOption(key, value);	
	},

	
	/**
	Start upload. Triggers `start` event.

	@method start
	*/
	start: function() {
		this.uploader.start();
	},

	
	/**
	Stop upload. Triggers `stop` event.

	@method stop
	*/
	stop: function() {
		this.uploader.stop();
	},


	/**
	Enable browse button.

	@method enable
	*/
	enable: function() {
		this._setButtonStatus(this.browse_button, 'enable');
		this.uploader.disableBrowse(false);
	},


	/**
	Disable browse button.

	@method disable
	*/
	disable: function() {
		this._setButtonStatus(this.browse_button, 'disable');
		this.uploader.disableBrowse(true);
	},

	
	/**
	Retrieve file by its unique id.

	@method getFile
	@param {String} id Unique id of the file
	@return {plupload.File}
	*/
	getFile: function(id) {
		var file;
		
		if (typeof id === 'number') {
			file = this.uploader.files[id];	
		} else {
			file = this.uploader.getFile(id);	
		}
		return file;
	},

	/**
	Return array of files currently in the queue.
	
	@method getFiles
	@return {Array} Array of files in the queue represented by plupload.File objects
	*/
	getFiles: function() {
		return this.uploader.files;
	},

	
	/**
	Remove the file from the queue.

	@method removeFile
	@param {plupload.File|String} file File to remove, might be specified directly or by its unique id
	*/
	removeFile: function(file) {
		if (plupload.typeOf(file) === 'string') {
			file = this.getFile(file);
		}
		this.uploader.removeFile(file);
	},

	
	/**
	Clear the file queue.

	@method clearQueue
	*/
	clearQueue: function() {
		this.uploader.splice();

		this.element.removeClass('has-open-info');
	},


	/**
	Retrieve internal plupload.Uploader object (usually not required).

	@method getUploader
	@return {plupload.Uploader}
	*/
	getUploader: function() {
		return this.uploader;
	},


	/**
	Trigger refresh procedure, specifically browse_button re-measure and re-position operations.
	Might get handy, when UI Widget is placed within the popup, that is constantly hidden and shown
	again - without calling this method after each show operation, dialog trigger might get displaced
	and disfunctional.

	@method refresh
	*/
	refresh: function() {
		this.uploader.refresh();
	},


	/**
	Display a message in notification area.

	@method notify
	@param {Enum} type Type of the message, either `error` or `info`
	@param {String} message The text message to display.
	*/
	notify: function(type, message) {
		this.element.addClass('has-error');
		this.message.find('.k-upload__message__body').html(message);
	},

	
	/**
	Destroy the widget, the uploader, free associated resources and bring back original html.

	@method destroy
	*/
	destroy: function() {		
		// destroy uploader instance
		this.uploader.destroy();

		// unbind all button events
		$('.plupload_button', this.element).unbind();

		
		// restore the elements initial state
		this.element
			.empty()
			.html(this.contents_bak);
		this.contents_bak = '';

		$.Widget.prototype.destroy.apply(this);
	},
	
	
	_handleState: function() {
		var self = this
		, up = this.uploader
		, filesPending = up.files.length - (up.total.uploaded + up.total.failed)
		, maxCount = up.getOption('filters').max_file_count || 0
		;
						
		if (plupload.STARTED === up.state) {
			this.progressbar.removeClass('bar-danger bar-success').parent().addClass('active is-uploading');

			$([])
				.add(this.stop_button)
				.add('.plupload_started')
					.removeClass('plupload_hidden');

			this._setButtonStatus(this.start_button, 'disable');

			if (!this.options.multiple_queues) {
				this.disable();
			}
							
			$('.plupload_upload_status', this.element).html(o.sprintf(_('Uploaded %d/%d files'), up.total.uploaded, up.files.length));
		} 
		else if (plupload.STOPPED === up.state) {
			setTimeout(function() {
				self.progressbar.parent().removeClass('active is-uploading');
			}, 500);

			$([])
				.add(this.stop_button)
				.add('.plupload_started')
					.addClass('plupload_hidden');

			if (filesPending) {
				this._setButtonStatus(this.start_button, 'enable');
			} else {
				this._setButtonStatus(this.start_button, 'disable');
			}

			// if max_file_count defined, only that many files can be queued at once
			if (this.options.multiple_queues && maxCount && maxCount > filesPending) {
				this.enable();
			}

			if (!up.files.length) {
				this.browse_button.text(this.browse_button.data('caption-original'));

				this.element.removeClass('has-file');
			} else if (this.options.multi_selection === false) {
				this.browse_button.text(this.browse_button.data('caption-update'));

				this.element.addClass('has-file');
			} else {
				if (!this.options.autostart) {
					this.start_button.show();
				}

				this.element.addClass('has-file');
			}

			this._updateTotalProgress();
		}

		up.refresh();
	},

	_setButtonStatus: function(button, status) {
		if (status === 'enable') {
			button.removeClass('disabled');
		} else {
			button.addClass('disabled');
		}
	},
	
	_handleFileStatus: function(file) {
		var $file = $('#' + file.id), text;

		// since this method might be called asynchronously, file row might not yet be rendered
		if (!$file.length) {
			return;	
		}

		switch (file.status) {
			case plupload.DONE:
				text = 'done';
				break;
			
			case plupload.FAILED:
				text = 'failed';
				break;

			case plupload.QUEUED:
				text = 'delete';
				break;

			case plupload.UPLOADING:
				text = 'uploading';
				
				// scroll uploading file into the view if its bottom boundary is out of it
				var scroller = $('.plupload_scroll', this.element)
				, scrollTop = scroller.scrollTop()
				, scrollerHeight = scroller.height()
				, rowOffset = $file.position().top + $file.height()
				;
					
				if (scrollerHeight < rowOffset) {
					scroller.scrollTop(scrollTop + rowOffset - scrollerHeight);
				}		

				// Set file specific progress
				$file
					.find('.plupload_file_percent')
						.html(file.percent + '%')
						.end()
					.find('.plupload_file_progress')
						.css('width', file.percent + '%')
						.end()
					.find('.plupload_file_size')
						.html(plupload.formatSize(file.size));			
				break;
		}

		$file.find('.plupload_file_status').removeClass('is-uploading is-in-queue').addClass('is-' + text).text(text);
	},
	
	
	_updateTotalProgress: function() {
		var up = this.uploader;

		// Scroll to end of file list
		//this.filelist[0].scrollTop = this.filelist[0].scrollHeight;

		this.progressbar.css('width', up.total.percent + '%');

		if (up.total.percent == 100) {
			this.progressbar.parent().removeClass('active');
		}

		if (this.options.multi_selection) {
			var template = '';

			if (plupload.STARTED === up.state) {
				template = this.templates['upload-uploading'];
			}
			else if (up.total.percent == 100) {
				template = this.templates['upload-finished'];
			}
			else if (up.files.length) {
				template = this.templates['upload-pending'];
			} else {
				template = this.options.multi_selection ? this.templates['upload-empty-multiple'] : this.templates['upload-empty-single'];
			}

			var html = this._renderTemplate(template, {
				'percent' : up.total.percent,
				'size'    : plupload.formatSize(up.total.size),
				'uploaded': up.total.uploaded,
				'total'   : up.files.length,
				'remaining': (up.files.length - (up.total.uploaded + up.total.failed)),
				'failed'  : up.total.failed
			});

			$('.js-content', this.element).html(html);
		}
	},

	_renderTemplate: function(template, replacements, fallback) {
		var html, replacement, self = this;

		if (!fallback) {
			fallback = self.uploader;
		}

		html = template.replace(/\{(\w+)\}/g, function($0, $1) {
			if (replacements.hasOwnProperty($1)) {
				replacement = replacements[$1];
			} else {
				replacement = fallback[$1] || '';
			}

			if (typeof replacement === 'function') {
				replacement = replacement($0, $1);
			}

			return replacement;
		});

		return html;
	},


	_addFiles: function(files) {
		var self = this, html = '';

		if (plupload.typeOf(files) !== 'array') {
			files = [files];
		}

		$.each(files, function(i, file) {
			var ext = o.Mime.getFileExtension(file.name) || 'none';

			html += self._renderTemplate(self.file_template, {
				'ext'  : ext,
				'size' : plupload.formatSize(file.size)
			}, file);
		});

		if (!self.options.multi_selection) {
			self.filelist.empty();
		}

		self.filelist.append(html);
	},


	_addFormFields: function() {
		var self = this;

		// re-add from fresh
		$('.plupload_file_fields', this.filelist).html('');

		plupload.each(this.uploader.files, function(file, count) {
			var fields = ''
			, id = self.id + '_' + count
			;

			if (file.target_name) {
				fields += '<input type="hidden" name="' + id + '_tmpname" value="'+plupload.xmlEncode(file.target_name)+'" />';
			}
			fields += '<input type="hidden" name="' + id + '_name" value="'+plupload.xmlEncode(file.name)+'" />';
			fields += '<input type="hidden" name="' + id + '_status" value="' + (file.status === plupload.DONE ? 'done' : 'failed') + '" />';

			$('#' + file.id).find('.plupload_file_fields').html(fields);
		});

		this.counter.val(this.uploader.files.length);
	},
	
	_enableRenaming: function() {
		var self = this;

		this.filelist.dblclick(function(e) {
			var nameSpan = $(e.target), nameInput, file, parts, name, ext = "";

			if (!nameSpan.hasClass('plupload_file_name_wrapper')) {
				return;
			}
		
			// Get file name and split out name and extension
			file = self.uploader.getFile(nameSpan.closest('.plupload_file')[0].id);
			name = file.name;
			parts = /^(.+)(\.[^.]+)$/.exec(name);
			if (parts) {
				name = parts[1];
				ext = parts[2];
			}

			// Display input element
			nameInput = $('<input class="plupload_file_rename" type="text" />').width(nameSpan.width()).insertAfter(nameSpan.hide());
			nameInput.val(name).blur(function() {
				nameSpan.show().parent().scrollLeft(0).end().next().remove();
			}).keydown(function(e) {
				var nameInput = $(this);

				if ($.inArray(e.keyCode, [13, 27]) !== -1) {
					e.preventDefault();

					// Rename file and glue extension back on
					if (e.keyCode === 13) {
						file.name = nameInput.val() + ext;
						nameSpan.html(file.name);
					}
					nameInput.blur();
				}
			})[0].focus();
		});
	}
});

} (window, document, plupload, mOxie, kQuery));
