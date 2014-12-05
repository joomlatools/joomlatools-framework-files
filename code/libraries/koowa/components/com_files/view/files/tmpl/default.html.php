<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= import('scripts.html');?>

<script>
    Files.sitebase = '<?= $sitebase; ?>';
    Files.token = '<?= $token; ?>';

    window.addEvent('domready', function() {
        var config = <?= json_encode(KObjectConfig::unbox(parameters()->config)); ?>,
            options = {
                cookie: {
                    path: '<?=object('request')->getSiteUrl()?>'
                },
                state: {
                    defaults: {
                        limit: <?= (int) parameters()->limit; ?>,
                        offset: <?= (int) parameters()->offset; ?>,
                        types: <?= json_encode(parameters()->types); ?>
                    }
                },
                root_text: <?= json_encode(translate('Root folder')) ?>,
                types: <?= json_encode(parameters()->types); ?>,
                container: <?= json_encode($container ? $container->toArray() : null); ?>,
                thumbnails: <?= json_encode($container ? $container->getParameters()->thumbnails : true); ?>
            };
        options = Object.append(options, config);

        Files.app = new Files.App(options);
    });
</script>


<div id="files-app" class="com_files">
	<?= import('templates_icons.html'); ?>
	<?= import('templates_details.html'); ?>

	<div id="files-sidebar">
        <h3><?= translate('Folders'); ?></h3>
		<div id="files-tree"></div>
	</div>

    <div id="files-canvas">
        <div class="path" style="height: 24px;">
            <div id="files-pathway"></div>
            <div class="files-layout-controls btn-group" data-toggle="buttons-radio">
                <button class="btn files-layout-switcher" data-layout="icons" title="<?= translate('Show files as icons'); ?>">
                    <i class="icon-th icon-grid-view-2"></i>
                </button>
                <button class="btn files-layout-switcher" data-layout="details" title="<?= translate('Show files in a list'); ?>">
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
                <?= helper('paginator.pagination') ?>
            </td></tr>
            </tfoot>
        </table>

        <?= import('uploader.html');?>
    </div>
    <div style="clear: both"></div>
</div>

<div id="files-new-folder-modal" class="koowa mfp-hide" style="max-width: 600px; position: relative; width: auto; margin: 20px auto;">
    <form class="files-modal well">
        <div style="text-align: center;">
            <h3 style=" float: none">
                <?= translate('Create a new folder in {folder}', array(
                    'folder' => '<span class="upload-files-to"></span>'
                )) ?>
            </h3>
        </div>
        <div class="input-append">
            <input class="span5 focus" type="text" id="files-new-folder-input" placeholder="<?= translate('Enter a folder name') ?>" />
            <button id="files-new-folder-create" class="btn btn-primary" disabled><?= translate('Create'); ?></button>
        </div>
    </form>
</div>

<div id="files-move-modal" class="koowa mfp-hide" style="max-width: 600px; position: relative; width: auto; margin: 20px auto;">
    <form class="files-modal well">
        <div>
            <h3><?= translate('Move to') ?></h3>
        </div>
        <div class="tree-container"></div>
        <div class="form-actions" style="padding-left: 0">
            <button class="btn btn-primary" ><?= translate('Move'); ?></button>
        </div>
    </form>
</div>

<div id="files-copy-modal" class="koowa mfp-hide" style="max-width: 600px; position: relative; width: auto; margin: 20px auto;">
    <form class="files-modal well">
        <div>
            <h3><?= translate('Copy to') ?></h3>
        </div>
        <div class="tree-container"></div>
        <div class="form-actions" style="padding-left: 0">
            <button class="btn btn-primary" ><?= translate('Copy'); ?></button>
        </div>
    </form>
</div>
