<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Modal Template Helper
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesTemplateHelperModal extends KTemplateHelperAbstract
{
	public function select($config = array())
	{
        $config = new KObjectConfig($config);
        $config->append(array(
            'name' => '',
            'attribs' => array(),
            'button_attribs' => array(),
            'visible' => true,
            'link' => '',
            'link_text' => $this->translate('Select')
        ))->append(array(
            'id' => $config->name,
            'value' => $config->name
        ));

        $attribs = $this->buildAttributes($config->attribs);
        $button_attribs = $this->buildAttributes($config->button_attribs);

        $input = '<input name="%1$s" id="%2$s" value="%3$s" %4$s size="40" %5$s />';

        $link = '<span class="input-group-btn"><a class="koowa-modal btn mfp-iframe" %s href="%s">%s</a></span>';

        $html = sprintf($input, $config->name, $config->id, $config->value, $config->visible ? 'type="text" readonly' : 'type="hidden"', $attribs);
        $html .= sprintf($link, $button_attribs, $config->link, $config->link_text);

        $html .= $this->getTemplate()->getHelper('behavior')->modal();

		return $html;
	}
}
