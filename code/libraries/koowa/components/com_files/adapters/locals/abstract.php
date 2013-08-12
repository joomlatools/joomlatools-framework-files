<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Abstract Local Adapter
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
abstract class ComFilesAdapterLocalAbstract extends KObject
{
	/**
	 * Path to the node
	 */
	protected $_path = null;

	/**
	 * A pointer for the FileInfo object
	 */
	protected $_handle = null;

	public function __construct(KConfig $config)
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

	public function setPath($path)
	{
		$path = $this->normalize($path);

		$this->_path = $path;
		$this->_handle = new SplFileInfo($path);

		unset($this->_metadata);

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

	public function getRealPath()
	{
		return $this->_path;
	}

	public function normalize($string)
	{
		return str_replace('\\', '/', $string);
	}
}
