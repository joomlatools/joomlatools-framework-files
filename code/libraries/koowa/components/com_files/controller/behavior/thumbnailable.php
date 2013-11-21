<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Thumbnailable Controller Behavior
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesControllerBehaviorThumbnailable extends KControllerBehaviorAbstract
{
	protected function _afterBrowse(KControllerContextInterface $context)
	{
		if (!$this->getRequest()->query->thumbnails || $this->getModel()->getContainer()->parameters->thumbnails !== true) {
			return;
		}

		$files = array();
		foreach ($context->result as $row) 
		{
			if ($row->getIdentifier()->name === 'file') {
				$files[] = $row->name;
			}
		}

		$folder = $this->getRequest()->query->folder;
		$thumbnails = $this->getObject('com:files.controller.thumbnail', array(
			'request' => array(
				'container' => $this->getModel()->getState()->container,
				'folder' => $folder,
				'filename' => $files,
				'limit' => 0,
				'offset' => 0
			)
		))->browse();

		foreach ($thumbnails as $thumbnail) 
		{
			if ($row = $context->result->find($thumbnail->filename)) {
				$row->thumbnail = $thumbnail->thumbnail;
			}
		}
		
		foreach ($context->result as $row) 
		{
			if (!is_string($row->thumbnail)) {
				$row->thumbnail = false;
			}
		}
	}
}