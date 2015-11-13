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
<?= import('templates_compact.html');?>

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
        <?= import('compact_sidebar.html'); ?>

        <!-- Content -->
        <div class="k-content">

          <!-- Component -->
            <div class="k-component">

                <!-- Form -->
                <form class="k-list-layout -koowa-grid" id="k-offcanvas-container" action="" method="get">

                    <? // @TODO: :Ercan: Get a working dynamic pathway here; ?>
                    <?= import('compact_breadcrumbs.html'); ?>

                    <? // @TODO: Ercan: We need to fix the uploader; ?>
                    <?= import('compact_upload.html'); ?>

                    <? // @TODO: Ercan: We need to fix the scopebar; ?>
                    <?= import('compact_scopebar.html'); ?>

                    <? // @TODO: Ercan: We need to fix the file picker so it's a table; ?>
                    <?= import('compact_select.html'); ?>

                </form><!-- .k-list-layout -->

            </div><!-- .k-component -->

        </div><!-- k-content -->

        <!-- Sidebar -->
        <?= import('compact_sidebar_right.html'); ?>

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->
