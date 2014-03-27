<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * File Size Filter
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesFilterFileSize extends KFilterAbstract
{
    public function validate($entity)
	{
		$max = $entity->getContainer()->parameters->maximum_size;

		if ($max)
		{
			$size = $entity->contents ? strlen($entity->contents) : false;
			if (!$size && is_uploaded_file($entity->file)) {
				$size = filesize($entity->file);
			} elseif ($entity->file instanceof SplFileInfo && $entity->file->isFile()) {
				$size = $entity->file->getSize();
			}

			if ($size && $size > $max) {
                return $this->_error($this->getObject('translator')->translate('File is too big'));
			}
		}
	}
}
