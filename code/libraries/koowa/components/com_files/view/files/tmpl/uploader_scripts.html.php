<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= @helper('translator.script', array('translations' => array(
    'Filename',
    'Status',
    'Size',
    'Add files',
    'Start upload',
    'Clear queue',
    'Uploaded %d/%d files',
    '%d files queued',
    'Drag files here.',
    'Error: File too large:',
    'Error: Invalid file extension:',
    'A file with the same name already exists. Would you like to overwrite it?',
    'Are you sure you want to clear the upload queue? This cannot be undone!',
    'Following files already exist. Would you like to overwrite them? {names}',
    'Drop your files to upload to {folder}',
    '{html5} or {flash} required for uploading files from your computer.',
    'HTML5 enabled browser',
    'Flash Player',
    'Uploaded successfully!',
    'Select files from your computer',
    'Select file'
))); ?>

<? /*
For debugging:
<ktml:script src="media://koowa/com_files/js/plupload/moxie.js" />
<ktml:script src="media://koowa/com_files/js/plupload/plupload.dev.js" />
<ktml:script src="media://koowa/com_files/js/plupload/plupload.queue.js" />
*/ ?>
<ktml:script src="media://koowa/com_files/js/uploader.min.js" />

<script>
window.addEvent('domready', function() {
    var timeout = null,
        createUploader = function() {
            if (Files.app) {
                Files.createUploader({
                    multi_selection: <?= json_encode((!isset($multi_selection) || $multi_selection !== false) ? true : false) ?>,
                    media_path: 'media://'
                });

                if (timeout) {
                    clearTimeout(timeout);
                }
            } else {
                timeout = setTimeout(createUploader, 100);
            }
        };

    createUploader();

});
</script>
