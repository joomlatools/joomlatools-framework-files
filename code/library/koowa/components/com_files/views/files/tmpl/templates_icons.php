<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<textarea style="display: none" id="file_preview">
<div>
<div class="preview extension-[%=metadata.extension%]">
    [% var view_path = Files.app.createRoute({view: 'file', format: 'raw', name: name, folder: folder}); %]
    <img src="media://com_files/images/document-64.png" width="64" height="64" alt="[%=name%]" border="0" />

    <div class="btn-toolbar">
        [% if (typeof image !== 'undefined') { %]
        <a class="btn btn-mini" href="[%=view_path%]" target="_blank">
            <i class="icon-eye-open"></i> <?= @text('View'); ?>
        </a>
        [% } else { %]
        <a class="btn btn-mini" href="[%=view_path%]" target="_blank" download="[%=name%]">
            <i class="icon-download"></i> <?= @text('Download'); ?>
        </a>
        [% } %]
    </div>
</div>
<hr />
<div class="details">
    <table class="table table-condensed parameters">
        <tbody>
        <tr>
            <td class="detail-label"><?= @text('Name'); ?></td>
            <td>[%=name%]</td>
        </tr>
        <tr>
            <td class="detail-label"><?= @text('Size'); ?></td>
            <td>[%=size.humanize()%]</td>
        </tr>
        <tr>
            <td class="detail-label"><?= @text('Modified'); ?></td>
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
    			<a href="#" class="navigate"></a>
    	</div>
    	<div class="files-icons-controls">
    	<div class="controls" style="display:none">
    		<input type="checkbox" class="files-select" value="" />
    	</div>
    	<div class="ellipsis" style="width:[%= icon_size%]px" title="[%=name%]">
    		[%=name%]
    	</div>
    	</div>
    </div>
</div>
</textarea>

<textarea style="display: none" id="icons_file">
<div class="files-node-shadow">
    <div class="imgOutline files-node files-file">
    	<div class="imgTotal files-node-thumbnail" style="width:[%= icon_size%]px; height: [%= icon_size*0.75%]px">
    	 	<a class="navigate extension-label" href="#"
    	 		data-filetype="[%=filetype%]"
    	 		data-extension="[%=metadata.extension%]"></a>
    	</div>
    	<div class="files-icons-controls">
    	<div class="controls" style="display:none">
    		<input type="checkbox" class="files-select" value="" />
    	</div>
    	<div class="ellipsis" style="width:[%= icon_size%]px" title="[%=name%]">
    		[%=name%]
    	</div>
    	</div>
    </div>
</div>
</textarea>

<textarea style="display: none" id="icons_image">
<div class="files-node-shadow">
    <div class="imgOutline [%= typeof thumbnail === 'string' ? 'thumbnails' : 'nothumbnails' %] files-node files-image [%= typeof thumbnail === 'string' ? (client_cache ? 'load' : 'loading') : '' %]">
    	<div class="imgTotal files-node-thumbnail" style="width:[%= icon_size%]px; height: [%= icon_size*0.75%]px">
    		<a class=" navigate" href="#" title="[%=name%]"
    	 		data-filetype="[%=filetype%]"
    	 		data-extension="[%=metadata.extension%]">
    		[% if (typeof thumbnail === 'string') { %]
    		    <div class="spinner"></div>
    			<img src="[%= client_cache || Files.blank_image %]" alt="[%=name%]" border="0" class="image-thumbnail [%= client_cache ? 'loaded' : '' %]" style="max-width: [%=metadata.image.width%]px" />
    		[% } %]
    		</a>
    	</div>
    	<div class="files-icons-controls">
    	<div class="controls" style="display:none">
    		<input type="checkbox" class="files-select" value="" />
    	</div>
    	<div class="ellipsis" style="width:[%= icon_size%]px" title="[%=name%]">
    		[%=name%]
    	</div>
    	</div>
    </div>
</div>
</textarea>