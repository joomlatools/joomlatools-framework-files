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

                    // Cleanup template content.
                    var content = my.template.text();

                    content = content.replace(/([href|src])="\/\[%=/g, "$1=\"[%=");

                    my.template.text(content);

                    my.url = config.url;
                    my.csrf_token = config.csrf_token;
                },
                render: function(attachment)
                {
                    var data = {
                        url: attachment.url,
                        name: this.escape(attachment.name),
                        type: attachment.type,
                        thumbnail: attachment.thumbnail ? attachment.thumbnail.thumbnail : null
                    }

                    return new EJS({element: my.template.get(0)}).render(data);
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
                    var data = {
                        csrf_token: this.csrf_token,
                        _action: 'attach'
                    };

                    this.template.trigger('before.insert', data);

                    $.ajax({
                        url: this.replace(this.url, {name: attachment}),
                        method: 'POST',
                        data: data,
                        success: function(event, data) {
                            my.template.trigger('after.insert', attachment);
                        }
                    });
                },
                remove: function(attachment)
                {
                    var data = {
                        csrf_token: this.csrf_token,
                        _action: 'detach'
                    };

                    this.template.trigger('before.remove', data);

                    $.ajax({
                        url: this.replace(this.url, {name: attachment}),
                        method: 'POST',
                        data: data,
                        success: function(event, data) {
                            my.template.trigger('after.remove', attachment);
                        }
                    });
                },
                replace: function(text, params)
                {
                    $.each(params, function(key, value) {

                        var search = '%7B' + key + '%7D';

                        if (text.search(search) === -1) {
                            search = '{' + key + '}';
                        }

                        text = text.replace(search, my.escape(value));
                    });

                    return text;
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