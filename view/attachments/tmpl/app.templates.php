<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die; ?>

<textarea style="display: none" id="attachments_container">
<ul class="sidebar-nav">

</ul>
</textarea>

<textarea style="display: none"  id="attachments_attachment">
<li class="files-node files-attachment">
    <a class="navigate" href="#" title="[%= name %]">
        [%= name %]
    </a>
</li>
</textarea>


<textarea style="display: none" id="attachments_details_attachment">
[%
var width = 0, height = 0, ratio = 0, is_image = (file.type == 'image');
if (is_image && file.metadata.image) {
    width  = file.metadata.image.width;
    height = file.metadata.image.height;
    ratio  = 150 / (width > height ? width : height);
}
%]
<div class="details">
    [% if (is_image) { %]
    <div style="text-align: center">
        <img class="icon" src="" alt="[%=name%]" border="0"
             onerror="kQuery(this).hide();"
             width="[%=Math.min(ratio*width, width)%]" height="[%=Math.min(ratio*height, height)%]" />
    </div>
    [% } else { %]
    <div style="text-align: center">
        <span class="koowa_icon--document"><i>[%=name%]</i></span>
    </div>
    [% } %]
    <table class="table table-condensed parameters">
        <tbody>
        <tr>
            <td class="detail-label"><?= translate('Name'); ?></td>
            <td>
                <div class="koowa_wrapped_content">
                    <div class="whitespace_preserver">[%=name%]</div>
                </div>
            </td>
        </tr>
        [% if (is_image) { %]
        <tr>
            <td class="detail-label"><?= translate('Dimensions'); ?></td>
            <td>[%=width%] x [%=height%]</td>
        </tr>
        [% } %]
        <tr>
            <td class="detail-label"><?= translate('Size'); ?></td>
            <td>[%=size.humanize()%]</td>
        </tr>
        <tr>
            <td class="detail-label"><?= translate('Attached by') ?></td>
            <td>
                <div class="koowa_wrapped_content">
                    <div class="whitespace_preserver">[%=attached_by_name%]</div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="detail-label"><?= translate('Attached on') ?></td>
            <td>
                <div class="koowa_wrapped_content">
                    <div class="whitespace_preserver">[%=getAttachedDate(true)%]
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
</textarea>