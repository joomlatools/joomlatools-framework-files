<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<?= @import('com://admin/files.views.files.initialize'); ?>

<script src="media://koowa/com_files/js/files.compact.js" />

<style src="media://koowa/com_files/css/files.css" />
<style src="media://koowa/com_files/css/files-compact.css" />

<script>
Files.sitebase = '<?= $sitebase; ?>';
Files.token = '<?= $token; ?>';

window.addEvent('domready', function() {
	var config = jQuery.parseJSON(<?= json_encode($state->config); ?>),
		options = {
            cookie: {
                path: '<?=KRequest::root()?>'
            },
            pathway: false,
			state: {
				defaults: {
					limit: 0,
					offset: 0
				}
			},
			editor: <?= json_encode($state->editor); ?>,
			tree: {
				theme: 'media://koowa/com_files/images/mootree.png'
			},
			types: <?= json_encode($state->types); ?>,
			container: <?= json_encode($container ? $container->slug : null); ?>
		};
	options = Files.utils.append(options, config);

	Files.app = new Files.Compact.App(options);

	$$('#tabs-pane_insert dt').addEvent('click', function(){
		setTimeout(function(){window.fireEvent('refresh');}, 300);
	});
});
</script>

<?= @import('com://admin/files.view.files.templates_compact');?>

<div id="files-compact" class="com_files">
	<?=	@helper('tabs.startPane', array('id' => 'pane_insert')); ?>
	<?= @helper('tabs.startPanel', array('title' => 'Insert')); ?>
		<div id="insert">
			<div id="files-tree-container" style="float: left">
				<div id="files-tree"></div>
			</div>

			<div id="files-grid" style="float: left"></div>
			<div id="details" style="float: left;">
				<div id="files-preview"></div>
			</div>
			<div class="clear" style="clear: both"></div>
		</div>
	<?= @helper('tabs.endPanel'); ?>
	<?= @helper('tabs.startPanel', array('title' => @translate('Upload'))); ?>

		<?= @import('com://admin/files.view.files.uploader'); ?>

	<?= @helper('tabs.endPanel'); ?>
	<?= @helper('tabs.endPane'); ?>
</div>
