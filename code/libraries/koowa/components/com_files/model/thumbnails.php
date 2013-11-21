<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Thumbnails Model
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelThumbnails extends ComKoowaModelDefault
{
    /**
     * A container object
     *
     * @var ComFilesDatabaseRowContainer
     */
    protected $_container;

    /**
     * Reset the cached container object if container changes
     * @param string $name
     */
    public function onStateChange($name)
    {
        if ($name === 'container') {
            unset($this->_container);
        }
    }

    /**
     * Returns the current container row
     *
     * @return ComFilesDatabaseRowContainer
     * @throws UnexpectedValueException
     */
    public function getContainer()
    {
        if(!isset($this->_container))
        {
            //Set the container
            $container = $this->getObject('com:files.model.containers')->slug($this->getState()->container)->getItem();

            if (!is_object($container) || $container->isNew()) {
                throw new UnexpectedValueException('Invalid container');
            }

            $this->_container = $container;
        }

        return $this->_container;
    }

	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

		$this->getState()
			->insert('container', 'com:files.filter.container', null)
			->insert('folder', 'com:files.filter.path')
			->insert('filename', 'com:files.filter.path', null, true, array('container'))
			->insert('files', 'com:files.filter.path', null)
			->insert('paths', 'com:files.filter.path', null)
			
			->insert('types', 'cmd', '')
			->insert('config'   , 'json', '')
			;
	}

	protected function _initialize(KObjectConfig $config)
	{
		$config->append(array(
			'state' => 'com:files.model.state'
		));

		parent::_initialize($config);
	}

	protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
    	parent::_buildQueryColumns($query);

    	if ($this->getState()->container) {
    		$query->columns(array('container' => 'c.slug'));
    	}
    }

	protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
    	parent::_buildQueryJoins($query);

    	if ($this->getState()->container) {
    		$query->join(array('c' => 'files_containers'), 'c.files_container_id = tbl.files_container_id');
    	}
    }

	protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        $state = $this->getState();

        if ($state->container) {
            $query->where('tbl.files_container_id = :container_id')->bind(array('container_id' => $this->getContainer()->id));
        }

        if ($state->folder !== false) {
            $query->where('tbl.folder = :folder')->bind(array('folder' => ltrim($state->folder, '/')));
        }

        // Need this for BC
        if (!empty($state->files)) {
            $query->where('tbl.filename IN :files')->bind(array('files' => (array) $state->files));
        }

        if ($state->filename) {
            $query->where('tbl.filename IN :filename')->bind(array('filename' => (array) $state->filename));
        }

        if ($state->paths)
        {
            $i = 0;
            foreach ((array)$state->paths as $path)
            {
                $file = basename($path);
                $folder = dirname($path);
                if ($folder === '.') {
                    $folder = '';
                }

                $query->where("(tbl.filename = :filename$i AND tbl.folder = :folder$i)", 'OR')
                    ->bind(array('filename'.$i => $file, 'folder'.$i => $folder));

                $i++;
            }
        }
		
	}
	
	protected function _buildQueryOrder(KDatabaseQueryInterface $query)
	{
		$sort       = $this->getState()->sort;
		$direction  = strtoupper($this->getState()->direction);
	
		if($sort) 
		{
			$column = $this->getTable()->mapColumns($sort);
			if(array_key_exists($column, $this->getTable()->getColumns())) {
				$query->order($column, $direction);
			}
		}	
	}
}