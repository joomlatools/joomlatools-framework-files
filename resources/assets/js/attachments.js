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
                    my.table = config.table;
                    my.row = config.row;
                    my.template = $(config.template);
                    my.urls = config.urls;
                    my.csrf_token = config.csrf_token;
                    my.table = config.table;
                },
                render: function(attachment)
                {
                    var data = {
                        url: this.url({view: "files", thumbnails: 1, name: attachment.name, format: "json", routed: 1}),
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

                    var data = {
                        name: attachment,
                        csrf_token: this.csrf_token,
                        table: this.table,
                        row: this.row
                    };

                    $.ajax({
                        url: this.url({view: "attachment"}),
                        method: 'POST',
                        data: data,
                        success: function(event, data) {
                            my.template.trigger('after.insert', attachment);
                        }
                    });
                },
                remove: function(attachment)
                {
                    this.template.trigger('before.remove', attachment);

                    var data = {
                        csrf_token: this.csrf_token,
                        _action: 'delete'
                    };

                    $.ajax({
                        url: this.url({view: "attachment", table: this.table, row: this.row, name: attachment}),
                        method: 'POST',
                        data: data,
                        success: function(event, data) {
                            my.template.trigger('after.remove', attachment);
                        }
                    });


                },
                url: function(params, decode)
                {
                    var url = null;

                    if (url = this.urls[params.view])
                    {
                        delete params.view;

                        var params = $.param(params);

                        if (decode) {
                            params = decodeURIComponent(params);
                        }

                        if (params.length)
                        {
                            url += url.search('\\?') ? '&' : '?';
                            url += params;
                        }
                    }

                    return url;
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