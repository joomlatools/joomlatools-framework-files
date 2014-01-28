<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Node Database Row
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesDatabaseRowNode extends KDatabaseRowAbstract
{
	protected $_adapter;

    protected $_container;

	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        // Mixin the behavior interface
        $this->mixin('koowa:behavior.mixin', $config);

		if ($config->validator !== false)
		{
			if ($config->validator === true) {
				$config->validator = 'com:files.database.validator.'.$this->getIdentifier()->name;
			}

			$this->addCommandHandler($this->getObject($config->validator));
		}
	}

	protected function _initialize(KObjectConfig $config)
	{
		$config->append(array(
			'validator' => true
		));

		parent::_initialize($config);
	}

	public function isNew()
	{
		return empty($this->name) || !$this->_adapter->exists();
	}

	public function copy()
	{
		$context = $this->getContext();
		$context->result = false;

		if ($this->invokeCommand('before.copy', $context) !== false)
		{
			$context->result = $this->_adapter->copy($this->destination_fullpath);
			$this->invokeCommand('after.copy', $context);
        }

		if ($context->result === false) {
			$this->setStatus(KDatabase::STATUS_FAILED);
		}
		else
		{
			if ($this->destination_folder) {
				$this->folder = $this->destination_folder;
			}
			if ($this->destination_name) {
				$this->name = $this->destination_name;
			}

			$this->setStatus($this->overwritten ? KDatabase::STATUS_UPDATED : KDatabase::STATUS_CREATED);
		}

		return $context->result;
	}

	public function move()
	{
		$context = $this->getContext();
		$context->result = false;

		if ($this->invokeCommand('before.move', $context) !== false)
		{
			$context->result = $this->_adapter->move($this->destination_fullpath);
			$this->invokeCommand('after.move', $context);
        }

		if ($context->result === false) {
			$this->setStatus(KDatabase::STATUS_FAILED);
		}
		else
		{
			if ($this->destination_folder) {
				$this->folder = $this->destination_folder;
			}

			if ($this->destination_name) {
				$this->name = $this->destination_name;
			}

			$this->setStatus($this->overwritten ? KDatabase::STATUS_UPDATED : KDatabase::STATUS_CREATED);
		}

		return $context->result;
	}

	public function delete()
	{
		$context = $this->getContext();
		$context->result = false;

		if ($this->invokeCommand('before.delete', $context) !== false)
		{
			$context->result = $this->_adapter->delete();
			$this->invokeCommand('after.delete', $context);
        }

		if ($context->result === false) {
			$this->setStatus(KDatabase::STATUS_FAILED);
		}
		else $this->setStatus(KDatabase::STATUS_DELETED);

		return $context->result;
	}

	public function __get($column)
	{
		if ($column == 'fullpath' && !isset($this->_data['fullpath'])) {
			return $this->getFullpath();
		}

		if ($column == 'path') {
			return trim(($this->folder ? $this->folder.'/' : '').$this->name, '/\\');
		}

		if ($column == 'destination_path')
		{
			$folder = !empty($this->destination_folder) ? $this->destination_folder.'/' : (!empty($this->folder) ? $this->folder.'/' : '');
			$name = !empty($this->destination_name) ? $this->destination_name : $this->name;
			return trim($folder.$name, '/\\');
		}

		if ($column == 'destination_fullpath') {
			return $this->getContainer()->path.'/'.$this->destination_path;
		}

		if ($column == 'adapter') {
			return $this->_adapter;
		}


		return parent::__get($column);
	}

	public function __set($column, $value)
	{
		parent::__set($column, $value);

		if (in_array($column, array('container', 'folder', 'name'))) {
			$this->setAdapter();
		}
	}

    public function getContainer()
    {
        if(!isset($this->_container))
        {
            //Set the container
            $container = is_object($this->container) ? $this->container : $this->getObject('com:files.model.containers')->slug($this->container)->getItem();

            if (!is_object($container) || $container->isNew()) {
                throw new UnexpectedValueException('Invalid container');
            }

            $this->_container = $container;
        }

        return $this->_container;
    }

	public function setAdapter()
	{
		$type = $this->getIdentifier()->name;
		$this->_adapter = $this->getContainer()->getAdapter($type, array(
			'path' => $this->getContainer()->path.'/'.($this->folder ? $this->folder.'/' : '').$this->name
		));

		unset($this->_data['fullpath']);
		unset($this->_data['metadata']);

		return $this;
	}

	public function setData($data, $modified = true)
	{
		$result = parent::setData($data, $modified);

		if (isset($data['container'])) {
			$this->setAdapter();
		}

		return $result;
	}

	public function getFullpath()
	{
		return $this->_adapter->getRealPath();
	}

    public function toArray()
    {
        $data = parent::toArray();

        unset($data['_token']);
        unset($data['action']);
        unset($data['option']);
        unset($data['format']);
        unset($data['view']);

		$data['container'] = $this->getContainer()->slug;
		$data['type'] = $this->getIdentifier()->name;

        return $data;
    }

    public function count()
    {
        return (int) !$this->isNew();
    }

    /**
     * Get the context
     *
     * @return KCommand
     */
    public function getContext()
    {
        $context = new KDatabaseContext();
        $context->setSubject($this);

        return $context;
    }
}
