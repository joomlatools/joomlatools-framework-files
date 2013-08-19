<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Filesize Template Helper
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesTemplateHelperFilesize extends KTemplateHelperAbstract
{
	public function humanize($config = array())
	{
		$config = new KObjectConfig($config);
		$config->append(array(
			'sizes' => array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB')
		));
		$bytes = $config->size;
		$result = '';
		$format = (($bytes > 1024*1024 && $bytes % 1024 !== 0) ? '%.2f' : '%d').' %s';

		foreach ($config->sizes as $s)
		{
			$size = $s;
			if ($bytes < 1024) {
				$result = $bytes;
				break;
			}
			$bytes /= 1024;
		}

		if ($result == 1) {
			$size = KStringInflector::singularize($size);
		}

		return sprintf($format, $result, $this->translate($size));
	}
}
