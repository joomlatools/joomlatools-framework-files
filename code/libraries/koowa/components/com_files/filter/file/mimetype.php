<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * File Mimetype Filter
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesFilterFileMimetype extends KFilterAbstract
{
    public function validate($row)
	{
		$mimetypes = KObjectConfig::unbox($row->getContainer()->parameters->allowed_mimetypes);

		if (is_array($mimetypes))
		{
			$mimetype = $row->mimetype;

			if (empty($mimetype))
            {
				if (is_uploaded_file($row->file) && $row->isImage())
                {
					$info = getimagesize($row->file);
					$mimetype = $info ? $info['mime'] : false;
				}
                elseif ($row->file instanceof SplFileInfo) {
					$mimetype = $this->getObject('com:files.mixin.mimetype')->getMimetype($row->file->getPathname());
				}
			}

			if ($mimetype && !in_array($mimetype, $mimetypes)) {
				return $this->_error($this->getObject('translator')->translate('Invalid Mimetype'));
			}
		}
	}
}
