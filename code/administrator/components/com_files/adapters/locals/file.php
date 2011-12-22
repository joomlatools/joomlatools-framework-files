<?php

class ComFilesAdapterLocalFile extends ComFilesAdapterLocalAbstract 
{
	public function getMetadata()
	{
		$metadata = false;
		if ($this->_handle) {
			$metadata = array(
				'extension' => pathinfo($this->_handle->getFilename(), PATHINFO_EXTENSION),
				'mimetype' => 'TODO'
			);	
			try {
				$metadata += array(
					'size' => $this->_handle->getSize(),
					'modified_date' => $this->_handle->getMTime()
				);
			} catch (RunTimeException $e) {
			}
		}

		return $metadata;
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
		
		if ($result) {
			clearstatcache();
		}
		
		return (bool) $result;
	}

	public function exists()
	{
		return is_file($this->_path);
	}
}