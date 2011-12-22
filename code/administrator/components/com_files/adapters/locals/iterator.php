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
		return ComFilesIteratorDirectory::getNodes($config);
	}	
}