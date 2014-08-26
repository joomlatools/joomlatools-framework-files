<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die; ?>

<?= @import('com:files.files.scripts.html'); ?>

<ktml:script src="media://koowa/com_files/js/files.compact.js" />

<script>
    Files.sitebase = '<?= $sitebase; ?>';
    Files.token = '<?= $token; ?>';

    window.addEvent('domready', function() {
        var config = <?= json_encode(state()->config); ?>,
            options = {
                cookie: {
                    path: '<?=@object('request')->getSiteUrl()?>'
                },
                root_text: <?= json_encode(@translate('Root folder')) ?>,
                editor: <?= json_encode(state()->editor); ?>,
                types: <?= json_encode(state()->types); ?>,
                container: <?= json_encode($container ? $container->toArray() : null); ?>,
                tree: {
                    dataFilter: function(response){
                        if (response.entities.length === 0) {
                            return [];
                        }

                        kQuery('.koowa_dialog__file_dialog_categories').css('display', 'block');
                        kQuery('.koowa_dialog--file_dialog').removeClass('koowa_dialog--no_categories');

                        return Files.app.tree.filterData(response);
                    }
                }
            },
            app = new Class({
                Extends: Files.Compact.App,
                fetch: function() {
                    this.grid.unspin();
                    return kQuery.Deferred();
                }
            });
        options = Object.append(options, config);

        Files.app = new app(options);
    });
</script>

<?= @import('com:files.files.templates_compact.html');?>

<div class="koowa_dialog koowa_dialog--file_dialog koowa_dialog--no_menu koowa_dialog--no_categories">
    <div class="koowa_dialog__layout">
        <div class="koowa_dialog__wrapper">
            <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_categories" style="display: none">
                <h2 class="koowa_dialog__title">
                    <?= @translate('Select a folder'); ?>
                </h2>
                <div class="koowa_dialog__child__content koowa_dialog__folders_files">
                    <div class="koowa_dialog__child__content__box">
                        <div class="koowa_dialog__files_tree" id="files-tree" style="overflow: auto; height: auto;"></div>
                    </div>
                </div>
            </div>
            <div id="koowa_dialog__file_dialog_upload" class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_upload">
                <h2 class="koowa_dialog__title">
                    <?= @translate('Upload a file'); ?>
                </h2>
                <div class="koowa_dialog__child__content">
                    <div class="koowa_dialog__child__content__box">
                        <?= @import('com:files.files.uploader.html', array('multi_selection' => (isset(state()->config['multi_selection']) ? state()->config['multi_selection'] : false))); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="files-grid" style="display: none"></div>
    <div id="files-preview" style="display: none"></div>
    <div id="insert-button-container" style="display: none"></div>
</div>

