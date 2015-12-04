<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Attachments Template Helper
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesTemplateHelperAttachments extends KTemplateHelperAbstract
{
    public function select($config = array())
    {
        $config = new KObjectConfigJson($config);

        $config->append(array(
            'name'     => 'attachments',
            'attribs'  => '',
            'value'    => array(),
            'multiple' => true,
            'callback' => 'attachmentSelectCallback',
            'link'     => $this->getTemplate()->route('view=files&routed=1&layout=select&tmpl=koowa'),
            'text'     => $this->getObject('translator')->translate('Select'),
            'attribs'  => array(
                'data-koowa-modal' => htmlentities(json_encode(array('mainClass' => 'koowa_dialog_modal koowa_dialog_modal--halfheight'))),
            )
        ))->append(array(
            'id'    => $config->name
        ));

        if ($config->callback) {
            $config->link .= '&callback='.urlencode($config->callback);
        }

        $html = '<span class="input-group-btn">';
        $html .= sprintf('<a class="koowa-modal btn mfp-iframe" %s href="%s">%s</a>', $config->attribs, $config->link, $config->text);
        $html .= '</span>';

        $html .= $this->getTemplate()->createHelper('behavior')->modal();

        return $html;
    }
}