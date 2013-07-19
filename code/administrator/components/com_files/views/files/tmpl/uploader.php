<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= @template('uploader_initialize') ?>

<div class="com_files" style="visibility: hidden">
    <div id="files-upload" style="clear: both" class="uploader-files-empty well">
        <div style="text-align: center;">
            <h3 style=" float: none">
                <?= @text('Upload files to %folder%', array(
                    '%folder%' => '<span id="upload-files-to"></span>'
                )) ?>
            </h3>
        </div>
        <div id="files-upload-controls" class="clearfix">
            <ul class="upload-buttons">
                <li><?= @text('Upload from:') ?></li>
                <li><a class="upload-form-toggle target-computer active" href="#computer"><?= @text('Computer'); ?></a></li>
                <li><a class="upload-form-toggle target-web" href="#web"><?= @text('Web'); ?></a></li>
                <li id="upload-max">
                    <?= @text('Each file should be smaller than %size%', array(
                        '%size%' => '<span id="upload-max-size"></span>'
                    )); ?>
                </li>
            </ul>
        </div>
        <div id="files-uploader-computer" class="upload-form">

            <div style="clear: both"></div>
            <div class="dropzone">
                <h2><?= @text('Drag files here') ?></h2>
            </div>
            <h3 class="nodropzone"><?= @text('OR Select a file to upload:') ?></h3>
            <div id="files-upload-multi"></div>

        </div>
        <div id="files-uploader-web" class="upload-form" style="display: none">
            <form action="" method="post" name="remoteForm" id="remoteForm" >
                <div class="remote-wrap">
                    <input type="text" placeholder="<?= @text('Remote URL') ?>" title="<?= @text('Remote URL') ?>" id="remote-url" name="file" size="50" />
                    <input type="text" placeholder="<?= @text('File name') ?>" id="remote-name" name="name" />
                </div>
                <input type="submit" class="remote-submit btn" disabled value="<?= @text('Transfer File'); ?>" />
                <input type="hidden" name="action" value="save" />
            </form>
        </div>
    </div>
</div>
