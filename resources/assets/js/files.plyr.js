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

var recorded_plays = [];

$(document).on('playing', function(event) {
    if (typeof event.detail !== 'undefined' && typeof event.detail.plyr !== 'undefined') {
        var plyr = event.detail.plyr;

        // If they've played over 3 seconds, then consider it played
        // This is the same timing convention used by Facebook, Instagram, and Twitter
        setTimeout(function() {
            if (!plyr.paused) {
                var media = $(plyr.media);

                var category = media.data('category');
                var action = 'Play ' + plyr.type;

                var title = media.data('title') || '';
                var id = parseInt(media.data('media-id'), 10) || 0;

                if (recorded_plays.indexOf(title) === -1) {
                    recorded_plays.push(title);

                    if (typeof window.GoogleAnalyticsObject !== 'undefined' && typeof window[window.GoogleAnalyticsObject] !== 'undefined') {
                        window[window.GoogleAnalyticsObject]('send', 'event', category, action, title, id);
                    }
                    else if (typeof _gaq !== 'undefined' && typeof _gat !== 'undefined') {
                        if (_gat._getTrackers().length) {
                            _gaq.push(function () {
                                var tracker = _gat._getTrackers()[0];
                                tracker._trackEvent(category, action, title, id);
                            });
                        }
                    }
                }
            }
        }, 3000);
    }
});

})(kQuery);