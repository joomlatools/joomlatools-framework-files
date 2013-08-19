<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Folder Name Filter
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesFilterFolderName extends KFilterAbstract
{
	protected $_walk = false;

	protected function _validate($context)
	{
		$value = $context->caller->name;

		if (strpos($value, '/') !== false) {
			$context->setError($this->getObject('translator')->translate('Folder names cannot contain slashes'));
			return false;
		}

		if ($this->_sanitize($value) == '') {
			$context->setError($this->getObject('translator')->translate('Invalid folder name'));
			return false;
		}
	}

	protected function _sanitize($value)
	{
		$value = str_replace('/', '', $value);
		return $this->getObject('com://admin/files.filter.path')->sanitize($value);
	}
}
