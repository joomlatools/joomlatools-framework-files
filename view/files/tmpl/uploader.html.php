<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= import('com:files.files.uploader_scripts.html') ?>


<? // Inline upload style ( 1 file allowed) ?>
<div class="k-upload k-upload--single">

    <div id="files-upload-controls" class="clearfix">
        <div class="k-upload__buttons">
            <div class="btn-group">
                <button class="btn btn-sm btn-default upload-form-toggle target-computer active" href="#computer"><?= translate('Computer'); ?></button>
                <button class="btn btn-sm btn-default upload-form-toggle target-web" href="#web"><?= translate('From URL'); ?></button>
            </div>
            <p id="upload-max">
                <?= translate('Each file should be smaller than {size}', array(
                    'size' => '<span id="upload-max-size"></span>'
                )); ?>
            </p>
        </div>
    </div>

    <div id="files-uploader-computer" class="k-upload__drop upload-form">
        <div id="files-upload" class="k-upload__drop__content">
            <div class="k-upload__drop__message">
                <p>Drop files here <small>(max 10MB)</small></p>
            </div>
            <? // @TODO: @Ercan: Make sure that the name of the selected file will be visible here before you click upload
               // Also make sure that the upload info (percentage thing) is visible while uploading. This is currently hidden by CSS
               // Last but not least; we should create a better visual loading bar when uploading. remove .visuall-hidden class from .k-upload__loading div below ?>
            <div class="k-upload__drop__uploader ercan-todo" id="files-upload-multi"></div>
            <div class="k-upload__loading visually-hidden">
                <div class="k-upload__loading__bar"></div>
                <div class="k-upload__loading__text">
                    50%
                </div>
            </div>
        </div>
    </div>

    <div id="files-uploader-web" class="upload-form k-upload__web" style="display: none">
        <form action="" method="post" name="remoteForm" id="remoteForm" >
            <div class="input-group remote-wrap">
                <div class="input-group-input">
                    <input class="form-control has-left-radius" type="text" placeholder="<?= translate('Remote Link') ?>" title="<?= translate('Remote Link') ?>" id="remote-url" name="file" size="50" />
                </div>
                <div class="input-group-input">
                    <input class="form-control" type="text" placeholder="<?= translate('File name') ?>" id="remote-name" name="name" />
                </div>
                <span class="input-group-btn">
                    <input type="submit" class="remote-submit btn" disabled value="<?= translate('Transfer File'); ?>" />
                </span>
            </div>
            <input type="hidden" name="action" value="save" />
        </form>
    </div>



</div>

<? if ( 1 == 2 ) : ?>
<div class="js-com_files" style="visibility: hidden">
    <div id="files-upload" style="clear: both" class="uploader-files-empty well">
        <div style="text-align: center;">
            <h3 style=" float: none">
                <?= translate('Upload files to {folder}', array(
                    'folder' => '<span id="upload-files-to"></span>'
                )) ?>
            </h3>
        </div>
        <div id="files-upload-controls" class="clearfix">
            <ul class="upload-buttons">
                <li><?= translate('Upload from:') ?></li>
                <li><a class="upload-form-toggle target-computer active" href="#computer"><?= translate('Computer'); ?></a></li>
                <li><a class="upload-form-toggle target-web" href="#web"><?= translate('Web'); ?></a></li>
                <li id="upload-max">
                    <?= translate('Each file should be smaller than {size}', array(
                        'size' => '<span id="upload-max-size"></span>'
                    )); ?>
                </li>
            </ul>
        </div>
        <div id="files-uploader-computer" class="upload-form">

            <div style="clear: both"></div>
            <div class="dropzone">
                <h2><?= translate('Drag files here') ?></h2>
            </div>
            <h3 class="nodropzone"><?= translate('Or select a file to upload:') ?></h3>
            <div id="files-upload-multi"></div>

        </div>
        <div id="files-uploader-web" class="upload-form" style="display: none">
            <form action="" method="post" name="remoteForm" id="remoteForm" >
                <div class="remote-wrap">
                    <input type="text" placeholder="<?= translate('Remote Link') ?>" title="<?= translate('Remote Link') ?>" id="remote-url" name="file" size="50" />
                    <input type="text" placeholder="<?= translate('File name') ?>" id="remote-name" name="name" />
                </div>
                <input type="submit" class="remote-submit btn" disabled value="<?= translate('Transfer File'); ?>" />
                <input type="hidden" name="action" value="save" />
            </form>
        </div>
    </div>
</div>
<? endif; ?>

