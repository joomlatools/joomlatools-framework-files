<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= @import('initialize');?>

<script>
Files.sitebase = '<?= $sitebase; ?>';
Files.token = '<?= $token; ?>';

window.addEvent('domready', function() {
	var config = jQuery.parseJSON(<?= json_encode($state->config); ?>),
		options = {
            cookie: {
                path: '<?=KRequest::root()?>'
            },
			state: {
				defaults: {
					limit: <?= (int) $state->limit; ?>,
					offset: <?= (int) $state->offset; ?>,
					types: <?= json_encode($state->types); ?>
				}
			},
			tree: {
				theme: 'media://koowa/com_files/images/mootree.png'
			},
			types: <?= json_encode($state->types); ?>,
			container: <?= json_encode($container ? $container->slug : null); ?>,
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
});
</script>


<div id="files-app" class="com_files">
	<?= @import('templates_icons'); ?>
	<?= @import('templates_details'); ?>

	<div id="files-sidebar">
        <h3><?= @translate('Folders'); ?></h3>
		<div id="files-tree"></div>
	</div>

	<div id="files-canvas">
	    <div class="path" style="height: 24px;">
            <div class="files-toolbar-controls btn-group">
                <button id="files-show-uploader" class="btn btn-mini"><?= @translate('Upload'); ?></button>
                <button id="files-new-folder-toolbar" class="btn btn-mini"><?= @translate('New Folder'); ?></button>
                <button id="files-batch-delete" class="btn btn-mini" disabled><?= @translate('Delete'); ?></button>
			</div>
            <div id="files-pathway"></div>
			<div class="files-layout-controls btn-group" data-toggle="buttons-radio">
				<button class="btn files-layout-switcher" data-layout="icons" title="<?= @translate('Show files as icons'); ?>">
                    <i class="icon-th icon-grid-view-2"></i>
				</button>
				<button class="btn files-layout-switcher" data-layout="details" title="<?= @translate('Show files in a list'); ?>">
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

		<?= @import('uploader');?>
	</div>
	<div style="clear: both"></div>
</div>

<div>
    <div id="files-new-folder-modal" style="display: none">
        <div class="com_files">
            <form class="files-modal well">
                <div style="text-align: center;">
                    <h3 style=" float: none">
                        <?= str_replace('{folder}', '<span class="upload-files-to"></span>', @translate('Create a new folder in {folder}')) ?>
                    </h3>
                </div>
                <div class="input-append">
                    <input class="span5 focus" type="text" id="files-new-folder-input" placeholder="<?= @translate('Enter a folder name') ?>" />
                    <button id="files-new-folder-create" class="btn btn-primary" disabled><?= @translate('Create'); ?></button>
                </div>
            </form>
        </div>
	</div>
</div>
