/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           https://github.com/nooku/nooku-platform for the canonical source repository
 */

(function ($)
{
    Attachments = {
        getInstance: function(config)
        {
            var my = {
                init: function (config)
                {
                    my.template = $(config.template);
                    my.url = config.url;
                    my.attachments = {};
                },
                render: function(attachment)
                {
                    var data = {
                        url: this.route(attachment.name),
                        name: this.escape(attachment.name),
                        type: attachment.type,
                        thumbnail: attachment.thumbnail
                    }

                    return output = new EJS({element: my.template.get(0)}).render(data);
                },
                escape: function(string)
                {
                    var entityMap = {
                        "&": "&amp;",
                        "<": "&lt;",
                        ">": "&gt;",
                        '"': '&quot;',
                        "'": '&#39;',
                        "/": '&#x2F;'
                    };

                    return String(string).replace(/[&<>"'\/]/g, function (s) {return entityMap[s]});
                },
                insert: function(attachment)
                {
                    this.template.trigger('before.insert', attachment);

                    var name = $.type(attachment) === 'string' ? attachment : attachment.name;

                    if (!this.attachments[name])
                    {
                        this.attachments[name] = 1;
                        this.template.trigger('after.insert', attachment);
                    }
                },
                remove: function(attachment)
                {
                    this.template.trigger('before.remove', attachment);

                    var name = $.type(attachment) === 'string' ? attachment : attachment.name;

                    if (this.attachments[name])
                    {
                        this.attachments[name] = 0;
                        this.template.trigger('after.remove', attachment);
                    }
                },
                route: function(name, format)
                {
                    format = format ? format : 'html';
                    return this.url.replace('%7Bname%7D', encodeURIComponent(name)).replace('%7Bformat%7D', format);
                },
                bind: function(event, handler)
                {
                    this.template.on(event, handler);
                }
            };

            my.init(config);

            return my;
        }
    }
})(kQuery)