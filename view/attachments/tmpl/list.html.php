<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
?>

<?= helper('ui.load', array('domain' => 'site')) ?>

<? if (count($attachments)): ?>
    <div class="koowa-attachments">
        <? if ($show_header): ?>
            <h3><?= translate('Attachments') ?></h3>
        <? endif ?>
        <ul class="attachments-list">
            <? foreach ($attachments as $attachment): ?>
                <? if ($attachment->file): ?>
                    <li>
                        <?= import("com:files.attachment.list.html", array('attachment' => $attachment)) ?>
                    </li>
                <? endif ?>
            <? endforeach ?>
        </ul>
    </div>
<? endif ?>


