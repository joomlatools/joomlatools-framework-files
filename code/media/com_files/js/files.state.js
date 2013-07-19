/**
 * @version     $Id: file.php 1304 2011-12-13 22:46:32Z ercanozkaya $
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

if (!Files) var Files = {};

Files.State = new Class({
	Implements: Options,
	data: {},
	defaults: {},
	options: {
		defaults: {}
	},
	initialize: function(options) {
		this.setOptions(options);

		if (this.options.data) {
            Files.utils.append(this.data, this.options.data);
		}
		if (this.options.defaults) {
            Files.utils.append(this.defaults, this.options.defaults);
            Files.utils.append(this.data, this.defaults);
		}
	},
	getData: function() {
		return this.data;
	},
	setDefaults: function() {
		this.set(this.defaults);

		return this;
	},
	set: function(key, value) {
		if (Files.utils.typeOf(key) == 'object') {
            Files.utils.append(this.data, key);
		} else {
			this.data[key] = value;
		}

		return this;
	},
	get: function(key, def) {
		return this.data[key] || def;
	},
	unset: function(key) {
		delete this.data[key];
	}
});