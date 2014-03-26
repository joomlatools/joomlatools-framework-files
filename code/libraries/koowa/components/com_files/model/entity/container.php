<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Container Entity
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityContainer extends KModelEntityRow
{
	/**
	 * A reference to the container configuration
	 *
	 * @var ComFilesModelEntityConfig
	 */
	protected $_parameters;

	public function getProperty($column)
	{
        if ($column == 'path' && !empty($this->_data['path']))
        {
            $result = $this->_data['path'];
            // Prepend with site root if it is a relative path
            if (!preg_match('#^(?:[a-z]\:|~*/)#i', $result)) {
                $result = JPATH_ROOT.'/'.$result;
            }

            $result = rtrim(str_replace('\\', '/', $result), '\\');
            return $result;
        }

        if ($column == 'relative_path') {
            return $this->getRelativePath();
        }

        if ($column == 'path_value') {
            return $this->_data['path'];
        }

        if ($column == 'parameters' && !is_object($this->_data['parameters'])) {
            return $this->getParameters();
        }

		return parent::getProperty($column);
	}

	public function getRelativePath()
	{
		$path = $this->path;
		$root = str_replace('\\', '/', JPATH_ROOT);

		return str_replace($root.'/', '', $path);
	}

	public function getParameters()
	{
		if (empty($this->_parameters))
        {
            $this->_parameters = $this->getObject('com:files.model.entity.config')
                ->setProperties(json_decode($this->_data['parameters'], true));
		}

		return $this->_parameters;
	}

	public function toArray()
	{
		$data = parent::toArray();

		$data['path']          = $this->path_value;
		$data['parameters']    = $this->parameters->toArray();
		$data['relative_path'] = $this->getRelativePath();

		return $data;
	}

	public function getData($modified = false)
	{
		$data = parent::getData($modified);

		if (isset($data['parameters'])) {
			$data['parameters'] = $this->parameters->getProperties();
		}

		return $data;
	}

	public function getAdapter($type, array $config = array())
	{
		return $this->getObject('com:files.adapter.'.$type, $config);
	}
}
