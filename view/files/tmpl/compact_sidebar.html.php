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

<div id="k-sidebar" class="k-sidebar">

    <div class="k-sidebar__item k-sidebar__item--overflow">
        <div class="k-sidebar__header">
            <?= translate('Select a folder'); ?>
        </div>
        <div class="koowa_dialog__wrapper__child koowa_dialog__file_dialog_categories">
            <div class="koowa_dialog__child__content koowa_dialog__folders_files">
                <div class="koowa_dialog__child__content__box">
                    <div class="koowa_dialog__files_tree" id="files-tree" style="overflow: auto; height: auto;"></div>
                </div>
            </div>
        </div>
    </div>

    <? if ($can_upload): ?>
        <div class="k-sidebar__item">
            <div class="k-sidebar__header">
                <?= translate('Create a new folder'); ?>
            </div>
            <div class="k-sidebar__content">
                <div class="margin-bottom--small" id="files-new-folder-modal">
                    <div class="input-group">
                        <input type="text" class="form-control" id="files-new-folder-input" />
                        <span class="input-group-btn">
                            <button id="files-new-folder-create" class="btn btn-default" disabled>
                                <span class="k-icon-plus"></span>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <? endif; ?>

</div>