<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= @template('initialize');?>

<script>
Files.sitebase = '<?= $sitebase; ?>';
Files.token = '<?= $token; ?>';

window.addEvent('domready', function() {
	var config = <?= json_encode($state->config); ?>,
		options = {
            cookie: {
                path: '<?=$this->getView()->baseurl?>'
            },
            title: false,
			state: {
				defaults: {
					limit: <?= (int) $state->limit; ?>,
					offset: <?= (int) $state->offset; ?>,
					types: <?= json_encode($state->types); ?>
				}
			},
			tree: {
				theme: 'media://com_files/images/mootree.png'
			},
			types: <?= json_encode($state->types); ?>,
			container: <?= json_encode($container ? $container->slug : 'files-files'); ?>,
			thumbnails: <?= json_encode($container ? $container->parameters->thumbnails : true); ?>
		};
	options = Files.utils.append(options, config);

	Files.app = new Files.App(options);

	//@TODO hide the uploader in a modal, make it pretty
	var tmp = new Element('div', {style: 'display:none'}).inject(document.body);
    $('files-upload').getParent().inject(tmp).setStyle('visibility', '');
    $('files-show-uploader').addEvent('click', function(e){
        e.stop();

        var handleClose = function(){
            $('files-upload').getParent().inject(tmp);
            SqueezeBox.removeEvent('close', handleClose);
        };
        SqueezeBox.addEvent('close', handleClose);
        SqueezeBox.open($('files-upload').getParent(), {
            handler: 'adopt',
            size: {x: 700, y: $('files-upload').getParent().measure(function(){
                this.setStyle('width', 700);
                var height = this.getSize().y;
                this.setStyle('width', '');
                return height;
            })}
        });
    });

	$('files-new-folder-modal').getElement('form').addEvent('submit', function(e){
		e.stop();
		var element = $('files-new-folder-input');
		var value = element.get('value');
		if (value.length > 0) {
			var folder = new Files.Folder({name: value, folder: Files.app.getPath()});
			folder.add(function(response, responseText) {
				if (response.status === false) {
					return alert(response.error);
				}
				element.set('value', '');
				$('files-new-folder-create').removeClass('valid').setProperty('disabled', 'disabled');
				var el = response.item;
				var cls = Files[el.type.capitalize()];
				var row = new cls(el);
				Files.app.grid.insert(row);
				Files.app.tree.selected.insert({
					text: row.name,
					id: row.path,
					data: {
						path: row.path,
						url: '#'+row.path,
						type: 'folder'
					}
				});
				Files.app.tree.selected.toggle(false, true);

				SqueezeBox.close();
			});
		};
	});

    Files.createModal = function(container, button){
        var modal = $(container), tmp = new Element('div', {style: 'display:none'}).inject(document.body);
        tmp.grab(modal);
        $(button).addEvent('click', function(e) {
    		e.stop();

    		var handleClose = function(){
                    modal.inject(tmp);

                    SqueezeBox.removeEvent('close', handleClose);
				},
				handleOpen = function(){
					var focus = modal.getElement('input.focus');
		    		if (focus) {
		        		focus.focus();
		    		}

					SqueezeBox.removeEvent('open', handleOpen);
				},
				sizes = modal.measure(function(){return this.getSize();});

			SqueezeBox.addEvent('close', handleClose);
			SqueezeBox.addEvent('open', handleOpen);
			SqueezeBox.open(modal.setStyle('display', ''), {
				handler: 'adopt',
				size: {x: sizes.x, y: sizes.y}
			});

    	});

    	var validate = function(){
    		if(this.value.trim()) {
    			$('files-new-folder-create').addClass('valid').removeProperty('disabled');
    		} else {
    			$('files-new-folder-create').removeClass('valid').setProperty('disabled', 'disabled');
    		}
    	};
    	$('files-new-folder-input').addEvent('change', validate);
    	if(window.addEventListener) {
    		$('files-new-folder-input').addEventListener('input', validate);
    	} else {
    		$('files-new-folder-input').addEvent('keyup', validate);
    	}
    };

    Files.createModal('files-new-folder-modal', 'files-new-folder-toolbar');

    var switchers = $$('.files-layout-switcher');

    switchers.filter(function(el) {
        return el.get('data-layout') == Files.app.grid.layout;
    }).addClass('active');

    switchers.addEvent('click', function(e) {
    	e.stop();
    	var layout = this.get('data-layout');
    	Files.app.grid.setLayout(layout);
    	switchers.removeClass('active');
    	this.addClass('active');
    });
});
</script>


<div id="files-app" class="com_files">
	<?= @template('templates_icons'); ?>
	<?= @template('templates_details'); ?>

	<div id="files-sidebar">
        <h3><?= @text('Folders'); ?></h3>
		<div id="files-tree"></div>
	</div>

	<div id="files-canvas">
	    <div class="path" style="height: 24px;">
            <div class="files-toolbar-controls btn-group">
                <button id="files-show-uploader" class="btn btn-mini"><?= @text('Upload'); ?></button>
                <button id="files-new-folder-toolbar" class="btn btn-mini"><?= @text('New Folder'); ?></button>
                <button id="files-batch-delete" class="btn btn-mini" disabled><?= @text('Delete'); ?></button>
			</div>
            <div id="files-pathway"></div>
			<div class="files-layout-controls btn-group" data-toggle="buttons-radio">
				<button class="btn files-layout-switcher" data-layout="icons" title="<?= @text('Show files as icons'); ?>">
                    <i class="icon-th icon-grid-view-2"></i>
				</button>
				<button class="btn files-layout-switcher" data-layout="details" title="<?= @text('Show files in a list'); ?>">
                    <i class="icon-list"></i>
				</button>
			</div>
		</div>
		<div class="view">
			<div id="files-grid"></div>
		</div>
        <table class="table">
            <tfoot>
            <tr><td>
                <?= @helper('paginator.pagination') ?>
            </td></tr>
            </tfoot>
        </table>

		<?= @template('uploader');?>
	</div>
	<div style="clear: both"></div>
</div>

<div>
    <div id="files-new-folder-modal" style="display: none">
        <div class="com_files">
            <form class="files-modal well">
                <div style="text-align: center;">
                    <h3 style=" float: none">
                        <?= str_replace('%folder%', '<span class="upload-files-to"></span>', @text('Create a new folder in %folder%')) ?>
                    </h3>
                </div>
                <div class="input-append">
                    <input class="span5 focus" type="text" id="files-new-folder-input" placeholder="<?= @text('Enter a folder name') ?>" />
                    <button id="files-new-folder-create" class="btn btn-primary" disabled><?= @text('Create'); ?></button>
                </div>
            </form>
        </div>
	</div>
</div>