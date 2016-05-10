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
            <span class="koowa_icon--document"></span><strong style="display: inline-block;vertical-align: top;margin-left: 10px;">[%=name%]</strong>
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
    <div class="k-grid__items">
        <div class="k-grid__items__folders"></div>
        <div class="k-grid__items__files"></div>
    </div>
</textarea>

<textarea style="display: none" id="icons_folder">
    <div class="k-grid__item k-grid__item--folder files-node files-folder">
        <div class="k-grid__item__title js-navigate-folder">
            <div class="controls" style="display: inline-block">
                <input type="checkbox" class="files-select" value="" />
            </div>
            <a href="#" class="navigate">[%=name%]</a>
        </div>
    </div>
</textarea>

<textarea style="display: none" id="icons_file">
    <div class="k-grid__item k-grid__item--file files-node files-file">
        <div class="k-grid__item__content">
            <div class="k-grid__file-wrapper">
                [%
                var icon = 'default',
                extension = name.substr(name.lastIndexOf('.')+1).toLowerCase();

                kQuery.each(Files.icon_map, function(key, value) {
                if (kQuery.inArray(extension, value) !== -1) {
                icon = key;
                }
                });
                %]
                <a class="k-grid__file navigate" href="#"
                   data-filetype="[%=filetype%]"
                   data-extension="[%=metadata.extension%]">
                    <div class="k-grid__item__cell">
                        <span class="koowa_icon--[%=icon%] koowa_icon--48 extension-label"></span>
                    </div>
                </a>

            </div>
        </div>
        <div class="k-grid__item__title js-select-node">
            <div class="controls" style="display: inline-block">
                <input type="checkbox" class="files-select" value="" />
            </div>
            [%=name%]
        </div>
    </div>
</textarea>

<textarea style="display: none" id="icons_image">
    <div class="k-grid__item k-grid__item--file  files-node files-image ">
        <div class="k-grid__item__content">
            <div class="k-grid__file-wrapper">
                <a  class="k-grid__file navigate
                    [%= typeof thumbnail === 'string' ? '' : 'koowa_icon--image koowa_icon--48' %]"  href="#" title="[%=name%]"
                   data-filetype="[%=filetype%]"
                   data-extension="[%=metadata.extension%]">
                    [% if (typeof thumbnail === 'string') { %]
                    <div class="k-grid__item__cell">
                        <div class="spinner"></div>
                        <img src="[%= client_cache || Files.blank_image %]" alt="[%=name%]" border="0" class="image-thumbnail [%= client_cache ? 'loaded' : '' %]" />
                    </div>
                    [% } %]
                </a>
            </div>
        </div>
        <div class="k-grid__item__title js-select-node">
            <div class="controls" style="display: inline-block">
                <input type="checkbox" class="files-select" value="" />
            </div>
            [%=name%]
        </div>
    </div>
</textarea>
