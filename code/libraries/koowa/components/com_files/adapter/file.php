<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * File Local Adapter
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesAdapterFile extends ComFilesAdapterAbstract
{
	protected $_metadata;

	public function getMetadata()
	{
		if ($this->_handle && empty($this->_metadata))
        {
			$this->_metadata = array(
				'extension' => strtolower(pathinfo($this->_handle->getFilename(), PATHINFO_EXTENSION)),
				'mimetype'  => $this->getObject('com:files.mixin.mimetype')->getMimetype($this->_path)
			);

            try
            {
				$this->_metadata += array(
					'size'          => $this->_handle->getSize(),
					'modified_date' => $this->_handle->getMTime()
				);
			}
            catch (RunTimeException $e) {}
		}

		return $this->_metadata;
	}

	public function getImageSize()
	{
		$result = @getimagesize($this->_path);

		if ($result) {
			$result = array_slice($result, 0, 2);
		}

		return $result;
	}

	public function move($target)
	{
		$result = false;
		$dir = dirname($target);

		if (!is_dir($dir)) {
			$result = mkdir($dir, 0755, true);
		}

		if (is_dir($dir) && is_writable($dir)) {
			$result = rename($this->_path, $target);
		}

		if ($result) {
			$this->setPath($target);
			clearstatcache();
		}

		return (bool) $result;
	}

	public function copy($target)
	{
		$result = false;
		$dir = dirname($target);

		if (!is_dir($dir)) {
			$result = mkdir($dir, 0755, true);
		}

		if (is_dir($dir) && is_writable($dir)) {
			$result = copy($this->_path, $target);
		}

		if ($result) {
			$this->setPath($target);
			clearstatcache();
		}

		return (bool) $result;
	}


	public function create()
	{
		$result = true;

		if (!is_file($this->_path)) {
			$result = touch($this->_path);
		}

		return $result;
	}

	public function delete()
	{
		$return = false;

		if (is_file($this->_path)) {
			$return = unlink($this->_path);
		}

		if ($return) {
			$this->_handle = null;
		}

		return $return;
	}

	public function read()
	{
		$result = null;

		if ($this->_handle->isReadable()) {
			$result = file_get_contents($this->_path);
		}

		return $result;
	}

	public function write($data)
	{
		$result = false;

		if (is_writable(dirname($this->_path)))
		{
			if (is_uploaded_file($data)) {
				$result = move_uploaded_file($data, $this->_path);
			} elseif (is_string($data)) {
				$result = file_put_contents($this->_path, $data);
			} elseif ($data instanceof SplFileObject)
			{
				$handle = @fopen($this->_path, 'w');
				if ($handle)
				{
					foreach ($data as $line) {
						$result = fwrite($handle, $line);
					}
					fclose($handle);
				}
			}
		}

		if ($result)
        {
			unset($this->_metadata);
			clearstatcache();
		}

		return (bool) $result;
	}

	public function exists()
	{
		return is_file($this->_path);
	}
}
