<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
defined('KOOWA') or die; ?>

<textarea style="display: none" id="attachments_container">
    <table id="document_list">
        <thead>
        <tr>
            <th class="koowa_dialog__title">
                <?= translate('Attached files'); ?>
                <span class="count"></span>
            </th>
        </tr>
        </thead>
        <tbody id="attachments-grid"></tbody>
    </table>
</textarea>

<textarea style="display: none" id="attachments_attachment">
    <table>
        <tbody class="template-item">
            <tr class="files-node files-attachment">
                <td>
                    <? if ($can_detach): ?>
                        <span class="detach_button k-table-button k-link-ontop" tabindex="0">x</span>
                    <? endif ?>
                    <a class="navigate k-link-coverall" href="#" title="[%= name %]">
                        [%= name %]
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</textarea>

<textarea style="display: none" id="attachments_details_attachment">
    [%
    var width = 0, height = 0, ratio = 0,
        is_image = (file.type == 'image');
    if (is_image && file.metadata.image) {
        width  = file.metadata.image.width;
        height = file.metadata.image.height;
        ratio  = 150 / (width > height ? width : height);
    }
    %]
    <div class="k-details">
        [% if (is_image) { %]
            [% if (thumbnail) { %]
            <div class="k-details-image-placeholder">
                <div class="k-details-image-placeholder__content">
                    <img class="icon" src="" alt="[%=name%]" border="0"
                         onerror="kQuery(this).hide();"
                         width="[%=Math.min(ratio*width, width)%]" height="[%=Math.min(ratio*height, height)%]" />
                </div>
            </div>
            [% } else { %]
            <div>
                <span class="koowa_icon--image koowa_icon--32"><i>[%=name%]</i></span>
            </div>
            [% } %]
        [% } else { %]
        <div style="text-align: center">
            <span class="koowa_icon--document koowa_icon--32"><i>[%=name%]</i></span>
        </div>
        [% } %]
        [% if (is_image) { %]
        <p>
            <strong class="labl"><?= translate('Dimensions'); ?></strong>
            [%=width%] x [%=height%]
        </p>
        [% } %]
        <p>
            <strong class="labl"><?= translate('Size'); ?></strong>
            [%=size.humanize()%]
        </p>
        <p>
            <strong class="labl"><?= translate('Attached by'); ?></strong>
            [%=attached_by_name%] <small><?= translate('on') ?> [%=getAttachedDate(true)%]</small>
        </p>
    </div>
</textarea>