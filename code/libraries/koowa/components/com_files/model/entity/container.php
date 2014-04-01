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
    public function getPropertyRelativePath()
    {
        $path = $this->fullpath;
        $root = str_replace('\\', '/', JPATH_ROOT);

        return str_replace($root.'/', '', $path);
    }

    public function getPropertyFullpath()
    {
        $result = $this->getProperty('path');

        // Prepend with site root if it is a relative path
        if (!preg_match('#^(?:[a-z]\:|~*/)#i', $result)) {
            $result = JPATH_ROOT.'/'.$result;
        }

        $result = rtrim(str_replace('\\', '/', $result), '\\');

        return $result;
    }

	public function toArray()
	{
		$data = parent::toArray();
        $data['relative_path'] = $this->getProperty('relative_path');
		$data['parameters']    = $this->getParameters()->toArray();

		return $data;
	}

	public function getAdapter($type, array $config = array())
	{
		return $this->getObject('com:files.adapter.'.$type, $config);
	}
}
