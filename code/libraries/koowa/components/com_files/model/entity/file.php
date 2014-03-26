<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * File Database Row
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityFile extends ComFilesModelEntityNode
{
	public static $image_extensions = array('jpg', 'jpeg', 'gif', 'png', 'tiff', 'tif', 'xbm', 'bmp');

	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        $this->addBehavior('com:files.database.behavior.thumbnail');
	}

	public function save()
	{
		$context = $this->getContext();
		$context->result = false;

		$is_new = $this->isNew();

		if ($this->invokeCommand('before.save', $context) !== false)
		{
			$context->result = $this->_adapter->write(!empty($this->contents) ? $this->contents : $this->file);
			$this->invokeCommand('after.save', $context);
        }

		if ($context->result === false) {
			$this->setStatus(KDatabase::STATUS_FAILED);
		} else {
            $this->setStatus($is_new ? KDatabase::STATUS_CREATED : KDatabase::STATUS_UPDATED);
        }

		return $context->result;
	}

	public function getProperty($column)
	{
		if (in_array($column, array('size', 'extension', 'modified_date', 'mimetype')))
        {
			$metadata = $this->_adapter->getMetadata();
			return $metadata && array_key_exists($column, $metadata) ? $metadata[$column] : false;
		}

        if (in_array($column, array('width', 'height')))
        {
            $metadata = $this->_adapter->getMetadata();
            return $metadata && array_key_exists($column, $metadata['image']) ? $metadata['image'][$column] : false;
        }

		if ($column == 'filename') {
			return pathinfo($this->name, PATHINFO_FILENAME);
		}

		if ($column == 'metadata')
		{
			$metadata = $this->_adapter->getMetadata();

			return $metadata;
		}

		return parent::getProperty($column);
	}	
	
	/**
	 * This method checks for computed properties as well
	 * 
	 * @param string $key
	 */
	public function hasProperty($key)
	{
		$result = parent::hasProperty($key);
		
		if (!$result) 
		{
			$var = $this->getProperty($key);
			if (!empty($var)) {
				$result = true;
			}
		}
		
		return $result;
		
	}

    public function toArray()
    {
        $data = parent::toArray();

        unset($data['file']);
		unset($data['contents']);

		$data['metadata'] = $this->metadata;

		if ($this->isImage()) {
			$data['type'] = 'image';
		}

        return $data;
    }

	public function isImage()
	{
		return in_array(strtolower($this->extension), self::$image_extensions);
	}
}
