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

<div class="k-sidebar-left k-js-sidebar-left">

    <div class="k-sidebar-item k-js-sidebar-overflow-item">
        <div class="k-sidebar-item__header">
            <?= translate('Select a folder'); ?>
        </div>
        <div class="k-tree" id="files-tree"></div>
    </div>

    <? if ($can_upload): ?>
        <div class="k-sidebar-item">
            <div class="k-sidebar-item__header">
                <?= translate('Create a new folder'); ?>
            </div>
            <div class="k-sidebar-item__content">
                <div id="files-new-folder-modal">
                    <div class="k-input-group">
                        <input type="text" class="k-form-control" id="files-new-folder-input" />
                        <span class="k-input-group__button">
                            <button id="files-new-folder-create" class="k-button k-button--default k-button--block" disabled>
                                <span class="k-icon-plus" aria-hidden="true"></span>
                                <span class="k-visually-hidden"><?= translate('Add folder'); ?></span>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <? endif; ?>

</div><!-- .k-sidebar-left -->