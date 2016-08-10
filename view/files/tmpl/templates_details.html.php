<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
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
    <div class="k-table-container">
        <div class="k-table">
            <table class="k-js-fixed-table-header k-js-responsive-table">
                <thead>
                <tr>
                    <th width="1%">
                        <input type="checkbox" class="-check-all" id="select-check-all" />
                    </th>
                    <th width="1%" data-toggle="true"></th>
                    <th class="files__sortable" data-name="name">
                        <?= translate('Name'); ?>
                        <span class="files__sortable--indicator koowa_icon--sort koowa_icon--12"></span>
                    </th>
                    <th width="1%" data-hide="phone">
                        <?= translate('Size'); ?>
                    </th>
                    <th class="files__sortable" width="1%" data-hide="phone,tablet,desktop" data-name="modified_on">
                        <?= translate('Last Modified'); ?>
                        <span class="files__sortable--indicator koowa_icon--sort koowa_icon--12"></span>
                    </th>
                    <th class="k-table-data--center" width="1%" data-hide="phone,tablet">
                        <span class="k-icon-data-transfer-download"></span>
                    </th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</textarea>

<textarea style="display: none" id="details_folder">
	<tr class="files-node files-folder">
		<td>
			<input type="checkbox" class="files-select" value="" />
		</td>
		<td>
            <span class="k-icon-folder-closed"></span>
		</td>
		<td class="k-table-data--ellipsis" colspan="4">
            <a href="#" class="navigate koowa-tooltip" data-koowa-tooltip='{"container":".koowa-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('Open folder') ?>">[%=name%]</a>
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
            <span class="k-icon-document-[%=icon%]"></span>
        </td>
		<td class="k-table-data--ellipsis">
            <a href="#" class="navigate koowa-tooltip" data-koowa-tooltip='{"container":".koowa-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('View file info') ?>">[%=name%]</a>
		</td>
		<td class="k-table-data--nowrap">
            [%=size.humanize()%]
		</td>
		<td class="k-table-data--nowrap">
            [%=getModifiedDate(true)%]
		</td>
        <td class="k-table-data--center">
            <a href="[%=download_link%]" target="_blank" download="[%=name%]">
                <span class="k-icon-data-transfer-download"></span>
            </a>
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
                <span class="k-icon-document-image"></span>
            [% } %]
		</td>
		<td class="k-table-data--ellipsis">
            <a href="#" class="navigate koowa-tooltip" data-koowa-tooltip='{"container":".koowa-container","delay":{"show":500,"hide":50}}' data-original-title="<?= translate('View image') ?>">[%=name%]</a>
		</td>
		<td class="k-table-data--nowrap">
            [%=size.humanize()%][% if (metadata.image) { %]<br />
            <small>([%=metadata.image.width%] x [%=metadata.image.height%])[% } %]</small>
		</td>
		<td class="k-table-data--nowrap">
            [%=getModifiedDate(true)%]
		</td>
        <td class="k-table-data--center">
            <a href="[%=download_link%]" target="_blank" download="[%=name%]">
                <span class="k-icon-data-transfer-download"></span>
            </a>
        </td>
	</tr>
</textarea>