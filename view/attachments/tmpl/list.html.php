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

                var url = "<?= route('view=file&routed=1&name={name}', true, false) ?>";

                attachment.url = Attachments.replace(url, {name: attachment.name});

                var output = Attachments.render(attachment);

                output = $(output).appendTo($('.attachments'));

                $('.delete', output).click(function () {
                    Attachments.detach(attachment.name);
                    $(this).closest('.attachment').remove();
                });
            };

            Attachments.bind('after.attach', function (event, context)
            {
                var url = "<?= route('view=attachments&name={name}&format=json', true, false) ?>";

                $.ajax({
                    url: Attachments.replace(url, {name: context.attachment}),
                    success: function (data) {
                        render(data.entities.pop());
                    }
                });
            });

            var attachments = <?= json_encode(array_values($entity->getAttachments()->toArray())) ?>;

            $.each(attachments, function (idx, attachment) {
                render(attachment);
            });
        });
    </script>

    <div class="attachments"></div>

    <!-- Attachment template begin -->
    <textarea style="display: none" id="attachment-template">
        [% if (file.type == 'image') { %]
            <div class="attachments__image [% if (thumbnail) { %]attachments__image--thumbnail[% } %]">
                <a href="[%=url%]">
                    [% if (thumbnail) { %]
                        <img src="[%=thumbnail%]"/>
                    [% } else { %]
                        <span class="koowa_icon--image koowa_icon--48"></span>
                        <div class="attachments__caption">
                            [%=name%]
                        </div>
                    [% } %]
                </a>
                <div class="attachments__caption">
                    <a class="btn btn-mini btn-danger delete" href="#">
                        <i class="icon-trash icon-white"></i>
                    </a>
                </div>
            </div>
        [% } else { %]
         <div class="attachments__file">
             <a href="[%=url%]">
                 <span class="koowa_icon--default koowa_icon--48 koowa_icon--[%=file.extension%]"></span>
                 <div class="attachments__caption">
                     [%=name%]
                 </div>
             </a>
             <div class="attachments__buttons">
                 <a class="btn btn-mini btn-danger delete" href="#">
                     <i class="icon-trash icon-white"></i>
                 </a>
             </div>
         </div>
        [% } %]
    </textarea>
    <!-- Attachment template end -->

    <? if (isset($select) && $select == true): ?>
        <script>
            kQuery(function($)
            {
                $('.attachments__image > a').magnificPopup({
                    type: 'image',
                    gallery:{
                        enabled:true
                    }
                });

                AttachmentsCallback = function(selected)
                {
                    Attachments.attach(selected);

                    if (typeof $.magnificPopup !== 'undefined' && $.magnificPopup.instance) {
                        $.magnificPopup.close();
                    }
                }
            });
        </script>
        <div class="attachments__select">
            <?= helper('com:files.attachments.select', array('name' => 'attachments', 'callback' => 'AttachmentsCallback')) ?>
        </div>
    <? endif ?>
<? endif ?>