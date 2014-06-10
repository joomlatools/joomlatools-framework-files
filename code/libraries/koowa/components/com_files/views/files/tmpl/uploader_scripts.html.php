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
    'All Files',
    '%d files queued',
    'Drag files here.',
    'Error: File too large:',
    'Error: Invalid file extension:',
    'A file with the same name already exists. Would you like to overwrite it?',
    'Are you sure you want to clear the upload queue? This cannot be undone!',
    'Following files already exist. Would you like to overwrite them? {names}',
    'Drop your files to upload to {folder}'
))); ?>

<script type="text/javascript" src="media://koowa/com_files/js/plupload/moxie.js"></script>
<script type="text/javascript" src="media://koowa/com_files/js/plupload/plupload.dev.js"></script>
<script type="text/javascript" src="media://koowa/com_files/js/plupload/plupload.queue.js"></script>

<script>
window.addEvent('domready', function() {
    Files.createUploader(<?= json_encode((!isset($multi_selection) || $multi_selection !== false) ? true : false) ?>);
});
</script>
