<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
?>

<span class="koowa_header">
    <? if ($show_icon): ?>
        <span class="koowa_header__item koowa_header__item--image_container">
            <a class="iconImage" href="<?= route($attachment->url, true, false) ?>">
                <span class="k-icon-document-<?= helper('com:files.icon.icon', array(
                    'extension' => $attachment->file->storage->extension
                )) ?> k-icon-document-<?= helper('com:files.icon.icon', array(
                    'extension' => $attachment->file->storage->extension
                )) ?> k-icon--size-medium" aria-hidden="true"></span>
            </a>
        </span>
    <? endif ?>
        <span class="koowa_header__item">
            <span class="koowa_wrapped_content">
                <span class="whitespace_preserver">
                    <a href="<?= route($attachment->url, true, false) ?>">
                        <span itemprop="name"><?=escape($attachment->file->name)?></span>
                        <? if ($show_info): ?>
                            (<span><?= strtolower($attachment->file->storage->extension) ?></span>,&nbsp;<!--
                             --><span><?= helper('com:files.filesize.humanize', array('size' => $attachment->file->storage->size));?></span>)
                        <? endif ?>
                    </a>
                </span>
            </span>
        </span>
</span>