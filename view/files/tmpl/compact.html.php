<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die;

$can_upload = isset(parameters()->config['can_upload']) ? parameters()->config['can_upload'] : true;
?>

<?= import('com:files.files.scripts.html'); ?>

<ktml:script src="media://koowa/com_files/js/files.compact.js" />

<script>
Files.sitebase = '<?= $sitebase; ?>';
Files.token = '<?= $token; ?>';

window.addEvent('domready', function() {
	var config = <?= json_encode(KObjectConfig::unbox(parameters()->config)); ?>,
		options = {
            cookie: {
                path: '<?=object('request')->getSiteUrl()?>'
            },
            root_text: <?= json_encode(translate('Root folder')) ?>,
			editor: <?= json_encode(parameters()->editor); ?>,
			types: <?= json_encode(KObjectConfig::unbox(parameters()->types)); ?>,
			container: <?= json_encode($container ? $container->toArray() : null); ?>
		};
	options = Object.append(options, config);

	Files.app = new Files.Compact.App(options);

    <? if ($can_upload): ?>
    $('files-new-folder-create').addEvent('click', function(e){
        e.stop();

        var element = $('files-new-folder-input'),
            value = element.get('value');

        if (value.length > 0) {
            var folder = new Files.Folder({name: value, folder: Files.app.getPath()});

            folder.add(function(response, responseText) {
                var el = response.entities[0],
                    cls = Files[el.type.capitalize()],
                    row = new cls(el);

                element.set('value', '');
                $('files-new-folder-create').removeClass('valid').setProperty('disabled', 'disabled');

                Files.app.tree.appendNode({
                    id: row.path,
                    label: row.name
                });
            });
        }
    });
    var validate = function(){
            if(this.value.trim()) {
                $('files-new-folder-create').addClass('valid').removeProperty('disabled');
            } else {
                $('files-new-folder-create').removeClass('valid').setProperty('disabled', 'disabled');
            }
        },
        input = $('files-new-folder-input');

    input.addEvent('change', validate);

    if (window.addEventListener) {
        input.addEventListener('input', validate);
    } else {
        input.addEvent('keyup', validate);
    }
    <? endif; ?>
});

kQuery(function($) {
    // Scroll to upload or insert area after click
    if ( $('body').width() <= '699' ) { // 699 is when colums go from stacked to aligned

        $('#files-grid').on('click', 'a.navigate', function() {
            $('html, body').animate({
                scrollTop: '5000' // Scroll to highest amount so it will at least scroll to the bottom where the insert button is
            }, 1000);
        });
    }

});
</script>

<?= import('com:files.files.templates_compact.html');?>


<div class="koowa_dialog koowa_dialog--file_dialog" >
    <div class="koowa_dialog__layout" style="padding-top: 0">
        <div class="koowa_dialog__wrapper">
            <div class="attachments-top">
                <div class="attachments-table">
                    <? if ($can_upload): ?>
                        <div class="attachments-upload">
                            <?= import('com:files.files.uploader.html') ?>
                        </div>
                    <? endif ?>
                    <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_categories">
                        <h2 class="koowa_dialog__title">
                            <?= translate('Select a folder'); ?>
                        </h2>
                        <div class="koowa_dialog__child__content koowa_dialog__folders_files">
                            <div class="koowa_dialog__child__content__box">
                                <div class="koowa_dialog__files_tree" id="files-tree" style="overflow: auto; height: auto;"></div>
                            </div>
                            <? if ($can_upload): ?>
                                <div class="koowa_dialog__new_folder">
                                    <h2 class="koowa_dialog__title koowa_dialog__title--child">
                                        <?= translate('Create a new folder'); ?>
                                    </h2>
                                    <div class="koowa_dialog__block" id="files-new-folder-modal">
                                        <div class="input-group" style="margin:0;">
                                            <input type="text" class="input-group-form-control" id="files-new-folder-input" />
                            <span class="input-group-btn">
                                <button id="files-new-folder-create" class="btn" disabled><?= translate('Add folder'); ?></button>
                            </span>
                                        </div>
                                    </div>
                                </div>
                            <? endif; ?>
                        </div>
                    </div>

                    <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_files">
                        <h2 class="koowa_dialog__title">
                            <?= translate('Select a file'); ?>
                        </h2>
                        <div class="koowa_dialog__child__content koowa_spinner_container" id="spinner_container">
                            <div class="koowa_dialog__child__content__box">
                                <div id="files-grid" style="max-height:450px;">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_insert">
                        <h2 class="koowa_dialog__title">
                            <?= translate('Selected file info'); ?>
                        </h2>
                        <div class="koowa_dialog__child__content">
                            <div class="koowa_dialog__child__content__box">
                                <div id="files-preview"></div>
                                <div id="insert-button-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
