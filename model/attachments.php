<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Attachments Model
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelAttachments extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()->insert('container', 'cmd');
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query->columns(array('tbl.*', 'container_slug' => 'containers.slug'));
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryJoins($query);

        $query->join('files_containers AS containers', 'containers.files_container_id = tbl.files_container_id', 'INNER');
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($container = $state->container) {
            $query->where('tbl.files_container_id IN :container')->bind(array('container' => (array) $container));
        }

        if (!$state->isUnique())
        {
            if ($row = $state->row) {
                $query->where('tbl.row = :row');
            }

            if ($table = $state->table) {
                $query->where('tbl.table = :table');
            }

            if ($name = $state->name) {
                $query->where('tbl.name = :name');
            }

            $query->bind(array('row' => $row, 'table' => $table, 'name' => $name));
        }
    }
}