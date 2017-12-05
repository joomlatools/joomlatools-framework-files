<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
defined('KOOWA') or die; ?>

<div class="files_player" style="clear: both">
    <audio
        <?= implode(', ', $attributes) ?>
        controls>
        <source src="<?= $url ?>" type="audio/<?= escape($file->extension) ?>" />
    </audio>
</div>