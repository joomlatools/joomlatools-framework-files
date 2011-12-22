<?php

class ComFilesAdapterLocalAbstract extends KObject
{
	/**
	 * Path to the node 
	 */
	protected $_path = null;

	/**
	 * A pointer for the FileInfo object
	 */
	protected $_handle = null;

	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		$this->setPath($config->path);
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'path' => ''
		));

		parent::_initialize($config);
	}
	
	public static function getInstance(KConfigInterface $config, KServiceInterface $container)
	{
        if (!$container->has($config->service_identifier)) 
        {
            $instance = new self($config);
            $container->set($config->service_identifier, $instance);
        }
        
        return $container->get($config->service_identifier);
	}

	public function setPath($path)
	{
		$this->_path = $path;
		$this->_handle = new SplFileInfo($this->_path);

		return $this;
	}

	public function getName()
	{
		return $this->normalize($this->_handle->getBasename());
	}

	public function getPath()
	{
		return $this->normalize($this->_handle->getPathname());
	}

	public function getDirname()
	{
		return $this->normalize(dirname($this->_handle->getPathname()));
	}

	public function normalize($string)
	{
		return str_replace('\\', '/', $string);
	}
}