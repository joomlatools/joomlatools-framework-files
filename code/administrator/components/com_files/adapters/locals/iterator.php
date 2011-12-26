<?php

class ComFilesAdapterLocalIterator extends KObject
{
	public function getFiles(array $config = array())
	{
		$config['type'] = 'files';
		return self::getNodes($config);
	}
	
	public function getFolders(array $config = array())
	{
		$config['type'] = 'folders';
		return self::getNodes($config);
	}
	
	public function getNodes(array $config = array())
	{
		$config['path'] = $this->getService('com://admin/files.adapter.local.folder', 
					array('path' => $config['path']))->getEncodedPath();
					
		$results = ComFilesIteratorDirectory::getNodes($config);
		
		foreach ($results as &$result) {
			$result = rawurldecode($result);
		}
		return $results;
	}	
}