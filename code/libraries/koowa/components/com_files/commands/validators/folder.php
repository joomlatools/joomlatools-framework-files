<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Folder Validator Command
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesCommandValidatorFolder extends ComFilesCommandValidatorNode
{
	protected function _databaseBeforeSave(KCommandInterface $context)
	{
		return parent::_databaseBeforeSave($context) && $this->getObject('com://admin/files.filter.folder.uploadable')->validate($context->subject);
	}
}
