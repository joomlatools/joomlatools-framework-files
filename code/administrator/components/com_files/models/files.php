<?php
/**
 * @version     $Id$
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Files Model Class
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 */

class ComFilesModelFiles extends ComFilesModelNodes
{   
    public function getList()
    {	
        if (!isset($this->_list))
        {
            $state = $this->_state;
            
            $files = $this->container->getAdapter('iterator')->getFiles(array(
        		'path' => $this->_getPath(),
        		'exclude' => array('.svn', '.htaccess', '.git', 'CVS', 'index.html', '.DS_Store', 'Thumbs.db', 'Desktop.ini'),
        		'filter' => array($this, 'iteratorFilter'),
        		'map' => array($this, 'iteratorMap')
        	));
            $this->_total = count($files);

            $files = array_slice($files, $state->offset, $state->limit ? $state->limit : $this->_total);

            if (strtolower($this->_state->direction) == 'desc') {
                $files = array_reverse($files);
            }

            $data = array();
            foreach ($files as $file)
            {
                $data[] = array(
                	'container' => $state->container,
                	'folder' => $state->folder,
                	'name' => $file
                );
            }

            $this->_list = $this->getRowset(array(
                'data' => $data
            ));
        }

        return parent::getList();
    }

	public function iteratorMap($file)
	{
		return $file->getBasename();
	}

	public function iteratorFilter($file)
	{
		if ($this->_state->name) {
			if (!in_array($file->getFilename(), (array) $this->_state->name)) {
				return false;
			}
		}
		
		if ($this->_state->types) {
			if ((in_array($file->getExtension(), ComFilesDatabaseRowFile::$image_extensions) && !in_array('image', (array) $this->_state->types))
			|| (!in_array($file->getExtension(), ComFilesDatabaseRowFile::$image_extensions) && !in_array('file', (array) $this->_state->types))
			) {
				return false;
			}
		}
		if ($this->_state->search && stripos($file->getFilename(), $this->_state->search) === false) return false;
	}
}
