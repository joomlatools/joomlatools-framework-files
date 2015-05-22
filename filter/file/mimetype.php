<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * File Mimetype Filter
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesFilterFileMimetype extends KFilterAbstract
{
    public function validate($entity)
	{
		$mimetypes = KObjectConfig::unbox($entity->getContainer()->getParameters()->allowed_mimetypes);

		if (is_array($mimetypes))
		{
            $mimetype = null;
            $resolver = $this->getObject('filesystem.mimetype.fileinfo');

            try {
                $mimetype = $this->getObject('filesystem.mimetype.extension')->fromPath($this->_path);
            }
            catch (Exception $e) {}

            if (is_uploaded_file($entity->file)) {
                $mimetype = $resolver->fromPath($entity->file);
            }
            elseif ($entity->file instanceof SplFileInfo) {
                $mimetype = $resolver->fromPath($entity->file->getPathname());
            }

			if ($mimetype && !in_array($mimetype, $mimetypes)) {
				return $this->addError($this->getObject('translator')->translate('Invalid Mimetype'));
			}
		}
	}
}
