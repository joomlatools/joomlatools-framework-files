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
    <ktml:script src="media://koowa/com_files/js/files.attachments.js"/>
    <ktml:style src="media://koowa/com_files/css/files.css"/>

    <?= helper('behavior.modal') ?>

    <script>
        kQuery(function($) {
            Attachments = Attachments.getInstance(
                {
                    url: "<?= route('', true, false) ?>",
                    template: '#attachment-template',
                    selector: '#attachment-template',
                    csrf_token: <?= json_encode(object('user')->getSession()->getToken()) ?>
                }
            );

            Attachments.bind('before.attach', function(event, context)  {setContext(context)});
            Attachments.bind('before.detach', function(event, context) {setContext(context)});

            var setContext = function(context) {
                context.data.attachment = context.attachment;
            };

            var render = function (attachment) {
                var images = $('#attachments-images');
                var files = $('#attachments-files');

                var url = "<?= route('view=file&routed=1&name={name}', true, false) ?>";

                attachment.url = Attachments.replace(url, {name: attachment.name});

                var output = Attachments.render(attachment);

                if (attachment.type == 'image') {
                    output = $(output).appendTo(images)
                    $('a.koowa-modal', output).magnificPopup({'type': 'image'});
                }
                else output = $(output).appendTo(files);

                $('.delete', output).click(function () {
                    Attachments.detach(attachment.name);
                    $(this).closest('.attachment').remove();
                });
            };

            Attachments.bind('after.attach', function (event, context)
            {
                var url = "<?= route('view=files&thumbnails=1&routed=1&name={name}&format=json', true, false) ?>";

                $.ajax({
                    url: Attachments.replace(url, {name: context.attachment}),
                    success: function (data) {
                        render(data.entities.pop());
                    }
                });
            });

            var attachments = <?= json_encode(array_values($entity->getAttachments()->getFiles()->toArray())) ?>;

            $.each(attachments, function (idx, attachment) {
                render(attachment);
            });
        });
    </script>

    <div id="attachments-list" class="attachments">
        <div id="attachments-images" class="attachments__images"></div>
        <ul id="attachments-files" class="attachments__files"></ul>
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
                    Attachments.attach(selected);

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