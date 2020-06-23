/**
 * @package     Joomlatools Framework Files
 * @copyright   Copyright (C) 2020 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

var Files = Files || {};

(function($) {

/**
 * Makes elements into Plyr
 *
 * @example new Files.Plyr('.plyr', { controls: ["play", "download"]});
 * @extends Koowa.Class
 */
Files.Plyr = Koowa.Class.extend({

    defaults: {
        selectors: {
            html5: 'video, audio',
            embed: '[data-type]'
        }
    },

    options: {controls: [
        "play-large",
        "play",
        "progress",
        "current-time",
        "mute",
        "volume",
        "fullscreen"
    ]},

    initialize: function(selector){
        var selector = typeof selector !== 'undefined' ? selector : false;
        var options  = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this.options;
        
        player = Plyr.setup(selector, options);

        if (!player) {
            // No selector passed, possibly options as first argument
            // If options are the first argument
            options = selector ? selector : options;

            console.log(options);

            // Default selector
            var targets = document.querySelectorAll([this.defaults.selectors.html5, this.defaults.selectors.embed].join(','));

            targets = Array.from(targets);

            if (null == targets) {
                return null;
            }

            return targets.map(function (t) {
                return new Plyr(t, options);
            });
        }

        return player;
    }
});

})(kQuery);