<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
defined('KOOWA') or die; ?>

<? if (count($srcset)): ?>

    <img style="width: 100%" <?= count($attributes) ? implode(' ', $attributes) : '' ?>
         src="<?= $url ?>" srcset="<?= implode(', ', $srcset) ?>" sizes="100vw">

<? else: ?>

    <img style="width: 100%" <?= count($attributes) ? implode(' ', $attributes) : '' ?> src="<?= $url ?>">

<? endif ?>