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
		var id = this.element.attr('id');
		if (!id) {
			id = plupload.guid();
			this.element.attr('id', id);
		}
		this.id = id;
				
		// backup the elements initial state
		this.contents_bak = this.element.html();
		this.element.id = this.element.attr('id');

		this.content = $('.plupload_content', this.element);
		
		// list of files, may become sortable
		this.filelist = $('.plupload_filelist_content', this.element)
			.attr({
				id: id + '_filelist',
				unselectable: 'on'
			});
		

		// buttons
		this.browse_button = $('.plupload_add', this.element).attr('id', id + '_browse').addClass('disabled');
		this.stop_button = $('.plupload_stop', this.element).attr('id', id + '_stop');
		this.start_button = $('.plupload_start', this.element).attr('id', id + '_start')
			.addClass('disabled').hide();

		// progressbar
		this.progressbar = $('.bar', this.element);
		
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
		this._handleUploadErrors();
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

	_handleUploadErrors: function() {
		var self = this, event;

		event = function(uploader, file, result) {
			var response = $.parseJSON(result.response);

			if (response.status === false)
			{
				self.notify('error', response.error ? response.error : Koowa.translate('Unknown error'));

				self.progressbar.removeClass('bar-success').addClass('bar-danger')
					.parent().removeClass('active');

				uploader.stop();
			}
		};

		this.uploader.bind('FileUploaded', event);
		this.uploader.bind('ChunkUploaded', event);
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
			this.filelist.parent().attr('id', this.id + '_dropbox');
			options.drop_element = this.id + '_dropbox';
		}

		this.filelist.on('click', function(e) {
			if ($(e.target).hasClass('plupload_action_icon')) {
				self.removeFile($(e.target).closest('.plupload_file').attr('id'));
				e.preventDefault();
			}
		});

		uploader = this.uploader = uploaders[id] = new plupload.Uploader($.extend(this.options, options));

		// retrieve full normalized set of options
		this.options = uploader.getOption();

		if (self.options.views.thumbs) {
			uploader.settings.required_features.display_media = true;
		}

		// for backward compatibility
		if (self.options.max_file_count) {
			plupload.extend(uploader.getOption('filters'), {
				max_file_count: self.options.max_file_count
			});
		}

		plupload.addFileFilter('max_file_count', function(maxCount, file, cb) {
			if (maxCount <= this.files.length - (this.total.uploaded + this.total.failed)) {
				this._setButtonStatus(this.browse_button, 'disable');
				this.disableBrowse();
				
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
				self.filelist.parent().addClass('plupload_dropbox');
			}

			self._displayThumbs();
			
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
		
		uploader.bind('UploadFile', function(up, file) {
			self._handleFileStatus(file);
		});
		
		uploader.bind('FileUploaded', function(up, file, result) {
			self._handleFileStatus(file);
			self._trigger('uploaded', null, { up: up, file: file, result: result } );
		});
		
		uploader.bind('UploadProgress', function(up, file) {
			self._handleFileStatus(file);
			self._updateTotalProgress();
			self._trigger('progress', null, { up: up, file: file } );
		});
		
		uploader.bind('UploadComplete', function(up, files) {

			self.progressbar.css('width', '100%')
				.parent().removeClass('active');

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
		alert(message);
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
		var up = this.uploader
		, filesPending = up.files.length - (up.total.uploaded + up.total.failed)
		, maxCount = up.getOption('filters').max_file_count || 0
		;
						
		if (plupload.STARTED === up.state) {			
			$([])
				.add(this.stop_button)
				.add('.plupload_started')
					.removeClass('plupload_hidden');

			this._setButtonStatus(this.start_button, 'disable');

			if (!this.options.multiple_queues) {
				this._setButtonStatus(this.browse_button, 'disable');
				up.disableBrowse();
			}
							
			$('.plupload_upload_status', this.element).html(o.sprintf(_('Uploaded %d/%d files'), up.total.uploaded, up.files.length));
		} 
		else if (plupload.STOPPED === up.state) {
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
				this._setButtonStatus(this.browse_button, 'enable');
				up.disableBrowse(false);
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
		var $file = $('#' + file.id), actionClass, iconClass, text;
		
		// since this method might be called asynchronously, file row might not yet be rendered
		if (!$file.length) {
			return;	
		}

		switch (file.status) {
			case plupload.DONE: 
				actionClass = 'plupload_done';
				text = 'done';
				iconClass = 'plupload_action_icon ui-icon ui-icon-circle-check';
				break;
			
			case plupload.FAILED:
				actionClass = 'ui-state-error plupload_failed';
				text = 'failed';
				iconClass = 'plupload_action_icon ui-icon ui-icon-alert';
				break;

			case plupload.QUEUED:
				actionClass = 'plupload_delete';
				text = 'delete';
				iconClass = 'plupload_action_icon ui-icon ui-icon-circle-minus';
				break;

			case plupload.UPLOADING:
				actionClass = 'ui-state-highlight plupload_uploading';
				text = 'uploading';
				iconClass = 'plupload_action_icon ui-icon ui-icon-circle-arrow-w';
				
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
		actionClass += ' ui-state-default plupload_file';

		$file
			.attr('class', actionClass)
			.find('.plupload_action_icon')
				.text(text)
				.attr('class', iconClass);
	},
	
	
	_updateTotalProgress: function() {
		var up = this.uploader;

		// Scroll to end of file list
		this.filelist[0].scrollTop = this.filelist[0].scrollHeight;

		this.progressbar.css('width', up.total.percent + '%');

		if (up.total.percent == 100) {
			this.progressbar.parent().removeClass('active');
		}
		
		this.element
			.find('.plupload_total_status')
				.html(up.total.percent + '%')
				.end()
			.find('.plupload_total_file_size')
				.html(plupload.formatSize(up.total.size))
				.end()
			.find('.plupload_upload_status')
				.html(o.sprintf(_('Uploaded %d/%d files'), up.total.uploaded, up.files.length));
	},


	_displayThumbs: function() {
		var self = this
		, tw, th // thumb width/height
		, cols
		, num = 0 // number of simultaneously visible thumbs
		, thumbs = [] // array of thumbs to preload at any given moment
		, loading = false
		;

		if (!this.options.views.thumbs) {
			return;
		}


		function onLast(el, eventName, cb) {
			var timer;
			
			el.on(eventName, function() {
				clearTimeout(timer);
				timer = setTimeout(function() {
					clearTimeout(timer);
					cb();
				}, 300);
			});
		}


		// calculate number of simultaneously visible thumbs
		function measure() {
			if (!tw || !th) {
				var wrapper = $('.plupload_file:eq(0)', self.filelist);
				tw = wrapper.outerWidth(true);
				th = wrapper.outerHeight(true);
			}

			var aw = self.content.width(), ah = self.content.height();
			cols = Math.floor(aw / tw);
			num =  cols * (Math.ceil(ah / th) + 1);
		}


		function pickThumbsToLoad() {
			// calculate index of virst visible thumb
			var startIdx = Math.floor(self.content.scrollTop() / th) * cols;
			// get potentially visible thumbs that are not yet visible
			thumbs = $('.plupload_file .plupload_file_thumb', self.filelist)
				.slice(startIdx, startIdx + num)
				.filter('.plupload_thumb_toload')
				.get();
		}
		

		function init() {
			function mpl() { // measure, pick, load
				if (self.view_mode !== 'thumbs') {
					return;
				}
				measure();
				pickThumbsToLoad();
				lazyLoad();
			}

			onLast(self.window, 'resize', mpl);
			onLast(self.content, 'scroll',  mpl);

			self.element.on('viewchanged selected', mpl);

			mpl();
		}


		function preloadThumb(file, cb) {
			var img = new o.Image();

			img.onload = function() {
				var thumb = $('#' + file.id + ' .plupload_file_thumb', self.filelist);
				this.embed(thumb[0], { 
					width: self.options.thumb_width, 
					height: self.options.thumb_height, 
					crop: true,
					preserveHeaders: false,
					swf_url: o.resolveUrl(self.options.flash_swf_url),
					xap_url: o.resolveUrl(self.options.silverlight_xap_url)
				});
			};

			img.bind("embedded error", function(e) {
				$('#' + file.id, self.filelist)
					.find('.plupload_file_thumb')
						.removeClass('plupload_thumb_loading')
						.addClass('plupload_thumb_' + e.type)
					;
				this.destroy();
				setTimeout(cb, 1); // detach, otherwise ui might hang (in SilverLight for example)
			});

			$('#' + file.id, self.filelist)
				.find('.plupload_file_thumb')
					.removeClass('plupload_thumb_toload')
					.addClass('plupload_thumb_loading')
				;
			img.load(file.getSource());
		}


		function lazyLoad() {
			if (self.view_mode !== 'thumbs' || loading) {
				return;
			}	

			pickThumbsToLoad();
			if (!thumbs.length) {
				return;
			}

			loading = true;

			preloadThumb(self.getFile($(thumbs.shift()).closest('.plupload_file').attr('id')), function() {
				loading = false;
				lazyLoad();
			});
		}

		// this has to run only once to measure structures and bind listeners
		this.element.on('selected', function onselected() {
			self.element.off('selected', onselected);
			init();
		});
	},


	_addFiles: function(files) {
		var self = this, file_html, html = '';

		file_html = '<li class="plupload_file ui-state-default plupload_delete" id="{id}" style="width:{thumb_width}px;">' +
			'<div class="plupload_file_thumb plupload_thumb_toload" style="width: {thumb_width}px; height: {thumb_height}px;">' +
				'<div class="plupload_file_dummy ui-widget-content" style="line-height: {thumb_height}px;"><span class="ui-state-disabled">{ext} </span></div>' +
			'</div>' +
			'<div class="plupload_file_status">' +
				'<div class="plupload_file_progress ui-widget-header" style="width: 0%"> </div>' + 
				'<span class="plupload_file_percent">{percent} </span>' +
			'</div>' +
			'<div class="plupload_file_name" title="{name}">' +
				'<span class="plupload_file_name_wrapper">{name} </span>' +
			'<div class="plupload_action_icon">delete</div>' +
			'</div>' +						
			'<div class="plupload_file_action">' +
			'</div>' +
			'<div class="plupload_file_size">{size} </div>' +
			'<div class="plupload_file_fields"> </div>' +
		'</li>';

		if (plupload.typeOf(files) !== 'array') {
			files = [files];
		}

		$.each(files, function(i, file) {
			var ext = o.Mime.getFileExtension(file.name) || 'none';

			html += file_html.replace(/\{(\w+)\}/g, function($0, $1) {
				switch ($1) {
					case 'thumb_width':
					case 'thumb_height':
						return self.options[$1];
					
					case 'size':
						return plupload.formatSize(file.size);

					case 'ext':
						return ext;

					default:
						return file[$1] || '';
				}
			});
		});

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
