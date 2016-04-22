<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' );

JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);
?>

<?= helper('behavior.koowa'); ?>
<?= helper('behavior.local_dates'); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.tooltip'); ?>
<?= helper('behavior.modal'); ?>
<?= helper('behavior.tree'); ?>

<?= helper('translator.script', array('strings' => array(
    'B', 'KB', 'MB', 'GB', 'TB', 'PB',
    'You are deleting {item}. Are you sure?',
    'You are deleting {items}. Are you sure?',
    '{count} files and folders',
    '{count} folders',
    '{count} files',
    'All Files',
    'An error occurred during request',
    'An error occurred with status code: {code}',
    'An error occurred: {error}',
    'Unknown error',
    'Insert'
))); ?>

<ktml:script src="assets://files/js/history/history.js" />

<ktml:script src="assets://files/js/ejs/ejs.js" />


<ktml:script src="assets://files/js/spin.min.js" />

<ktml:script src="assets://files/js/files.utilities.js" />
<ktml:script src="assets://files/js/files.state.js" />
<ktml:script src="assets://files/js/files.template.js" />
<ktml:script src="assets://files/js/files.grid.js" />
<ktml:script src="assets://files/js/files.tree.js" />
<ktml:script src="assets://files/js/files.row.js" />
<ktml:script src="assets://files/js/files.paginator.js" />
<ktml:script src="assets://files/js/files.pathway.js" />

<ktml:script src="assets://files/js/files.app.js" />
<ktml:script src="assets://files/js/files.attachments.app.js" />
<ktml:script src="assets://files/js/files.uploader.js" />

<ktml:script src="assets://files/js/files.copymove.js" />

<?= helper('icon.icon_map'); ?>
