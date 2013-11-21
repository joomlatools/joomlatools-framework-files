<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Containers Database Table
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesDatabaseTableContainers extends KDatabaseTableDefault
{
	protected function _initialize(KObjectConfig $config)
	{
		$behavior = $this->getObject('koowa:database.behavior.sluggable', array('columns' => array('id', 'title')));

		$config->append(array(
			'filters' => array(
				'slug' 				 => 'cmd',
				'path'               => 'com:files.filter.path',
				'parameters'         => 'json'
			),
			'behaviors' => array($behavior)
		));

		parent::_initialize($config);
	}
}