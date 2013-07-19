<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' );

/* DEBUG FILES:
<script src="media://koowa/com_files/js/spin.min.js" />

<script src="media://koowa/com_files/js/files.utilities.js" />
<script src="media://koowa/com_files/js/files.state.js" />
<script src="media://koowa/com_files/js/files.template.js" />
<script src="media://koowa/com_files/js/files.grid.js" />
<script src="media://koowa/com_files/js/files.tree.js" />
<script src="media://koowa/com_files/js/files.row.js" />
<script src="media://koowa/com_files/js/files.paginator.js" />
<script src="media://koowa/com_files/js/files.pathway.js" />

<script src="media://koowa/com_files/js/files.app.js" />
*/
?>

<?= @helper('behavior.mootools'); ?>
<?= @helper('behavior.koowa'); ?>
<?= @helper('behavior.keepalive'); ?>
<?= @helper('behavior.tooltip'); ?>
<?= @helper('behavior.modal'); ?>

<?= @helper('bootstrap.load'); ?>
<?= @helper('behavior.jquery'); ?>

<script src="media://koowa/com_files/js/history/history.js" />
<? if (JBrowser::getInstance()->getBrowser() === 'msie'): ?>
<script src="media://koowa/com_files/js/history/history.html4.js" />
<? endif; ?>

<script src="media://koowa/com_files/js/ejs/ejs.js" />

<script src="media://system/js/mootree.js" />

<script src="media://koowa/com_files/js/files.min.js" />
