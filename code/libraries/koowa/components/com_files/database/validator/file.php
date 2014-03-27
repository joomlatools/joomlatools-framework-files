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
class ComFilesDatabaseValidatorFile extends ComFilesDatabaseValidatorNode
{
	protected function _beforeSave(KDatabaseContextInterface $context)
	{
		$row = $context->subject;

		if (is_string($row->file) && !is_uploaded_file($row->file))
		{
			// remote file
            $file = $this->getObject('com:files.model.entity.url');
            $file->setProperties(array('file' => $row->file));

            if (!$file->load()) {
                throw new RuntimeException('File cannot be downloaded');
            }

            $row->contents = $file->contents;

			if (empty($row->name))
			{
				$uri = $this->getObject('lib:http.url', array('url' => $row->file));
	        	$path = $uri->toString(KHttpUrl::PATH | KHttpUrl::FORMAT);
	        	if (strpos($path, '/') !== false) {
	        		$path = basename($path);
	        	}

	        	$row->name = $path;
			}
		}

        $result = parent::_beforeSave($context);

        if ($result)
        {
            $filter = $this->getObject('com:files.filter.file.uploadable');
            $result = $filter->validate($context->getSubject());
            if ($result === false)
            {
                $errors = $filter->getErrors();
                if (count($errors)) {
                    $context->getSubject()->setStatusMessage(array_shift($errors));
                }
            }
        }

		return $result;

	}
}
