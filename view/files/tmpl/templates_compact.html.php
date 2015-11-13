<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<textarea style="display: none" id="compact_details_image">
[%
var width = 0, height = 0, ratio = 0;
if (metadata.image) {
    width  = metadata.image.width;
    height = metadata.image.height;
    ratio  = 150 / (width > height ? width : height);
}
%]
<div class="details">
    <p>
        <img class="icon" src="" alt="[%=name%]" border="0"
             onerror="kQuery(this).hide();"
            width="[%=Math.min(ratio*width, width)%]" height="[%=Math.min(ratio*height, height)%]" />
    </p>
    <p>
        <strong class="labl"><?= translate('Name'); ?></strong>
        [%=name%]
    </p>
    <p>
        <strong class="labl"><?= translate('Dimensions'); ?></strong>
        [%=width%] x [%=height%]
    </p>
    <p>
        <strong class="labl"><?= translate('Size'); ?></strong>
        [%=size.humanize()%]
    </p>
</div>
</textarea>

<textarea style="display: none" id="compact_details_file">
<div class="details">
    <div style="text-align: center">
        <span class="koowa_icon--document"><i>[%=name%]</i></span>
    </div>
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
            <tr>
                <td class="detail-label"><?= translate('Size'); ?></td>
                <td>[%=size.humanize()%]</td>
            </tr>
        </tbody>
    </table>
</div>
</textarea>

<textarea style="display: none" id="compact_container">
<ul>

</ul>
</textarea>


<? // @TODO: Make this work, right now we're only getting the <a> tags while we need the <tr> and <td> as well; ?>

<textarea style="display: none"  id="compact_folder">
    <li class="files-node files-folder">
        <a class="navigate" href="#" title="[%= name %]">
            [%= name %]
        </a>
    </li>
</textarea>

<textarea style="display: none"  id="compact_image">
    <li class="files-node files-image">
        <a class="navigate" href="#" title="[%= name %]">
            [%= name %]
        </a>
    </li>
</textarea>

<textarea style="display: none"  id="compact_file">
    <li class="files-node files-file">
        <a class="navigate" href="#" title="[%= name %]">
            [%= name %]
        </a>
    </li>
</textarea>
