<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die;
$can_attach = isset(parameters()->config['can_attach']) ? parameters()->config['can_attach'] : true;
$can_detach = isset(parameters()->config['can_detach']) ? parameters()->config['can_detach'] : true;
?>

<?= import('com:files.files.scripts.html'); ?>

<ktml:script src="media://koowa/com_files/js/files.attachments.app.js" />
<ktml:style src="media://koowa/com_files/css/attachments.css" />

<script>
    Files.sitebase = '<?= $sitebase; ?>';
    Files.token = '<?= $token; ?>';

    kQuery(function($) {
        var config = <?= json_encode(KObjectConfig::unbox(parameters()->config)); ?>,
            options = {
                cookie: {
                    path: '<?=object('request')->getSiteUrl()?>'
                },
                attachments: {
                    permissions: {
                        attach: <?= json_encode($can_attach) ?>,
                        detach: <?= json_encode($can_detach) ?>
                    }
                },
                root_text: <?= json_encode(translate('Root folder')) ?>,
                editor: <?= json_encode(parameters()->editor); ?>,
                types: <?= json_encode(KObjectConfig::unbox(parameters()->types)); ?>,
                container: <?= json_encode($container ? $container->toArray() : null); ?>
            };
        options = Object.append(options, config);

        Files.app = new Files.Attachments.App(options);

        var app = Files.app;

        var onClickFile = function(e)
        {
            var row = document.id(e.target).getParent('.files-node').retrieve('row');

            app.grid.selected = row.path;

            $(app.attachments.grid.container).find('li.files-node').removeClass('active');

            if (app.attachments.permissions.attach) {
                document.id('detach-button-container').setStyle('display', 'none');
                document.id('attach-button-container').setStyle('display', 'block');
            }
        };

        var onClickAttachment = function(e)
        {
            var row = document.id(e.target).getParent('.files-node').retrieve('row');

            app.attachments.grid.selected = row.name;

            $(app.grid.container).find('li.files-node').removeClass('active');

            if (app.attachments.permissions.detach) {
                document.id('attach-button-container').setStyle('display', 'none');
                document.id('detach-button-container').setStyle('display', 'block');
            }
        }

        app.grid.addEvent('clickFile', onClickFile);
        app.grid.addEvent('clickImage', onClickFile);
        app.attachments.grid.addEvent('clickAttachment', onClickAttachment);
    });

    // @TODO: this whole jQuery thing can be removed

//    kQuery(function($) {
//        var files_trigger = $('.koowa_dialog__menu__child--files'),
//            upload_trigger = $('.koowa_dialog__menu__child--upload'),
//            attachments_trigger = $('.koowa_dialog__menu__child--attachments'),
//            files_dialog = $('.koowa_dialog__file_dialog_files, .koowa_dialog__file_dialog_attach'),
//            upload_dialog = $('.koowa_dialog__file_dialog_upload'),
//            attachments_dialog = $('.koowa_dialog__file_dialog_attachments, .koowa_dialog__file_dialog_detach');
//
//        // Set initially
//        if (<?//= $can_attach ? 1 : 0 ?>//) {
//            files_dialog.hide();
//            attachments_dialog.hide();
//            upload_trigger.addClass('active');
//        } else {
//            upload_dialog.hide();
//            files_dialog.hide();
//            attachments_trigger.addClass('active');
//        }
//
//        files_trigger.click(function() {
//            $(this).addClass('active')
//                .siblings().removeClass('active');
//
//            upload_dialog.hide();
//            attachments_dialog.hide();
//            files_dialog.show();
//        });
//
//        upload_trigger.click(function() {
//            $(this).addClass('active')
//                .siblings().removeClass('active');
//
//            files_dialog.hide();
//            attachments_dialog.hide();
//            upload_dialog.show();
//        });
//
//        attachments_trigger.click(function() {
//            $(this).addClass('active')
//                .siblings().removeClass('active');
//
//            files_dialog.hide();
//            upload_dialog.hide();
//            attachments_dialog.show();
//        });
//
//        // Scroll to upload or insert area after click
//        if ( $('body').width() <= '699' ) { // 699 is when colums go from stacked to aligned
//            upload_trigger.click(function() {
//                $('html, body').animate({
//                    scrollTop: upload_dialog.offset().top
//                }, 1000);
//            });
//
//            $('#files-grid').on('click', 'a.navigate', function() {
//                $('html, body').animate({
//                    scrollTop: '5000' // Scroll to highest amount so it will at least scroll to the bottom where the insert button is
//                }, 1000);
//            });
//        }
//    });
</script>

<script>
    kQuery(function($) {
        $('.fileman-attachments-uploader').on('uploader:uploaded', function(uploader, file, result) {
            // attach file
        })
    });

</script>

<?= import('com:files.files.templates_compact.html');?>
<?= import('com:files.attachments.app.templates');?>

<? if ( 1 == 1 ) : ?>
    <div class="koowa_dialog koowa_dialog--file_dialog">
        <div class="koowa_dialog__layout">
            <div class="koowa_dialog__wrapper">
                <div class="attachments-top">
                    <div class="attachments-table">
                        <div class="attachments-upload">
                            <?= helper('uploader.container', array(
                                'container' => 'fileman-attachments',
                                'element' => '.fileman-attachments-uploader',
                                'options'   => array(
                                    'multi_selection' => true,
                                    'url' => route('component=fileman&view=file&plupload=1&routed=1&format=json&container=' .
                                                   (isset($container) ? $container->slug : ''), false, false)
                                )
                            )) ?>
                        </div>
                        <div class="attachments-lists">
                            <div class="attachments-existing">

                                <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_files">
                                    <h2 class="koowa_dialog__title">
                                        <?= translate('Select a file to attach'); ?>
                                    </h2>
                                    <div class="koowa_dialog__child__content koowa_spinner_container" id="files-spinner">
                                        <div class="koowa_dialog__child__content__box">
                                            <div id="files-grid" style="max-height:450px;">

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="attachments-files">

                                <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_attachments">
                                    <h2 class="koowa_dialog__title">
                                        <?= translate('Attached files'); ?>
                                    </h2>
                                    <div class="koowa_dialog__child__content koowa_spinner_container" id="attachments-spinner">
                                        <div class="koowa_dialog__child__content__box">
                                            <div id="attachments-grid" style="max-height:450px;">

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div id="preview" class="attachments-selected">
                    <h2 class="koowa_dialog__title">
                        <?= translate('Selected file info'); ?>
                    </h2>
                    <div class="koowa_dialog__child__content">
                        <div class="koowa_dialog__child__content__box">
                            <div id="files-preview"></div>
                            <div id="attach-button-container" style="display: none">
                                <button class="btn btn-primary" type="button" id="attach-button"><?= translate('Attach') ?></button>
                            </div>
                            <div id="detach-button-container" style="display: none">
                                <button class="btn btn-danger" type="button" id="detach-button"><?= translate('Detach') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<? else :?>
<div class="koowa_dialog koowa_dialog--file_dialog">
    <div class="koowa_dialog__menu koowa_dialog__menu--fullwidth">
        <a class="koowa_dialog__menu__child--upload" <?= ($can_attach) ? '' : 'style="display: none";' ?>>
            <?= translate('Upload'); ?>
        </a>
        <a class="koowa_dialog__menu__child--files" <?= ($can_attach) ? '' : 'style="display: none";' ?>>
            <?= translate('Select'); ?>
        </a>
        <a class="koowa_dialog__menu__child--attachments"><?= translate('Attachments'); ?> <span>(22)</span></a>
    </div>
    <div class="koowa_dialog__layout">
        <div class="koowa_dialog__wrapper">
                <div id="koowa_dialog__file_dialog_upload" class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_upload koowa_dialog__file_dialog_upload--fullwidth">
                    <h2 class="koowa_dialog__title">
                        <?= translate('Upload a file to attach'); ?>
                    </h2>
                    <div class="koowa_dialog__child__content">
                        <div class="koowa_dialog__child__content__box">
                            <?= import('com:files.files.uploader.html', array('multi_selection' => true)); ?>
                        </div>
                    </div>
                </div>
                <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_files">
                    <h2 class="koowa_dialog__title">
                        <?= translate('Select a file to attach'); ?>
                    </h2>
                    <div class="koowa_dialog__child__content koowa_spinner_container" id="files-spinner">
                        <div class="koowa_dialog__child__content__box">
                            <div id="files-grid" style="max-height:450px;">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_attach koowa_dialog__file_dialog_attach--fullwidth">
                    <h2 class="koowa_dialog__title">
                        <?= translate('Selected file info'); ?>
                    </h2>
                    <div class="koowa_dialog__child__content">
                        <div class="koowa_dialog__child__content__box">
                            <div id="files-preview"></div>
                            <div id="attach-button-container">
                                <div style="text-align: center; display: none">
                                    <button class="btn btn-primary" type="button" id="attach-button" disabled><?= translate('Attach') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_attachments">
                    <h2 class="koowa_dialog__title">
                        <?= translate('Attached files'); ?>
                    </h2>
                    <div class="koowa_dialog__child__content koowa_spinner_container" id="attachments-spinner">
                        <div class="koowa_dialog__child__content__box">
                            <div id="attachments-grid" style="max-height:450px;">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_detach koowa_dialog__file_dialog_detach--fullwidth">
                    <h2 class="koowa_dialog__title">
                        <?= translate('Selected attachment info'); ?>
                    </h2>
                    <div class="koowa_dialog__child__content">
                        <div class="koowa_dialog__child__content__box">
                            <div id="attachments-preview"></div>
                            <div id="detach-button-container">
                                <div style="text-align: center; display: none">
                                    <button class="btn btn-danger" type="button" id="detach-button" disabled><?= translate('Detach') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
<? endif; ?>