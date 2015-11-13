<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */
defined('KOOWA') or die;
?>

<div class="k-table-container">
    <div class="k-table" id="files-grid"></div>
    <? // @TODO: @Ercan: Fix pagination for select window; ?>
    <div class="k-table-pagination ercan-todo">
        <?= helper('paginator.pagination') ?>
    </div><!-- .k-table-pagination -->
</div>