/*
 * Used in view=files&layout=select to provide an Insert button
 *
 * If the request contains &callback=foo window.parent.foo will be called when the button is clicked or
 * a file is uploaded. This can be used to implement file select modals.
 */
setTimeout(function() {
    window.addEvent('domready', function(){
        if (kQuery('#files-upload-multi').length === 0) {
            return;
        }

        var app = Files.app;

        app.addEvent('uploadFile', function(row) {
            app.selected = row.path;

            kQuery('#insert-button').trigger('click');
        });

        var button_html = '<div style="text-align: center; display: none">' +
            '<button class="btn btn-primary" type="button" id="insert-button" disabled>'+Koowa.translate('Insert')+'</button>' +
            '</div>';
        kQuery('#insert-button-container').append(kQuery.parseHTML(button_html));

        var onClickNode = function(e) {
            var row = document.id(e.target).getParent('.files-node').retrieve('row');

            app.selected = row.path;

            document.id('insert-button').set('disabled', false)
                .getParent().setStyle('display', 'block');
        };

        app.grid.addEvent('clickFile', onClickNode);
        app.grid.addEvent('clickImage', onClickNode);

        // Select the initial file for preview
        var url = app.getUrl();

        if (url.getData('file')) {
            var select = url.getData('file').replace(/\+/g, ' ');
            select = app.active ? app.active+'/'+select : select;
            var node = app.grid.nodes.get(select);

            if (node && node.element) {
                var event = node.filetype === 'image' ? 'clickImage' : 'clickFile';
                app.grid.fireEvent(event, [{target: node.element.getElement('a')}]);
            }
        }

        if (url.getData('callback')) {
            var callback = window.parent[url.getData('callback')];
            if (typeof callback === 'function') {
                kQuery('#insert-button').click(function(e) {
                    e.preventDefault();

                    callback(app.selected);
                });

            }
        }
    });

}, 1000);