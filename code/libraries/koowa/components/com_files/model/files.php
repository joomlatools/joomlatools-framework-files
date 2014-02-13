<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Files Model
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelFiles extends ComFilesModelNodes
{
    public function getList()
    {
        if (!isset($this->_list))
        {
            $state = $this->getState();
            $files = $this->getContainer()->getAdapter('iterator')->getFiles(array(
        		'path'    => $this->_getPath(),
        		'exclude' => array('.svn', '.htaccess', 'web.config', '.git', 'CVS', 'index.html', '.DS_Store', 'Thumbs.db', 'Desktop.ini'),
        		'filter'  => array($this, 'iteratorFilter'),
        		'map'     => array($this, 'iteratorMap'),
            	'sort'    => $state->sort
        	));

        	if ($files === false) {
        		throw new UnexpectedValueException('Invalid folder');
        	}

            $this->_total = count($files);
            
            if (strtolower($state->direction) == 'desc') {
            	$files = array_reverse($files);
            }

            $files = array_slice($files, $state->offset, $state->limit ? $state->limit : $this->_total);

            $data = array();
            foreach ($files as $file)
            {
                $data[] = array(
                	'container' => $state->container,
                	'folder'    => $state->folder,
                	'name'      => $file
                );
            }

            $this->_list = $this->getRowset()->addRow($data);
        }

        return parent::getList();
    }

	public function iteratorMap($path)
	{
		return basename($path);
	}

	public function iteratorFilter($path)
	{
        $state     = $this->getState();
		$filename  = basename($path);
		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

		if ($state->name)
        {
			if (!in_array($filename, (array) $state->name)) {
				return false;
			}
		}

		if ($state->types)
        {
			if ((in_array($extension, ComFilesDatabaseRowFile::$image_extensions) && !in_array('image', (array) $state->types))
			|| (!in_array($extension, ComFilesDatabaseRowFile::$image_extensions) && !in_array('file', (array) $state->types))
			) {
				return false;
			}
		}

		if ($state->search && stripos($filename, $state->search) === false) {
            return false;
        }
	}
}
