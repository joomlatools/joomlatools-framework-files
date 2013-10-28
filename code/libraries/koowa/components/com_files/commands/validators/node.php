<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * File Validator Command
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesCommandValidatorNode extends KCommandInvokerAbstract
{
	protected function _databaseBeforeSave(KCommandContext $context)
	{
		$row = $context->caller;

		if (!$row->isNew() && !$row->overwrite)
        {
		    $translator = $this->getObject('translator');
			$context->setError($translator->translate('Resource already exists and overwrite switch is not present.'));
			return false;
		}

		return true;
	}

	protected function _databaseBeforeCopy(KCommandContext $context)
	{
		$row        = $context->caller;
		$translator = $this->getObject('translator');

		if (!array_intersect(array('destination_folder', 'destination_name'), $row->getModified())) {
			$context->setError($translator->translate('Please supply a destination.'));
			return false;
		}

		if ($row->fullpath === $row->destination_fullpath) {
			$context->setError($translator->translate('Source and destination are the same.'));
			return false;
		}

		$dest_adapter = $row->container->getAdapter($row->getIdentifier()->name, array(
			'path' => $row->destination_fullpath
		));
		$exists = $dest_adapter->exists();

		if ($exists)
		{
			if (!$row->overwrite) {
				$context->setError($translator->translate('Destination resource already exists.'));
				return false;
			} else {
				$row->overwritten = true;

			}
		}

		return true;
	}

	protected function _databaseBeforeMove(KCommandContext $context)
	{
		return $this->_databaseBeforeCopy($context);
	}
}
