<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Config State
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesConfigState extends KConfigState
{
	/**
	 * Needed to make sure form filter does not add config to the form action
	 */
	public function getData($unique = false)
	{
		$data = parent::getData($unique);
		
		unset($data['config']);
		
		return $data;
	}
	
	public function get($name, $default = null)
    {
    	$result = parent::get($name, $default);

        if ($name === 'container' && is_string($result))
        {
            $result = KService::get('com://admin/files.model.containers')->slug($result)->getItem();

	        if (!is_object($result) || $result->isNew()) {
	            throw new UnexpectedValueException('Invalid container');
	        }

	        $this->_data['container']->value = $result;
        }

        return $result;
  	}
  	
    public function toArray($values = true)
    {
    	$array = parent::toArray($values);
    	if (!empty($array['container']) && $array['container'] instanceof KDatabaseRowInterface) {
    		$array['container'] = $array['container']->slug;
    	}

        return $array;
    }
}
