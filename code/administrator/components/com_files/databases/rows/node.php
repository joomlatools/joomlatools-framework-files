<?php

class ComFilesDatabaseRowNode extends KDatabaseRowAbstract
{
	protected $_adapter;
	
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->mixin(new KMixinCommandchain($config->append(array('mixer' => $this))));

		if ($config->validator !== false)
		{
			if ($config->validator === true) {
				$config->validator = 'com://admin/files.command.validator.'.$this->getIdentifier()->name;
			}

			$this->getCommandChain()->enqueue($this->getService($config->validator));
		}
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'dispatch_events'   => false,
			'enable_callbacks'  => true,
			'validator' 		=> true
		));

		parent::_initialize($config);
	}	

	public function isNew()
	{
		return empty($this->name) || !$this->_adapter->exists();
	}

	public function __get($column)
	{
		if ($column == 'fullpath' && !isset($this->_data['fullpath'])) {
			return $this->getFullpath();
		}
		
		if ($column == 'path') {
			return trim(($this->folder ? $this->folder.'/' : '').$this->name, '/\\');
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
	
	public function setAdapter()
	{
		$type = $this->getIdentifier()->name;
		$this->_adapter = $this->container->getAdapter($type, array('path' => $this->fullpath));
		
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
		$path = $this->container->path.'/'.($this->folder ? $this->folder.'/' : '').$this->name;

		return $path;
	}

    public function toArray()
    {
        $data = parent::toArray();
        
		$data['container'] = $this->container->slug;
		$data['type'] = $this->getIdentifier()->name;

        return $data;
    }	
}