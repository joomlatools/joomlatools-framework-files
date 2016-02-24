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
<div style="position: relative;background-color: white;margin: 20px auto;max-width: 300px;">
<div class="preview extension-[%=metadata.extension%]">
    [% var view_path = Files.app.createRoute({view: 'file', format: 'html', name: name, folder: folder}); %]
    <span class="koowa_icon--document"><i>[%=name%]</i></span>

    <div class="btn-toolbar">
        [% if (typeof image !== 'undefined') { %]
        <a class="btn btn-mini" href="[%=view_path%]" target="_blank">
            <i class="icon-eye-open"></i> <?= translate('View'); ?>
        </a>
        [% } else { %]
        <a class="btn btn-mini" href="[%=view_path%]" target="_blank" download="[%=name%]">
            <i class="icon-download"></i> <?= translate('Download'); ?>
        </a>
        [% } %]
    </div>
</div>
<hr />
<div class="details">
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
        <tr>
            <td class="detail-label"><?= translate('Modified'); ?></td>
            <td>[%=getModifiedDate(true)%]</td>
        </tr>
        </tbody>
    </table>
</div>
</div>
</textarea>

<textarea style="display: none" id="icons_container">
<div>

</div>
</textarea>

<textarea style="display: none" id="icons_folder">
<div class="files-node-shadow">
    <div class="imgOutline files-node files-folder">
    	<div class="imgTotal files-node-thumbnail" style="width:[%= icon_size%]px; height: [%= icon_size*0.75%]px">
    			<a href="#" class="navigate koowa_icon--folder koowa_icon--48"></a>
    	</div>

        <div class="files-icons-controls">
            <label class="checkbox ellipsis" style="width:[%= icon_size%]px" title="[%=name%]">
                <input type="checkbox" class="files-select" />[%=name%]
            </label>
        </div>

    </div>
</div>
</textarea>

<textarea style="display: none" id="icons_file">
<div class="files-node-shadow">
    <div class="imgOutline files-node files-file">
    	<div class="imgTotal files-node-thumbnail" style="width:[%= icon_size%]px; height: [%= icon_size*0.75%]px">

            [%
            var icon = 'default',
            extension = name.substr(name.lastIndexOf('.')+1).toLowerCase();

            kQuery.each(Files.icon_map, function(key, value) {
                if (kQuery.inArray(extension, value) !== -1) {
                    icon = key;
                }
            });
            %]
    	 	<a class="navigate koowa_icon--[%=icon%] koowa_icon--48 extension-label" href="#"
    	 		data-filetype="[%=filetype%]"
    	 		data-extension="[%=metadata.extension%]"></a>
    	</div>

        <div class="files-icons-controls">
            <label class="checkbox ellipsis" style="width:[%= icon_size%]px" title="[%=name%]">
                <input type="checkbox" class="files-select" />[%=name%]
            </label>
        </div>
    </div>
</div>
</textarea>

<textarea style="display: none" id="icons_image">
<div class="files-node-shadow">
    <div class="imgOutline [%= typeof thumbnail === 'string' ? 'thumbnails' : 'nothumbnails' %] files-node files-image [%= typeof thumbnail === 'string' ? (client_cache ? 'load' : 'loading') : '' %]">
    	<div class="imgTotal files-node-thumbnail" style="width:[%= icon_size%]px; height: [%= icon_size*0.75%]px">
      		<a class="navigate
      		        [%= typeof thumbnail === 'string' ? '' : 'koowa_icon--image koowa_icon--48' %]"  href="#" title="[%=name%]"
      	 		data-filetype="[%=filetype%]"
      	 		data-extension="[%=metadata.extension%]">
      		[% if (typeof thumbnail === 'string') { %]
      		    <div class="spinner"></div>
      			<img src="[%= client_cache || Files.blank_image %]" alt="[%=name%]" border="0" class="image-thumbnail [%= client_cache ? 'loaded' : '' %]" style="max-width: [%=metadata.image? metadata.image.width : 512%]px" />
      		[% } %]
      		</a>
    	</div>

    	<div class="files-icons-controls">
        	<label class="checkbox ellipsis" style="width:[%= icon_size%]px" title="[%=name%]">
        		<input type="checkbox" class="files-select" />[%=name%]
        	</label>
    	</div>
    </div>
</div>
</textarea>
