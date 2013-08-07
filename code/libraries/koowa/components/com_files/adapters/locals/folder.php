<?php
/**
  * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

class ComFilesAdapterLocalFolder extends ComFilesAdapterLocalAbstract
{
	public function move($target)
	{
		$result = false;
		$dir = dirname($target);

		if (!is_dir($target)) {
			$result = mkdir($target, 0755, true);
		}

		if (is_dir($target))
		{
			$result = true; // needed for empty directories
			$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_path), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($iterator as $f)
			{
				if ($f->isDir())
				{
					$path = $target.'/'.$iterator->getSubPathName();
					if (!is_dir($path)) {
						$result = mkdir($path);
					}
				}
				else $result = copy($f, $target.'/'.$iterator->getSubPathName());

				if ($result === false) {
					break;
				}
			}
		}

		if ($result && $this->delete()) {
			$this->setPath($target);
		} else {
			$result = false;
		}

		return $result;
	}

	public function copy($target)
	{
		$result = false;
		$dir = dirname($target);

		if (!is_dir($target)) {
			$result = mkdir($target, 0755, true);
		}

		if (is_dir($target))
		{
			$result = true; // needed for empty directories
			$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_path), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($iterator as $f)
			{
				if ($f->isDir()) {
					$path = $target.'/'.$iterator->getSubPathName();
					if (!is_dir($path)) {
						$result = mkdir($path);
					}
				} else {
					$result = copy($f, $target.'/'.$iterator->getSubPathName());
				}

				if ($result === false) {
					break;
				}
			}
		}

		if ($result) {
			$this->setPath($target);
		}

		return $result;
	}

	public function delete()
	{
		if (!file_exists($this->_path)) {
			return true;
		}

		$iter = new RecursiveDirectoryIterator($this->_path);
		foreach (new RecursiveIteratorIterator($iter, RecursiveIteratorIterator::CHILD_FIRST) as $f) 
		{
			if ($f->isDir()) {
				rmdir($f->getPathname());
			} else {
				unlink($f->getPathname());
			}
		}

		return rmdir($this->_path);
	}

	public function create()
	{
		$result = true;

		if (!is_dir($this->_path)) {
			$result = mkdir($this->_path, 0755, true);
		}

		return $result;
	}

	public function exists()
	{
		return is_dir($this->_path);
	}
}
