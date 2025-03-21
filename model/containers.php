<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Containers Model
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelContainers extends KModelDatabase
{
    public static $containers = array();

	protected function _buildQueryWhere(KDatabaseQueryInterface $query)
	{
		parent::_buildQueryWhere($query);

        $state = $this->getState();

		if ($state->search) {
            $query->where('tbl.title LIKE :search')->bind(array('search' =>  '%'.$state->search.'%'));
        }
	}

    public static function getContainer($slug)
    {
        if (!isset(self::$containers[$slug]))
        {
            $query = KObjectManager::getInstance()->getObject('database.query.select')
                ->columns('tbl.*')
                ->table(array('tbl' => 'files_containers'))
                ->where('tbl.slug = :slug')
                ->bind(['slug' => $slug])
            ;
    
            self::$containers[$slug] = KObjectManager::getInstance()->getObject('database.adapter.mysqli')->select($query, KDatabase::FETCH_OBJECT);
        }

        return self::$containers[$slug];
    }
}
