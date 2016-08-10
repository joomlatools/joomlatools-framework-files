<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<textarea style="display: none" id="file_preview">
<div class="well extension-[%=metadata.extension%]">
    <div class="k-file-info">
        [% var view_path = Files.app.createRoute({view: 'file', format: 'html', name: name, folder: folder}); %]
        <p>
            <span class="k-icon-document"></span><strong style="display: inline-block;vertical-align: top;margin-left: 10px;">[%=name%]</strong>
        </p>
        <p>
            [% if (typeof image !== 'undefined') { %]
            <a class="btn btn-mini" href="[%=view_path%]" target="_blank">
                <i class="icon-eye-open"></i> <?= translate('View'); ?>
            </a>
            [% } else { %]
            <a class="btn btn-mini" href="[%=view_path%]" target="_blank" download="[%=name%]">
                <i class="icon-download"></i> <?= translate('Download'); ?>
            </a>
            [% } %]
        </p>
        <div class="k-mini-table">
            <table>
                <tbody>
                <tr>
                    <td class="detail-label"><?= translate('Name'); ?></td>
                    <td>
                        <div class="koowa_wrapped_content">
                            <div class="whitespace_preserver">[%=name%]</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="detail-label"><?= translate('Size'); ?></td>
                    <td>[%=size.humanize()%]</td>
                </tr>
                <tr>
                    <td class="detail-label"><?= translate('Modified'); ?></td>
                    <td>[%=getModifiedDate(true)%]</td>
                </tr>
                </tbody>
            </table>
        </div>
        <? // @TODO: Robin: move this to scss files; ?>
        <style type="text/css">
            .k-mini-table table {
                width: 100%;
            }
            .k-mini-table td {
                padding: 5px 5px 5px 0;
                border-bottom: 1px solid #ccc;
            }
            .k-mini-table tr:last-child td {
                border-bottom: none;
            }
        </style>
    </div>
</div>
</textarea>

<textarea style="display: none" id="icons_container">
    <div class="k-gallery__items"></div>
</textarea>

<textarea style="display: none" id="icons_folder">
    <div class="k-gallery__item k-gallery__item--folder files-node files-folder">
        <div class="k-card k-card--rounded js-navigate-folder">
            <div class="k-card__body">
                <label class="k-card__section">
                    <input type="checkbox" class="files-select" value="" />
                    <a href="javascript:void(0)" class="navigate">[%=name%]</a>
                </label>
            </div>
        </div>
    </div>
</textarea>

<textarea style="display: none" id="icons_file">
    <div class="k-gallery__item k-gallery__item--file files-node files-file">
        <div class="k-card k-card--rounded">
            [%
            var icon = 'default',
            extension = name.substr(name.lastIndexOf('.')+1).toLowerCase();

            kQuery.each(Files.icon_map, function(key, value) {
            if (kQuery.inArray(extension, value) !== -1) {
            icon = key;
            }
            });
            %]
            <a href="javascript:void(0)"
               class="k-card__body"
               data-filetype="[%=filetype%]"
               data-extension="[%=metadata.extension%]">
                <div class="k-card__section k-card__section--small-spacing">
                    <div class="k-ratio-block k-ratio-block--4-to-3">
                        <div class="k-ratio-block__body">
                            <div class="k-ratio-block__centered">
                                <span class="k-icon-document-[%=icon%] k-icon--size-large k-icon--accent extension-label"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <label class="k-card__caption k-card__caption--overflow js-select-node">
                <input type="checkbox" class="files-select" value="" />
                [%=name%]
            </label>
        </div>
    </div>
</textarea>

<textarea style="display: none" id="icons_image">
    <div class="k-gallery__item k-gallery__item--file files-node files-image">
        <div class="k-card k-card--rounded">
            <a href="javascript:void(0)"
               class="k-card__body"
               title="[%=name%]"
               data-filetype="[%=filetype%]"
               data-extension="[%=metadata.extension%]">
                <div class="k-card__section k-card__section--small-spacing">
                    <div class="k-ratio-block k-ratio-block--4-to-3">
                        <div class="k-ratio-block__body">
                            <div class="k-ratio-block__centered">
                            [% if (typeof thumbnail === 'string') { %]
                                [% var width = metadata.image.width %]
                                [% var height = metadata.image.height %]
                                <? // @TODO: Ercan: I guess we need the new spinner here ?>
                                <!--<div class="spinner"></div>-->
                                        <img class="image-thumbnail [%= client_cache ? 'loaded' : '' %]" src="" alt="[%=name%]" border="0" />
                            [% } else { %]
                                <span class="k-icon-document-image k-icon--size-large k-icon--accent"></span>
                            [% }%]
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <label class="k-card__caption k-card__caption--overflow js-select-node">
                <input type="checkbox" class="files-select" value="" />
                [%=name%]
            </label>
        </div>
    </div>
</textarea>
