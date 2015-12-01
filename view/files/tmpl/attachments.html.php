<?
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */
?>

<!-- Only existing entities are attachable -->
<? if (!$entity->isNew()): ?>
    <ktml:script src="media://koowa/com_files/js/ejs/ejs.js"/>
    <ktml:script src="media://koowa/com_files/js/attachments.js"/>
    <ktml:style src="media://koowa/com_files/css/files.css"/>

    <?= helper('behavior.modal') ?>

        <script>
            kQuery(function($)
            {
                Attachments = Attachments.getInstance(
                    {
                        post_url : "<?= route('', true, false) ?>",
                        get_url: "<?= route('view=files&thumbnails=1&format=json&name={name}&format={format}&routed=1', true, false) ?>",
                        template: '#attachment-template',
                        csrf_token: <?= json_encode(object('user')->getSession()->getToken()) ?>
                    }
                );

                var render = function(attachment)
                {
                    var images = $('#attachments-images');
                    var files = $('#attachments-files');

                    var output = Attachments.render(attachment);

                    if (attachment.type == 'image')
                    {
                        output = images.append(output)
                        $('a.koowa-modal', output).magnificPopup({'type': 'image'});
                    }
                    else output = files.append(output);

                    $('.delete', output).click(function()
                    {
                        Attachments.remove(attachment.name);
                        $(this).closest('.attachment').remove();
                    });
                };

                Attachments.bind('after.insert', function(event, attachment)
                {
                    $.ajax({
                        url: Attachments.route(attachment, 'json'),
                        success: function(data) {
                            render(data.entities.pop());
                        }
                    });
                });

                var attachments = <?= json_encode(helper('com:files.attachments.fileinfo', array('entity' => $entity))) ?>

                $.each(attachments, function (idx, attachment) {
                    render(attachment);
                });
            });
        </script>

    <div id="attachments-list" class="attachments">
        <div id="attachments-images" class="attachments__images"></div>
        <div id="attachments-files" class="attachments__files"></div>
    </div>

    <!-- Attachment template begin -->
    <textarea style="display: none" id="attachment-template">
        [% if(type == 'image') { %]
        <div class="attachment attachment--thumbnail">
            <a class="koowa-modal mfp-iframe" href="[%=url%]">
                <img src="[%=thumbnail%]" />
            </a>
            <div class="attachment__caption">
                <a class="btn btn-mini btn-danger delete" href="#">
                    <i class="icon-trash icon-white"></i>
                </a>
            </div>
            <input name="attachments[]" value="[%=name%]" type="hidden"/>
        </div>
        [% } else { %]
        <li class="attachment">
            <a href="[%=url%]">[%=name%]</a>
            <div class="button__group">
                <a class="btn btn-mini btn-danger delete" href="#">
                    <i class="icon-trash icon-white"></i>
                </a>
            </div>
            <input name="attachments[]" value="[%=name%]" type="hidden"/>
        </li>
        [% } %]
    </textarea>
    <!-- Attachment template end -->

    <? if (isset($select) && $select == true): ?>
        <script>
            kQuery(function($)
            {
                AttachmentsCallback = function(selected)
                {
                    Attachments.insert(selected);

                    if (typeof $.magnificPopup !== 'undefined' && $.magnificPopup.instance) {
                        $.magnificPopup.close();
                    }
                }
            });
        </script>
        <div class="attachment__select">
            <?= helper('com:files.attachments.select', array('name' => 'attachments', 'callback' => 'AttachmentsCallback')) ?>
        </div>
    <? endif ?>
<? endif ?>