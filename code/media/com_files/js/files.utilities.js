/**
 * @version     $Id$
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

if(!Files) var Files = {};

if (!Files._) {
	Files._ = function(string) {
		return string;
	};
}

// Legacy for Joomla 1.5
if (!Files.utils) {
    Files.utils = {
        append: function(a,b){
            if(window.$extend) return $extend(a,b);
            else return Object.append(a,b);
        },
        typeOf: function(subject) {
            if(window.$type) return $type(subject);
            else return typeOf(subject);
        },
        merge: function(a,b){
            if(window.$merge) return $merge(a,b);
            else {
                var i=Array.slice(arguments);i.unshift({});
                return Object.merge.apply(null,i);
            }
        },
        each: function(a,b,c){
            if(window.$each) return $each(a,b,c);
            else return Object.each(a,b,c);
        }
    };
}

Files.Filesize = new Class({
	Implements: Options,
	options: {
		units: ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB']
	},
	initialize: function(size, options) {
		this.setOptions(options);
		this.size = size;
	},
	humanize: function() {
		var i = 0, size = this.size;
		while (size >= 1024) {
			size /= 1024;
			i++;
		}

		return (i === 0 || size % 1 === 0 ? size : size.toFixed(2)) + ' ' + Files._(this.options.units[i]);
	}
});

Files.FileTypes = {};
Files.FileTypes.map = {
	'audio': ['aif','aiff','alac','amr','flac','ogg','m3u','m4a','mid','mp3','mpa','wav','wma'],
	'video': ['3gp','avi','flv','mkv','mov','mp4','mpg','mpeg','rm','swf','vob','wmv'],
	'image': ['bmp','gif','jpg','jpeg','png','psd','tif','tiff'],
	'document': ['doc','docx','rtf','txt','xls','xlsx','pdf','ppt','pptx','pps','xml'],
	'archive': ['7z','gz','rar','tar','zip']
};

Files.getFileType = function(extension) {
	var type = 'document';
	extension = extension.toLowerCase();
    Files.utils.each(Files.FileTypes.map, function(value, key) {
		if (value.contains(extension)) {
			type = key;
		}
	});
	return type;
};