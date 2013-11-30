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
class ComFilesDatabaseValidatorNode extends KCommandInvokerAbstract
{
	protected function _beforeSave(KDatabaseContext $context)
	{
        $row = $context->getSubject();

        if (!$row->isNew() && !$row->overwrite)
        {
            $translator = $this->getObject('translator');
            $row->setStatusMessage($translator->translate('Resource already exists and overwrite switch is not present.'));
            return false;
        }

		return true;
	}

	protected function _beforeCopy(KDatabaseContext $context)
	{
		$row        = $context->subject;
		$translator = $this->getObject('translator');

		if (!array_intersect(array('destination_folder', 'destination_name'), $row->getModified()))
        {
            $row->setStatusMessage($translator->translate('Please supply a destination.'));
			return false;
		}

		if ($row->fullpath === $row->destination_fullpath)
        {
            $row->setStatusMessage($translator->translate('Source and destination are the same.'));
			return false;
		}

		$dest_adapter = $row->getContainer()->getAdapter($row->getIdentifier()->name, array(
			'path' => $row->destination_fullpath
		));

		$exists = $dest_adapter->exists();

		if ($exists)
		{
			if (!$row->overwrite)
            {
                $row->setStatusMessage($translator->translate('Destination resource already exists.'));
				return false;
			}
            else $row->overwritten = true;
		}

		return true;
	}

	protected function _beforeMove(KCommandInterface $context)
	{
		return $this->_beforeCopy($context);
	}
}
