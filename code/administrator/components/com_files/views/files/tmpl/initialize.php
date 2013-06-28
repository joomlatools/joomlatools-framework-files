<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
defined('KOOWA') or die( 'Restricted access' );

/* DEBUG FILES:
<script src="media://com_files/js/spin.min.js" />

<script src="media://com_files/js/files.utilities.js" />
<script src="media://com_files/js/files.state.js" />
<script src="media://com_files/js/files.template.js" />
<script src="media://com_files/js/files.grid.js" />
<script src="media://com_files/js/files.tree.js" />
<script src="media://com_files/js/files.row.js" />
<script src="media://com_files/js/files.paginator.js" />
<script src="media://com_files/js/files.pathway.js" />

<script src="media://com_files/js/files.app.js" />
*/
?>

<?= @helper('behavior.mootools'); ?>
<?= @helper('behavior.keepalive'); ?>
<?= @helper('behavior.tooltip'); ?>
<?= @helper('behavior.modal'); ?>

<?= @helper('com://admin/extman.template.helper.behavior.bootstrap'); ?>
<?= @helper('com://admin/extman.template.helper.behavior.jquery'); ?>

    <!--
<? if (version_compare(JVERSION, '1.7.0', '<')):
jimport('joomla.environment.browser');
?>
<script src="media://com_files/js/delegation.js" />
<script src="media://com_files/js/uri.js" />
<? endif; ?>

<script src="media://com_files/js/history/history.js" />
<? if (JBrowser::getInstance()->getBrowser() === 'msie'): ?>
<script src="media://com_files/js/history/history.html4.js" />
<? endif; ?>

<script src="media://com_files/js/ejs/ejs.js" />

<script src="media://lib_koowa/js/koowa.js" />
<script src="media://system/js/mootree.js" />

<script src="media://com_files/js/files.min.js" />
-->
<? if (version_compare(JVERSION, '1.6.0', '<')): ?>
    <!--
    <script>
    if (SqueezeBox.open === undefined) {
        SqueezeBox = Files.utils.append(SqueezeBox, {
            open: function(subject, options) {
                this.initialize();

                if (this.element != null) this.close();
                this.element = document.id(subject) || false;

                this.setOptions($merge(this.presets, options || {}));

                if (this.element && this.options.parse) {
                    var obj = this.element.getProperty(this.options.parse);
                    if (obj && (obj = JSON.decode(obj, this.options.parseSecure))) this.setOptions(obj);
                }
                this.url = ((this.element) ? (this.element.get('href')) : subject) || this.options.url || '';

                this.assignOptions();

                var handler = handler || this.options.handler;
                if (handler) return this.setContent(handler, this.parsers[handler].call(this, true));
                var ret = false;
                return this.parsers.some(function(parser, key) {
                    var content = parser.call(this);
                    if (content) {
                        ret = this.setContent(key, content);
                        return true;
                    }
                    return false;
                }, this);
            }
        });
    }
    /* Joomla! 1.5 fix */
    if(!SqueezeBox.handlers.clone) {
        SqueezeBox.handlers.adopt = function(a){return a;}
    }
    window.addEvent('domready', function(){
        if(!SqueezeBox.fx.win) {
            SqueezeBox.fx.win = SqueezeBox.fx.window;
        }
    });
    </script>
    -->
<? endif; ?>