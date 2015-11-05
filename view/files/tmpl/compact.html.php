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

<ktml:script src="assets://files/js/files.compact.js" />

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


<!-- Wrapper -->
<div class="k-wrapper">

    <!-- Titlebar -->
    <div class="k-titlebar">

        <!-- Title -->
        <h2>Insert / Upload file</h2>

    </div><!-- .k-titlebar -->

    <!-- Content wrapper -->
    <div class="k-content-wrapper">

        <!-- Sidebar -->
        <div id="k-sidebar" class="k-sidebar">
            Sidebar
        </div>

        <!-- Content -->
        <div class="k-content">

          <!-- Component -->
            <div class="k-component">

                <!-- Form -->
                <form class="k-list-layout -koowa-grid" id="k-offcanvas-container" action="" method="get">

                    <!-- Scopebar -->
                    Scopebar

                    <!-- Table -->











                    <?= import('com:files.files.templates_compact.html');?>

                    <div class="koowa_dialog koowa_dialog--file_dialog">
                        <div class="koowa_dialog__menu">
                            <? if ($can_upload): ?>
                                <a class="koowa_dialog__menu__child--download"><?= translate('Upload'); ?></a>
                            <? endif; ?>
                            <a class="koowa_dialog__menu__child--insert"><?= translate('Select'); ?></a>
                        </div>
                        <div class="koowa_dialog__layout">
                            <div class="koowa_dialog__wrapper">
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

                                <? if ($can_upload): ?>
                                    <div id="koowa_dialog__file_dialog_upload" class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_upload">
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
                                    <div class="koowa_dialog__child__content" id="spinner_container">
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










                </form><!-- .k-list-layout -->

            </div><!-- .k-component -->

        </div><!-- k-content -->

        <!-- Sidebar -->
        <div id="k-sidebar-right" class="k-sidebar-right">
            <div class="k-sidebar__item">
                <div class="k-sidebar__header">
                    Selected file
                </div>
                <div class="k-sidebar__content">
                    <p><img src="http://demo.joomlatools.com/joomla3/docman/image-gallery/127-donkey-1/file"></p>
                    <p><strong class="labl">Name</strong> Donkey.jpg</p>
                    <p><strong class="labl">Dimensions</strong> 1280 x 897</p>
                    <p><strong class="labl">Size</strong> 449.57KB</p>
                    <p style="margin-top: 1em;"><a href="select-document.html" class="btn btn-success btn-block">Insert file</a></p>
                </div>
            </div>
        </div><!-- .k-sidebar-right -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->
