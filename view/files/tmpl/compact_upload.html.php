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




<? if ($can_upload): ?>
    <div id="koowa_dialog__file_dialog_upload">
        <?= import('com:files.files.uploader.html', array('multi_selection' => false)); ?>
    </div>
<? endif; ?>




<? // @TODO: @Ercan: When I remove this everything stil keeps working? I might have added it here whilst I shouldn't have :) ?>
<? if ( 1 == 2 ) : ?>
<div class="koowa_dialog koowa_dialog--file_dialog koowa_dialog--no_menu koowa_dialog--no_categories">
    <div class="koowa_dialog__layout">
        <div class="koowa_dialog__wrapper">
            <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_categories" style="display: none">
                <h2 class="koowa_dialog__title">
                    <?= translate('Select a folder'); ?>
                </h2>
                <div class="koowa_dialog__child__content koowa_dialog__folders_files">
                    <div class="koowa_dialog__child__content__box">
                        <div class="koowa_dialog__files_tree" id="files-tree" style="overflow: auto; height: auto;"></div>
                    </div>
                </div>
            </div>
            <div id="koowa_dialog__file_dialog_upload" class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_upload">
                <h2 class="koowa_dialog__title">
                    <?= translate('Upload a file'); ?>
                </h2>
                <div class="koowa_dialog__child__content">
                    <div class="koowa_dialog__child__content__box">
                        <?= import('com:files.files.uploader.html', array('multi_selection' => (isset(parameters()->config['multi_selection']) ? parameters()->config['multi_selection'] : false))); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="files-grid" style="display: none"></div>
    <div id="files-preview" style="display: none"></div>
    <div id="insert-button-container" style="display: none"></div>
</div>
<? endif; ?>
