<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<script>
window.addEvent('domready', function() {
	document.id('files-canvas').addEvent('click:relay(input.-check-all)', function(e) {
		var value = e.target.checked,
			grid = Files.app.grid,
			nodes = grid.nodes;

		Object.each(nodes, function(node) {
			if (value && !node.checked) {
				grid.checkNode(node);
			} else if (!value && node.checked) {
				grid.checkNode(node);
			}

		});

	});
})
</script>

<textarea style="display: none" id="details_container">
<div class="manager">
	<table class="table table-striped"  style="clear: both;">
		<thead>
			<tr>
                <th width="10">
                    <div class="btn-group">
                        <label class="btn dropdown-toggle" style="
                        padding-top: 6px;
                        padding-bottom: 6px;
                        border-bottom: none;
                        padding-left: 10px;
                        border-top: none;
                        border-left: none;
                        border-radius: 0;
                        ">
                            <input type="checkbox" class="-check-all" id="select-check-all" />
                        </label>
                    </div>
                </th>
                <th width="32"></th>
				<th><?= translate('Name'); ?></th>
                <th width="100" style="text-align: center;"><?= translate('Size'); ?></th>
                <th width="100" style="text-align: center;"><?= translate('Last Modified'); ?></th>
                <th width="1" style="text-align: center;"><i class="icon-download"></i></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
</textarea>

<textarea style="display: none" id="details_folder">
	<tr class="files-node files-folder">
		<td>
			<input type="checkbox" class="files-select" value="" />
		</td>
		<td>
            <span class="koowa_icon--folder"><i>[%=name%]</i></span>
		</td>
		<td colspan="4">
            <div class="koowa_wrapped_content">
                <div class="whitespace_preserver">
                    <a href="#" class="navigate">[%=name%]</a>
                </div>
            </div>
		</td>
	</tr>
</textarea>

<textarea style="display: none" id="details_file">
	<tr class="files-node files-file">
		<td>
			<input type="checkbox" class="files-select" value="" />
		</td>
        [%
        var icon = 'default',
        extension = name.substr(name.lastIndexOf('.')+1).toLowerCase();

        kQuery.each(Files.icon_map, function(key, value) {
        if (kQuery.inArray(extension, value) !== -1) {
        icon = key;
        }
        });
        %]
        <td>
            <span class="koowa_icon--[%=icon%]"><i>[%=icon%]</i></span>
        </td>
		<td>
            <div class="koowa_wrapped_content">
                <div class="whitespace_preserver">
                    <a href="#" class="navigate">[%=name%]</a>
                </div>
            </div>
		</td>
		<td style="text-align: center;">
            <div class="koowa_wrapped_content">
                <div class="whitespace_preserver">
                    [%=size.humanize()%]
                </div>
            </div>
		</td>
		<td style="text-align: center;">
            <div class="koowa_wrapped_content">
                <div class="whitespace_preserver">
                    [%=getModifiedDate(true)%]
                </div>
            </div>
		</td>
        <td align="right">
            <a class="btn btn-mini" href="[%=download_link%]" target="_blank" download="[%=name%]"><i class="icon-download"></i></a>
        </td>
	</tr>
</textarea>

<textarea style="display: none" id="details_image">
	<tr class="files-node files-image">
		<td>
			<input type="checkbox" class="files-select" value="" />
		</td>
		<td>
            [% if (typeof thumbnail === 'string') { %]
                <img src="[%= client_cache || Files.blank_image %]" alt="[%=name%]" border="0" class="image-thumbnail [%= client_cache ? 'loaded' : '' %]" height="24px" />
            [% } else { %]
                <span class="koowa_icon--image"><i>[%=name%]</i></span>
            [% } %]
		</td>
		<td>
            <div class="koowa_wrapped_content">
                <div class="whitespace_preserver">
                    <a href="#" class="navigate">[%=name%]</a>
                </div>
            </div>
		</td>
		<td style="text-align: center;">
            <div class="koowa_wrapped_content">
                <div class="whitespace_preserver">
                    [%=size.humanize()%][% if (metadata.image) { %]<br />
                    ([%=metadata.image.width%] x [%=metadata.image.height%])[% } %]
                </div>
            </div>
		</td>
		<td style="text-align: center;">
            <div class="koowa_wrapped_content">
                <div class="whitespace_preserver">
                    [%=getModifiedDate(true)%]
                </div>
            </div>
		</td>
        <td align="right">
            <a class="btn btn-mini" href="[%=download_link%]" target="_blank" download="[%=name%]"><i class="icon-download"></i></a>
        </td>
	</tr>
</textarea>