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
    });

    kQuery(function($) {
        var insert_trigger = $('.koowa_dialog__menu__child--insert'),
            upload_trigger = $('.koowa_dialog__menu__child--download'),
            insert_dialog  = $('.koowa_dialog__file_dialog_files, .koowa_dialog__file_dialog_insert'),
            upload_dialog  = $('.koowa_dialog__file_dialog_upload');

        // Set initially
        if (upload_dialog.length) {
            insert_dialog.hide();
            upload_trigger.addClass('active');
        } else {
            upload_dialog.hide();
            insert_trigger.addClass('active');
        }

        insert_trigger.click(function() {
            $(this).addClass('active')
                .siblings().removeClass('active');

            upload_dialog.hide();

            insert_dialog.show();
        });

        upload_trigger.click(function() {
            $(this).addClass('active')
                .siblings().removeClass('active');

            insert_dialog.hide();
            upload_dialog.show();
        });

        // Scroll to upload or insert area after click
        if ( $('body').width() <= '699' ) { // 699 is when colums go from stacked to aligned
            upload_trigger.click(function() {
                $('html, body').animate({
                    scrollTop: upload_dialog.offset().top
                }, 1000);
            });

            $('#files-grid').on('click', 'a.navigate', function() {
                $('html, body').animate({
                    scrollTop: '5000' // Scroll to highest amount so it will at least scroll to the bottom where the insert button is
                }, 1000);
            });
        }

    });
</script>

<?= import('com:files.files.templates_compact.html');?>

<div class="koowa_dialog koowa_dialog--file_dialog">
    <div class="koowa_dialog__menu koowa_dialog__menu--fullwidth">
        <? if ($can_upload): ?>
            <a class="koowa_dialog__menu__child--download"><?= translate('Upload'); ?></a>
        <? endif; ?>
        <a class="koowa_dialog__menu__child--insert"><?= translate('Select'); ?></a>
    </div>
    <div class="koowa_dialog__layout">
        <div class="koowa_dialog__wrapper">
            <? if ($can_upload): ?>
                <div id="koowa_dialog__file_dialog_upload" class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_upload koowa_dialog__file_dialog_upload--fullwidth">
                    <h2 class="koowa_dialog__title">
                        <?= translate('Upload a file'); ?>
                    </h2>
                    <div class="koowa_dialog__child__content">
                        <div class="koowa_dialog__child__content__box">
                            <?= import('com:files.files.uploader.html', array('multi_selection' => false)); ?>
                        </div>
                    </div>
                </div>
            <? endif; ?>
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
            <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_insert koowa_dialog__file_dialog_insert--fullwidth">
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
