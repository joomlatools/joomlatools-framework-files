<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die( 'Restricted access' ); ?>

<div class="k-dynamic-content-holder">
    <?= import('com:files.files.uploader_scripts.html') ?>
</div>

<div id="files-upload-multi" class="k-upload--boxed-top"></div>

<script>
    window.addEvent('domready', function() {
        var timeout = null,
            createUploader = function() {
                if (Files.app) {
                    Files.createUploader({
                        multi_selection: <?= json_encode((!isset($multi_selection) || $multi_selection !== false) ? true : false) ?>
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

