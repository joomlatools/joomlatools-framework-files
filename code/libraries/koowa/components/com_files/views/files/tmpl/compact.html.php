<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= @import('com:files.files.scripts.html'); ?>

<script src="media://koowa/com_files/js/files.compact.js" />

<style src="media://koowa/com_files/css/files.css" />

<script>
Files.sitebase = '<?= $sitebase; ?>';
Files.token = '<?= $token; ?>';

window.addEvent('domready', function() {
	var config = <?= json_encode($state->config); ?>,
		options = {
            cookie: {
                path: '<?=@object('request')->getBaseUrl('site')?>'
            },
            pathway: false,
			state: {
				defaults: {
					limit: 0,
					offset: 0
				}
			},
			editor: <?= json_encode($state->editor); ?>,
			types: <?= json_encode($state->types); ?>,
			container: <?= json_encode($container ? $container->toArray() : null); ?>,
            uploader_dialog: false,
            folder_dialog: false
		};
	options = Object.append(options, config);

	Files.app = new Files.Compact.App(options);

	$$('#tabs-pane_insert dt').addEvent('click', function(){
		setTimeout(function(){window.fireEvent('refresh');}, 300);
	});
});
</script>

<?= @import('com:files.files.templates_compact.html');?>

<div class="docman_dialog docman_dialog--file_dialog">
    <div class="docman_dialog__menu">
        <a class="docman_dialog__menu__child--insert"><?= @translate('Select'); ?></a>
        <a class="docman_dialog__menu__child--download"><?= @translate('Upload'); ?></a>
    </div>
    <div class="docman_dialog__layout">
        <div class="docman_dialog__wrapper">
            <div class="docman_dialog__wrapper__child docman_dialog__file_dialog_categories">
                <h2 class="docman_dialog__title">
                    <?= @translate('Select a folder'); ?>
                </h2>
                <div class="docman_dialog__child__content docman_dialog__folders_files">
                    <div class="docman_dialog__child__content__box">
                        <div class="docman_dialog__files_tree" id="files-tree" style="overflow: auto; height: auto;"></div>
                    </div>
                    <div class="docman_dialog__new_folder">
                        <h2 class="docman_dialog__title docman_dialog__title--child">
                            <?= @translate('Create a new folder'); ?>
                        </h2>
                        <div class="docman_dialog__block" id="files-new-folder-modal">
                            <div class="input-group" style="margin:0;">
                                <input type="text" class="input-group-form-control" id="files-new-folder-input" />
                        <span class="input-group-btn">
                            <button id="files-new-folder-create" class="btn" disabled><?= @translate('Add folder'); ?></button>
                        </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="docman_dialog__wrapper__child docman_dialog__file_dialog_files">
                <h2 class="docman_dialog__title">
                    <?= @translate('Select a file'); ?>
                </h2>
                <div class="docman_dialog__child__content">
                    <div class="docman_dialog__child__content__box">
                        <div id="files-grid">

                        </div>
                    </div>
                </div>
            </div>
            <div class="docman_dialog__wrapper__child docman_dialog__file_dialog_insert">
                <h2 class="docman_dialog__title">
                    <?= @translate('Selected file info'); ?>
                </h2>
                <div class="docman_dialog__child__content">
                    <div class="docman_dialog__child__content__box">
                        <div id="files-preview"></div>
                        <div id="insert-button-container"></div>
                    </div>
                </div>
            </div>
            <div id="docman_dialog__file_dialog_upload" class="docman_dialog__wrapper__child docman_dialog__file_dialog_upload">
                <h2 class="docman_dialog__title">
                    <?= @translate('Upload files to docman'); ?>
                </h2>
                <div class="docman_dialog__child__content">
                    <div class="docman_dialog__child__content__box">
                        <?= @import('com:files.files.uploader.html', array('multi_selection' => false)); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
