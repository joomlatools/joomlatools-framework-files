<?php

class ComFilesAdapterLocalFolder extends ComFilesAdapterLocalAbstract 
{
	public function delete()
	{
		if (!file_exists($this->_path)) {
			return true;
		}
		
		$iter = new RecursiveDirectoryIterator($this->_path);
		foreach (new RecursiveIteratorIterator($iter, RecursiveIteratorIterator::CHILD_FIRST) as $f) {
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